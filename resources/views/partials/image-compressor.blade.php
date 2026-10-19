{{-- Silent Automatic Image Compression (Compressor.js) --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/compressorjs/1.2.1/compressor.min.js"></script>
<script>
    (function () {
        'use strict';

        var CONFIG = {
            quality: 0.6,
            maxWidth: 1200,
            maxHeight: 1200,
            convertSize: 1000000
        };

        var STATUS_CLASS = 'silent-compress-status';
        var statusMap = new WeakMap();
        var inputState = new WeakMap();
        var pendingCount = 0;
        var deferredForms = new Map();
        var bypassSubmit = new WeakSet();

        function isImageFile(file) {
            if (!file) return false;
            if (file.type && file.type.indexOf('image/') === 0) return true;
            return /\.(jpe?g|png|webp|gif|bmp|heic|heif|avif)$/i.test(file.name || '');
        }

        function formatSize(bytes) {
            if (!bytes && bytes !== 0) return '';
            if (bytes >= 1024 * 1024) return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
            return Math.max(1, Math.round(bytes / 1024)) + ' KB';
        }

        function showStatus(input, text, tone) {
            var el = statusMap.get(input);
            if (!el || !el.isConnected) {
                el = document.createElement('div');
                el.className = STATUS_CLASS;
                el.style.cssText = 'display:flex;align-items:center;gap:.35rem;margin-top:.45rem;font-size:.78rem;font-weight:700;line-height:1.4;';
                if (input.parentNode) {
                    input.parentNode.insertBefore(el, input.nextSibling);
                } else {
                    input.insertAdjacentElement('afterend', el);
                }
                statusMap.set(input, el);
            }
            el.textContent = text;
            el.style.color = tone === 'success' ? '#047857' : (tone === 'error' ? '#b91c1c' : '#475569');
        }

        function clearStatus(input) {
            var el = statusMap.get(input);
            if (el && el.isConnected) el.remove();
            statusMap.delete(input);
        }

        function flushDeferredForms() {
            if (pendingCount > 0) return;

            var queue = Array.from(deferredForms.entries());
            deferredForms.clear();

            queue.forEach(function (entry) {
                var form = entry[0];
                var submitter = entry[1];

                if (!form || !form.isConnected) return;

                bypassSubmit.add(form);
                try {
                    if (typeof form.requestSubmit === 'function') {
                        if (submitter && submitter.isConnected) {
                            form.requestSubmit(submitter);
                        } else {
                            form.requestSubmit();
                        }
                    } else {
                        form.submit();
                    }
                } catch (err) {
                    bypassSubmit.delete(form);
                    try { form.submit(); } catch (e) {}
                }
            });
        }

        function rebuildInputFiles(input, files) {
            var dataTransfer = new DataTransfer();
            files.forEach(function (file) {
                dataTransfer.items.add(file);
            });
            input.files = dataTransfer.files;
        }

        function processInput(input) {
            var files = Array.prototype.slice.call(input.files || []);
            if (!files.length) {
                clearStatus(input);
                return;
            }

            var imageFiles = files.filter(isImageFile);
            if (!imageFiles.length || typeof Compressor === 'undefined') return;

            var state = inputState.get(input) || { token: 0 };
            state.token += 1;
            var token = state.token;
            inputState.set(input, state);

            var totalBefore = imageFiles.reduce(function (sum, file) { return sum + (file.size || 0); }, 0);
            var results = files.slice();
            pendingCount += 1;

            showStatus(input, imageFiles.length > 1
                ? '⏳ Mengoptimalkan ' + imageFiles.length + ' foto...'
                : '⏳ Mengoptimalkan foto...', 'info');

            var tasks = imageFiles.map(function (file) {
                return new Promise(function (resolve) {
                    new Compressor(file, Object.assign({}, CONFIG, {
                        success: function (result) {
                            try {
                                var outputType = result.type || file.type || 'image/jpeg';
                                var compressed = new File([result], file.name, {
                                    type: outputType,
                                    lastModified: Date.now()
                                });
                                results[files.indexOf(file)] = compressed;
                            } catch (err) {
                                console.warn('[ImageCompressor] Gagal membuat File:', err);
                            }
                            resolve();
                        },
                        error: function (err) {
                            console.warn('[ImageCompressor] Gagal mengompres ' + file.name + ':', err);
                            resolve();
                        }
                    }));
                });
            });

            Promise.all(tasks).then(function () {
                pendingCount = Math.max(0, pendingCount - 1);

                if (state.token !== token) {
                    flushDeferredForms();
                    return;
                }

                var replaced = false;
                try {
                    rebuildInputFiles(input, results);
                    replaced = true;
                } catch (err) {
                    console.warn('[ImageCompressor] DataTransfer tidak didukung:', err);
                }

                var totalAfter = results.reduce(function (sum, file) { return sum + (file.size || 0); }, 0);
                var detail = totalBefore > 0 && totalAfter > 0
                    ? ' (' + formatSize(totalBefore) + ' → ' + formatSize(totalAfter) + ')'
                    : '';

                if (replaced) {
                    showStatus(input, '✅ Foto dioptimalkan otomatis' + detail, 'success');
                } else {
                    showStatus(input, 'Foto siap diunggah.', 'info');
                }
                flushDeferredForms();
            }).catch(function (err) {
                pendingCount = Math.max(0, pendingCount - 1);
                console.warn('[ImageCompressor] Proses kompresi gagal:', err);
                showStatus(input, 'Foto siap diunggah.', 'info');
                flushDeferredForms();
            });
        }

        document.addEventListener('change', function (event) {
            var input = event.target;

            if (!input || input.tagName !== 'INPUT' || input.type !== 'file') return;
            if (input.dataset.noCompress === 'true') return;

            processInput(input);
        });

        document.addEventListener('submit', function (event) {
            var form = event.target;
            if (!form || form.tagName !== 'FORM') return;

            if (bypassSubmit.has(form)) {
                bypassSubmit.delete(form);
                return;
            }

            if (pendingCount > 0) {
                event.preventDefault();
                deferredForms.set(form, event.submitter || null);
            }
        }, true);
    })();
</script>

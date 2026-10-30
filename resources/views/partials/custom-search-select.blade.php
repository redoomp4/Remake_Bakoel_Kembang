{{-- Custom Searchable Select Dropdown (Mobile-Friendly & Thumb-Friendly for Elderly) --}}
{{-- Tanpa Select2 / Tanpa Inline Creation --}}

<style>
    .custom-search-select-wrapper {
        position: relative;
        width: 100%;
        font-family: inherit;
    }

    /* Pemicu Dropdown - Touch Target Minimal 48px (52px), Font Tegas Minimal 16px */
    .css-trigger-btn {
        width: 100%;
        min-height: 52px;
        padding: 0.75rem 1rem;
        background-color: #ffffff;
        border: 2px solid #cbd5e1;
        border-radius: 0.85rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        cursor: pointer;
        transition: all 0.2s ease-in-out;
        text-align: left;
        box-sizing: border-box;
    }

    .css-trigger-btn:hover {
        border-color: #059669;
        background-color: #fafafa;
    }

    .css-trigger-btn:focus,
    .css-trigger-btn.is-active {
        outline: none;
        border-color: #047857;
        box-shadow: 0 0 0 4px rgba(4, 120, 87, 0.15);
    }

    .css-trigger-content {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        min-width: 0;
        flex: 1;
    }

    .css-trigger-icon {
        width: 36px;
        height: 36px;
        border-radius: 0.6rem;
        background-color: #ecfdf5;
        color: #047857;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        font-weight: 800;
        flex-shrink: 0;
    }

    .css-trigger-text {
        min-width: 0;
        flex: 1;
    }

    .css-trigger-main {
        display: block;
        font-size: 1.05rem; /* >= 16px */
        font-weight: 800;
        color: #0f172a;
        line-height: 1.35;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .css-trigger-placeholder {
        color: #64748b;
        font-weight: 700;
    }

    .css-trigger-sub {
        display: block;
        font-size: 0.78rem;
        font-weight: 600;
        color: #64748b;
        margin-top: 0.1rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .css-trigger-chevron {
        color: #64748b;
        font-size: 1.1rem;
        transition: transform 0.2s ease;
        flex-shrink: 0;
    }

    .css-trigger-btn.is-active .css-trigger-chevron {
        transform: rotate(180deg);
        color: #047857;
    }

    /* Modal / Popover Dropdown */
    .css-dropdown-panel {
        display: none;
        position: absolute;
        top: calc(100% + 6px);
        left: 0;
        right: 0;
        background: #ffffff;
        border: 2px solid #059669;
        border-radius: 1rem;
        box-shadow: 0 15px 35px -5px rgba(0, 0, 0, 0.18), 0 8px 16px -6px rgba(0, 0, 0, 0.1);
        z-index: 9999;
        overflow: hidden;
        box-sizing: border-box;
    }

    .css-dropdown-panel.is-open {
        display: flex;
        flex-direction: column;
    }

    /* Tampilan Khusus Mobile HP (Bottom Sheet / Overlay Bersahabat Lansia) */
    @media (max-width: 640px) {
        .css-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.55);
            backdrop-filter: blur(2px);
            z-index: 99998;
        }

        .css-backdrop.is-open {
            display: block;
        }

        .css-dropdown-panel.is-open {
            position: fixed;
            top: auto;
            bottom: 0;
            left: 0;
            right: 0;
            width: 100%;
            max-height: 85vh;
            border-radius: 1.5rem 1.5rem 0 0;
            border-width: 2px 2px 0 2px;
            z-index: 99999;
            animation: slideUpCss 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes slideUpCss {
            from {
                transform: translateY(100%);
            }
            to {
                transform: translateY(0);
            }
        }
    }

    /* Header Panel */
    .css-panel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.9rem 1.1rem;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
    }

    .css-panel-title {
        font-size: 1rem;
        font-weight: 900;
        color: #047857;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin: 0;
    }

    .css-btn-close {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.35rem;
        min-height: 48px; /* Touch target minimal 48px */
        padding: 0.5rem 1rem;
        background: #e2e8f0;
        border: none;
        border-radius: 0.75rem;
        font-size: 0.95rem;
        font-weight: 800;
        color: #334155;
        cursor: pointer;
        transition: background 0.15s;
    }

    .css-btn-close:hover,
    .css-btn-close:active {
        background: #cbd5e1;
        color: #0f172a;
    }

    /* Kotak Pencarian Nyaman di Jempol */
    .css-search-box {
        padding: 0.75rem 1rem;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        position: relative;
    }

    .css-search-input {
        width: 100%;
        min-height: 48px; /* Touch target minimal 48px */
        height: 48px;
        padding: 0.65rem 2.5rem 0.65rem 1rem;
        font-size: 1.05rem; /* Minimal 16px cegah auto zoom di iOS */
        font-weight: 700;
        color: #0f172a;
        background: #ffffff;
        border: 2px solid #a7f3d0;
        border-radius: 0.75rem;
        box-sizing: border-box;
        transition: all 0.2s;
    }

    .css-search-input:focus {
        outline: none;
        border-color: #059669;
        box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.2);
    }

    .css-search-clear {
        position: absolute;
        right: 1.5rem;
        top: 50%;
        transform: translateY(-50%);
        min-width: 36px;
        min-height: 36px;
        display: none;
        align-items: center;
        justify-content: center;
        background: #f1f5f9;
        border: none;
        border-radius: 50%;
        color: #64748b;
        font-size: 0.85rem;
        font-weight: 800;
        cursor: pointer;
    }

    /* Daftar Pilihan */
    .css-options-list {
        list-style: none;
        margin: 0;
        padding: 0;
        max-height: 320px;
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
    }

    @media (max-width: 640px) {
        .css-options-list {
            max-height: 52vh;
        }
    }

    .css-option-item {
        width: 100%;
        min-height: 52px; /* Touch target minimal 48px */
        padding: 0.85rem 1.1rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.85rem;
        border: none;
        border-bottom: 1px solid #f1f5f9;
        background: #ffffff;
        text-align: left;
        cursor: pointer;
        transition: background 0.15s ease;
        box-sizing: border-box;
    }

    .css-option-item:hover,
    .css-option-item:focus {
        background: #ecfdf5;
        outline: none;
    }

    .css-option-item.is-selected {
        background: #f0fdf4;
        border-left: 5px solid #059669;
    }

    .css-option-info {
        min-width: 0;
        flex: 1;
    }

    .css-option-name {
        font-size: 1.05rem; /* Minimal 16px */
        font-weight: 800;
        color: #0f172a;
        line-height: 1.35;
    }

    .css-option-meta {
        font-size: 0.82rem;
        font-weight: 600;
        color: #64748b;
        margin-top: 0.2rem;
    }

    .css-option-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.35rem 0.75rem;
        border-radius: 9999px;
        background: #d1fae5;
        color: #065f46;
        font-size: 0.85rem;
        font-weight: 900;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .css-option-badge.out-of-stock {
        background: #fee2e2;
        color: #991b1b;
    }

    .css-empty-state {
        padding: 2.5rem 1.25rem;
        text-align: center;
        color: #64748b;
        font-size: 0.95rem;
        font-weight: 700;
    }

    /* Visually hide native select while preserving accessible DOM & forms */
    .css-native-hidden {
        position: absolute !important;
        width: 1px !important;
        height: 1px !important;
        padding: 0 !important;
        margin: -1px !important;
        overflow: hidden !important;
        clip: rect(0, 0, 0, 0) !important;
        white-space: nowrap !important;
        border: 0 !important;
    }
</style>

<script>
    (function () {
        'use strict';

        /**
         * Mengubah elemen <select> biasa menjadi Dropdown Berfitur Search
         * Ramah HP & Lansia (Touch target >= 48px, Font >= 16px).
         * Murni tanpa Select2 / Inline creation.
         */
        window.makeSearchableSelect = function (selectElement, options) {
            if (!selectElement) return null;
            if (selectElement._searchableSelectInstance) {
                selectElement._searchableSelectInstance.refresh();
                return selectElement._searchableSelectInstance;
            }

            options = options || {};
            const placeholder = options.placeholder || '-- Ketuk untuk Memilih --';
            const searchPlaceholder = options.searchPlaceholder || '🔍 Cari nama atau kode bunga...';
            const emptyText = options.emptyText || 'Bunga atau item tidak ditemukan.';

            // Sembunyikan select asli namun biarkan tetap di DOM untuk form submit
            selectElement.classList.add('css-native-hidden');

            // Wrapper Utama
            const wrapper = document.createElement('div');
            wrapper.className = 'custom-search-select-wrapper';

            // Tombol Pemicu Utama (Touch target >= 48px, Font >= 16px)
            const trigger = document.createElement('button');
            trigger.type = 'button';
            trigger.className = 'css-trigger-btn';
            trigger.setAttribute('aria-haspopup', 'listbox');
            trigger.setAttribute('aria-expanded', 'false');

            trigger.innerHTML = `
                <div class="css-trigger-content">
                    <div class="css-trigger-icon">🌸</div>
                    <div class="css-trigger-text">
                        <span class="css-trigger-main css-trigger-placeholder">${placeholder}</span>
                        <span class="css-trigger-sub"></span>
                    </div>
                </div>
                <i class="fas fa-chevron-down css-trigger-chevron"></i>
            `;

            // Backdrop Layar HP
            const backdrop = document.createElement('div');
            backdrop.className = 'css-backdrop';

            // Panel Dropdown
            const panel = document.createElement('div');
            panel.className = 'css-dropdown-panel';

            panel.innerHTML = `
                <div class="css-panel-header">
                    <h3 class="css-panel-title"><i class="fas fa-boxes-stacked"></i> Pilih Bunga / Item</h3>
                    <button type="button" class="css-btn-close">
                        <i class="fas fa-times"></i> Tutup
                    </button>
                </div>
                <div class="css-search-box">
                    <input type="text" class="css-search-input" placeholder="${searchPlaceholder}" autocomplete="off">
                    <button type="button" class="css-search-clear">✕</button>
                </div>
                <div class="css-options-list" role="listbox"></div>
            `;

            wrapper.appendChild(trigger);
            wrapper.appendChild(backdrop);
            wrapper.appendChild(panel);

            selectElement.parentNode.insertBefore(wrapper, selectElement.nextSibling);

            const labelEl = trigger.querySelector('.css-trigger-main');
            const subEl = trigger.querySelector('.css-trigger-sub');
            const searchInput = panel.querySelector('.css-search-input');
            const clearBtn = panel.querySelector('.css-search-clear');
            const listEl = panel.querySelector('.css-options-list');
            const closeBtn = panel.querySelector('.css-btn-close');

            let itemsCache = [];

            function parseOption(option) {
                const text = option.textContent.trim();
                const value = option.value;
                const isSelected = option.selected;
                const isDisabled = option.disabled;

                if (!value && isDisabled) return null; // placeholder option

                const nama = option.dataset.nama || text.split('—')[0].trim();
                const stok = option.dataset.stok !== undefined ? option.dataset.stok : null;
                const satuan = option.dataset.satuan || 'pcs';
                const kode = option.dataset.kode || '';
                const lokasi = option.dataset.lokasi || '';
                const kondisi = option.dataset.kondisi || '';

                // Summary string yang ringkas: misal "Mawar Merah — Stok: 15 pcs"
                let conciseText = text;
                if (stok !== null) {
                    conciseText = `${nama} — Stok: ${stok} ${satuan}`;
                }

                let metaParts = [];
                if (kode) metaParts.push(kode);
                if (lokasi) metaParts.push(lokasi);
                if (kondisi) metaParts.push(kondisi);

                return {
                    value: value,
                    nama: nama,
                    stok: stok,
                    satuan: satuan,
                    conciseText: conciseText,
                    metaText: metaParts.join(' · '),
                    selected: isSelected,
                    originalOption: option
                };
            }

            function syncFromSelect() {
                itemsCache = [];
                const options = Array.from(selectElement.options);
                options.forEach(opt => {
                    const parsed = parseOption(opt);
                    if (parsed) itemsCache.push(parsed);
                });

                renderList(searchInput.value.trim());
                updateTriggerText();
            }

            function updateTriggerText() {
                const selected = itemsCache.find(it => String(it.value) === String(selectElement.value));
                if (selected && selectElement.value !== '') {
                    labelEl.textContent = selected.conciseText;
                    labelEl.classList.remove('css-trigger-placeholder');
                    if (selected.metaText) {
                        subEl.textContent = selected.metaText;
                        subEl.style.display = 'block';
                    } else {
                        subEl.style.display = 'none';
                    }
                } else {
                    labelEl.textContent = placeholder;
                    labelEl.classList.add('css-trigger-placeholder');
                    subEl.style.display = 'none';
                }
            }

            function renderList(query) {
                listEl.innerHTML = '';
                const q = (query || '').toLowerCase().trim();

                const filtered = itemsCache.filter(item => {
                    if (!q) return true;
                    return item.nama.toLowerCase().includes(q) ||
                           item.conciseText.toLowerCase().includes(q) ||
                           item.metaText.toLowerCase().includes(q) ||
                           String(item.value).toLowerCase().includes(q);
                });

                if (filtered.length === 0) {
                    listEl.innerHTML = `<div class="css-empty-state"><i class="fas fa-search mb-2" style="font-size:1.5rem;display:block;"></i>${emptyText}</div>`;
                    return;
                }

                filtered.forEach(item => {
                    const isSelected = String(item.value) === String(selectElement.value);
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = `css-option-item ${isSelected ? 'is-selected' : ''}`;
                    btn.setAttribute('role', 'option');
                    btn.setAttribute('aria-selected', isSelected ? 'true' : 'false');

                    let badgeHtml = '';
                    if (item.stok !== null) {
                        const stockNum = parseFloat(item.stok);
                        const isOut = !isNaN(stockNum) && stockNum <= 0;
                        badgeHtml = `<span class="css-option-badge ${isOut ? 'out-of-stock' : ''}">Stok: ${item.stok} ${item.satuan}</span>`;
                    }

                    btn.innerHTML = `
                        <div class="css-option-info">
                            <div class="css-option-name">${item.nama}</div>
                            ${item.metaText ? `<div class="css-option-meta">${item.metaText}</div>` : ''}
                        </div>
                        ${badgeHtml}
                    `;

                    btn.addEventListener('click', function () {
                        selectValue(item.value);
                        closeDropdown();
                    });

                    listEl.appendChild(btn);
                });
            }

            function selectValue(val) {
                selectElement.value = val;
                // Dispatch native change event so all existing controllers / calculate scripts work
                selectElement.dispatchEvent(new Event('change', { bubbles: true }));
                updateTriggerText();
                renderList(searchInput.value.trim());
            }

            function openDropdown() {
                panel.classList.add('is-open');
                backdrop.classList.add('is-open');
                trigger.classList.add('is-active');
                trigger.setAttribute('aria-expanded', 'true');
                searchInput.value = '';
                clearBtn.style.display = 'none';
                renderList('');

                setTimeout(() => {
                    searchInput.focus();
                }, 80);
            }

            function closeDropdown() {
                panel.classList.remove('is-open');
                backdrop.classList.remove('is-open');
                trigger.classList.remove('is-active');
                trigger.setAttribute('aria-expanded', 'false');
            }

            // Events
            trigger.addEventListener('click', function (e) {
                e.preventDefault();
                if (panel.classList.contains('is-open')) {
                    closeDropdown();
                } else {
                    openDropdown();
                }
            });

            closeBtn.addEventListener('click', closeDropdown);
            backdrop.addEventListener('click', closeDropdown);

            searchInput.addEventListener('input', function () {
                const val = this.value;
                clearBtn.style.display = val ? 'flex' : 'none';
                renderList(val);
            });

            clearBtn.addEventListener('click', function () {
                searchInput.value = '';
                this.style.display = 'none';
                renderList('');
                searchInput.focus();
            });

            // Klik di luar panel menutup dropdown di desktop
            document.addEventListener('click', function (e) {
                if (!wrapper.contains(e.target)) {
                    closeDropdown();
                }
            });

            // Keyboard navigation
            wrapper.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    closeDropdown();
                    trigger.focus();
                }
            });

            // Pantau perubahan dari native select (misal form.reset() atau set value programatik)
            selectElement.addEventListener('change', updateTriggerText);

            // MutationObserver untuk mendeteksi penambahan <option> baru secara dinamis
            const observer = new MutationObserver(function () {
                syncFromSelect();
            });
            observer.observe(selectElement, { childList: true, subtree: true });

            // Inisialisasi awal
            syncFromSelect();

            const instance = {
                refresh: syncFromSelect,
                close: closeDropdown,
                open: openDropdown
            };

            selectElement._searchableSelectInstance = instance;
            return instance;
        };

        // Auto-init elemen select yang memiliki atribut data-searchable="true"
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('select[data-searchable="true"]').forEach(function (el) {
                window.makeSearchableSelect(el);
            });
        });
    })();
</script>

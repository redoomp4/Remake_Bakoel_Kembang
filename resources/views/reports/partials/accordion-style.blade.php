<style>
    :root {
        --report-emerald: #0B4F35;
        --report-emerald-dark: #053b27;
        --report-emerald-soft: #ecfdf5;
        --report-blue: #1d4ed8;
        --report-blue-soft: #eff6ff;
        --report-amber: #b45309;
        --report-slate: #0f172a;
        --report-muted: #475569;
        --report-border: #cbd5e1;
        --report-bg: #f8fafc;
    }

    body {
        background: var(--report-bg);
        font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        font-size: 16px;
        color: var(--report-slate);
    }

    .report-shell,
    main > .container.report-shell {
        width: 100%;
        max-width: 1280px !important;
        min-width: 0;
        flex: 1 1 0%;
        box-sizing: border-box;
        margin-left: auto;
        margin-right: auto;
        padding: 2rem 1.25rem 3.5rem;
    }

    .report-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1.25rem;
        background: #ffffff;
        border: 2px solid #e2e8f0;
        border-radius: 1.25rem;
        padding: 1.6rem 2rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 4px 20px rgba(15, 23, 42, .06);
    }

    .report-header h1 {
        margin: 0;
        color: var(--report-emerald);
        font-size: clamp(1.75rem, 3.5vw, 2.3rem);
        font-weight: 900;
        letter-spacing: -.03em;
        line-height: 1.2;
    }

    .report-header p {
        margin: .5rem 0 0;
        color: var(--report-muted);
        font-size: 1.05rem;
        font-weight: 600;
    }

    .report-back {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .5rem;
        padding: .9rem 1.4rem;
        border-radius: 1rem;
        background: var(--report-emerald-soft);
        color: var(--report-emerald-dark);
        font-size: 1rem;
        font-weight: 800;
        text-decoration: none;
        border: 1.5px solid #a7f3d0;
        transition: all .15s ease-in-out;
    }

    .report-back:hover {
        background: #d1fae5;
        color: var(--report-emerald-dark);
        border-color: #6ee7b7;
    }

    .report-filter {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        gap: 1.25rem;
        background: #ffffff;
        border: 2px solid #e2e8f0;
        border-radius: 1.25rem;
        padding: 1.5rem 1.75rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 4px 20px rgba(15, 23, 42, .06);
    }

    .report-filter .field {
        display: flex;
        flex-direction: column;
        flex: 1 1 200px;
        min-width: 0;
    }

    .report-filter label {
        margin-bottom: .5rem;
        color: #334155;
        font-size: .95rem;
        font-weight: 800;
        letter-spacing: .03em;
        text-transform: uppercase;
    }

    .report-filter input,
    .report-filter select {
        width: 100%;
        box-sizing: border-box;
        padding: .9rem 1.15rem;
        border: 2px solid #cbd5e1;
        border-radius: .85rem;
        background: #ffffff;
        color: #0f172a;
        font-family: inherit;
        font-size: 1.05rem;
        font-weight: 600;
        min-height: 50px;
    }

    .report-filter input:focus,
    .report-filter select:focus {
        border-color: var(--report-emerald);
        outline: 0;
        box-shadow: 0 0 0 4px rgba(11, 79, 53, .15);
    }

    .report-filter .actions {
        display: flex;
        flex-wrap: wrap;
        gap: .75rem;
    }

    .report-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .5rem;
        padding: .95rem 1.5rem;
        border: 0;
        border-radius: .85rem;
        background: var(--report-emerald);
        color: #ffffff;
        font-family: inherit;
        font-size: 1rem;
        font-weight: 800;
        line-height: 1.2;
        text-decoration: none;
        cursor: pointer;
        white-space: nowrap;
        min-height: 50px;
        box-shadow: 0 2px 8px rgba(11, 79, 53, .2);
        transition: all .15s ease-in-out;
    }

    .report-btn:hover {
        background: var(--report-emerald-dark);
        color: #ffffff;
    }

    .report-btn--soft {
        background: #f1f5f9;
        color: #334155;
        box-shadow: none;
        border: 1.5px solid #cbd5e1;
    }

    .report-btn--soft:hover {
        background: #e2e8f0;
        color: #0f172a;
    }

    .report-btn--pdf {
        background: #be123c;
        box-shadow: 0 2px 8px rgba(190, 18, 60, .2);
    }

    .report-btn--pdf:hover {
        background: #9f1239;
    }

    .report-exports {
        display: flex;
        flex-wrap: wrap;
        gap: .75rem;
        margin-bottom: 1.5rem;
    }

    /* Giant Summary Cards */
    .report-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.25rem;
        margin-bottom: 1.75rem;
    }

    .report-card {
        position: relative;
        overflow: hidden;
        background: #ffffff;
        border: 2px solid #e2e8f0;
        border-radius: 1.25rem;
        padding: 1.6rem 1.75rem;
        box-shadow: 0 4px 20px rgba(15, 23, 42, .06);
    }

    .report-card--omzet {
        background: linear-gradient(145deg, #ecfdf5 0%, #ffffff 80%);
        border-color: #6ee7b7;
    }

    .report-card--laba {
        background: linear-gradient(145deg, #eff6ff 0%, #ffffff 80%);
        border-color: #93c5fd;
    }

    .report-card__label {
        position: relative;
        z-index: 1;
        color: #334155;
        font-size: 1rem;
        font-weight: 900;
        letter-spacing: .05em;
        text-transform: uppercase;
    }

    .report-card__value {
        position: relative;
        z-index: 1;
        margin-top: .65rem;
        color: var(--report-emerald);
        font-size: clamp(2rem, 5vw, 2.75rem);
        font-weight: 900;
        letter-spacing: -.035em;
        line-height: 1.1;
    }

    .report-card--laba .report-card__value {
        color: var(--report-blue);
    }

    .report-card__sub {
        position: relative;
        z-index: 1;
        margin-top: .6rem;
        color: #475569;
        font-size: 1rem;
        font-weight: 600;
        line-height: 1.35;
    }

    /* Main Table */
    .report-table-wrap {
        width: 100%;
        overflow-x: auto;
        background: #ffffff;
        border: 2px solid #e2e8f0;
        border-radius: 1.25rem;
        box-shadow: 0 4px 20px rgba(15, 23, 42, .06);
    }

    .report-table {
        width: 100%;
        min-width: 820px;
        margin: 0;
        border-collapse: collapse;
        font-size: 1.05rem;
    }

    .report-table th {
        padding: 1.2rem 1.25rem;
        background: #f1f5f9;
        border-bottom: 2px solid #cbd5e1;
        color: #1e293b;
        font-size: .95rem;
        font-weight: 900;
        letter-spacing: .05em;
        text-align: left;
        text-transform: uppercase;
    }

    .report-table th a {
        color: inherit;
        text-decoration: none;
    }

    .report-table th a:hover {
        color: var(--report-emerald);
    }

    .report-table td {
        padding: 1.25rem 1.25rem;
        border-bottom: 1.5px solid #e2e8f0;
        color: var(--report-slate);
        font-size: 1.05rem;
        font-weight: 600;
        vertical-align: middle;
    }

    .report-table > tbody > tr.parent-row:hover td {
        background: #f8fafc;
    }

    .report-name {
        display: block;
        color: var(--report-emerald-dark);
        font-size: 1.2rem;
        font-weight: 900;
        line-height: 1.25;
    }

    .report-meta {
        display: block;
        margin-top: .35rem;
        color: #64748b;
        font-size: .95rem;
        font-weight: 600;
    }

    .report-quantity {
        color: var(--report-emerald-dark);
        font-size: 1.25rem;
        font-weight: 900;
        white-space: nowrap;
    }

    .report-value {
        color: var(--report-slate);
        font-size: 1.2rem;
        font-weight: 900;
        white-space: nowrap;
    }

    /* Accordion Toggle Button */
    .report-toggle {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .5rem;
        padding: .85rem 1.25rem;
        border: 2px solid #6ee7b7;
        border-radius: .95rem;
        background: #ecfdf5;
        color: var(--report-emerald-dark);
        font-family: inherit;
        font-size: 1rem;
        font-weight: 900;
        cursor: pointer;
        white-space: nowrap;
        min-height: 46px;
        transition: all .15s ease-in-out;
    }

    .report-toggle:hover,
    .report-toggle.is-open {
        background: var(--report-emerald);
        border-color: var(--report-emerald);
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(11, 79, 53, .25);
    }

    /* Accordion Detail Row & Sub-table */
    .report-detail-row > td {
        padding: 0 !important;
        border-bottom: 2px solid #cbd5e1 !important;
        background: #f0fdf4 !important;
    }

    .report-detail-panel {
        padding: 1.4rem 1.6rem 1.6rem;
        overflow-x: auto;
    }

    .report-detail-title {
        margin: 0 0 .9rem;
        color: var(--report-emerald-dark);
        font-size: 1.05rem;
        font-weight: 900;
        letter-spacing: .03em;
        text-transform: uppercase;
    }

    .report-detail-table {
        width: 100%;
        min-width: 720px;
        border-collapse: separate;
        border-spacing: 0;
        overflow: hidden;
        border: 2px solid #bbf7d0;
        border-radius: 1rem;
        background: #ffffff;
        box-shadow: 0 2px 10px rgba(15, 23, 42, .04);
    }

    .report-detail-table th {
        padding: .95rem 1.15rem;
        background: #dcfce7;
        border-bottom: 2px solid #bbf7d0;
        color: #166534;
        font-size: .95rem;
        font-weight: 900;
        letter-spacing: .04em;
        text-align: left;
        text-transform: uppercase;
    }

    .report-detail-table td {
        padding: 1rem 1.15rem;
        border-bottom: 1.5px solid #f1f5f9;
        color: #1e293b;
        font-size: 1.02rem;
        font-weight: 600;
    }

    .report-detail-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .report-detail-table tfoot td {
        padding: 1.1rem 1.15rem;
        background: #f8fafc;
        border-top: 2px solid #bbf7d0;
        color: var(--report-emerald-dark);
        font-size: 1.05rem;
        font-weight: 900;
    }

    .report-badge {
        display: inline-flex;
        align-items: center;
        padding: .4rem .85rem;
        border-radius: 999px;
        font-size: .95rem;
        font-weight: 900;
        letter-spacing: .02em;
    }

    .report-badge--in {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #86efac;
    }

    .report-badge--out {
        background: #dbeafe;
        color: #1d4ed8;
        border: 1px solid #93c5fd;
    }

    .report-empty {
        padding: 3rem 1.5rem !important;
        color: var(--report-muted) !important;
        text-align: center !important;
        font-size: 1.1rem !important;
        font-weight: 700 !important;
    }

    .report-pagination {
        margin-top: 1.5rem;
        text-align: center;
    }

    .report-pagination .pagination {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: .25rem;
        margin-bottom: 0;
    }

    .report-pagination .summary {
        margin-bottom: .85rem;
        color: var(--report-muted);
        font-size: 1rem;
        font-weight: 700;
    }

    @media (max-width: 768px) {
        .report-shell,
        main > .container.report-shell {
            padding: 1.25rem .75rem 2.5rem;
        }

        .report-header,
        .report-filter {
            padding: 1.25rem 1rem;
            border-radius: 1rem;
        }

        .report-filter {
            flex-direction: column;
            align-items: stretch;
        }

        .report-filter .actions {
            display: grid;
            grid-template-columns: 1fr;
        }

        .report-filter .actions .report-btn {
            width: 100%;
        }

        .report-exports {
            display: grid;
            grid-template-columns: 1fr;
        }

        .report-exports .report-btn {
            width: 100%;
        }

        .report-cards {
            grid-template-columns: 1fr;
        }

        .report-table td,
        .report-table th {
            padding: 1rem .85rem;
        }

        .report-back {
            width: 100%;
        }
    }
</style>

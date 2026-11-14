// Rainpedia GitHub Pages Interactive Demo Toolbar & Status Interactivity
(function() {
    // Detect current page filename
    const path = window.location.pathname;
    let page = path.substring(path.lastIndexOf('/') + 1);
    if (!page || page === '') page = 'index.html';

    // Role detection
    let currentRole = 'public';
    if (page.startsWith('admin-') || page.startsWith('print-')) {
        currentRole = 'admin';
    } else if (page.startsWith('user-')) {
        currentRole = 'user';
    }

    const pages = [
        { group: '🌐 Public Area', items: [
            { file: 'index.html', title: 'Beranda (Landing)' },
            { file: 'catalog.html', title: 'Katalog Buku' },
            { file: 'book-detail.html', title: 'Detail Buku' },
            { file: 'about.html', title: 'Tentang Rainpedia' },
            { file: 'login.html', title: 'Halaman Login' },
            { file: 'register.html', title: 'Halaman Register' }
        ]},
        { group: '👤 User POV (User A: Rain)', items: [
            { file: 'user-dashboard.html', title: 'Dashboard User' },
            { file: 'user-catalog.html', title: 'Katalog Buku (User)' },
            { file: 'user-cart.html', title: 'Keranjang Belanja' },
            { file: 'user-checkout.html', title: 'Checkout Pembelian' },
            { file: 'user-transactions.html', title: 'Riwayat Pembelian' },
            { file: 'user-transaction-detail.html', title: 'Detail Pembelian' },
            { file: 'user-borrowings.html', title: 'Peminjaman Saya' },
            { file: 'user-chat.html', title: 'Tanya Admin (Chat)' },
            { file: 'user-notifications.html', title: 'Notifikasi' },
            { file: 'user-profile.html', title: 'Profil Pengguna' }
        ]},
        { group: '🛡️ Admin POV (Administrator)', items: [
            { file: 'admin-dashboard.html', title: 'Dashboard Admin' },
            { file: 'admin-orders.html', title: 'Data Pembelian' },
            { file: 'admin-order-detail.html', title: 'Detail Pesanan' },
            { file: 'admin-borrowings.html', title: 'Data Peminjaman Buku' },
            { file: 'admin-returns.html', title: 'Pengembalian Buku' },
            { file: 'admin-books.html', title: 'Manajemen Data Buku' },
            { file: 'admin-categories.html', title: 'Kategori Buku' },
            { file: 'admin-payments.html', title: 'Pendapatan & Denda' },
            { file: 'admin-chats.html', title: 'Chat Pelanggan' },
            { file: 'admin-users.html', title: 'Data User' },
            { file: 'admin-profile.html', title: 'Profil Admin' },
            { file: 'print-borrowings.html', title: 'Cetak Laporan Peminjaman' },
            { file: 'print-payments.html', title: 'Cetak Laporan Keuangan' }
        ]}
    ];

    document.addEventListener('DOMContentLoaded', () => {
        // Build demo toolbar HTML
        const toolbar = document.createElement('div');
        toolbar.className = 'demo-top-bar';
        toolbar.id = 'demoToolbar';

        let optionsHtml = '';
        pages.forEach(group => {
            optionsHtml += `<optgroup label="${group.group}">`;
            group.items.forEach(item => {
                const selected = item.file === page ? 'selected' : '';
                optionsHtml += `<option value="${item.file}" ${selected}>${item.title}</option>`;
            });
            optionsHtml += `</optgroup>`;
        });

        toolbar.innerHTML = `
            <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
                <span class="demo-badge">🚀 GitHub Pages Preview</span>
                <span style="color:#94a3b8; font-size:0.75rem;">Rainpedia Full Interactive Prototype</span>
            </div>
            <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                <div class="demo-roles">
                    <a href="index.html" class="demo-role-btn ${currentRole === 'public' ? 'active' : ''}">🌐 Public</a>
                    <a href="user-dashboard.html" class="demo-role-btn ${currentRole === 'user' ? 'active' : ''}">👤 User POV</a>
                    <a href="admin-dashboard.html" class="demo-role-btn ${currentRole === 'admin' ? 'active' : ''}">🛡️ Admin POV</a>
                </div>
                <select class="demo-page-select" onchange="window.location.href=this.value">
                    ${optionsHtml}
                </select>
                <button type="button" class="demo-role-btn" id="toggleDemoBar" title="Minimize Bar" style="padding:4px 6px;">✕</button>
            </div>
        `;

        document.body.prepend(toolbar);

        // Toggle minimize/maximize bar
        const toggleBtn = document.getElementById('toggleDemoBar');
        if (toggleBtn) {
            toggleBtn.addEventListener('click', () => {
                toolbar.style.display = 'none';
                const showBtn = document.createElement('button');
                showBtn.className = 'demo-toggle-btn';
                showBtn.id = 'demoShowBtn';
                showBtn.innerHTML = '⚙️ Demo Menu';
                showBtn.onclick = () => {
                    toolbar.style.display = 'flex';
                    showBtn.remove();
                };
                document.body.appendChild(showBtn);
            });
        }

        // Global status filtering for data tables and card lists
        initStatusFilters();
    });

    // Interactive Status Filter Engine
    function initStatusFilters() {
        const tabs = document.querySelectorAll('.status-tabs .status-tab, .admin-status-cards a, .dashboard-status-grid a');
        if (!tabs.length) return;

        // Check URL parameters for status
        const urlParams = new URLSearchParams(window.location.search);
        const activeStatusParam = urlParams.get('status') || '';

        // Handle tab clicks inside .status-tabs
        const filterTabs = document.querySelectorAll('.status-tabs .status-tab');
        filterTabs.forEach(tab => {
            tab.addEventListener('click', (e) => {
                const targetStatus = tab.getAttribute('data-status');
                if (targetStatus !== null) {
                    e.preventDefault();
                    filterTabs.forEach(t => t.classList.remove('active'));
                    tab.classList.add('active');
                    filterItemsByStatus(targetStatus);
                    updateExplainerCard(targetStatus);
                }
            });
        });

        // Initial trigger if param exists
        if (activeStatusParam) {
            filterTabs.forEach(tab => {
                if (tab.getAttribute('data-status') === activeStatusParam) {
                    filterTabs.forEach(t => t.classList.remove('active'));
                    tab.classList.add('active');
                    filterItemsByStatus(activeStatusParam);
                    updateExplainerCard(activeStatusParam);
                }
            });
        }
    }

    function filterItemsByStatus(status) {
        const rows = document.querySelectorAll('[data-item-status]');
        let matchCount = 0;
        rows.forEach(row => {
            const itemStatus = row.getAttribute('data-item-status');
            if (!status || status === '' || status === 'all' || itemStatus === status) {
                row.style.display = '';
                matchCount++;
            } else {
                row.style.display = 'none';
            }
        });

        const emptyMessage = document.getElementById('statusEmptyState');
        if (emptyMessage) {
            emptyMessage.style.display = matchCount === 0 ? 'block' : 'none';
        }

        const countDisplay = document.getElementById('displayedItemCount');
        if (countDisplay) {
            countDisplay.innerText = matchCount;
        }
    }

    function updateExplainerCard(status) {
        const explainer = document.getElementById('statusExplainer');
        if (!explainer) return;

        const info = window.STATUS_EXPLANATIONS ? window.STATUS_EXPLANATIONS[status] : null;
        if (info) {
            explainer.style.display = 'flex';
            explainer.innerHTML = `
                <div class="explainer-icon">${info.icon || 'ℹ️'}</div>
                <div>
                    <div class="explainer-title">${info.title}</div>
                    <div class="explainer-desc">${info.description}</div>
                </div>
            `;
        } else {
            explainer.style.display = 'none';
        }
    }

    // Expose helper to global window
    window.RainpediaDemo = {
        filterStatus: filterItemsByStatus,
        updateExplainer: updateExplainerCard
    };
})();

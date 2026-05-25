(function() {
    const config = window.inventoryPageConfig || {};
    const messages = config.messages || {};
    const routes = config.routes || {};

    function initProductSearch() {
        const $productSearch = $('#product_search');
        if ($productSearch.length === 0) return;

        $productSearch.select2({
            theme: 'bootstrap-5',
            width: '100%',
            allowClear: true,
            placeholder: messages.searchPlaceholder || 'Search by product name, barcode, or category...',
            templateResult: function (data) {
                if (!data.id) return data.text;

                const status = $(data.element).data('status') || 'normal';
                let statusBadgeClass = 'bg-success';
                let statusText = messages.statusNormal || 'Normal Stock';

                if (status === 'low') {
                    statusBadgeClass = 'bg-warning text-dark';
                    statusText = messages.statusLow || 'Low Stock';
                } else if (status === 'out') {
                    statusBadgeClass = 'bg-danger';
                    statusText = messages.statusOut || 'Out of Stock';
                }

                return $('<span class="d-flex justify-content-between align-items-center w-100">' +
                    '<span>' + data.text + '</span>' +
                    '<span class="badge ' + statusBadgeClass + '" style="margin-left: 10px;">' + statusText + '</span>' +
                    '</span>');
            },
            templateSelection: function (data) {
                if (!data.id) return data.text;
                return data.text;
            }
        });

        // Redirect when product is selected
        $productSearch.on('change', function () {
            const selectedId = $(this).val();
            if (selectedId) {
                const selectedText = $(this).find(':selected').text();
                const productName = selectedText.split(' -')[0].trim();
                window.location.href = (routes.search || '/inventory') + '?search=' + encodeURIComponent(productName);
            }
        });
    }

    function initCategorySelect() {
        const $categorySelect = $('select[name="CategoryID"]');
        if ($categorySelect.length === 0) return;

        $categorySelect.select2({
            theme: 'bootstrap-5',
            width: '100%',
            allowClear: true,
            placeholder: messages.selectCategory || 'All Categories'
        });
    }

    function initStockStatusFilter() {
        const $statusFilter = $('select[name="stock_status"]');
        if ($statusFilter.length === 0) return;

        $statusFilter.select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: messages.selectStatus || 'All Stock Statuses'
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        initProductSearch();
        initCategorySelect();
        initStockStatusFilter();
    });
})();

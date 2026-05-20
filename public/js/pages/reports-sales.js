(function() {
    const config = window.reportsSalesConfig || {};

    function initSearch(element) {
        let placeholderText = config.selectPlaceholder || 'Select';
        $(element).select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: placeholderText.trim(),
            dropdownParent: $(element).parent()
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        $('.searchable-select').each(function() {
            initSearch(this);
        });
    });
})();

(function () {
    const config = window.reportsSalesConfig || {};

    // function initSearch(element) {
    //     let placeholderText = config.selectPlaceholder || 'Select';
    //     $(element).select2({
    //         theme: 'bootstrap-5',
    //         width: '100%',
    //         placeholder: placeholderText.trim(),
    //         dropdownParent: $(element).parent()
    //     });
    // }

    function initSearch(element) {
        const $element = $(element);
        let placeholderText = messages.selectPlaceholder || 'Select';
        const emptyOptionText = $element.find('option[value=""]').first().text().trim();
        if (emptyOptionText) {
            placeholderText = emptyOptionText;
        }

        $element.select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: placeholderText,
            dropdownParent: $element.parent()
        });
    }


    document.addEventListener('DOMContentLoaded', function () {
        $('.searchable-select').each(function () {
            initSearch(this);
        });
    });
})();

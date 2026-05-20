(function() {
    const config = window.productsPageConfig || {};
    const messages = config.messages || {};

    function initSearch(element) {
        let placeholderText = messages.selectPlaceholder || 'Select';
        $(element).select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: placeholderText.trim(),
            dropdownParent: $(element).parent()
        });
    }

    function createAttributeRow() {
        const row = document.createElement('div');
        row.className = 'row g-2 align-items-center mb-2 attribute-row';
        row.innerHTML = `
            <div class="col-5">
                <input type="text" name="AttributeName[]" class="form-control form-control-sm bg-light border-0" placeholder="${messages.attributeNamePlaceholder || 'Attribute Name'}">
            </div>
            <div class="col-5">
                <input type="text" name="AttributeValue[]" class="form-control form-control-sm bg-light border-0" placeholder="${messages.attributeValuePlaceholder || 'Attribute Value'}">
            </div>
            <div class="col-2 text-end">
                <button type="button" class="btn btn-sm btn-light text-danger w-100 border-0 btn-remove-attribute"><i class="fas fa-trash-alt"></i></button>
            </div>
        `;
        return row;
    }

    document.addEventListener('DOMContentLoaded', function() {
        $('.searchable-select').each(function() {
            initSearch(this);
        });

        const deleteButtons = document.querySelectorAll('.btn-delete');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const form = this.closest('form');
                Swal.fire({
                    title: messages.deleteTitle || 'Are you sure?',
                    text: messages.deleteText || 'This action cannot be undone.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: messages.deleteConfirmBtn || 'Yes, delete it!',
                    cancelButtonText: messages.deleteCancelBtn || 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });

        document.querySelectorAll('.btn-add-attribute').forEach(button => {
            button.addEventListener('click', function() {
                const wrapper = this.closest('.card-body, .col-12, .col-md-12');
                const container = wrapper ? wrapper.querySelector('.attribute-rows') : null;
                if (!container) return;
                container.appendChild(createAttributeRow());
            });
        });

        document.addEventListener('click', function(event) {
            if (!event.target.classList.contains('btn-remove-attribute')) {
                return;
            }

            const container = event.target.closest('.attribute-rows');
            const rows = container.querySelectorAll('.attribute-row');

            if (rows.length === 1) {
                rows[0].querySelectorAll('input').forEach(input => input.value = '');
                return;
            }

            event.target.closest('.attribute-row').remove();
        });

        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function() {
                const scope = this.closest('.modal-content') || this;
                const rows = scope.querySelectorAll('.attribute-rows .attribute-row');

                if (!rows.length) {
                    return;
                }

                const attributes = [];
                rows.forEach(row => {
                    const nameInput = row.querySelector('input[name="AttributeName[]"]');
                    const valueInput = row.querySelector('input[name="AttributeValue[]"]');
                    const name = (nameInput?.value || '').trim();
                    const value = (valueInput?.value || '').trim();

                    if (name !== '' || value !== '') {
                        attributes.push({
                            name,
                            value
                        });
                    }
                });

                let payloadInput = this.querySelector('input[name="AttributesPayload"]');
                if (!payloadInput) {
                    payloadInput = document.createElement('input');
                    payloadInput.type = 'hidden';
                    payloadInput.name = 'AttributesPayload';
                    this.appendChild(payloadInput);
                }

                payloadInput.value = JSON.stringify(attributes);
            });
        });

        if (config.showAddModalOnError) {
            const addModalEl = document.getElementById('addProductModal');
            if (addModalEl) {
                const addModal = new bootstrap.Modal(addModalEl);
                addModal.show();
            }
        }
    });
})();

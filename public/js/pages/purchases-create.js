function initSearch(element) {
    let placeholderText = $(element).find('option[value=""]').text() || "Select";
    $(element).select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: placeholderText.trim(),
        dropdownParent: $(element).parent(),
    });
}

function calcTotal() {
    let total = 0;
    document.querySelectorAll('.subtotal-display').forEach(input => {
        total += parseFloat(input.value) || 0;
    });
    const grandTotalEl = document.getElementById('grandTotal');
    if (grandTotalEl) {
        grandTotalEl.innerText = total.toFixed(2);
    }
}

window.calcRow = function(element) {
    const row = element.closest('tr');
    if (!row) return;

    const cost = parseFloat(row.querySelector('.cost-input').value) || 0;
    const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
    const subtotalInput = row.querySelector('.subtotal-display');

    if (subtotalInput) {
        subtotalInput.value = (cost * qty).toFixed(2);
    }

    calcTotal();
};

window.updatePrice = function(select) {
    const cost = select.options[select.selectedIndex].getAttribute('data-cost');
    const row = select.closest('tr');
    if (row && cost) {
        const costInput = row.querySelector('.cost-input');
        if (costInput) {
            costInput.value = cost;
            calcRow(costInput);
        }
    }
};

function addPurchaseRow() {
    const table = document.getElementById('purchaseTable');
    const template = document.getElementById('purchaseRowTemplate');
    if (!table || !template) return;

    const tbody = table.querySelector('tbody');
    const newRowFragment = template.content.cloneNode(true);
    const currentRowIdx = window.purchaseRowIndex ?? 1;

    newRowFragment.querySelectorAll('[name]').forEach(el => {
        el.name = el.name.replace('__INDEX__', currentRowIdx);
    });

    tbody.appendChild(newRowFragment);
    const newRow = tbody.lastElementChild;
    if (newRow) {
        initSearch(newRow.querySelector('.product-select'));
    }

    window.purchaseRowIndex = currentRowIdx + 1;
}

document.addEventListener('DOMContentLoaded', function() {
    window.purchaseRowIndex = 1;

    document.querySelectorAll('.searchable-select').forEach(function(select) {
        initSearch(select);
    });

    document.getElementById('addRow')?.addEventListener('click', function() {
        addPurchaseRow();
    });

    document.addEventListener('click', function(e) {
        const removeBtn = e.target.closest('.remove-row');
        if (removeBtn) {
            const row = removeBtn.closest('tr');
            const rows = document.querySelectorAll('#purchaseTable tbody tr');
            if (row && rows.length > 1) {
                row.remove();
                calcTotal();
            }
        }
    });
});

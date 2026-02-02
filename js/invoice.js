/**
 * Invoice Create - Dynamic Items Management
 * Invoice App
 */

let itemCounter = 0;
let items = [];

// Format currency
function formatCurrency(amount) {
    return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
}

// Parse currency to number
function parseCurrency(str) {
    return parseFloat(str.replace(/[^0-9.-]+/g, '')) || 0;
}

// Customer selection handler
document.getElementById('customerId')?.addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    
    if (this.value === 'new') {
        // Redirect to add customer page
        window.location.href = '../../page/customers/add.php?return=create_invoice';
        return;
    }
    
    if (this.value) {
        // Fill customer data
        document.getElementById('customerName').value = selectedOption.dataset.name || '';
        document.getElementById('customerCompany').value = selectedOption.dataset.company || '';
        document.getElementById('customerEmail').value = selectedOption.dataset.email || '';
        document.getElementById('customerPhone').value = selectedOption.dataset.phone || '';
        document.getElementById('customerAddress').value = selectedOption.dataset.address || '';
    } else {
        // Clear customer data
        document.getElementById('customerName').value = '';
        document.getElementById('customerCompany').value = '';
        document.getElementById('customerEmail').value = '';
        document.getElementById('customerPhone').value = '';
        document.getElementById('customerAddress').value = '';
    }
});

// Add item button handler
document.getElementById('addItemBtn')?.addEventListener('click', function() {
    addItemRow();
});

// Add item row
function addItemRow(data = null) {
    itemCounter++;
    const itemId = 'item_' + itemCounter;
    
    const row = document.createElement('tr');
    row.id = itemId;
    row.innerHTML = `
        <td>
            <select class="form-select item-product" data-item-id="${itemId}" required>
                <option value="">Pilih Produk</option>
                ${productsData.map(product => `
                    <option value="${product.id}" 
                            data-code="${product.code || ''}"
                            data-name="${product.name}"
                            data-price="${product.price}"
                            data-unit="${product.unit}">
                        ${product.name} - ${formatCurrency(product.price)}
                    </option>
                `).join('')}
            </select>
            <input type="hidden" name="items[${itemCounter}][product_id]" class="item-product-id">
            <input type="hidden" name="items[${itemCounter}][product_code]" class="item-product-code">
            <input type="hidden" name="items[${itemCounter}][product_name]" class="item-product-name">
            <input type="hidden" name="items[${itemCounter}][unit]" class="item-unit">
        </td>
        <td>
            <input type="number" name="items[${itemCounter}][quantity]" class="form-input item-quantity" 
                   value="1" min="0.01" step="0.01" required>
        </td>
        <td>
            <input type="number" name="items[${itemCounter}][unit_price]" class="form-input item-price" 
                   value="0" min="0" step="0.01" required>
        </td>
        <td>
            <input type="text" class="form-input item-subtotal" value="Rp 0" readonly>
            <input type="hidden" name="items[${itemCounter}][subtotal]" class="item-subtotal-value" value="0">
        </td>
        <td>
            <button type="button" class="btn btn-danger text-sm px-3 py-1" onclick="removeItem('${itemId}')">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </button>
        </td>
    `;
    
    document.getElementById('itemsTableBody').appendChild(row);
    
    // Hide empty state
    document.getElementById('emptyState').style.display = 'none';
    
    // Attach event listeners
    attachItemEventListeners(itemId);
    
    // If data provided, fill it
    if (data) {
        fillItemData(itemId, data);
    }
    
    calculateTotal();
}

// Attach event listeners to item row
function attachItemEventListeners(itemId) {
    const row = document.getElementById(itemId);
    
    // Product selection
    const productSelect = row.querySelector('.item-product');
    productSelect.addEventListener('change', function() {
        const option = this.options[this.selectedIndex];
        if (this.value) {
            row.querySelector('.item-product-id').value = this.value;
            row.querySelector('.item-product-code').value = option.dataset.code || '';
            row.querySelector('.item-product-name').value = option.dataset.name || '';
            row.querySelector('.item-unit').value = option.dataset.unit || 'pcs';
            row.querySelector('.item-price').value = option.dataset.price || 0;
            calculateItemSubtotal(itemId);
        }
    });
    
    // Quantity change
    const quantityInput = row.querySelector('.item-quantity');
    quantityInput.addEventListener('input', function() {
        calculateItemSubtotal(itemId);
    });
    
    // Price change
    const priceInput = row.querySelector('.item-price');
    priceInput.addEventListener('input', function() {
        calculateItemSubtotal(itemId);
    });
}

// Calculate item subtotal
function calculateItemSubtotal(itemId) {
    const row = document.getElementById(itemId);
    const quantity = parseFloat(row.querySelector('.item-quantity').value) || 0;
    const price = parseFloat(row.querySelector('.item-price').value) || 0;
    const subtotal = quantity * price;
    
    row.querySelector('.item-subtotal').value = formatCurrency(subtotal);
    row.querySelector('.item-subtotal-value').value = subtotal;
    
    calculateTotal();
}

// Remove item
function removeItem(itemId) {
    const row = document.getElementById(itemId);
    if (row) {
        row.remove();
        
        // Show empty state if no items
        const tbody = document.getElementById('itemsTableBody');
        if (tbody.children.length === 0) {
            document.getElementById('emptyState').style.display = 'block';
        }
        
        calculateTotal();
    }
}

// Calculate total
function calculateTotal() {
    const tbody = document.getElementById('itemsTableBody');
    const rows = tbody.querySelectorAll('tr');
    
    let subtotal = 0;
    rows.forEach(row => {
        const itemSubtotal = parseFloat(row.querySelector('.item-subtotal-value').value) || 0;
        subtotal += itemSubtotal;
    });
    
    const taxRate = parseFloat(document.getElementById('taxRate').value) || 0;
    const taxAmount = subtotal * (taxRate / 100);
    const total = subtotal + taxAmount;
    
    // Update display
    document.getElementById('displaySubtotal').textContent = formatCurrency(subtotal);
    document.getElementById('displayTaxRate').textContent = taxRate;
    document.getElementById('displayTax').textContent = formatCurrency(taxAmount);
    document.getElementById('displayTotal').textContent = formatCurrency(total);
    
    // Update hidden inputs
    document.getElementById('subtotalInput').value = subtotal;
    document.getElementById('taxAmountInput').value = taxAmount;
    document.getElementById('totalInput').value = total;
}

// Tax rate change handler
document.getElementById('taxRate')?.addEventListener('input', function() {
    calculateTotal();
});

// Form submission handler
document.getElementById('invoiceForm')?.addEventListener('submit', function(e) {
    const tbody = document.getElementById('itemsTableBody');
    if (tbody.children.length === 0) {
        e.preventDefault();
        alert('Tambahkan minimal 1 item ke invoice!');
        return false;
    }
});

// Initialize - add one empty row
if (document.getElementById('itemsTableBody')) {
    // Don't add initial row, let user click "Add Item"
    // addItemRow();
}

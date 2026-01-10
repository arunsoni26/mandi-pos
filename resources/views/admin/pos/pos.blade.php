@extends('layouts.admin-app')

@php
    $isPOS = true;
@endphp

@section('content')

    <div class="row">
        <!-- CART PANEL -->
        <div class="col-lg-12">
        <div class="card shadow-sm">
            <div class="card-body" id="creditorTypeBody">            
                <div class="creditor-options">
                    
                    <label class="creditor-card">
                        <input type="radio" name="creditorTypeOption" value="Active Creditor">
                        <div class="card-content">
                            <div class="icon">🏪</div>
                            <div class="title">Active Creditor</div>
                            <div class="subtitle">व्यापारी</div>
                        </div>
                    </label>

                    <label class="creditor-card">
                        <input type="radio" name="creditorTypeOption" value="Raw Creditor">
                        <div class="card-content">
                            <div class="icon">🌾</div>
                            <div class="title">Raw Creditor</div>
                            <div class="subtitle">किसान</div>
                        </div>
                    </label>

                </div>

                <!-- Hidden input -->
                <input type="hidden" name="creditor_type" id="creditorType">
            </div>
            <div class="card-body" id="posBody" style="display:none;">
                <div class="d-flex align-items-center position-relative mb-4">
                    <button type="button" id="backToCreditorType" class="btn btn-secondary mb-3 z-2">
                        ← Back
                    </button>
    
                    <!-- Selected creditor type -->
                    <div id="selectedCreditorType" class="fw-bold position-absolute start-50 translate-middle-x text-center w-100">
                        <span class="badge bg-info fs-3"></span>
                    </div>
                </div>
                
                <div class="w-100 d-flex align-items-center">
                    <h5 class="card-title">Cart / Invoice Table</h5>
                    <span class="btn btn-primary ms-auto" onclick="addBlankRow();">Add</span>
                </div>
                <div class="row mb-3" id="creditorSelectionSection">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Select Creditor</label>
                        <select name="creditor_id" id="creditorSelect" class="form-control"></select>
                    </div>
                </div>
                <div class="row mb-3" id="creditorSelectedSection">
                </div>

                <div class="cart-panel border rounded p-2 mt-3 mb-3 table-responsive" id="cartItems">
                    <table class="table table-bordered table-sm align-middle text-center">
                        <thead class="table-light">
                            <tr>
                                <th>Product</th>
                                <th>Pieces</th>
                                <th>Weight (kg)</th>
                                <th>Rate</th>
                                <th>Total</th>
                                <th>Customer</th>
                                <th>Remove</th>
                            </tr>
                        </thead>
                        <tbody id="cartTableBody">
                        </tbody>
                    </table>
                </div>

                <!-- WAGE SECTION -->
                <div class="mb-3 p-2 border rounded bg-light w-50 ms-auto">
                    <label class="form-label fw-bold">Wage Charge (₹9 per piece)</label>
                    <input name="wage" id="totalWage" class="form-control" readonly>
                </div>

                <div class="d-grid gap-2">
                    <div class="mt-4 text-end">
                    <h4>Grand Total: ₹ <span id="grandTotal">0.00</span></h4>
                    <input id="cartGT" type="hidden">
                    </div>

                    <div class="d-flex gap-2">
                        <span id="saveGenerateInvoiceBtn" class="btn btn-success flex-fill">
                            Save & Generate Invoice
                        </span>
                        <span id="saveNextBtn" class="btn btn-primary flex-fill">
                            Save & Next
                        </span>
                    </div>
                    <!-- <button id="clearCartBtn" class="btn btn-outline-secondary">Clear Cart</button> -->
                </div>
            </div>
        </div>
        </div>
        <!-- CART PANEL -->
    </div>

    <!-- INVOICE MODAL -->
    <div class="modal fade" id="invoiceModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Invoice</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="cartInvoice">
                    <div>
                        <div class="col-md-6">
                            <div class="alert alert-success d-flex justify-content-between align-items-center">
                                <strong>Invoice Saved Successfully</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <a href="" target="_blank" class="btn btn-secondary" id="printInvoiceBtn">Show Invoice</a>
                </div>
            </div>
        </div>
    </div>
    <!-- INVOICE MODAL -->
@endsection

@push('custom-css')
<style>
    .select2-container {
        width: -webkit-fill-available !important;
    }
    .select2-container .select2-selection--single {
        height: 42px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 42px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 42px !important;
    }
</style>


<style>
    .creditor-options {
        display: flex;
        gap: 20px;
    }

    .creditor-card {
        flex: 1;
        border: 2px solid #ddd;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.3s ease;
        text-align: center;
        padding: 20px;
        position: relative;
    }

    .creditor-card input {
        display: none;
    }

    .creditor-card .icon {
        font-size: 40px;
        margin-bottom: 10px;
    }

    .creditor-card .title {
        font-size: 18px;
        font-weight: 600;
    }

    .creditor-card .subtitle {
        font-size: 14px;
        color: #666;
    }

    /* Hover effect */
    .creditor-card:hover {
        border-color: #0d6efd;
        transform: translateY(-3px);
    }

    /* Selected state */
    .creditor-card input:checked + .card-content {
        color: #0d6efd;
    }

    .creditor-card input:checked + .card-content::after {
        content: "✔";
        position: absolute;
        top: 12px;
        right: 15px;
        font-size: 18px;
        color: #0d6efd;
    }

    .creditor-card input:checked ~ .card-content,
    .creditor-card input:checked + .card-content {
        font-weight: 600;
    }

    .creditor-card input:checked {
        display: none;
    }

    .creditor-card:has(input:checked) {
        border-color: #0d6efd;
        background: #f0f6ff;
    }

    .ts-dropdown {
        z-index: 9999 !important;
    }
</style>
@endpush

@push('custom-scripts')
<script>
    $(document).ready(function () {
        // When selecting creditor type
        $('input[name="creditorTypeOption"]').on('change', function () {
            let selectedTitle = $(this).closest('.creditor-card')
                                       .find('.title')
                                       .text();
            let selectedSubTitle = $(this).closest('.creditor-card')
                                       .find('.subtitle')
                                       .text();
            let icon = $(this).closest('.creditor-card').find('.icon').text();

            // Set hidden input value
            $('#creditorType').val($(this).val());

            // Show selected option text
            $('#selectedCreditorType span').html(icon + ' ' + selectedTitle + ' (' + selectedSubTitle + ')');

            // 🔥 RESET Select2 when type changes
            resetCreditorSelect2();

            // Hide creditor type body
            $('#creditorTypeBody').slideUp();

            // Show POS body
            $('#posBody').slideDown();
        });

        // Back button click
        $('#backToCreditorType').on('click', function () {
            // Hide POS body
            $('#posBody').slideUp();

            // Show creditor type body
            $('#creditorTypeBody').slideDown();

            // Optional: clear selection
            $('input[name="creditorTypeOption"]').prop('checked', false);
            $('#creditorType').val('');
        });
    });

    function resetCreditorSelect2() {
        let $select = $('#creditorSelect');

        // Clear selection
        $select.val(null).trigger('change');

        // Remove any loaded options (AJAX cache)
        $select.find('option').remove();

        // Close dropdown if open
        $select.select2('close');
    }
    
    setTimeout(() => {
        document.getElementById('sidebar-hide').click();
    }, 200);

    window.openCustomerModal = function(cusId) {
        console.log('cusId--->>>>', cusId);
        
        $('#creditorSelect').select2('close');
        $.ajax({
            type: 'post',
            url: "{{route('admin.customers.form')}}",
            data: {
                customerId: cusId,
                customerType: $('#creditorType').val(),
                isPOS: true
            },
            headers:{
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(data) {
                $('#addEditContent').html(data);
                $('#editModal').modal('show');
            },
            error: function(err) {
                console.log('err---->>>>', err);
            }
        });
    }

    window.showSelectedCreditor = function(customer) {
        if (!customer) return;

        $('#creditorSelectedSection').html(`
            <div class="col-md-6">
                <div class="alert alert-success d-flex justify-content-between align-items-center">
                    <strong>${customer.name}</strong>
                    <button type="button" class="btn btn-sm btn-danger" id="removeCreditor">
                        Remove
                    </button>
                </div>
                <input type="hidden" name="creditor_id" id="selectedCreditorId" value="${customer.id}">
            </div>
        `);
    }

    $(document).on('click', '#removeCreditor', function () {
        // Clear selected creditor
        $('#creditorSelectedSection').empty();

        // Reset select2
        $('#creditorSelect').val(null).trigger('change');

        // Show select dropdown again
        $('#creditorSelectionSection').show();
    });

    $(document).ready(function () {
        $('#creditorSelect').select2({
            placeholder: 'Select or type creditor name',
            ajax: {
                url: '{{ route('admin.customers.creditors') }}',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        searchTerm: params.term,
                        type: $('#creditorType').val()
                    };
                },
                processResults: data => {
                    let results = data.creditors.map(c => ({
                        id: c.id,
                        text: c.name,        // PLAIN TEXT
                        customer: c
                    }));

                    if (data.creditors.length === 0) {
                        results.push({
                            id: 'create_new',
                            text: 'Add new customer',
                            isNew: true
                        });
                    }

                    return { results };
                }
            },

            // 🔽 Dropdown appearance only
            templateResult: function (data) {
                if (data.loading) return data.text;

                if (data.id === 'create_new') {
                    return $('<div class="fw-bold">➕ Add new customer</div>');
                }

                return $(`
                    <div class="d-flex justify-content-between align-items-center">
                        <span>${data.text}</span>
                        <span class="badge bg-secondary">View</span>
                    </div>
                `);
            },

            // ✅ Selected value (NO HTML)
            templateSelection: function (data) {
                return data.text || data.id;
            }
        });

        $('#creditorSelect').on('select2:select', function (e) {
            let data = e.params.data;

            if (data.id === 'create_new') {
                openCustomerModal();
                $(this).val(null).trigger('change');
            } else {
                openCustomerModal(data.id);
            }
        });
    });


    window.onCreditorSelected = function (creditorId) {
        if (!creditorId) return;

        fetch(`{{ url('admin/pos/load-today-invoice')}}/${creditorId}`)
            .then(res => res.json())
            .then(res => {
                resetCart();

                if (res.exists) {
                    cart = res.cart;
                    console.log('cart->>', cart);
                    

                    Object.keys(cart).forEach(id => {
                        updateCart(id);
                        rowId = parseInt(id)+1;
                        restoreCustomer(id);
                    });
                } else {
                    cart = {}; // fresh invoice
                    addBlankRow();
                }

                calculateGrandTotal();
            });
    }
    
    function resetCart() {
        cart = {};
        document.getElementById('cartTableBody').innerHTML = '';
    }

    function restoreCustomer(id) {
        const c = cart[id];
        if (!c.debtor_customer_id) return;

        const input = document.querySelector(
            `.cart-customer[data-id="${id}"]`
        );

        if (!input || !input.tomselect) return;

        input.tomselect.addOption({
            id: c.debtor_customer_id,
            name: c.debtor_customer_name
        });

        input.tomselect.setValue(c.debtor_customer_id);
    }


    window.initDebtorTomSelect = function (input, id) {
        if (input.tomselect) return;

        const ts = new TomSelect(input, {
            valueField: 'id',
            labelField: 'name',
            searchField: 'name',
            create: true,
            persist: false,
            maxItems: 1,
            dropdownParent: 'body',

            load(query, callback) {
                if (!query.length) return callback();

                fetch(`{{ route('admin.customers.debtors') }}?type=debtor&q=${encodeURIComponent(query)}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network error');
                        }
                        return response.json();
                    })
                    .then(data => {
                        callback(data); // must be [{id, name}]
                    })
                    .catch(err => {
                        console.error('TomSelect AJAX error:', err);
                        callback([]);
                    });
            },

            onChange(value) {
                cart[id].debtor_customer_id = value;

                const item = ts.options[value];
                cart[id].debtor_customer_name = item?.name || value;
            }
        });

        // Restore value if already set
        if (cart[id].debtor_customer_id) {
            ts.addOption({
                id: cart[id].debtor_customer_id,
                name: cart[id].debtor_customer_name
            });
            ts.setValue(cart[id].debtor_customer_id);
        }
    }

</script>
<script>
    function isPositive(val) {
        return validator.isNumeric(val.toString()) && Number(val) > 0;
    }

    function focusNextInput(current) {
        const inputs = Array.from(
            document.querySelectorAll('input:not([readonly])')
        ).filter(el => !el.disabled && el.offsetParent !== null);

        const index = inputs.indexOf(current);
        if (index > -1 && index < inputs.length - 1) {
            inputs[index + 1].focus();
        }
    }

    function showError(input, message) {
        clearInputError(input);

        const error = document.createElement('div');
        error.className = 'validation-error';
        error.style.color = 'red';
        error.style.fontSize = '12px';
        error.textContent = message;

        input.classList.add('is-invalid');
        input.parentNode.appendChild(error);
        input.focus();
    }

    function clearInputError(input) {
        input.classList.remove('is-invalid');
        const error = input.parentNode.querySelector('.validation-error');
        if (error) error.remove();
    }

    function clearRowErrors(row) {
        row.querySelectorAll('.validation-error').forEach(e => e.remove());
        row.querySelectorAll('.is-invalid').forEach(i => i.classList.remove('is-invalid'));
    }

    function validateRow(row) {
        const product = row.querySelector('.cart-product');
        const pieces = row.querySelector('.cart-pieces');
        const weight = row.querySelector('.cart-weight');
        const rate = row.querySelector('.cart-rate');
        const customer = row.querySelector('.cart-customer');

        // ❌ Product required
        if (validator.isEmpty(product.value.trim())) {
            showError(product, 'Product is required');
            return false;
        }

        // ❌ Pieces required & > 0
        if (validator.isEmpty(pieces.value.trim())) {
            showError(pieces, 'Pieces is required');
            return false;
        }
        if (Number(pieces.value) <= 0) {
            showError(pieces, 'Pieces must be greater than 0');
            return false;
        }

        // ❌ Weight required & > 0
        if (validator.isEmpty(weight.value.trim())) {
            showError(weight, 'Weight is required');
            return false;
        }
        if (Number(weight.value) <= 0) {
            showError(weight, 'Weight must be greater than 0');
            return false;
        }

        // ❌ Rate required & > 0
        if (validator.isEmpty(rate.value.trim())) {
            showError(rate, 'Rate is required');
            return false;
        }
        if (Number(rate.value) <= 0) {
            showError(rate, 'Rate must be greater than 0');
            return false;
        }

        // ❌ Customer required
        if (validator.isEmpty(customer.value.trim())) {
            showError(customer, 'Customer is required');
            return false;
        }

        return true; // ✅ row is fully valid
    }

    function calculateRowTotal(id) {
        const item = cart[id];

        const pieces = Number(item.pieces) || 0;
        const weight = Number(item.weight) || 0;
        const rate = Number(item.rate) || 0;

        const total = weight * rate;
        item.total = total;

        // Update row total (UI update)
        const rowTotalCell = document.querySelector(`.cart-total[data-id="${id}"]`);
        if (rowTotalCell) rowTotalCell.value = total.toFixed(2);

        return total;
    }

    function calculateGrandTotal() {
        let totalPieces = 0;
        let totalAmount = 0;

        Object.keys(cart).forEach(id => {
            const item = cart[id];

            totalPieces += Number(item.pieces) || 0;
            totalAmount += Number(item.total) || 0;
        });

        const wage = totalPieces * WAGE_PER_PIECE;
        const grandTotal = totalAmount - wage;

        document.getElementById('totalWage').value = wage.toFixed(2);
        document.getElementById('grandTotal').innerText = grandTotal.toFixed(2);
        document.getElementById('cartGT').value = grandTotal.toFixed(2);

        return grandTotal;
    }

    let PRODUCTS = [
        { id: 1, name: 'Green Tea 100g', price: 120 },
        { id: 2, name: 'Assam Tea 250g', price: 220 }
    ];
    let cart = {}; // {id:{pieces, weight, rate, total, customer}}
    const WAGE_PER_PIECE = 9;

    const fmt = v => '₹' + Number(v).toFixed(2);

    function updateCart(id) {
        const c = cart[id];

        const row = document.createElement('tr');
        row.setAttribute('data-row-id', id);

        row.innerHTML = `
            <td><input class="form-control cart-product" data-id="${id}" value="${c.product || ''}"></td>
            <td><input type="number" class="form-control cart-pieces" data-id="${id}" value="${c.pieces || ''}"></td>
            <td><input type="number" class="form-control cart-weight" data-id="${id}" value="${c.weight || ''}"></td>
            <td><input type="number" class="form-control cart-rate" data-id="${id}" value="${c.rate || ''}"></td>
            <td><input class="form-control cart-total" data-id="${id}" readonly value="${c.total || 0}"></td>
            <td>
                <input type="text" class="form-control input-sm cart-customer customer-select" data-id="${id}" placeholder="Search or type customer">
            </td>
            <td><button class="btn btn-danger btn-sm" onclick="removeItem('${id}')">X</button></td>
        `;

        document.getElementById('cartTableBody').appendChild(row);

        // 🔥 Init Tom Select ONLY for this row
        const customerInput = row.querySelector('.cart-customer');
        initDebtorTomSelect(customerInput, id);

        // 🔥 RESTORE VALUE IF EXISTS
        if (c.customer) {
            $(row).find('.customer-select')
                .append(new Option(c.customer, c.customer, true, true))
                .trigger('change');
        }

        calculateGrandTotal();
    }

    window.rowId = 0;
    function addBlankRow() {
        cart[rowId] = {
            product: "",
            pieces: "",
            weight: "",
            rate: "",
            total: 0,
            debtor_customer_id: "",
            debtor_customer_name: ""
        };

        updateCart(rowId);

        // Put cursor in the new row's first cell
        setTimeout(() => {
            const firstInput = document.querySelector(`input[data-id="${rowId}"]`);
            if (firstInput) firstInput.focus();
        }, 50);

        rowId++;
    }

    document.addEventListener('keydown', function (e) {
        if (e.key !== 'Enter') return;

        const input = e.target;
        if (!input.matches('input')) return;

        e.preventDefault();

        const row = input.closest('tr');
        if (!row) return;

        clearRowErrors(row);

        if (!validateRow(row)) return;

        // 👉 If Enter pressed on customer and valid → add row
        if (input.classList.contains('cart-customer')) {
            addBlankRow();

            // 👇 Auto-focus product of new row
            setTimeout(() => {
                const firstInput = document.querySelector(
                    `tr[data-row-id="${rowId}"] .cart-product`
                );
                if (firstInput) firstInput.focus();
            }, 0);
        }

        focusNextInput(input);
    });

    // EVENT LISTENERS (editable cart fields)
    document.addEventListener('input', e => {
        const id = e.target.dataset.id;
        if (!id) return;

        // 🛑 If cart[id] doesn't exist, create a blank structure
        if (!cart[id]) {
            cart[id] = {
                product: "",
                pieces: 0,
                weight: 0,
                rate: 0,
                total: 0,
                debtor_customer_id: "",
                debtor_customer_name: ""
            };
        }

        if (e.target.classList.contains('cart-product')) {
            cart[id].product = e.target.value;
        }
        if (e.target.classList.contains('cart-pieces')) {
            cart[id].pieces = Number(e.target.value);
        }
        if (e.target.classList.contains('cart-weight')) {
            cart[id].weight = Number(e.target.value);
        }
        if (e.target.classList.contains('cart-rate')) {
            cart[id].rate = Number(e.target.value);
        }
        // if (e.target.classList.contains('cart-customer')) {
        //     console.log('e.target.value---->>>>', e.target.value);
            
        //     cart[id].debtor_customer_id = e.target.value;
        // }

        // 🔥 Update only this row total
        calculateRowTotal(id);

        // 🔥 Update full invoice totals
        calculateGrandTotal();
    });

    function removeItem(id) {
        delete cart[id];
        if (confirm('Are you sure you want to delete this cart item?')) {
            $('tr[data-row-id="'+id+'"]').remove();
        }
        // rowId--;
    }

    var generateInvoice = false;
    // Save & Generate Invoice
    document.getElementById('saveGenerateInvoiceBtn').addEventListener('click', (e) => {
        e.preventDefault();

        generateInvoice = true;
        saveInvoice();
    });

    // Save Invoice
    document.getElementById('saveNextBtn').addEventListener('click', (e) => {
        e.preventDefault();
        saveInvoice();
    });

    window.saveInvoice = function () {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "post",
            url: "{{route('admin.pos.save')}}",
            data: {
                cart: cart,
                creditorId: $('#selectedCreditorId').val()
            },
            success: function (res) {
                
                if (res.status !== 'success') return;

                $('#printInvoiceBtn').hide();
                if (generateInvoice) {
                    $('#printInvoiceBtn').attr('href', res.creditor_invoice_url);
                    $('#printInvoiceBtn').show();
                                
                    generateInvoice = false;
                }

                // 🔥 Show Invoice Modal
                new bootstrap.Modal($('#invoiceModal')).show();
            }
        });
    }

</script>
@endpush
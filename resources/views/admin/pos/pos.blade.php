@extends('layouts.admin-app')

@section('content')

    <div class="row">
        <!-- CART PANEL -->
        <div class="col-lg-12">
        <div class="card shadow-sm">
            <div class="card-body">
            <div class="w-100 d-flex align-items-center">
                <h5 class="card-title">Cart / Invoice Table</h5>
                <span class="btn btn-primary ms-auto" onclick="addBlankRow();">Add</span>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Select Creditor</label>
                    <select name="creditor_id" id="creditorSelect" class="form-control"></select>
                </div>
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
                    <button id="saveGenerateInvoiceBtn" class="btn btn-success flex-fill">
                        Save & Generate Invoice
                    </button>
                    <button id="saveNextBtn" class="btn btn-primary flex-fill">
                        Save & Next
                    </button>
                </div>
                <button id="clearCartBtn" class="btn btn-outline-secondary">Clear Cart</button>
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
                <!-- Invoice Header -->
                <div class="text-center mb-3">
                    <h3 class="fw-bold">माँ कर्मा ट्रेडर्स</h3>
                    <p class="mb-0">नई सब्जीमंडी, सारंगपुर जिला राजगढ़ (म.प्र.)</p>
                    <p class="mb-0">आयुष साहू | 📞 6261451385, सावरिया पाटीदार | 📞 7067692263, अशोक साहू | 📞9826137177</p>
                    <hr>

                    <p><strong>ख़रीदी बिल</strong></p>
                </div>

                <!-- Creditor & Date Row -->
                <div class="row mb-3">
                    <div class="col-6">
                    <h6 class="mb-0">
                        <strong>नाम:</strong>
                        <span id="invCreditor">John Doe</span>
                    </h6>
                    </div>
                    <div class="col-6 text-end">
                    <h6 class="mb-0">
                        <strong>दिनांक:</strong>
                        <span id="invDate">2025-12-30</span>
                    </h6>
                    </div>
                </div>

                <!-- Items Table -->
                <table class="table table-bordered table-sm text-center align-middle">
                    <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>वस्तु</th>
                        <th>बोरी/थैले</th>
                        <th>वज़न (kg)</th>
                        <th>मूल्य</th>
                        <th>कुल</th>
                        <th>ग्राहक</th>
                    </tr>
                    </thead>
                    <tbody id="invItems">
                    <tr>
                        <td>1</td>
                        <td>Wheat</td>
                        <td>1</td>
                        <td>1</td>
                        <td>₹1.00</td>
                        <td>₹1.00</td>
                        <td>Arun Soni</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Rice</td>
                        <td>1</td>
                        <td>1</td>
                        <td>₹1.00</td>
                        <td>₹1.00</td>
                        <td>Deepika Soni</td>
                    </tr>
                    </tbody>
                </table>

                <!-- Totals Section -->
                <div class="row justify-content-end mt-3">
                    <div class="col-md-5">
                    <table class="table table-bordered">
                        <tr>
                        <th class="text-end">कुल वेतन</th>
                        <td class="text-end" id="invWage">₹18.00</td>
                        </tr>
                        <tr class="table-light">
                        <th class="text-end fs-5">कुल योग</th>
                        <td class="text-end fs-5 fw-bold" id="invCartGT">₹20.00</td>
                        </tr>
                    </table>
                    </div>
                </div>

                <!-- Footer -->
                <div class="text-center mt-4">
                    <p class="mb-0"><em>आपके व्यवसाय के लिए धन्यवाद!</em></p>
                </div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" id="printInvoiceBtn">Print</button>
            </div>
        </div>
      </div>
    </div>
    <!-- INVOICE MODAL -->
@endsection

@push('custom-css')
<style>
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
@endpush

@push('custom-scripts')
<script>
    // // Hook into existing cart update
    // const oldRenderCart = renderCart;
    // renderCart = function () {
    //     oldRenderCart();
    // };

    // // --- PDF Download ---
    // document.getElementById('downloadPdf').addEventListener('click', () => {
    //     const element = document.body;
    //     const opt = { margin: 1, filename: 'invoice.pdf', html2canvas: { scale: 2 }, jsPDF: { unit: 'in', format: 'a4' } };
    //     html2pdf().from(element).set(opt).save();
    // });
    
    setTimeout(() => {
        document.getElementById('sidebar-hide').click();
    }, 100);
    
    $(document).ready(function () {
        $('#creditorSelect').select2({
            placeholder: 'Select or type creditor name',
            tags: true,
            ajax: {
                url: '{{ route('admin.customers.creditors')}}',
                dataType: 'json',
                delay: 250,
                data: params => ({ q: params.term }),
                processResults: data => ({
                    results: data.map(c => ({
                        id: c.id,
                        text: c.name
                    }))
                })
            },
            createTag: function (params) {
                return {
                    id: params.term,
                    text: params.term,
                    newTag: true
                };
            }
        });

        $('#creditorSelect').on('change', function () {
            selectedCreditorId = $(this).val(); // id OR string
            onCreditorSelected(selectedCreditorId);
        });
    });

    function onCreditorSelected(creditorId) {
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

    document.getElementById('clearCartBtn').addEventListener('click', () => {
        cart = {};
        document.getElementById('cartTableBody').innerHTML = '';
    });


    // INVOICE
    document.getElementById('saveGenerateInvoiceBtn').addEventListener('click', () => {
        const body = document.getElementById('invItems'); body.innerHTML = '';
        let i = 1;
        console.log('cart--->>>', cart);
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "post",
            url: "{{route('admin.pos.save')}}",
            data: {
                cart: cart,
                creditorId: $('#creditorSelect').val()
            },
            success: function (res) {
                
                if (res.status !== 'success') return;

                const body = document.getElementById('invItems');
                body.innerHTML = '';

                let i = 1;

                // 🔥 Invoice Header
                $('#invCreditor').text(res.invoice.summary.creditor_name);

                $('#invDate').text(res.invoice.summary.invoice_date);

                // 🔥 Invoice Items (DB VALUES)
                res.invoice.items.forEach(item => {
                    body.insertAdjacentHTML('beforeend', `
                        <tr>
                            <td>${i++}</td>
                            <td>${item.product}</td>
                            <td>${item.pieces}</td>
                            <td>${item.weight}</td>
                            <td>${fmt(item.rate)}</td>
                            <td>${fmt(item.total)}</td>
                            <td>${item.debtor_name}</td>
                        </tr>
                    `);
                });

                // 🔥 Totals (DB VALUES)
                $('#invAmount').text(fmt(res.invoice.summary.total_amount));

                $('#invWage').text(fmt(res.invoice.summary.total_wage));

                $('#invCartGT').text(fmt(res.invoice.summary.grand_total));

                // 🔥 Show Invoice Modal
                new bootstrap.Modal($('#invoiceModal')).show();
            }
        });
    });

    // document.getElementById('printInvoiceBtn').addEventListener('click',()=>window.print());
    document.getElementById('printInvoiceBtn').addEventListener('click', () => {
        const invoice = document.getElementById('cartInvoice').innerHTML;
        const win = window.open('', '', 'width=800,height=600');
        win.document.write(`
            <html>
            <head>
                <title>Invoice</title>
                    
                <!-- Bootstrap CSS -->
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

                <style>
                    /* General print formatting */
                    body {
                        font-family: Arial, sans-serif;
                        margin: 20px;
                    }

                    /* Table styling */
                    table {
                        width: 100%;
                        border-collapse: collapse;
                        margin-top: 10px;
                    }

                    table, th, td {
                        border: 1px solid #000;
                    }

                    th, td {
                        padding: 8px 12px;
                        text-align: left;
                    }

                    th {
                        background: #f0f0f0;
                        font-weight: bold;
                    }

                    /* Optional: remove Print window margins */
                    @page {
                        margin: 20px;
                    }
                </style>
            </head>
            <body>
                ${invoice}
            </body>
            </html>
        `);
        win.document.close();
        setTimeout(() => {
            win.print();
        }, 1000);
    });
</script>
@endpush
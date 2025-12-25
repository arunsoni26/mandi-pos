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
            <table class="table table-bordered table-sm text-center">
              <thead class="table-light">
                <tr>
                  <th>#</th><th>Product</th><th>Pieces</th><th>Weight (kg)</th><th>Rate</th><th>Total</th><th>Customer</th>
                </tr>
              </thead>
              <tbody id="invItems"></tbody>
            </table>
            <hr>
            <h5>Total Wage: <span id="invWage"></span></h5>
            <h4>Grand Total: <span id="invCartGT"></span></h4>
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
        });

        // $('.cart-debtor').select2({
        //     placeholder: 'Select or type customer name',
        //     tags: true,
        //     ajax: {
        //         url: '',
        //         dataType: 'json',
        //         delay: 250,
        //         data: params => ({ q: params.term }),
        //         processResults: data => ({
        //             results: data.map(c => ({
        //                 id: c.id,
        //                 text: c.name
        //             }))
        //         })
        //     },
        //     createTag: function (params) {
        //         return {
        //             id: params.term,
        //             text: params.term,
        //             newTag: true
        //         };
        //     }
        // }).on('change', function () {
        //     const id = $(this).data('id');
        //     cart[id].debtor_customer_id = $(this).val(); // id OR string
        // });
    });

    window.initCustomerSelect2 = function(context = document) {
        console.log('context====>>>', context);
        
        $(context).find('.cart-customer').each(function () {

            // Prevent re-initialization
            if ($(this).hasClass("select2-hidden-accessible")) return;

            $(this).select2({
                placeholder: 'Select / Add Customer',
                width: '100%',
                tags: true,
                // allowClear: true,

                ajax: {
                    url: '{{ route('admin.customers.debtors')}}', // Laravel route
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term,
                            type: 'debtor' // or both if needed
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data.map(item => ({
                                id: item.id,
                                text: item.name
                            }))
                        };
                    },
                    cache: true
                }
            });

        });
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
        const rate = Number(item.rate) || 0;

        const total = pieces * rate;
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
        const grandTotal = totalAmount + wage;

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

    function updateCart(rowId) {
        const c = cart[rowId];

        const row = document.createElement('tr');
        row.setAttribute('data-row-id', rowId);

        row.innerHTML = `
            <td><input class="form-control cart-product" data-id="${rowId}" value="${c.product || ''}"></td>
            <td><input type="number" class="form-control cart-pieces" data-id="${rowId}" value="${c.pieces || ''}"></td>
            <td><input type="number" class="form-control cart-weight" data-id="${rowId}" value="${c.weight || ''}"></td>
            <td><input type="number" class="form-control cart-rate" data-id="${rowId}" value="${c.rate || ''}"></td>
            <td><input class="form-control cart-total" readonly value="${c.total || 0}"></td>
            <td>
                <select class="form-control cart-customer" data-id="${rowId}" style="width:100%"></select>
            </td>
            <td><button class="btn btn-danger btn-sm" onclick="removeItem('${rowId}')">X</button></td>
        `;

        document.getElementById('cartTableBody').appendChild(row);

        // 🔥 INIT SELECT2 ONLY FOR THIS ROW
        initCustomerSelect2(row);

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
            debtor_customer_id: ""
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
                debtor_customer_id: ""
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
        if (e.target.classList.contains('cart-customer')) {
            cart[id].customer = e.target.value;
        }

        // 🔥 Update only this row total
        calculateRowTotal(id);

        // 🔥 Update full invoice totals
        calculateGrandTotal();
    });

    function removeItem(id) {
        delete cart[id];
        // updateCart();
        rowId--;
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
        Object.values(cart).forEach(c => {
            body.insertAdjacentHTML('beforeend', `
                <tr>
                    <td>${i++}</td>
                    <td>${c.product}</td>
                    <td>${c.pieces}</td>
                    <td>${c.weight}</td>
                    <td>${fmt(c.rate)}</td>
                    <td>${fmt(c.total)}</td>
                    <td>${c.customer}</td>
                </tr>`
            );
        });

        let totalPieces = Object.values(cart).reduce((s, c) => s + Number(c.pieces), 0);
        document.getElementById('invWage').textContent = fmt(totalPieces * WAGE_PER_PIECE);
        document.getElementById('invCartGT').textContent = fmt(document.getElementById('cartGT').value);

        new bootstrap.Modal(document.getElementById('invoiceModal')).show();
    });

    // document.getElementById('printInvoiceBtn').addEventListener('click',()=>window.print());
    document.getElementById('printInvoiceBtn').addEventListener('click', () => {
        const invoice = document.getElementById('cartInvoice').innerHTML;
        const win = window.open('', '', 'width=800,height=600');
        win.document.write(`
            <html>
            <head>
                <title>Invoice</title>
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
        win.print();
    });

    // updateCart();
</script>
@endpush
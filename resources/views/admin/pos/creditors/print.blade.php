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

        table,
        th,
        td {
            border: 1px solid #000;
        }

        th,
        td {
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

<body onload="window.print()">
    <div class="" id="cartInvoice">
        <!-- Invoice Header -->
        <div class="text-center mb-3">
            <h3 class="fw-bold">Maa Karma Traders</h3>
            <p class="mb-0">New Sabjimandi, Sarangpur Jila Rajgarh (M.P.)</p>
            <p class="mb-0">Ayush Sahu | 📞 6261451385, Sawariya Patidar | 📞 7067692263, Ashok Sahu | 📞9826137177</p>
            <hr>

            <p><strong>Creditor Bill</strong></p>
        </div>

        <!-- Creditor & Date Row -->
        <div class="row mb-3">
            <div class="col-6">
                <h6 class="mb-0">
                    <strong>Invoice No:</strong>
                    <span id="invDate">{{ invoiceNumber($invoice) }}</span>
                </h6>
            </div>
            <div class="col-6 text-end">
                <h6 class="mb-0">
                    <strong>Invoice Date:</strong>
                    <span id="invDate">{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d-m-Y') }}</span>
                </h6>
            </div>
            <div class="col-6">
                <h6 class="mb-0">
                    <strong>Name:</strong>
                    <span id="invCreditor">{{ $invoice->creditor->name }}</span>
                </h6>
            </div>
        </div>

        <!-- Items Table -->
        <table class="table table-bordered table-sm text-center align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Product</th>
                    <th>Pieces</th>
                    <th>Weight</th>
                    <th>Rate</th>
                    <th>Total</th>
                    <th>Debtor</th>
                </tr>
            </thead>
            <tbody id="invItems">
                @foreach($invoice->items as $i => $item)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $item->product_name }}</td>
                        <td>{{ $item->pieces }}</td>
                        <td>{{ $item->weight }}</td>
                        <td>{{ number_format($item->rate, 2) }}</td>
                        <td>{{ number_format($item->total, 2) }}</td>
                        <td>{{ optional($item->debtorCustomer)->name }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals Section -->
        <div class="row justify-content-end mt-3">
            <div class="col-md-5">
            <table class="table table-bordered">
                <tr>
                    <th class="text-end">Total Amount</th>
                    <td class="text-end" id="invWage">₹{{ number_format($invoice->total_amount, 2) }}</td>
                </tr>
                <tr>
                    <th class="text-end">Total Wage</th>
                    <td class="text-end" id="invWage">₹{{ number_format($invoice->total_wage, 2) }}</td>
                </tr>
                <tr class="table-light">
                    <th class="text-end fs-5">Grand Total</th>
                    <td class="text-end fs-5 fw-bold" id="invCartGT">₹{{ number_format($invoice->grand_total, 2) }}</td>
                </tr>
            </table>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-4">
            <p class="mb-0"><em>Thank you for your business</em></p>
        </div>
    </div>
</body>

</html>
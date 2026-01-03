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

<body>
    <div class="" id="cartInvoice">
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
</body>

</html>
<html lang="en"><head>
    <meta charset="utf-8">
    <!--  This file has been downloaded from bootdey.com @bootdey on twitter -->
    <!--  All snippets are MIT license http://bootdey.com/license -->
    <title>Invoice - Bootdey.com</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://netdna.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
    <style type="text/css">
    	body{
background:#eee;
margin-top:20px;
}
.text-danger strong {
        	color: #9f181c;
		}
		.receipt-main {
			background: #ffffff none repeat scroll 0 0;
			border-bottom: 12px solid #333333;
			border-top: 12px solid #9f181c;
			margin-top: 50px;
			margin-bottom: 50px;
			padding: 40px 30px !important;
			position: relative;
			box-shadow: 0 1px 21px #acacac;
			color: #333333;
			font-family: open sans;
		}
		.receipt-main p {
			color: #333333;
			font-family: open sans;
			line-height: 1.42857;
		}
		.receipt-footer h1 {
			font-size: 15px;
			font-weight: 400 !important;
			margin: 0 !important;
		}
		.receipt-main::after {
			background: #414143 none repeat scroll 0 0;
			content: "";
			height: 5px;
			left: 0;
			position: absolute;
			right: 0;
			top: -13px;
		}
		.receipt-main thead {
			background: #414143 none repeat scroll 0 0;
		}
		.receipt-main thead th {
			color:#fff;
		}
		.receipt-right h5 {
			font-size: 16px;
			font-weight: bold;
			margin: 0 0 7px 0;
		}
		.receipt-right p {
			font-size: 12px;
			margin: 0px;
		}
		.receipt-right p i {
			text-align: center;
			width: 18px;
		}
		.receipt-main td {
			padding: 9px 20px !important;
		}
		.receipt-main th {
			padding: 13px 20px !important;
		}
		.receipt-main td {
			font-size: 13px;
			font-weight: initial !important;
		}
		.receipt-main td p:last-child {
			margin: 0;
			padding: 0;
		}	
		.receipt-main td h2 {
			font-size: 20px;
			font-weight: 900;
			margin: 0;
			text-transform: uppercase;
		}
		.receipt-header-mid .receipt-left h1 {
			font-weight: 100;
			margin: 34px 0 0;
			text-align: right;
			text-transform: uppercase;
		}
		.receipt-header-mid {
			margin: 24px 0;
			overflow: hidden;
		}
		
		#container {
			background-color: #dcdcdc;
		}
    </style>
</head>
<body style="">
<div class="col-md-12">   
 <div class="row">
		
        <div class="receipt-main col-xs-10 col-sm-10 col-md-6 col-xs-offset-1 col-sm-offset-1 col-md-offset-3">
            <div class="row">
    			<div class="receipt-header">
					<div class="col-xs-6 col-sm-6 col-md-6">
						<!-- <div class="receipt-left">
							<img class="img-responsive" alt="iamgurdeeposahan" src="https://bootdey.com/img/Content/avatar/avatar6.png" style="width: 71px; border-radius: 43px;">
						</div> -->
					</div>
					<div class="col-xs-6 col-sm-6 col-md-6 text-right">
						<div class="receipt-right">
							<h5>Maa Karma Traders</h5>
							<p>Ayush Sahu | 📞 6261451385, Sawariya Patidar | 📞 7067692263, Ashok Sahu | 📞9826137177<i class="fa fa-phone"></i></p>
							<!-- <p><a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="d8bbb7b5a8b9b6a198bfb5b9b1b4f6bbb7b5">[email&nbsp;protected]</a> <i class="fa fa-envelope-o"></i></p> -->
							<p>New Sabjimandi, Sarangpur Jila Rajgarh (M.P.) <i class="fa fa-location-arrow"></i></p>
						</div>
					</div>
				</div>
            </div>
			
			<div class="row">
				<div class="receipt-header receipt-header-mid">
					<div class="col-xs-8 col-sm-8 col-md-8 text-left">
						<div class="receipt-right">
							<h5>{{ $invoice->debitor->name }} </h5>
							<p><b>Mobile :</b> {{ $invoice->debitor->mobile ?? 'N/A' }}</p>
							<!-- <p><b>Email :</b> <a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="63001610170c0e061123040e020a0f4d000c0e">[email&nbsp;protected]</a></p>
							<p><b>Address :</b> New York, USA</p> -->
						</div>
					</div>
					<div class="col-xs-4 col-sm-4 col-md-4">
						<div class="receipt-left" style="float:right;">
                            <h3>{{ str_replace('INV', 'INVD', invoiceNumber($invoice)) }}</h3>
						</div>
					</div>
				</div>
            </div>
			
            <div class="row">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <!-- <th>Description</th>
                            <th>Amount</th> -->
                            <th>#</th>
                            <th>Product</th>
                            <th>Pieces</th>
                            <th>Weight</th>
                            <th>Rate</th>
                            <th>Total</th>
                            <th>Debitor</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->items as $i => $item)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $item->product_name }}</td>
                            <td>{{ $item->pieces }}</td>
                            <td>{{ $item->weight }}</td>
                            <td>{{ number_format($item->rate, 2) }}</td>
                            <td>{{ number_format($item->total, 2) }}</td>
                            <td>{{ optional($invoice->debitor)->name }}</td>
                            <td>{{ $item->invoice_status ?? '' }}Draft</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div>
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
			
			<div class="row">
				<div class="receipt-header receipt-header-mid receipt-footer">
					<div class="col-xs-8 col-sm-8 col-md-8 text-left">
						<div class="receipt-right">
							<p><b>Date :</b> {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d-m-Y') }}</p>
							<h5 style="color: rgb(140, 140, 140);">Thank you for your business!</h5>
						</div>
					</div>
					<div class="col-xs-4 col-sm-4 col-md-4">
						<div class="receipt-left">
							<h1>Signature</h1>
						</div>
					</div>
				</div>
            </div>
			
        </div>    
	</div>
</div>
<script data-cfasync="false" src="/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script><script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
<script src="https://netdna.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script type="text/javascript">
	
</script>
<script defer="" src="https://static.cloudflareinsights.com/beacon.min.js/vcd15cbe7772f49c399c6a5babf22c1241717689176015" integrity="sha512-ZpsOmlRQV6y907TI0dKBHq9Md29nnaEIPlkf84rnaERnq6zvWvPUqr2ft8M1aS28oN72PdrCzSjY4U6VaAw1EQ==" data-cf-beacon="{&quot;version&quot;:&quot;2024.11.0&quot;,&quot;token&quot;:&quot;0982648f21b7499c83a555a3f57e966f&quot;,&quot;r&quot;:1,&quot;server_timing&quot;:{&quot;name&quot;:{&quot;cfCacheStatus&quot;:true,&quot;cfEdge&quot;:true,&quot;cfExtPri&quot;:true,&quot;cfL4&quot;:true,&quot;cfOrigin&quot;:true,&quot;cfSpeedBrain&quot;:true},&quot;location_startswith&quot;:null}}" crossorigin="anonymous"></script>

</body>
</html>
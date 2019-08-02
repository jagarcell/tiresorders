<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

	<title></title>

	<script src="//code.jquery.com/jquery-1.12.4.js"></script>
	<script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  	<script type="text/javascript" src="/public/js/ordertoprint.js"></script>

	<link rel="stylesheet" type="text/css" href="/public/css/app.css">
	<link rel="stylesheet" type="text/css" href="/public/css/listorders.css">
</head>
<body>
	<input type="number" id="orderId" value="{{$orderid}}" hidden="">
	<div id="orderDetails" class="frameWidth">
		<div class="labelDiv">ORDER TO PRINT</div>
		<div class="orderHeaderDiv">
			<table id="orderHeader" class="orderHeaderTable fixed_header">
				<thead>
					<tr>
						<th class="orderCustomerColumn">CUSTOMER</th>
						<th class="orderDateColumn">ORDER DATE</th>
					</tr>
				</thead>
				<tbody class="orderBody">
					<tr>
						<td id="orderCustomer" class="orderCustomerColumn"></td>
						<td id="orderDate" class="orderDateColumn"></td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="orderLinesDiv">
			<table id="orderLines" class="orderLinesTable fixed_header">
				<thead>
					<tr>
						<th class="itemColumn">Item</th>
						<th class="qtyColumn alignRight">Qty</th>
						<th class="priceColumn alignRight">Price</th>
						<th class="subTotalColumn alignRight">Subtotal</th>
					</tr>
				</thead>
				<tbody>
					<!-- LINES ADDED FROM JAVASCRIPT -->
				</tbody>
			</table>
		</div>
		<div class="orderTotalDiv">
			<table id="orderTotal" class="orderLinesTable fixed_header">
				<thead>
					<tr>
						<th class="itemColumn"></th>
						<th class="qtyColumn alignRight"></th>
						<th class="priceColumn alignRight">Total</th>
						<th class="subTotalColumn alignRight" id="ordertotal"></th>
					</tr>
				</thead>
			</table>
		</div>
		<div id="specialInstructionsDiv">
			<label>Special Instructions:</label>
			<div id="specialInstructionsText" class="specialInstructionsTextClass"></div>
		</div>
		<div id="deliveryDiv">
			<label>Please Deliver To:</label>
			<div  id="deliveryAddressDiv" class="deliveryAddressDivClass">
				
			</div>
		</div>
		<div>
			<input id="printOrder" type="button" value="PRINT" class="actionButton invoiceButton">
		</div>
	</div>
</body>
</html>
@extends('layouts.app')
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="shortcut icon" href="public/favicon.ico"/>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

	<title>List Orders</title>

	@section('scripts')
	<script src="//code.jquery.com/jquery-1.12.4.js"></script>
	<script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  	<script type="text/javascript" src="public/js/listorders.js"></script>
	@endsection

	@section('styles')
	<link rel="stylesheet" type="text/css" href="public/css/listorders.css">
	@endsection
</head>
<body>
@section('content')
<div class="mainDiv">
	<input id="orderId" type="number" hidden="">
	<div id="ordersList" class="frameWidth">
		<div class="labelDiv">ORDERS</div>
		<div class="tableDiv">
			<table id="listordersTable" class="listordersTable fixed_header">
				<thead>
					<tr>
						<th class="customerColumn">Customer</th>
						<th class="dateColumn">Date</th>
						<th class="totalColumn">Total</th>
						<th class="statusColumn">Status</th>
					</tr>
				</thead>
				<tbody>
					@foreach($orders as $key => $order)
					<tr id="{{$order->id}}" onclick="rowClick(this)">
						<td class="customerColumn">{{$order->customer}}</td>
						<td class="dateColumn">{{$order->orderdate}}</td>
						<td class="totalColumn">{{sprintf('%.02f', $order->total)}}</td>
						<td class="statusColumn">{{$order->status}}</td>
					</tr>
					@endforeach
				</tbody>
			</table>
		</div>
		<div class="ordersRefresh">
			<div class="orderStatusSelect">
				<div class="statusDiv labelShowDiv"><label>Show:</label></div>
				<div class="statusDiv"><input type="checkbox" name="" value="all" class="statusCheck statusAll"> All</div>
				<div class="statusDiv"><input type="checkbox" id="open" value="open" class="statusCheck individualStatus" checked="true"> Open</div>
				<div class="statusDiv"><input type="checkbox" id="pickup" value="pickup" class="statusCheck individualStatus"> Pickup</div>
				<div class="statusDiv"><input type="checkbox" id="delivery" value="delivery" class="statusCheck individualStatus"> Delivery</div>
				<div class="statusDiv"><input type="checkbox" id="invoiced" value="invoiced" class="statusCheck individualStatus"> Invoiced</div>
			</div>
			<!--div class="refreshButton">
				<input type="button" id="refreshButton" class="actionButton" value="REFRESH ORDERS">
			</div-->
		</div>
	</div>

	<div id="orderDetails" class="frameWidth">
		<div class="deleteSelectedButtonDiv">
			<input type="button" id="deleteSelectedButton" class="actionButton deleteSelectedButton" value="Delete Selected">
		</div>
		<div class="labelDiv">ORDER TO PROCESS</div>
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
			<table id="orderLines" class="orderLinesTable fixed_header orderLinesResponsive">
				<thead class="orderLinesResponsive">
					<tr class="orderLinesResponsive">
						<th class="itemColumn orderLinesResponsive">Item</th>
						<th class="qtyColumn alignRight orderLinesResponsive">Qty</th>
						<th class="priceColumn alignRight orderLinesResponsive">Price</th>
						<th class="subTotalColumn alignRight orderLinesResponsive">Subtotal</th>
						<th class="selectColumn orderLinesResponsive"></th>
					</tr>
				</thead>
				<tbody class="orderLinesResponsive">
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
						<th class="selectColumn"><a id="deleteSelected"></a></th>
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
			<input id="sendInvoice" type="button" value="Send To Invoice" class="actionButton invoiceButton">
		</div>
		<!--div>
			<input id="deleteOrder" type="button" value="Delete" class="actionButton deleteOrderButton">
		</div-->
	</div>
</div>
@endsection
</body>
</html>
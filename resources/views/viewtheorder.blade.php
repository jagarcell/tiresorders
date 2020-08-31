@extends('layouts.app')
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

	<title>ORDER</title>

	@section('scripts')
	<script src="//code.jquery.com/jquery-1.12.4.js"></script>
	<script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  	<script type="text/javascript" src="public/js/viewtheorder.js"></script>
	@endsection

	@section('styles')
	<link rel="stylesheet" type="text/css" href="public/css/viewtheorder.css">
	@endsection
</head>
<body>
@section('content')
<div class="mainDiv">
	<div class="deleteSelectedButtonDiv">
		<input type="button"  id="deleteSelectedButton" value=" DELETE SELECTED" class="actionButton deleteSelectedButton" >
	</div>
	@isset($order)
	@isset($order->user)
	<div class="userDiv">Order For: {{$order->user}}</div>
	@else
	<div class="orderLabelDiv">ORDER IN PROGRESS</div>
	@endisset
	<div class="orderDiv">
		<table id="orderTable" class="tableDiv fixed_header">
			<thead class="orderHeader">
				<tr>
					<th class="firstCol">Item</th>
					<th class="secondCol">Qty</th>
					<th class="thirdCol">Price</th>
					<th class="fourthCol">SubTotal</th>
					@isset($order->submit)
					<th class="fifthCol deleteSelected textCentered"></th>
					@endisset
					@isset($order->user)
					<th class="fifthCol checkCol"><a id="deleteSelected" class="textCentered">Check For Invoice</a></th>
					@endisset
				</tr>
			</thead>
			<tbody>
				@isset($order)
				@foreach($order->lines as $key => $orderLine)
				<tr id="{{$orderLine->item_qbid}}">
					<td class="firstCol borderBottom">{{$orderLine->name}}</td>
					<td class="secondCol borderBottom">{{$orderLine->qty}}</td>
					<td class="thirdCol borderBottom">{{sprintf('%.02f', $orderLine->price)}}</td>
					<td class="fourthCol borderBottom">{{sprintf('%.02f', $orderLine->subTotal)}}</td>
					</td>
					<td class="fifthCol deleteSelected checkCol"><input id="checkbox_{{$orderLine->item_qbid}}"  type="checkbox" class="checkBoxInput" onchange="checkForDeleteChange()">
				</tr>
				@endforeach
				@endisset
			</tbody>
		</table>
		<div class="orderTotalDiv">
			<table class="orderTotalTable fixed_header">
				<thead>
					<tr>
						<th class="firstCol"></th>
						<th class="secondCol"></th>
						<th class="thirdCol" style="padding-right: 0px !important;">Order Total</th>
						<th id="orderTotal" class="fourthCol orderTotal orderTotalHeader" style="padding-right: 20px !important;">{{sprintf('%.02f', $order->orderTotal)}}</th>
						<th class="fifthCol"></th>
					</tr>
				</thead>
			</table>
		</div>
		<label class="additionalInstructionsLabel">Special Instructions:</label>
		<div class="additionalInstructionsDiv">
			@isset($order->specialinstructionsreadonly)
			<textarea id="additionalInstructionsText" class="additionalInstructions" readonly="">{{$order->specialinstructions}}</textarea>
			@else
			<textarea id="additionalInstructionsText" class="additionalInstructions">{{$order->specialinstructions}}</textarea>
			@endisset
		</div>
		@isset($order->delivery)
		<br>
			<label>Please Deliver To:</label>
			<div  id="deliveryAddressDiv" class="deliveryAddressDivClass">
				{{$order->address}}
			</div>
		@endisset
	</div>
	<div id="submitMessage" class="submitMessage"></div>
	@isset($order->submit)
	<div id="myProgress">
	  <div id="myBar"></div>
	</div>
	<div class="orderDiv submitButtonDiv">
		<input type="button" id="deliveryButton" class="actionButton submitButton" value="SUBMIT FOR DELIVERY">
		<input type="button" id="pickUpButton" class="actionButton middleButton submitButton" value="SUBMIT FOR PICKUP">
		<input type="button" id="continueShoppingButton" class="actionButton submitButton" value="CONTINUE SHOPPING">
	</div>
	@endisset
	@else
	<div class="noOrderInProgressDiv">YOU HAVE NO ORDER IN PROGRESS</div>
	<div class="orderDiv submitButtonDiv">
		<input type="button" class="actionButton submitButton" value="CONTINUE SHOPPING">
	</div>
	@endisset
</div>
@endsection
</body>
</html>
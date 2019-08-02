<!DOCTYPE html>
<html>
<head>
	<title></title>
	<script src="//code.jquery.com/jquery-1.12.4.js"></script>
	<script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  	<script type="text/javascript" src="public/js/viewtheorder.js"></script>

	<link rel="stylesheet" type="text/css" href="public/css/viewtheorder.css">
</head>
<body>
<div class="mainDiv">
	<div class="orderDiv">
		<table id="orderTable" class="tableDiv fixed_header">
			<thead>
				<tr>
					<th class="firstCol">Item</th>
					<th class="secondCol">Qty</th>
					<th class="thirdCol">Price</th>
					<th class="fourthCol">SubTotal</th>
				</tr>
			</thead>
			<tbody>
				@isset($order)
				@foreach($order->lines as $key => $orderLine)
				<tr id="{{$orderLine->item_qbid}}">
					<td class="firstCol">{{$orderLine->name}}</td>
					<td class="secondCol" style="text-align: right;">{{$orderLine->qty}}</td>
					<td class="thirdCol" style="text-align: right;">{{sprintf('%.02f', $orderLine->price)}}</td>
					<td class="fourthCol" style="text-align: right;">{{sprintf('%.02f', $orderLine->subtotal)}}</td>
				</tr>
				@endforeach
				@isset($order->total)
				<tr>
					<td class="firstCol"></td>
					<td class="secondCol"></td>
					<td class="thirdCol" style="font-weight: bold;">Order Total</td>
					<td id="orderTotal" class="fourthCol orderTotal" style="text-align: right; border-top: double;">{{sprintf('%.02f', $order->total)}}</td>
				</tr>
				@endisset
				@endisset
			</tbody>
		</table>
	</div>

	@isset($order->specialInstructions)
	<div>{{$order->specialInstructions}}</div>
	@endisset
	
	@isset($order->deliveryaddress)
	<div>Please deliver this order to:</div>
	<div>{{$order->deliveryaddress}}</div>
	@endisset

	@isset($order->phonenumber)
	<label>Ph:</label><div style="display: inline-block;">{{$order->phonenumber}}</div>
	@endisset

	@isset($order->link)
	<div class="orderlink"><a href="{{$order->link}}">View Order</a>
	@endisset
</div>
</body>
</html>
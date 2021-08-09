@extends('layouts.app')
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="shortcut icon" href="public/favicon.ico"/>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

	<title>PLACE AN ORDER</title>
	@section('styles')
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css">
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<link rel="stylesheet" type="text/css" href="public/css/placeanorder.css">
	@endsection

	@section('scripts')
	<script src="//code.jquery.com/jquery-1.12.4.js"></script>
	<script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

	<script type="text/javascript" src="public/js/placeanorder.js"></script>
	@endsection
</head>
<body>
@section('content')
<div class="mainDiv">
	<!-- THIS IS THE BANNER AT THE TOP OF THE CLIENTS PAGE -->
	<div class="ofertasDiv" hidden>
		<a href="https://goo.gl/maps/KBbbJs5neKbgiyLB8" target="_blank">
		<img src="public/img/logos/Moving.png" class="ofertasBanner">
		</a>
	</div>
	<!-- THIS IS THE SLIDE SHOW OF THE SPECIALS -->
	<div class="ofertaWrap" onclick="specialClick(this)">
		@if(count($Inventory) > 0)
		<div class="ofertasSlideDiv">
			@foreach($Inventory as $key => $item)
			<div id="{{$item->id}}" class="ofertaNDiv">
				<div class="ofertaNComponent ofertaNComponent1">
					<LABEL class="labelClass itemDescriptionFont itemNameId">{{$item->name}}</LABEL>
				</div>
				<div class="ofertaNComponent ofertaNComponent2">
					<img src="/public/{{$item->imgpath}}" class="productImageOferta imgPathId">
				</div>
				<div class="ofertaNComponent ofertaNComponent3 ofertaBG" >
					<LABEL class="labelClass itemSpecialFont ofertaId">${{$item->oferta}}</LABEL>
				</div>
			</div>
			@endforeach
		</div>
		@endif
	</div>
	<!-- HTML TEMPLATE TO CREATE A SLIDE SHOW DIV FROM JS -->
	<div class="ofertasSlideDivTemplate" hidden>
		<div id="item-id" class="ofertaNDiv">
			<div class="ofertaNComponent ofertaNComponent1">
				<LABEL class="labelClass itemDescriptionFont itemNameId">item-name</LABEL>
			</div>
			<div class="ofertaNComponent ofertaNComponent2">
				<img src="" class="productImageOferta imgPathId">
			</div>
			<div class="ofertaNComponent ofertaNComponent3 ofertaBG" >
				<LABEL class="labelClass itemSpecialFont ofertaId">item-oferta</LABEL>
			</div>
		</div>
	</div>

	<div class="searchDiv">
		<input type="text" id="searchText" placeholder="Enter Your Search" autofocus="true" class="searchBar">
		<input type="button" id="searchButton" class="actionButton searchButton" value="Search">
		<img src="public/img/logos/Tire1.jpeg" id="tireAnimImg" class="imgFrame">
	</div>

	<div class="tableDiv">
		<div class="addToOrderButtonDiv">
			<input type="button" id="addToOrderButton" class="addToOrderButton actionButton" value="Add Selected To Order">
		</div>
		<table id="itemsTable" class="itemsTable fixed_header">
			<thead class="orderHeader">
				<tr>
					<th class="firstCol borderBottom textCentered">Inventory Item</th>
					<th class="secondCol borderBottom textCentered">In Stock</th>
					<th class="thirdCol borderBottom textCentered">Qty</th>
					<th class="fourthCol borderBottom textCentered" style="padding-right: 0px !important;">Price</th>
					<th class="fifthCol borderBottom textCentered" style="padding-right: 0px !important;">SubTotal</th>
					<th class="sixCol borderBottom textCentered addSelected"></th>
				</tr>
			</thead>
			<tbody>
				<!-- THE ROWS ARE ADDED FROM JAVASCRIPT -->
			</tbody>
		</table>
		<div id="noItemsFoundDiv" class="noItemFound">NO ITEMS MATCHED YOUR SEARCH</div>
	</div>
	<div class="orderTotalDiv">
		<table id="totalTable" class="fixed_header orderTable">
			<thead>
				<tr>
					<th class="firstCol textCentered"></th>
					<th class="secondCol textCentered"></th>
					<th class="thirdCol textCentered"></th>
					<th class="fourthCol alignRight" style="padding-right: 0px !important;">Total</th>
					<th id="orderTotal" class="fifthCol alignRight orderTotalHeader">0.00</th>
					<th class="sixCol textCentered addSelected"></th>
				</tr>
			</thead>
		</table>
	</div>
	<br>
	<br>
	
	<div class="poweredBy">
		Powered By: <a target="_blank" href="https://www.allwebdone.com">www.allwebdone.com</a>
	</div>
</div>

@endsection
</body>
</html>
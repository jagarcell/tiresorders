@extends('layouts.app')
<!DOCTYPE html>
<html>
<head>
    <!-- CSRF Token -->
    <meta  name="csrf-token" content="{{ csrf_token() }}">
	<link rel="shortcut icon" href="public/favicon.ico"/>

	<title>Inventory</title>
	<!-- STYLES -->
	<link rel="stylesheet" type="text/css" href="public/css/inventory.css">

	@section('scripts')
	<script src="//code.jquery.com/jquery-1.12.4.js"></script>
	<script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  	<script type="text/javascript" src="public/js/inventory.js"></script>
    <script type="text/javascript" src="public/js/dropzone.js"></script>
	@endsection

	@section('styles')
	<link rel="stylesheet" type="text/css" href="public/css/inventory.css">
    <link rel="stylesheet" type="text/css" href="public/css/dropzone.css">
	@endsection
</head>
<body>
@section('content')
{{ csrf_field() }}
<div class="inventoryMainDiv">
	<div class="searchDiv">
		<input type="text" id="searchText" placeholder="Enter Your Search" autofocus="" class="searchBar">
		<input type="button" id="searchButton" class="actionButton searchButton" value="Search">
		<img src="public/img/logos/Tire1.jpeg" id="tireAnimImg" class="imgFrame">
	</div>
	<div class="labelDiv">PRODUCTS IN STOCK</div>
	<div class="tableDiv">
		<table id="InventoryTable" class="inventoryTable fixed_header">
			<thead class="InventoryHeader">
				<tr>
					<th class="firstCol">Description</th>
					<th class="secondCol alignRight" title="QUANTITY IN PURCHASE ORDERS">In PO</th>
					<th class="secondCol alignRight" title="QUANTITY IN STOCK">In Stock</th>
					<th class="thirdCol alignRight" title="QUANTITY IN CUSTOMERS SALES ORDERS">In SO</th>
					<th class="fourthCol alignRight">Base Cost</th>
					<th class="fourthCol alignRight" title="SPECIAL OFFER PRICE">Special</th>
				</tr>
			</thead>
			<tbody>
				@foreach($Inventory as $key => $item)
				@if($item->pricemodified)
				<tr id="{{$item->id}}" style="color: black;">
				@else
				<tr id="{{$item->id}}" style="color: red;">
				@endif
					<td class="firstCol">
						<div>
							@if(strlen($item->imgpath) > 0)
								<div  class="imgDiv">
									<img src="public/{{$item->imgpath}}" class="prodImg" onclick="imgClick(this)" title="CLICK TO CHANGE THE PHOTO">
								</div>
							@else
							<form action="/fileupload" method="post" enctype="multipart/form-data" class="dropzone" style="width: 100%; height: 60px; border-style: none !important;"  id="dropzone{{$item->id}}">
								@csrf
								<input type="text" name="itemid" hidden="" value="{{$item->id}}">
							</form>
							@endif
						</div>
						<div>{{$item->name}}</div>
					</td>
					<td class="secondCol alignRight borderBottom">{{sprintf('%.02f', $item->inpurchaseorders)}}</td>
					<td class="secondCol alignRight borderBottom">{{sprintf('%.02f', $item->instock)}}</td>
					<td class="thirdCol alignRight borderBottom">{{sprintf('%.02f', $item->inorders)}}</td>
					@if($item->pricemodified)
					<td class="fourthCol borderBottom"><input type="text" value="{{sprintf('%.02f', $item->price)}}" class="alignRight" style="color: black;" onchange="priceChange(this)"></td>
					@else
					<td class="fourthCol"><input type="text" value="{{sprintf('%.02f', $item->price)}}" class="alignRight" style="color: red;" onchange="priceChange(this)"></td>
					@endif
					
					<td class="fourthCol borderBottom"><input type="text" value="{{sprintf('%.02f', $item->oferta)}}" class="alignRight" style="color: black;" onchange="ofertaChange(this)"></td>
				</tr>
				@endforeach
			</tbody>
		</table>
		<div id="noItemsFoundDiv" class="noItemFound">NO ITEMS MATCHED YOUR SEARCH</div>
	</div>
	<div id="updateMessage" class="updateMessage"></div>
	<div id="myProgress">
	  <div id="myBar"></div>
	</div>
	<div class="inventoryButtons">
		<div class="updateInventory"><input id="updateInventory" type="button" class="actionButton updateInventoryButton" value="UPDATE WITH QB" title="SYNCHRONIZE LOCAL INVENTORY WITH QUICKBOOKS"></div>
		<div class="updateInventory1">
			<form action="/csvexport" method="POST">
				@csrf
				<input type="submit" class="actionButton updateInventoryButton" value="EXPORT CSV" title="EXPORT INVENTORY TO CSV">
			</form>
			<form enctype="multipart/form-data" action="/csvimport" method="POST" class="csvForm">
				@csrf
				<input type="hidden" name="MAX_FILE_SIZE" value="500000">
				<div class="chooseFile">
					<input type="submit" class="actionButton updateInventoryButton" value="IMPORT CSV" title="IMPORT INVENTORY FROM CSV">
					<input type="file" class="csvImport"  name="csvFile" accept=".csv">
				</div>
			</form class="csvForm">
		</div>
	</div>
</div>
@endsection
</body>
</html>
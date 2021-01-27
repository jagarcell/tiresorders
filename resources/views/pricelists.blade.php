@extends('layouts.app')
<!DOCTYPE html>
<html>
<head>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

	<title>PRICE LISTS</title>

	@section('scripts')
	<script src="//code.jquery.com/jquery-1.12.4.js"></script>
	<script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  	<script type="text/javascript" src="public/js/pricelists.js"></script>
	@endsection

	@section('styles')
	<link rel="shortcut icon" href="public/favicon.ico"/>
    <link rel="stylesheet" type="text/css" href="public/css/dropzone.css">
    <link rel="stylesheet" type="text/css" href="public/css/pricelists.css">
	@endsection
</head>
<body>
@section('content')	
<div id="pricelistsMainDiv" class="pricelistsMainDiv">
	<div class="searchDiv">
		<input type="text" id="searchText" placeholder="Enter Your Search" autofocus="" class="searchBar">
		<input type="button" id="searchButton" class="actionButton searchButton" value="Search">
		<img src="public/img/logos/Tire1.jpeg" id="tireAnimImg" class="imgFrame">
	</div>

	<div class="labelDiv">PRICE LISTS</div>
	<div class="descriptionDiv">
		<select id="descriptionSelect" class="descriptionSelect">
		@foreach($pricelists as $key => $pricelist)
		<option value="{{$pricelist->id}}">
			{{$pricelist->description}}
		</option>
		@endforeach
		</select>
		<input type="text" id="newListDescription" class="descriptionSelect" placeholder="ENTER HERE A DESCRIPTION" onchange="newListDescriptionChange(this)">
		<input type="button" value="+" id="newListButton" class="newList" title="ADD A NEW LIST">
	</div>

	<div id="thereAreLists">
		<div id="priceChangeFactor">
			<label for="percentage">Apply This Percentage:</label><input type="number" id="percentage" value="0" class="percentageClass"><label>%</label><select id="priceChangeType"><option value="up">Up</option><option value="down">Down</option></select><label>To All Prices</label><input id="goButtonId" type="button" class="actionButton goButton" value="GO">
		</div>
		<div class="listDiv">
			<table id="priceListTable" class="listTable fixed_header">
				<thead class="listTableHead">
					<tr class="listTableHeadRow">
						<th class="itemColumn">Item</th>
						<th class="priceColumnHeader">Price</th>
					</tr>
				</thead>
				<tbody class="listTableBody">
					@if(isset($pricelists[0]->lines))
					@foreach($pricelists[0]->lines as $key => $line)
					@if($line->modified)
					<tr id="{{$line->id}}" class="listTableBodyRow">
						<td class="itemColumn">{{$line->description}}</td>
						<td class="priceColumnValue"><input type="number" value="{{sprintf('%.02f', $line->price)}}" class="priceInput"  onchange="priceValueChange(this)"></td>
					</tr>
					@else
					<tr id="{{$line->id}}" class="listTableBodyRow notmodified">
						<td class="itemColumn">{{$line->description}}</td>
						<td class="priceColumnValue"><input type="number" value="{{sprintf('%.02f', $line->price)}}" class="priceInput"  onchange="priceValueChange(this)"></td>
					</tr>
					@endif
					@endforeach
					@endif
				</tbody>
			</table>
		</div>

		<div id="noItemsFoundDiv" class="noItemFound">NO ITEMS MATCHED YOUR SEARCH</div>

		<div id="deletedMessage" class="deleteMessageClass">
			THE LIST WAS DELETED
		</div>
		<div class="buttonsDiv">
			<input type="button" id="deleteListButton" value="DELETE LIST" class="actionButton deleteListButton">
		</div>
	</div>
	<div id="thereAreNotLists">
		<h1>YOU HAVE NOT CREATED PRICE LISTS</h1>
	</div>
</div>
@endsection
</body>
</html>
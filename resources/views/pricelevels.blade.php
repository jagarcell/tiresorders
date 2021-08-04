@extends('layouts.app')
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="shortcut icon" href="public/favicon.ico"/>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

	<title>PRICE LEVELS</title>

	@section('scripts')
	<script src="//code.jquery.com/jquery-1.12.4.js"></script>
	<script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  	<script type="text/javascript" src="public/js/pricelevels.js"></script>
	@endsection

	@section('styles')
	<link rel="stylesheet" type="text/css" href="public/css/pricelevels.css">
	@endsection
</head>
<body>
@section('content')
<div id="pricelevelsMainDiv" class="pricelevelsMainDiv">
	<div class="labelDiv">PRICE LEVELS</div>
	<div class="tableDiv">
		<table id="pricelevelsTable" class="pricelevelsTable fixed_header">
			<thead>
				<tr>
					<th class="firstCol">Description</th>
					<th class="secondCol perctHeaderAlign">%</th>
					<th class="thirdCol">Type</th>
				</tr>
			</thead>
			<tbody>
				@foreach($pricelevels as $key => $pricelevel)
				<tr id="{{$pricelevel->id}}">
					<td class="firstCol"><input type="text" value="{{$pricelevel->description}}" onchange="descriptionChange(this)"></td>
					<td class="secondCol"><input type="text" value="{{$pricelevel->percentage}}" onchange="percentageChange(this)" class="perctAlign"></td>
					<td class="thirdCol">
						<select onchange="typeChange(this)">
							<option value="discount" {{($pricelevel->type == 'discount') ? 'selected' : ' '}}>Discount</option>
							<option value="increment" {{($pricelevel->type == 'increment') ? 'selected' : ''}}>Price Up</option>
						</select>
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
	<div class="tableDiv">
		<input id="addPriceLevel" type="button" class="actionButton addPriceLevel" value="ADD A PRICE LEVEL">
	</div>
	<div id="updateMessage" class="updateMessage"></div>
</div>
@endsection
</body>
</html>
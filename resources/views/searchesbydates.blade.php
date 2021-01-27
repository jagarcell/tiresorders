@extends('layouts.app')
<!DOCTYPE html>
<html>
<head>
    <!-- CSRF Token -->
    <meta  name="csrf-token" content="{{ csrf_token() }}">
	<link rel="shortcut icon" href="public/favicon.ico"/>

	<title>Searches By Dates</title>

	@section('scripts')
	<script src="//code.jquery.com/jquery-1.12.4.js"></script>
	<script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  	<script type="text/javascript" src="public/js/searchesbydates.js"></script>
	@endsection

	@section('styles')
	<link rel="stylesheet" type="text/css" href="public/css/searchesbydates.css">
	@endsection
</head>
<body>
@section('content')
{{ csrf_field() }}
<div class="searchesByDatesMainDiv">
	<table id="searchesTable" class="searchesbydatesTable fixed_header">
		<thead>
			<tr>
				<th>SEARCH DATE</th>
				<th>SEARCH TEXT</th>
				<th>CLIENT</th>
			</tr>
		</thead>
		<tbody>
			@foreach($searchesbydates as $key => $search)
			<tr>
				<td>{{$search->searchdate}}</td>
				<td>{{$search->searchtext}}</td>
				<td>{{$search->name}}</td>
			</tr>
			@endforeach
		</tbody>
	</table>
</div>
@endsection
</body>
</html>
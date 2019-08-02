@extends('layouts.app')
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

	<title>List Of Users</title>
	@section('scripts')
	<script type="text/javascript" src="public/js/listusers.js"></script>
	@endsection

	@section('styles')
	<link rel="stylesheet" type="text/css" href="public/css/listusers.css">
	@endsection
</head>
<body>
	@section('content')
	<div id="listusersMainDiv" class="listusersMainDiv">

		<input id="userId" type="text" value="{{$user->id}}" hidden="">
		<input id="qbCustomerId" type="text" hidden="">

		<!-- USERS LIST -->
		<div id="usersDiv" class="usersDiv">
			<div class="labelDiv">USERS</div>
			<table id="usersTable" class="usersTable fixed_header">
				<thead>
				<tr>
					<th class="firstCol">User</th>
					<th class="secondCol">Email</th>	
					<th class="fourthCol">Price Level</th>
					<!--th class="fourthCol">Price List</th-->
					<th class="fifthCol">Type</th>
				</tr>
				</thead>
				<tbody>
				@foreach($users as $key => $user)
				<tr id="{{$user->id}}" onclick="userClick(this)">
					<td class="firstCol">{{$user->qbuser}}</td>
					<td class="secondCol">{{$user->email}}</td>
					@isset($user->level)
					<td class="fourthCol">{{$user->pricelevel}}</td>
					@endisset
					@isset($user->list)
					<td class="fourthCol">{{$user->pricelist}}</td>
					@endisset
					@isset($user->nopricelevel)
					<td class="fourthCol"></td>
					@endisset
					@if($user->email_verified_at !== null)
					<td class="fifthCol" style="color: green;">{{$user->type}}</td>
					@else
					<td class="fifthCol" style="color: green;">{{$user->type}}</td>
					@endif
				</tr>
				@endforeach
				</tbody>
			</table>
		</div>

		<!-- USER EDITION FORM -->
		<div id="userEditDiv" class="userEditDiv">
			<div class="labelDiv">USER EDITION</div>
			<div class="editDiv">
				<div id="qbUserFieldDiv" class="userFieldDiv">
					<label for="qbCustomer">Name:</label>
					<input id="qbCustomer" type="text" class="userEditInput checkQbNameButton" value="{{$user->name}}">
					<input type="button" id="checkName" value="Check" class="checkQbNameButton checkQbButtonWidth actionButton">
				</div>
				<div id="qbNameDiv" class="userFieldDiv">
					<label for="qbName">Qb Name:</label><div id="qbName" class="userEditInput checkQbNameButton" style="color: green"></div>
					<input type="button" id="qbUseName" value="Use" class="checkQbNameButton checkQbButtonWidth actionButton">
				</div>

				<div id="userFieldDiv" class="userFieldDiv">
					<label for="userName">User Name:</label><input id="userName" class="userEditInput" type="text" placeholder="Enter A Name Dor This User">
				</div>

				<div class="userFieldDiv">
					<label for="userAddress">Address:</label><input id="userAddress" class="userEditInput checkQbAddressButton" type="text" placeholder="Enter The User's Address">
					<input type="button" id="checkAddress" value="Check" class="checkQbAddressButton checkQbButtonWidth actionButton">
				</div>
				<div id="qbAddressDiv" class="userFieldDiv">
					<label for="qbAddress">Qb Address:</label><div id="qbAddress" class="userEditInput checkQbAddressButton" style="color: green"></div>
					<input type="button" id="qbUseAddress" value="Use" class="checkQbAddressButton checkQbButtonWidth actionButton">
				</div>

				<div class="userFieldDiv">
					<label>Phone:</label><input id="userPhone" class="userEditInput checkQbPhoneButton" type="text" placeholder="Enter The User's Phone">
					<input type="button" id="checkPhone" value="Check" class="checkQbPhoneButton checkQbButtonWidth actionButton">
				</div>
				<div id="qbPhoneDiv" class="userFieldDiv">
					<label for="qbPhone">Qb Phone:</label><div id="qbPhone" class="userEditInput checkQbPhoneButton" style="color: green"></div>
					<input type="button" id="qbUsePhone" value="Use" class="checkQbPhoneButton checkQbButtonWidth actionButton">	
				</div>

				<div class="userFieldDiv">
					<label>Email:</label><input id="userEmail" class="userEditInput" type="text" placeholder="Enter The User's Email" readonly="">
				</div>
				<div id="priceLevelsDiv" class="userFieldDiv">
					<label for="priceLevels">Price Lavel:</label>
					<select id="priceLevels" class="userEditInput">
						<option value="-1">None</option>
						@isset($priceLevels)
						@foreach($priceLevels as $key => $priceLevel)
						<option value="{{$priceLevel->id}}">{{$priceLevel->description}}</option>
						@endforeach
						@endisset	
					</select>
				</div>
				<div id="priceListsDiv" class="userFieldDiv">
					<label for="priceLists">Price List:</label>
					<select id="priceLists" class="userEditInput">
						<option value="-1">None</option>
						@isset($priceLists)
						@foreach($priceLists as $key => $priceList)
						<option value="{{$priceList->id}}">{{$priceList->description}}</option>
						@endforeach
						@endisset	
					</select>
				</div>
				<div id="savedMessage" class="savedMessage"></div>
			</div>
		</div>
		<div id="buttonsDiv">
			<div id="actionMessage">
				<!-- MESSAGE ADDED FROM JAVASCRIPT -->
			</div>
			<div id="emailVerifyButton" class="userFieldDiv emailVerifyButtonDiv">
				<input id="resendButton" type="button" class="actionButton" value="RESEND VERIFY EMAIL">
			</div>
			<!--div class="userFieldDiv passwdResetDiv">
				<input id="passwdResetButton" type="button" class="actionButton passwdResetButton" value="PASSWD RESET">
			</div-->
			<div class="userFieldDiv saveButton">
				<input id="deleteUserButton"  type="button" class="actionButton minWidth" value="DELETE USER">
				<input id="saveButton" type="button" class="actionButton minWidth" value="SAVE CHANGES">
			</div>
		</div>
	</div>
	@endsection
</body>
</html>
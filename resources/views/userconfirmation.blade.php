<!DOCTYPE html>
<html>
<head>
	<title></title>
	<link rel="stylesheet" type="text/css" href="public/css/userconfirmation.css">
</head>
<body>
	<div>
		<div style="font-style: italic;">{{env('APP_NAME')}}</div>
		@if($userType == 'admin')
		<div class="loginField"><label style="	color: #0f0; font-size: 16px;">A NEW USER HAS BEEN SUCCESFULLY CREATED</label></div>
		@else
		<div class="loginField"><label style="	color: #0f0; font-size: 16px;">YOUR USER HAS BEEN SUCCESFULLY CREATED</label></div>
		@endif
		@if($userType == 'admin')
		<div class="loginField"><label style="display: inline; padding-left: 2em;">   Login: </label><p style="display: inline;">{{$userLogin}}</p></div>
		<div class="loginField"><label style="display: inline;">Password: </label><p style="display: inline;">{{$userPassword}}</p></div>
		@else
		<div class="loginField"><label style="display: inline; padding-left: 2em;">   Your Login Is: </label><p style="display: inline;">{{$userLogin}}</p></div>
		<div class="loginField"><label style="display: inline;">Your Password Is: </label><p style="display: inline;">{{$userPassword}}</p></div>
		@endif
		<div>
			Please Visit: {{env('APP_URL') . '/login'}}
		</div>
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>
		<div>
			Powered By: <a href="https://www.allwebdone.com">www.allwebdone.com</a>
			
		</div>

	</div>
</body>
</html>
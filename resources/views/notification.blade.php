@extends('layouts.app')
<!DOCTYPE html>
<html>
<head>
    <!-- CSRF Token -->
    <meta  name="csrf-token" content="{{ csrf_token() }}">

	<title>Notification</title>
	<!-- STYLES -->
	<link rel="stylesheet" type="text/css" href="public/css/inventory.css">

	@section('scripts')
	<script src="//code.jquery.com/jquery-1.12.4.js"></script>
	<script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  	<script type="text/javascript" src="/public/js/notification.js"></script>
    <script type="text/javascript" src="/public/js/dropzone.js"></script>
	@endsection

	@section('styles')
	<link rel="stylesheet" type="text/css" href="/public/css/notification.css">
    <link rel="stylesheet" type="text/css" href="/public/css/dropzone.css">
	@endsection
</head>
<body>
@section('content')
{{ csrf_field() }}
<div class="notiMainDiv">
    <div class="notiTitle">
        <label>Notification</label>
    </div>
    <div class="notiFrame">
        <div class="notiImage">

        </div>
        <div class="notiSubject">

        </div>
        <div class="notiText">
            
        </div>
    </div>
</div>
@endsection
</body>
</html>
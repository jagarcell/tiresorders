@extends('layouts.app')
<!DOCTYPE html>
<html>
<head>
    <!-- CSRF Token -->
    <meta  name="csrf-token" content="{{ csrf_token() }}">

	<title>Notification</title>

    <!-- SCRIPTS -->
	@section('scripts')
	<script src="//code.jquery.com/jquery-1.12.4.js"></script>
	<script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  	<script type="text/javascript" src="/public/js/notification.js"></script>
    <script type="text/javascript" src="/public/js/dropzone.js"></script>
	@endsection

    <!-- STYLES -->
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
        <div class="smartphone"><img src="/public/img/logos/Smartphone.png" class="smartPhoneImage"></div>
    </div>
    <div class="notiFrame">
        <div class="notiImage">
            <form action="/fileupload" method="post" enctype="multipart/form-data" class="dropzone" style="width: 100%; height: 100%; border-style: none !important;" id="dropzone1">
                @csrf
                <input type="text" name="notiimagepath" value="/public/img/notification" hidden="">
            </form>
        </div>
        <div class="notiSubject">
            <input type="text" placeholder="Subject">
        </div>
        <div class="notiText">
            <input type="text" placeholder="Message">
        </div>
    </div>
    <div class="notiSendDiv">
        <input type="button" value="SEND NOTIFICATION" class="notiSendButton actionButton">
        <div class="notiToDiv">TO:</div>
        <div class="notiToSelectDiv">
            <select>
                <option value="1">EVRYONE</option>
                <option value="2">ADMIN</option>
                <option value="3">USER</option>
                <option value="4">ADMIN & USER</option>
            </select>
        </div>
    </div>
</div>
@endsection
</body>
</html>
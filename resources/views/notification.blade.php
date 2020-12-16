@extends('layouts.app')
<!DOCTYPE html>
<html>
<head>
    <!-- CSRF Token -->
    <meta  name="csrf-token" content="{{ csrf_token() }}">

	<title>Notification</title>

    <link rel="shortcut icon" href="/public/favicon.ico"/>

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
            <form action="/pushnotificationtmageupload" method="post" enctype="multipart/form-data" class="dropzone" style="width: 100%; height: 100%; border-style: none !important;" id="dropzone1">
                @csrf
            </form>
        </div>
        <form action="/fb/sendnotification" method="POST" id="notiForm">
            <div class="notiSubject">
                <input type="text" placeholder="Subject" name="title">
            </div>
            <div class="notiText">
                <textarea class="messageText" placeholder="Message" name="body"></textarea>
            </div>
            <!-- THE NAME FOR THE IMAGE INPUT WILL BE SET TO 'image'
             FROM JAVASCRIPT IF AN IMAGE IS UPLOADED -->
            <input type="hidden" name="" value="/public/img/notification" id="notiImage">
        </form>
    </div>
    <div class="notiSendDiv">
        <input type="submit"  value="SEND NOTIFICATION" class="notiSendButton actionButton" form="notiForm">
        <div class="notiToDiv">TO:</div>
        <div class="notiToSelectDiv">
            <select form="notiForm" name="to">
                <option value="everyone" title="Every One Using The App">EVERYONE</option>
                <option value="admin" title="Only Authenticated Admin">ADMIN</option>
                <option value="user" title="Only Authenticated User">USER</option>
                <option value="admin&user" title="Both Aunthenticated Admin And User">ADMIN & USER</option>
            </select>
        </div>
    </div>
</div>
@endsection
</body>
</html>
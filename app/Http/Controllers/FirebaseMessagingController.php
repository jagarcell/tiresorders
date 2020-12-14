<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\FirebaseMessaging;

class FirebaseMessagingController extends Controller
{
    public function TestMessage(Request $request)
    {
        # code...
        return (new FirebaseMessaging())->TestMessage($request);
    }

    public function AddFcmToken(Request $request)
    {
        # code...
        return (new FirebaseMessaging())->AddFcmToken($request);
    }

    public function SendNotification(Request $request)
    {
        return (new FirebaseMessaging())->SendNotification($request);
    }

    public function Notification(Request $request)
    {
        return (new FirebaseMessaging())->Notification($request);
    }
}

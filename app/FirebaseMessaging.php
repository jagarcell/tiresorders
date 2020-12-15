<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use App\Users;

class FirebaseMessaging extends Model
{
    public function TestMessage($request)
    {
        # code...
        $url = "https://fcm.googleapis.com/fcm/send";

        $registeredTokens = (new Fcm())->where('id', '>', -1)->get();
        $tokens = array();
        foreach($registeredTokens as $key => $registeredToken){
            array_push($tokens, $registeredToken->token);
        }

        $serverKey = env('FCM_SERVER_KEY');

        $title = "Message From Prestige Tires";
        $body = "Great Special This Week";
        $notification = array('title' =>$title , 'body' => $body, 'image' => '/public/img/coding.jpg');
        $arrayToSend = array('registration_ids' => $tokens, 'notification' => $notification,'priority'=>'high');
        $json = json_encode($arrayToSend);
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: key='. $serverKey;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
        
		//So that curl_exec returns the contents of the cURL; rather than echoing it
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
        
        //Send the request
        $response = curl_exec($ch);
        //Close request
        if ($response === FALSE) {
        die('FCM Send Error: ' . curl_error($ch));
        }
        curl_close($ch);
    }

    /** 
    *
    * Parameters:
    * @ token
    * @ apikey (optional if it is an authenticated user)
    *
    */

    public function AddFcmToken($request)
    {
         # code...
         $token = $request['token'];

         // DEFAULT userId IS -1 
         // WILL BE USED IF NO apikey IS PROVIDED (NO AUTHENTICATED USER) OR
         // IF THERE IS NOT AN USER THAT MATCHS THE PROVIDED apikey   
         $userId = -1;

        // IF apikey IS SET IT IS AN AUTHENTICATED USER
         if(isset($request['apikey'])){
            // GET THE USER FOR THIS apikey
            $apiKey = $request['apikey'];
            $users = (new Users())->where('api_key', $apiKey)->get();
            // IF THERE IS AN USER FOR THIS apikey ...
            if(count($users) > 0){
                // ... THEN SET THE userId TO THIS USER'S ID
                $user = $users[0];
                $userId = $user->id;
            }
         }

         // GET THE RECORD FOR THE FCM TOKEN
         try {
            $tokens = $this->where('fcm_token', $token)->get();
            // IF THERE IS NOT A MATCHING RECORD FOR THE TOKEN ...
            if(count($tokens) == 0){
                // ... CREATE ONE ASSOCIATED TO THE USER ID
                $this->fcm_token = $token;
                $this->userid = $userId;
                $this->save();
            }
            else{
                // A RECORD WAS FOUND FOR THE FCM TOKEN
                // LET'S UPDATE THE USER ASSOCIATED TO IT
                $this->where('fcm_token', $token)->update(['userid' => $userId]);
            }
            // EEVRYTHING OK
            return ['status' => 'OK'];
        } catch (\Throwable $th) {
            // SOMETHING WENT WRONG
            return ['status' => 'ERROR', 'message' => $th];
        }
    }

    /** 
    *
    * @param title
    * @param body
    * @param image
    * @param to [all, admin or user]
    *
    */
    public function SendNotification($request)
    {
        // GET THE FIRBASE MESSAGONG CONFIG PARAMETERS
        $fbconfig = config('firebasemessaging');

        // SET THE INFO FOR THE NOTIFICATION
        $title = isset($request['title']) ? $request['title'] : $fbconfig['FCM_NOTIFICATION_DEFAULT_TITLE'];
        $body = isset($request['body']) ? $request['body'] : $fbconfig['FCM_NOTIFICATION_DEFAULT_BODY'];
        $image = isset($request['image']) ? $request['image'] : $fbconfig['FCM_NOTIFICATION_DEFAULT_IMAGE'];
        $to = isset($request['to']) ? $request['to'] : $fbconfig['FCM_NOTIFICATION_DEFAULT_TO'];

        // FIREBASE SEND URL
        $url = "https://fcm.googleapis.com/fcm/send";

        // ARRAY TO HOLD THE VALID TOKENS TO NOTIFY    
        $tokens = array();

        // DETERMINE WHO IS GOING TO GET NOTIFICATIONS
        switch ($to) {
            case 'everyone':
                // ALL APPLICATION WITH A VALID TOKEN
                // USERS WILL GET THE NOTIFICACTION
                $registeredTokens = $this->where('id', '>', -1)->get();
                foreach($registeredTokens as $key => $registeredToken){
                    array_push($tokens, $registeredToken->fcm_token);
                }
                break;
            default:
                // ONLY THE TYPE OF USERS (admin or user) INDICATED
                // BY $to WILL RECEIVE THE NOTIFICATION
                $registeredTokens = DB::table('users')
                    ->join('firebase_messagings', 'users.id', '=', 'firebase_messagings.userid')
                        ->where('users.type', '=', $to)->select('firebase_messagings.fcm_token')->get();
                foreach($registeredTokens as $key => $registeredToken){
                    array_push($tokens, $registeredToken->fcm_token);
                }
            break;
        }

        // THE FCM SERVER KEY NEEDED TO SEND NOTIFICATIONS
        $serverKey = $fbconfig['FCM_SERVER_KEY'];

        $notification = array('title' =>$title , 'body' => $body, 'image' => env('APP_URL') . $image);

        $arrayToSend = array('registration_ids' => $tokens, 'notification' => $notification,'priority'=>'high');
        $json = json_encode($arrayToSend);
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: key='. $serverKey;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
        
		//So that curl_exec returns the contents of the cURL; rather than echoing it
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);//Send the request
        $response = curl_exec($ch);

        //Close request
        if ($response === FALSE) {
            die('FCM Send Error: ' . curl_error($ch));
        }
        curl_close($ch);
        return view('notification');
    }

    public function Notification($notification)
    {
        return view('notification');
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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
        //Send the request
        $response = curl_exec($ch);
        //Close request
        if ($response === FALSE) {
        die('FCM Send Error: ' . curl_error($ch));
        }
        curl_close($ch);
    }

    public function AddFcmToken($request)
    {
         # code...
         $token = $request['token'];
         $userId = -1;
         if(isset($request['apikey'])){
            $apiKey = $request['apikey'];
            $users = (new Users())->where('api_key', $apiKey)->get();
            if(count($users) > 0){
                $user = $users[0];
                $userId = $user->id;
            }
         }

         try {
            $tokens = $this->where('fcm_token', $token)->get();
            dd($tokens);
            if(count($tokens) == 0){
                $this->fcm_token = $token;
                $this->userid = $userId;
                $this->save();
            }
            else{
                $this->where('fcm_token', $token)->update(['userid' => $userId]);
            }
            return ['status' => 'OK'];
        } catch (\Throwable $th) {
            return ['status' => 'ERROR'];
        }
    }
}

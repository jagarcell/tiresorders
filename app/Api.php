<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

use App\Users;
use App\Mail\ApiCodeIntructions;
use App\Inventory;

class Api extends Model
{
    //

	public function GenerateApiKey($n) { 
	    $characters = '0123456789abcdefABCDEF'; 
	    $randomString = ''; 
	  
	    for ($i = 0; $i < $n; $i++) { 
	        $index = rand(0, strlen($characters) - 1); 
	        $randomString .= $characters[$index]; 
	    } 
	  
	    return $randomString; 
	}

	public function CreateApiKey($request)
	{
		# code...
		$userId = $request['userId'];
    	$api_key = $this->GenerateApiKey(64);
    	$user = (new Users())->where('id', $userId)->get();
    	if(count($user) > 0){
    		$useremail = $user[0]->email;
    		if(strlen($user[0]->api_key) > 0){
    			$this->SendInstructions($user[0]->api_key, $useremail);
	    		return ['status' => 'ok', 'api_key' => $user[0]->api_key];
    		}
    		else{
    			$exists = 0;
    			for($i = 0; $i < 10; $i++){
    				$apiKeyExists = (new Users())->where('api_key', $api_key)->get();
    				if(count($apiKeyExists) > 0){
    					$exists = 1;
    					continue;
    				}
    				$exists = 0;
    			}
    			if($exists > 0){
		    		return ['status' => 'error'];
    			}
	    		(new Users())->where('id', $userId)->update(['api_key' => $api_key]);
	    		$this->SendInstructions($api_key, $useremail);
	    		return ['status' => 'ok', 'api_key' => $api_key];
    		}
    	}
    	else{
    		return ['status' => 'error'];
    	}
	}

	public function SendInstructions($api_key, $useremail)
	{
		# code...
        $date = getdate();
        $stamp = $date['mon'] . '/' . $date['mday'] . '/' . $date['year'] . ' - ' . $date['hours'] . ':' . $date['seconds'];
	    for($i = 3; $i > -1; $i--){
	        try {
	            Mail::to($useremail)->send(new ApiCodeIntructions($api_key));
	            Storage::disk('local')->append('ApiCodeInstructions.txt', 'Api Instructions Sent To: ' . $useremail . ' on ' . $stamp);
	            break;
	        } catch (\Exception $e) {
	            if($i = 0){
	                Storage::disk('local')->append('ApiCodeInstructions.txt', 'Failed To Send Api Instructions Email To: ' . $useremail . ' on ' . $stamp . ' This Was The Last Try');
	            }
	            else{
	                Storage::disk('local')->append('ApiCodeInstructions.txt', 'Failed To Send Api Instructions Email To: ' . $useremail . ' on ' . $stamp . ' Will Retry');
	            }
	        }
	    }
	}

	public function Inventory($request)
	{
		# code...
		return json_encode((new Inventory())->SearchForApi($request));
	}

	public function ApiTest($request)
	{
		# code...
		//The url you wish to send the POST request to
		$url = 'http://www.tiresorders.com/api/inventory';

		$api_key = 'c5BCBeb8B5CeD4cd832dcd0E5a5E42FeBc28e3c4b41685dFd0dd41f0bBBeCe7D';
		//The data you want to send via POST
		$fields = ['api_key' => $api_key,];

		//url-ify the data for the POST
		$fields_string = http_build_query($fields);

		//open connection
		$ch = curl_init();

		//set the url, number of POST vars, POST data
		curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch,CURLOPT_POST, true);
		curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);

		//So that curl_exec returns the contents of the cURL; rather than echoing it
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);

		//execute post
		$result = curl_exec($ch);

		$r = json_decode($result, false);
/*
		foreach ($r as $key => $value) {
			# code...
			var_dump($value);
		}
*/
	}

    public function PublicInventory($request)
    {
		return ((new Inventory())->where('id', '>', -1)->get(['name', 'imgpath']), true);
	}
}

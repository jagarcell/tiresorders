<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Api;

class ApiController extends Controller
{
    //
    public function Inventory(Request $request)
    {
    	# code...
        return (new Api())->Inventory($request);
    }

    public function CreateApiKey(Request $request)
    {
    	# code...
    	return (new Api())->CreateApiKey($request);
    }


    public function ApiTest(Request $request)
    {
        return (new Api())->ApiTest($request);
    }

    public function PublicInventory(Request $request)
    {
        # code...
        return (new Api())->PublicInventory($request);
    }

    public function CreateKeys(Request $request)
    {
        # code...
        return (new Api())->CreateKeys($request);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Inventory;

class uploadController extends Controller
{
    //
    public function fileUpload(Request $request)
    {
        $fileToUpload = $request->file('file');
        $itemid = $request['itemid'];

        if(!empty($_FILES)){
	        $path = $fileToUpload->storeAs('img', $_FILES['file']['name'], 'welcome_images');
	        (new Inventory())->where('id', $itemid)->update(['imgpath' => $path]);
        }
    }

    public function PushNoificationImageUpload(Request $request)
    {
        
    }
}

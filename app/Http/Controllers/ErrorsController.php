<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ErrorsController extends Controller
{
    //
    public function QbConnectError(Request $request)
    {
    	# code...
    	return view('/qbconnecterror');
    }
}

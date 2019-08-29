<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SearchesDates;

class SearchesDatesController extends Controller
{
    //
    public function SearchDetails(Request $request)
    {
    	# code...
    	return (new SearchesDates())->SearchDetails($request);
    }
}

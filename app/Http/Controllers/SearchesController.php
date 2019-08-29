<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Searches;

class SearchesController extends Controller
{
    //
    public function AddNewSearch(Request $request)
    {
    	# code...
    	return (new Searches())->AddNewSearch($request);
    }

    public function ShowSearches(Request $request)
    {
    	# code...

    	return (new Searches())->ShowSearches($request);
    }

    public function DeleteSearches(Request $request)
    {
        # code...
        return (new Searches())->DeleteSearches($request);
    }
}

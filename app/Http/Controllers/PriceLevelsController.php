<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PriceLevels;

class PriceLevelsController extends Controller
{
    //
    public function __construct()
    {
        # code...
        $this->middleware('verified');
    }

    public function listPriceLevels(Request $request)
    {
    	# code...
    	return (new PriceLevels())->listPriceLevels($request);
    }

    public function savePriceLevel(Request $request)
    {
    	# code...
    	return (new PriceLevels())->savePriceLevel($request);
    }

    public function getPriceLevels(Request $request)
    {
        # code...
        return (new PriceLevels())->getPriceLevels($request);
    }
}

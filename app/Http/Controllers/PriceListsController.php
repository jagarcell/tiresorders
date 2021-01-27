<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\PriceListHeader;
use App\PriceListLines;

class PriceListsController extends Controller
{
    //
    public function ModifyPrices(Request $request)
    {
        return (new PriceListHeader())->ModifyPrices($reuqest);
    }

    public function GetPriceListsHeaders(Request $request)
    {
        # code...
        return (new PriceListHeader())->GetPriceListsHeaders($request);
    }

    public function PriceLists(Request $request)
    {
    	# code...
    	return (new PriceListHeader())->PriceLists($request);
    }

    public function CreateNewList(Request $request)
    {
    	# code...
    	return (new PriceListHeader())->CreateNewList($request);
    }

    public function UpdatePrices(Request $request)
    {
        # code...
        return (new PriceListLines())->UpdatePrices($request);
    }

    public function PriceListById(Request $request)
    {
        # code...
        return (new PriceListHeader())->PriceListById($request);
    }

    public function DeleteListById(Request $request)
    {
        # code...
        return (new PriceListHeader())->DeleteListById($request);
    }

    public function ListQty(Request $request)
    {
        # code...
        return (new PriceListHeader())->ListQty($request);
    }

    public function SearchInList(Request $request)
    {
        # code...
        return (new PriceListLines())->SearchInList($request);
    }
}

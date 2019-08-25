<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Inventory;
use App\QuickBooks;

class InventoryController extends Controller
{
    //
    public function __construct()
    {
    	# code...
    	$this->middleware('qbconn');
        $this->middleware('verified');
    }

    public function getQbInventory(Request $request)
    {
    	# code...
    	return (new QuickBooks())->placeAnOrder($request);
    }

    public function searchInventory(Request $request)
    {
    	# code...
    	return (new Inventory())->searchInventory($request);
    }

    public function SearchFor(Request $request)
    {
        # code...
        return (new Inventory())->SearchFor($request);
    }

    public function SyncronizeInventories(Request $request)
    {
        return (new Inventory())->SyncronizeInventories($request);
    }

    public function GetInventory(Request $request)
    {
        (new Inventory())->GetInventory($request);
    }

    public function Inventory(Request $request)
    {
        return (new Inventory())->Inventory($request);
    }

    /**
    *
    * @param    id, price
    *
    **/

    public function UpdateItem(Request $request)
    {
        return (new Inventory())->UpdateItem($request);
    }

    public function PriceLists(Request $request)
    {
        # code...
        return (new Inventory())->PriceLists($request);
    }
}

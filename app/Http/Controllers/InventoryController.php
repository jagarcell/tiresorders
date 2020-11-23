<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Inventory;
use App\QuickBooks;
use Carbon\Carbon;

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

    public function SearchItemById(Request $request)
    {
        return (new Inventory())->SearchItemById($request);
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

    public function UpdateOferta(Request $request)
    {
        return (new Inventory())->UpdateOferta($request);
    }
    
    public function PriceLists(Request $request)
    {
        # code...
        return (new Inventory())->PriceLists($request);
    }

    public function DateTimeOffset(Request $request)
    {
        # code...
        printf("Now: %s", Carbon::now());
        return;    
        return (new Inventory())->DateTimeOffset($request);
    }
}

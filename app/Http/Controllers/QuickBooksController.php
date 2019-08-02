<?php

namespace App\Http\Controllers;

use App\QuickBooks;
use Illuminate\Http\Request;

class QuickBooksController extends Controller
{
    //
    public function __construct()
    {
    	# Check if we are connected to QuickBooks
    	# Before executing the requested action
//    	$this->middleware('qbconn');
    }

    public function QbCallback(Request $request)
    {
    	# code...
    	return (new QuickBooks())->QbCallback($request);
    }

    public function CompanyInfo(Request $request)
    {
    	# code...
    	return (new QuickBooks())->CompanyInfo();
    }

    public function InventorySummary(Request $request)
    {
        # code...
        return (new QuickBooks())->InventorySummary($request);
    }

    public function InventorySummaryWithView(Request $request)
    {
        # code...
        $companyInfo = json_decode($this->companyInfo($request), true);
        $companyName = $companyInfo['CompanyName'];
        session('companyName', $companyName);
        $InventorySummary = json_decode($this->InventorySummary($request), true);
        return view('inventorysummary', ['companyName' => $companyName, 'InventorySummary' => $InventorySummary]);
    }

    public function Invoice(Request $request)
    {
    	# code...
    	return (new QuickBooks())->Invoice($request);
    }

    public function customersregister(Request $request)
    {
        # code...
        return (new QuickBooks())->customersregister($request);
    }

    public function Customer(Request $request)
    {
        # code...
        $qbCustomerId = $request['qbcustomerid'];
        return (new QuickBooks())->Customer($qbCustomerId);
    }

    public function qbItemByQbId(Request $request)
    {
        return (new QuickBooks())->qbItemByQbId($request);
    }

    public function Connect(Request $request)
    {
        # code...
        return (new QuickBooks())->Connect($request);
    }

    public function Disconnect(Request $request)
    {
        # code...
        return (new QuickBooks())->Disconnect($request);
    }

    public function ListUsersQbCustomers(Request $request)
    {
        # code...
        return (new QuickBooks())->ListUsersQbCustomers($request);
    }
}

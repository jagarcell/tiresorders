<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Orders;
use App\QuickBooks;

class OrdersController extends Controller
{
    //
    public function __construct()
    {
    	# code...
        $this->middleware('qbconn');
        $this->middleware('auth');
    }

    public function placeAnOrderTest(Request $request)
    {
        # code...
        return (new Orders())->placeAnOrderTest($request);
    }

    public function placeAnOrder(Request $request)
    {
        # code...
        return (new Orders())->placeAnOrder($request);
    }

    public function getOrders(Request $request)
    {
    	# code...
    	return (new Orders())->getOrders($request);
    }

    public function addItemToOrder(Request $request)
    {
        # code...
        return (new Orders())->addItemToOrder($request);
    }

    public function viewTheOrder(Request $request)
    {
        # code...
        return (new Orders())->viewTheOrder($request);
    }

    public function deleteLineByQbItemId(Request $request)
    {
        # code...
        /**
        * @param qbItemId
        */
        $qbItemIds = $request['qbItemIds'];
        return (new Orders())->deleteLineByQbItemId($qbItemIds);
    }

    public function submitOrder(Request $request)
    {
        # code...
        return (new Orders())->submitOrder($request);
    }

    public function PurchaseOrder(Request $request)
    {
        # code...
        return (new Orders())->PurchaseOrder($request);
    }

    public function ListOrders(Request $request)
    {
        # code...
        return (new Orders())->ListOrders($request);
    }

    public function ListOpenOrders(Request $request)
    {
        # code...
        return (new Orders())->ListOpenOrders($request);
    }

    public function ListOrdersByStatus(Request $request)
    {
        # code...
        return (new Orders())->ListOrdersByStatus($request);
    }

    public function OrderById(Request $request)
    {
        # code...
        return (new Orders())->OrderById($request);
    }

    /**
    * @param orderLinesIds Array
    *
    */
    public function deleteOrderLines(Request $request)
    {
        # code...
        return (new Orders())->deleteOrderLines($request);
    }

    public function InvoiceOrder(Request $request)
    {
        # code...
        return (new Orders())->InvoiceOrder($request);
    }

    public function ViewTheOrderByOrderId(Request $request)
    {
        # code...
        return (new Orders())->ViewTheOrderByOrderId($request);
    }

    public function continueShopping(Request $request)
    {
        # code...
        return (new Orders())->continueShopping($request);
    }
}

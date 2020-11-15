<?php

namespace App;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

use App\Users;
use App\Inventory;
use App\QuickBooks;
use App\OrderLines;
use App\Mail\PurchaseOrder;

class Orders extends Model
{
    //
    public function getOrders(Request $request)
    {
    	# code...
    	return $this->where('id', '>', -1)->get();
    }


    public function placeAnOrderTest(Request $request)
    {
        # code...
        $Inventory = (new Inventory())->where('oferta', '>', 0)->get();
        return view('/placeanordertest', ['Inventory' => $Inventory]);
    }

    public function placeAnOrder(Request $request)
    {
        # code...
        $Inventory = (new Inventory())->where('oferta', '>', 0)->get();
        return view('/placeanorder', ['Inventory' => $Inventory]);
    }

    public function findOrderByUserId($userId)
    {
        # code...
        return $this->where('user_id', $userId)->where('status', 'open')->get();
    }

    public function findOpenOrderByUserId($userId)
    {
        # code...
        return $this->where('user_id', $userId)->where('status', 'open')->get();
    }

    /** 
    * @param  linesdata
    * @return status, order
    *
    * Check if there is an open order
    * to see if we just need to add an
    * item or if we need to create a new
    * order first
    */

    public function addItemToOrder(Request $request)
    {
        # code...
        $linesdata = $request['linesdata'];
        
        $user = Auth::user();
        $order = $this->findOrderByUserId($user->id);
        if(is_null($order) || count($order) == 0){
            // ORDER DOESN'T EXIST SO LET'S CREATE IT
            $this->user_id = $user->id;
            $this->orderdate = date("Y-m-d");
            $this->status = 'open';
            try {
                $this->save();
                $order = $this;
                $order->id = $this->id;
            } catch (\Exception $e) {
                return(['status' => 'failed']);
            }
        }
        else{
            // ORDER ALREDY EXISTS
            $order = $order[0];
        }

        foreach ($linesdata as $key => $linedata) {
            # code...
            $qbitemid = $linedata['qbitemid'];
            $price = $linedata['price'];
            $qty = $linedata['qty'];

            // NOW WE ARE SURE THAT WE HAVE AN ORDER HEADER
            // LET'S ADD THE ITEM AS AN ORDER LINE
            $orderLine = (new OrderLines())->findLineByQbItemId($order->id, $qbitemid);
            if(!is_null($orderLine) && count($orderLine) > 0){
                $qty += $orderLine[0]->qty;
                (new OrderLines())->where('item_qbid', $qbitemid)->update(['qty' => $qty]);
            }
            else{
                $orderLine = (new OrderLines());
                $orderLine->order_id = $order->id;
                $orderLine->item_qbid = $qbitemid;
                $orderLine->qty = $qty;
                $orderLine->price = $price;
                try {
                    $orderLine->save();
                } catch (\Exception $e) {
                    return['status' => 'failed'];   
                }
            }
            $Inventory = (new Inventory());
            $Items = $Inventory->where('qbitemid', $qbitemid)->get();
            if(count($Items) > 0){
                $Item = $Items[0];
                $Inorders = $Item->inorders + $qty;
                $Inventory->where('id', $Item->id)->update(['inorders' => $Inorders]);
            }
        }

        $OrderLines = (new OrderLines())->getOrderLinesByOrderId($order->id);
        $order->lines = $OrderLines;
        return ['status' => 'success', 'order' => $order ];
    }

    public function viewTheOrder(Request $request)
    {
        /**
        ** @return
        ** order
        */
        $user = Auth::user();
        $userid = $user->id;
        $request['userid'] = $userid;
        $result = $this->getOrderByUserId($request);
        if($result['status'] == 'success'){
            if(isset($result['order'])){
                $order = $result['order'];
                $order->submit = "yes";
                return view('viewtheorder', ['order' => $order]);
            }
            else{
                return view('viewtheorder');
            }
        }
    }

    public function getOrderByUserId(Request $request)
    {
        # code...
        $userid = $request['userid'];
        $orders = $this->findOrderByUserId($userid);

        if(is_null($orders)){
            return (['status' => 'failed']);
        }

        if(count($orders) == 0){
            return (['status' => 'success']);
        }

        $order = $orders[0];
        $OrderLines = (new OrderLines())->getOrderLinesByOrderId($order->id);
        if(is_null($order) || is_null($OrderLines)){
            return (['status' => 'failed']);
        }
        else{
            $order->total = 0;
            foreach ($OrderLines as $key => $OrderLine) {
                # code...
                $Items = (new Inventory())->findItemByQbItemId($OrderLine->item_qbid);
                if (!is_null($Items) && count($Items) > 0) {
                    # code...
                    $Item = $Items[0];
                    $OrderLine->name = $Item->name;
                    $OrderLine->subtotal = $OrderLine->price * $OrderLine->qty;
                    $order->total += $OrderLine->subtotal;
                }
            }
            $order->lines = $OrderLines;

            return (['status' => 'success', 'order' => $order]);
        }
    }

    public function getOpenOrderByUserId(Request $request)
    {
        # code...
        $userid = $request['userid'];
        $orders = $this->findOpenOrderByUserId($userid);
        if(is_null($orders)){
            return (['status' => 'failed']);
        }

        if(count($orders) == 0){
            return (['status' => 'success']);
        }

        $order = $orders[0];
        $OrderLines = (new OrderLines())->getOrderLinesByOrderId($order->id);
        if(is_null($order) || is_null($OrderLines)){
            return (['status' => 'failed']);
        }
        else{
            $order->total = 0;
            foreach ($OrderLines as $key => $OrderLine) {
                # code...
                $Items = (new Inventory())->findItemByQbItemId($OrderLine->item_qbid);
                if (!is_null($Items) && count($Items) > 0) {
                    # code...
                    $Item = $Items[0];
                    $OrderLine->description = $Item->description;
                    $OrderLine->subtotal = $OrderLine->price * $OrderLine->qty;
                    $order->total += $OrderLine->subtotal;
                }
            }
            $order->lines = $OrderLines;

            return (['status' => 'success', 'order' => $order]);
        }
    }

    public function deleteLineByQbItemId($qbItemIds)
    {
        
        # code...
        $user = Auth::user();
        $orders = $this->where('user_id', $user->id)->where('status', 'open')->get();

        if(!is_null($orders) && count($orders) > 0){
            $order = $orders[0];
            $deletedLines = [];
            foreach ($qbItemIds as $key => $qbItemId) {
                # code...

                $deletedLine = (new OrderLines())->deleteLineByQbItemIdAndOrderId($qbItemId, $order->id);

                $Inventory = (new Inventory());
                $Items = $Inventory->where('qbitemid', $qbItemId)->get();
                if(count($Items) > 0){
                    $Item = $Items[0];
 
                    $Inpurchaseorders = $Item->inpurchaseorders - $deletedLine->qty;
                    $Inventory->where('id', $Item->id)->update(
                        ['inpurchaseorders' => $Inpurchaseorders]);
                }
                array_push($deletedLines, $deletedLine);
            }


            $orderLines = (new OrderLines())->getOrderLinesByOrderId($order->id);
            if(is_null($orderLines) || count($orderLines) == 0){
                try {
                    $this->where('user_id', $user->id)->delete();
                } catch (\Exception $e) {
                    
                }
            }
            return ['status' => 'success', 'deletedlines' => $deletedLines];
        }
        else{
            return ['status' => 'failed'];
        }
    }

    public function submitOrder(Request $request)
    {
        # code...
        $buttonId = $request['buttonid'];
        $specialInstructions = $request['specialinstructions'];

        $user = Auth::user();
        $request['userid'] = $user->id;

        $result = $this->getOrderByUserId($request);

        if($result['status'] ==  'success'){
            $order = $result['order'];
            if($buttonId == 'deliveryButton'){
                $this->where('id', $order->id)->update(
                    ['status' => 'delivery', 'specialinstructions' => $specialInstructions]);

                $order->deliveryaddress = $user->address;
                $order->phonenumber = $user->phone;
            }
            if($buttonId == 'pickUpButton'){
                $this->where('id', $order->id)->update(
                    ['status' => 'pickup', 'specialinstructions' => $specialInstructions]);
            }
            if($buttonId == 'continueShoppingButton'){
                $this->where('id', $order->id)->update(
                    ['specialinstructions' => $specialInstructions]);
            }
            $date = getdate();
            $stamp = $date['mon'] . '/' . $date['mday'] . '/' . $date['year'] . ' - ' . $date['hours'] . ':' . $date['seconds'];
            for($i = 3; $i > -1; $i--){
                try {
                    Mail::to($user->email)->send((new PurchaseOrder($order))->subject('Your Order Has Been Submitted'));
                    Storage::disk('local')->append('usercreation.txt', 'Order Confirmation Sent To: ' . $user->email . ' on ' . $stamp);
                    break;
                } catch (\Exception $e) {
                    if($i == 0){
                        Storage::disk('local')->append('usercreation.txt', 'Failed To Send Order Confirmation To: ' . $user->email . ' Last Try on ' . $stamp);
                    }
                    else{
                        Storage::disk('local')->append('usercreation.txt', 'Failed To Send Order Confirmation To: ' . $user->email . ' on ' . $stamp . ' Will Retry');
                    }
                }
            }

            $order->link = env('APP_URL') . "/viewtheorderbyorderid?orderId=" . $order->id;
            $order->specialInstructions = $specialInstructions;
            $adminUsers = (new Users())->where('type', 'admin')->get();

            foreach ($adminUsers as $key => $adminUser) {
                for($i = 3; $i > -1; $i--){
                    try {
                        Mail::to($adminUser->email)->send((new PurchaseOrder($order))->subject($user->name . ' - Purchase Order'));
                        Storage::disk('local')->append('usercreation.txt', 'Order Confirmation Sent To: ' . $adminUser->email . ' on ' . $stamp);
                        break;
                    } catch (\Exception $e) {
                        if($i == 0){
                            Storage::disk('local')->append('usercreation.txt', 'Failed Order Confirmation Sent To: ' . $adminUser->email . ' on ' . $stamp . ' Last Try');
                            return "failed to send";
                        }
                        else{
                            Storage::disk('local')->append('usercreation.txt', 'Failed Order Confirmation Sent To: ' . $adminUser->email . ' on ' . $stamp . ' Will Retry ');
                        }
                    }
                }
            }
            return "sent";
        }
        return "failed to send";
    }

    /**
    *
    * @param userid
    *
    */

    public function PurchaseOrder(Request $request)
    {
        # code...
        $user = Auth::user();
        if(!is_null($user) && $user->type == 'admin'){
            $result = $this->getOrderByUserId($request);
            $user = (new Users())->findUserById($request);
            $userName = "USER NOT FOUND";
            if(!is_null($user) && count($user) > 0){
                $userName = $user[0]->name;
            }
            if($result['status'] == 'success'){
                $order = $result['order'];
                $order->user = $userName;
                return view('viewtheorder', ['order' => $order]);
            }
        }
        return view('welcome');
    }

    public function ListOrders(Request $request)
    {
        # code...
        $orders = $this->where('id', '>', -1)->orderBY('orderdate', 'desc')->get();
        if(is_null($orders)){
            return view('welcome');
        }
        foreach ($orders as $key => $order) {
            # code...
            $request['userid'] = $order->user_id;
            $user = (new Users())->findUserById($request);
            if(!is_null($user) && count($user)){
                $customer = $user[0];
                $order->customer = $customer->name;
            }
            else{
                $order->customer = "NOT FOUND";
            }
            $OrderTotal = 0;
            $OrderLines = (new OrderLines())->getOrderLinesByOrderId($order->id);
            if(!is_null($OrderLines)){
                foreach ($OrderLines as $key1 => $OrderLine) {
                    # code...
                    $OrderTotal += $OrderLine->qty * $OrderLine->price;
                }
                $order->total = $OrderTotal;
            }
            else{
                $order->total = 0;
            }
        }
        return view('listorders', ['orders' => $orders]);
    }

    public function ListOpenOrders(Request $request)
    {
        # code...

        $date = getdate();
        $stamp = $date['mon'] . '/' . $date['mday'] . '/' . $date['year'] . ' - ' . $date['hours'] . ':' . $date['seconds'];
        $authUser = Auth::user();
        Storage::disk('local')->append('listopenorders.txt', 'User ' . $authUser->name . ' logged in /listopenorders on ' . $stamp);

        $orders = $this->where('id', '>', -1)->where('status', 'open')->orderBY('orderdate', 'desc')->get();
        if(is_null($orders)){
            return view('welcome');
        }
        foreach ($orders as $key => $order) {
            # code...
            $request['userid'] = $order->user_id;
            $user = (new Users())->findUserById($request);
            if(!is_null($user) && count($user)){
                $customer = $user[0];
                $order->customer = $customer->name;
            }
            else{
                $order->customer = "NOT FOUND";
            }
            $OrderTotal = 0;
            $OrderLines = (new OrderLines())->getOrderLinesByOrderId($order->id);
            if(!is_null($OrderLines)){
                foreach ($OrderLines as $key1 => $OrderLine) {
                    # code...
                    $OrderTotal += $OrderLine->qty * $OrderLine->price;
                }
                $order->total = $OrderTotal;
            }
            else{
                $order->total = 0;
            }
        }
        return view('listorders', ['orders' => $orders]);
    }

    public function ListOrdersByStatus(Request $request)
    {
        # code...
        $open = $request['open'];
        $pickup = $request['pickup'];
        $delivery = $request['delivery'];
        $invoiced = $request['invoiced'];

        $orders = $this->where('status', $open)->orWhere('status', $pickup)->orWhere('status', $delivery)->orWhere('status', $invoiced)->orderBY('orderdate', 'desc')->get();
        if(is_null($orders)){
            return view('welcome');
        }
        foreach ($orders as $key => $order) {
            # code...
            $request['userid'] = $order->user_id;
            $user = (new Users())->findUserById($request);
            if(!is_null($user) && count($user)){
                $customer = $user[0];
                $order->customer = $customer->name;
            }
            else{
                $order->customer = "NOT FOUND";
            }
            $date = date_create($order->orderdate);
            $order->orderdate = $date->format('m/d/Y');
            $OrderTotal = 0;
            $OrderLines = (new OrderLines())->getOrderLinesByOrderId($order->id);
            if(!is_null($OrderLines)){
                foreach ($OrderLines as $key1 => $OrderLine) {
                    # code...
                    $OrderTotal += $OrderLine->qty * $OrderLine->price;
                }
                $order->total = $OrderTotal;
            }
            else{
                $order->total = 0;
            }
        }
        return ['orders' => $orders];
    }

    public function ViewTheOrderByOrderId(Request $request)
    {
        # FIND DE ORDER BY ORDER ID
        $result = $this->OrderById($request);
        $authUser = Auth::user();
        if($result['status'] == 'ok'){
            $order = $result['order'];
            $order->specialinstructionsreadonly = "";
            if($authUser->type == 'admin'){
                // IF THE ORDER IS FOR DELIVERY ...
                if($order->status == 'delivery'){
                    // ... SET A DELIVERY INDICATOR FOR THE VIEW
                    $order->delivery = "";
                }
                // ADD THE USER IN THE ORDER'S NAME TO THE ORDER
                $userInOrder = (new Users())->where('id', $order->user_id)->get();
                $order->user = ($userInOrder[0])->name;
                return view('viewtheorder', ['order' => $order]);
            }
            else{
                return redirect('/placeanorder');
            }
        }

        return view('/');
    }

    /**
    *
    * @param orderId
    * @return order 
    *
    **/

    public function OrderById(Request $request)
    {
        $id = $request['orderId'];
        $orders = $this->where('id', $id)->get();
        if(count($orders) > 0){
            $order = $orders[0];
            $orderdate = date_create($order->orderdate);
            $order->orderdate = $orderdate->format('m/d/Y');
            $userId = $order->user_id;
            $users = (new Users())->where('id', $userId)->get();
            if(count($users) > 0){
                $user = $users[0];
                $order->customer = $user->name;
                $order->phone = $user->phone;
                $orderLines = (new OrderLines())->getOrderLinesByOrderId($order->id);
                $orderTotal = 0;
                foreach ($orderLines as $key => $orderLine) {
                    # code...
                    $qbItems = (new Inventory())->findItemByQbItemId($orderLine->item_qbid);
                    if(count($qbItems) > 0){
                        $qbItem = $qbItems[0];
                        $orderLine->name = $qbItem->name;
                    }
                    else{
                        $orderLine->name = "ITEM NOT FOUND";
                    }
                    $orderLine->subTotal = $orderLine->qty * $orderLine->price;
                    $orderTotal += $orderLine->subTotal;
                }
                $order->orderTotal = $orderTotal;
                $order->lines = $orderLines;
                $order->address = $user->address;
                return ['status' => 'ok', 'order' => $order];
            }
            else{
                return ['status' => 'fail', 'message' => 'Customer Not Found'];
            }
        }
        else{
            return ['status' => 'fail', 'message' => 'Order Not Found'];
        }
    }

    /**
    * @param orderLinesIds Array, orderId
    *
    */
    public function deleteOrderLines(Request $request)
    {
        # code...
        $orderLinesIds = $request['orderLinesIds'];
        $orderId = -1;
        if(count($orderLinesIds) > 0){
            $orderLine = (new OrderLines())->findLineById($orderLinesIds[0]);
            $orderId = $orderLine->order_id;
            foreach ($orderLinesIds as $key => $orderLineId) {
                (new OrderLines())->deleteLineById($orderLineId);
            }
            $orderLines = (new OrderLines())->getOrderLinesByOrderId($orderId);
            if(count($orderLines) > 0){
                return ['orderisempty' => false, 'orderid' => $orderId];
            }
            else{
                $this->where('id', $orderId)->delete();
                return ['orderisempty' => true];
            }
        }
        return ['orderisempty' => false, 'orderid' => $orderId];
    }

    /**
    * @param orderId
    *
    */
    public function InvoiceOrder(Request $request)
    {
        # code...
        $orderId = $request['orderid'];
        $status = "";
        try {
            $this->where('id', $orderId)->update(['status' => 'invoiced']);
            $status = "OK";
        } catch (\Exception $e) {
            $status = "FAILED";
        }
        return ['status' => $status];
    }

    public function continueShopping(Request $request)
    {
        # code...
        $specialInstructions = $request['specialinstructions'];

        $user = Auth::user();
        $request['userid'] = $user->id;

        $result = $this->getOrderByUserId($request);

        if($result['status'] ==  'success'){
            $order = $result['order'];
            $this->where('id', $order->id)->update(['specialinstructions' => $specialInstructions]);
        }
        return view('/placeanorder');
    }
}

<?php

namespace App;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use \Datetime;
use \DateInterval;
use \DateTimeZone;

/*
use App\QuickBooks;
use App\QbToken;
use App\PriceLevels;
use App\Orders;
use App\OrderLines;
use App\ItemsInOrders;
use App\PriceListHeader;
use App\PriceListLines;
use App\QbItemsIds;
use App\Searches;
use App\SearchesDates;
*/

class Inventory extends Model
{
    //
    public function SearchForApi(Request $request)
    {
        # code...
        $api_key = $request['api_key'];

        $Items = array();

        $ItemsForApi = array();

        $users = (new Users())->where('api_key', $api_key)->get();

        if(count($users) == 0){
            return $ItemsForApi;
        }

        $user = $users[0];

        // IF THE USER IS WORKING WITH PRICE LEVELS ...
        if($user->pricelevels_id != -1){
            // ... WE SEARCH THE LOCAL INVENTORY
            $basequery = "select * from inventories";
            $Items = DB::select($basequery);
            $priceLevels = (new PriceLevels())->getPriceLevel($user->pricelevels_id);

            if(count($priceLevels)){
                $priceLevel = $priceLevels[0];
                $factor = 1;
                if($priceLevel->type == 'increment'){
                    $factor = 1 + $priceLevel->percentage/100;
                }
                else{
                    $factor = 1 - $priceLevel->percentage/100;
                }
                foreach ($Items as $key => $Item) {
                    $Item->price *= $factor;

                    $imgpath = "";
                    if(strlen($Item->imgpath) > 0){
                        $imgpath = env('APP_URL') . "/public/" . $Item->imgpath;
                    }
                    array_push(
                        $ItemsForApi, 
                        (object) [
                            'Description' => $Item->name,
                            'Instock' => $Item->instock,
                            'Price' => $Item->price,
                            'Imgpath' => $imgpath,
                        ]
                    );
                }
            }
        }

        // IF THE USER IS WORKING WITH PRICE LISTS ...
        if($user->pricelist_id != -1){
            // ... WE SEARCH THE PRICE LIST
            $basequery = "select * from price_list_lines";

            $Items = DB::select($basequery . " where pricelistheaderid=" . $user->pricelist_id);

            foreach ($Items as $key => $Item) {
                # code...
                $LocalItems = $this->FindItemByLocalItemId($Item->localitemid);

                if(count($LocalItems) > 0){
                    $LocalItem = $LocalItems[0];

                    $imgpath = "";

                    if(strlen($LocalItem->imgpath) > 0){
                        $imgpath = env('APP_URL') . "/public/" . $LocalItem->imgpath;
                    }

                    array_push(
                        $ItemsForApi, 
                        (object)[
                            'Description' => $LocalItem->name,
                            'Instock' => $LocalItem->instock,
                            'Price' => $LocalItem->price,
                            'Imgpath' => $imgpath,
                        ]
                    );
                }
            }
        }

        return ($ItemsForApi);
    }

    public function searchInventory(Request $request)
    {
    	# code...
        $Description = $request['description'];
        $Keywords = explode(" ", $Description);

        $query = " where archive = 0 and ((description like '%";
        $first = true;
        foreach ($Keywords as $key => $Keyword) {
            # code...
            if($first){
                $first = false;
                $query = $query . $Keyword . "%')";
            }
            else{
                $query = $query . "or (description like '%" . $Keyword . "%')";
            }
        }
        foreach ($Keywords as $key => $Keyword) {
            # code...
            $query = $query . "or (name like '%" . $Keyword . "%')";
        }

        $query = $query . ")";

        $queryorder = " order by price";

        $user = Auth::user();
        $Items = array();

        // IF THE USER IS WORKING WITH PRICE LEVELS ...
        if($user->pricelevels_id != -1){
            // ... WE SEARCH THE LOCAL INVENTORY
            $basequery = "select * from inventories";
            $Items = DB::select($basequery . $query . $queryorder);
            $priceLevels = (new PriceLevels())->getPriceLevel($user->pricelevels_id);

            if(count($priceLevels)){
                $priceLevel = $priceLevels[0];
                $factor = 1;
                if($priceLevel->type == 'increment'){
                    $factor = 1 + $priceLevel->percentage/100;
                }
                else{
                    $factor = 1 - $priceLevel->percentage/100;
                }
                foreach ($Items as $key => $Item) {
                    $Item->price *= $factor;
                }
            }
        }

        // IF THE USER IS WORKING WITH PRICE LISTS ...
        if($user->pricelist_id != -1){
            // ... WE SEARCH THE PRICE LIST
            $basequery = "select * from price_list_lines";

            $Items = DB::select($basequery . $query . " and pricelistheaderid=" . $user->pricelist_id . $queryorder);

            foreach ($Items as $key => $Item) {
                # code...
                $LocalItems = $this->FindItemByLocalItemId($Item->localitemid);
                if(count($LocalItems) > 0){
                    $LocalItem = $LocalItems[0];
                    $Item->qbitemid = $LocalItem->qbitemid;
                    $Item->instock = $LocalItem->instock;
                    $Item->inorders = $LocalItem->inorders;
                    $Item->modified = $LocalItem->pricemodified;
                    $Item->imgpath = $LocalItem->imgpath;
                    $Item->inpurchaseorders = $LocalItem->inpurchaseorders;
                }
            }
        }

        try {
            DB::beginTransaction();
            $searchId = -1;
            // WE CHECK IF THERE WERE MATCHES
            if(count($Items) > 0){
                // IF WE FOUND SOMETHING THEN MATCH=TRUE
                $searchId = (new Searches())->AddNewSearch($Description, true);
            }
            else{
                $searchId = (new Searches())->AddNewSearch($Description, false);
            }

            $searchDate = date("Y-m-d H:i:s");

            (new SearchesDates())->AddNewDate($user->id, $searchId, $searchDate);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
        }

    	return $Items;
    }

    public function SearchFor($request)
    {
        # code...
        $Description = $request['description'];
        $Keywords = explode(" ", $Description);
        $status = "ok";

        $query = " where archive=0 and ((description like '%";
        $first = true;
        foreach ($Keywords as $key => $Keyword) {
            # code...
            if($first){
                $first = false;
                $query = $query . $Keyword . "%')";
            }
            else{
                $query = $query . "or (description like '%" . $Keyword . "%')";
            }
        }
        foreach ($Keywords as $key => $Keyword) {
            # code...
            $query = $query . "or (name like '%" . $Keyword . "%')";
        }

        $query = $query . ")";

        $queryorder = " order by name";

        $basequery = "select * from inventories";
        $Items = DB::select($basequery . $query . $queryorder);
        $q = $basequery . $query . $queryorder;
        return ['status' => $status, 'items' => $Items, 'query' => $q];
    }

    public function findItemByQbItemId($qbItemId)
    {
    	# code...
    	return $this->where('qbitemid', $qbItemId)->get();
    }

    public function SearchItemById($request)
    {
        # code...
        $id = $request['id'];
        return $this->FindItemByLocalItemId($id);
    }

    public function FindItemByLocalItemId($localitemid)
    {
        # code...
        return $this->where('id', $localitemid)->get();
    }

    public function SyncronizeInventories(Request $request)
    {
        $result = (new QbToken())->GetDataService();

        $dataService = $result['dataService'];
        $dataService->throwExceptionOnError(true);


        if(is_null($dataService))
        {
            $authUrl = $result['authUrl'];
            session(['qbapi' => 'InventorySummary']);
            return ['authUrl' => $authUrl];
        }

        try {
            $Count = $dataService->query("SELECT COUNT(*) FROM Item");
        } catch (\SdkException $e) {
            return ['status' => 'fail', 'message' => $e];
        }

        // $Count1 IS SET TO 100 BECAUSE THAT IS THE
        // TOP RESULTS THAT QUICKBOOKS WILL GIVE US
        $Count1 = $Count/100;

        // ROUNDED COUNT
        $Fcount = floor($Count1);

        // REMAINING COUNT AFTER ROUNDING
        $Rest = ($Count1 - $Fcount);
        if($Rest > 0){
            $Fcount += 1;
        }

        DB::beginTransaction();

        $update = 0;
        (new Inventory())->where('id', '>', -1)->update(['update' => $update]);

        // ADD OR UPDATE QUICKBOOKS ITEMS TO LOCAL INVENTORY
        for($i = 0; $i < $Fcount; $i++){
            try {
                $QbInventory = $dataService->query("SELECT * FROM Item STARTPOSITION " . $i*100 . "  MAXRESULTS 100");
            } catch (\SdkException $e) {
                DB::rollback();
                return ['status' => 'fail', 'message' => $e];                
            }
            try {
                $this->Sync($QbInventory, $update);
            } catch (\QueryException $e) {
                return ['status' => 'fail', 'message' => $e];                
            }
        }
        

        // REMOVE ITEMS FROM LOCAL INVENTORY THAT ARE NOT ANY MORE IN QUICKBOOKS
        $LocalInventory = $this->where('update', '<', $update + 1)->update(['archive' => true]);

        // ADD TO THE STOCK THE PRODUCTS IN PURCHASE ORDERS
        try {
            $QbPurchaseOrders = $dataService->query("SELECT * FROM PurchaseOrder");
        } catch (\SdkException $e) {
            DB::rollback();
            return ['status' => 'fail', 'message' => $e];                
        }

        foreach ($QbPurchaseOrders as $key => $QbPurchaseOrder) {
            # IF THE ORDER IS OPEN ...
            if($QbPurchaseOrder->POStatus == 'Open'){
                # ... LET'S PROCCESS THE ORDER LINES
                $QbPurchaseOrderLines = $QbPurchaseOrder->Line;
                if(gettype($QbPurchaseOrderLines) == "array"){
                    foreach ($QbPurchaseOrderLines as $key1 => $QbPurchaseOrderLine) {
                        $this->ProccessQbPoLine($QbPurchaseOrderLine);                        
                    }
                }
                else{
                    $QbPurchaseOrderLine = $QbPurchaseOrder->Line;
                    $this->ProccessQbPoLine($QbPurchaseOrderLine);                        
                }
            }
        }

        $PriceListHeaders = (new PriceListHeader())->where('id', '>', -1)->get();
        
        $LocalInventory = $this->where('id', '>', -1)->where('archive', 0)->get();

        // LET'S UPDATE THE PRICE LISTS
        $update = 0;

        (new PriceListLines())->where('id', '>', -1)->update(['update' => 0]);

        foreach ($LocalInventory as $key => $Item) {
            # code...
            // CHECK IF THIS INVENTORY ITEM IS ALREADY IN THE PRICE LIST
            $PriceListLines = new PriceListLines();
            $ItemsInLists = $PriceListLines->GetItemPriceByItemId($Item->id);

            if(count($ItemsInLists) > 0){
                // THE ITEM IS IN THIS LISTS, LET'S UPDATE DESCRIPTIONS
                $PriceListLines->where('localitemid', $Item->id)->update(['description' => $Item->description, 'name' => $Item->name, 'update' => 1]);
            }
            else{
                // THIS ITEM ISN'T IN THE LIST, LET'S ADD IT
                foreach ($PriceListHeaders as $key => $PriceListHeader) {
                    # code...
                    $PriceListLines->pricelistheaderid = $PriceListHeader->id;
                    $PriceListLines->localitemid = $Item->id;
                    $PriceListLines->qbitemid = $Item->qbitemid;
                    $PriceListLines->price = $Item->price;
                    if($Item->description === null) {
                        $PriceListLines->description = "";
                    } else {
                        $PriceListLines->description = $Item->description;
                    }
                    if($Item->name === null){
                        $PriceListLines->name = "";
                    }
                    else{
                        $PriceListLines->name = $Item->name;
                    }
                    $PriceListLines->update = 1;
                    $PriceListLines->save();
                }
            }
        }

        // LET'S REMOVE FROM THE LIST THE PRODUCTS NOT PRESENT IN QUICKBOOKS
        (new PriceListLines())->where('update', 0)->delete();

        DB::commit();
        // RETURN THE UPDATED LOCAL INVENTORY  
        return ['status' => 'ok', 'LocalInventory' => $LocalInventory];
    }

    public function ProccessQbPoLine($QbPurchaseOrderLine)
    {
        # LET'S SEE IF THE ITEM HAS BEEN SEEN BEFORE
        try {
            $QbItemId = $QbPurchaseOrderLine->ItemBasedExpenseLineDetail->ItemRef;
            $Qty = $QbPurchaseOrderLine->ItemBasedExpenseLineDetail->Qty;
        } catch (\Exception $e) {
        }
        $QbItems = $this->where('qbitemid', $QbItemId)->get();
        if(count($QbItems) > 0){
            // IF IT IS IN THE LOCAL INVENTORY ADD THE QTY IN ORDERS
            $QbItem = $QbItems[0];
            $QtyInOrders = $QbItem->inpurchaseorders + $Qty;
            $this->where('id', $QbItem->id)->update(['inpurchaseorders' => $QtyInOrders]);
        }
    }

    public function Sync($QbInventory, $update)
    {
        foreach ($QbInventory as $key => $qbItem) {

            // SEARCH THE QB INVENTORY
            if($qbItem->Type == 'Inventory'){

                $localItems = $this->where('qbitemid', $qbItem->Id)->get();
                // IF THE QBITEM IS NOT IN THE LOCAL
                // INVENTORY THEN  WHE WILL CREATE IT
                if(count($localItems) == 0){
                    $Inventory = new Inventory();
                    $Inventory->qbitemid = $qbItem->Id;
                    if($qbItem->Description === null){
                        $Inventory->description = "";
                    }
                    else{
                        $Inventory->description = $qbItem->Description;
                    }
                    if($qbItem->Name === null){
                       $Inventory->name = "";
                    }
                    else{
                       $Inventory->name = $qbItem->Name;
                    }
                    $Inventory->instock = $qbItem->QtyOnHand;
                    $Inventory->inorders = 0;
                    $Inventory->price = $qbItem->UnitPrice;
                    $Inventory->pricemodified = false;
                    $Inventory->inpurchaseorders = 0;
                    $Inventory->update = $update + 1;
                    $Inventory->archive = false;
                    $Inventory->save();
                }
                // IF IT IS ALREADY IN THE LOCAL INVENTORY
                // THEN LET'S UPDATE SOME NEEDED FIELDS
                else{
                    $localItem = $localItems[0];
                    if($qbItem->Description === null){
                        $localItem->description = "";
                    }
                    else{
                        $localItem->description = $qbItem->Description;
                    }
                    if($qbItem->Name === null){
                        $localItem->name = "";
                    }
                    else{
                        $localItem->name = $qbItem->Name;
                    }
                    $localItem->instock = $qbItem->QtyOnHand;

                    if($localItem->price != $qbItem->UnitPrice){
                        $localItem->price = $qbItem->UnitPrice;
                        $localItem->pricemodified = false;
                    }                    

                    $localItem->inpurchaseorders = 0;
                    $localItem->update = $update + 1;
                    $localItem->archive = false;
                    $localItem->update();
                }
            }
        }
    }

    public function GetInventory(Request $request)
    {
        return $this->where('id', '>', -1)->where('archive', 0)->orderBy('name')->get();
    }

    public function Inventory(Request $request)
    {
        $date = getdate();
        $stamp = $date['mon'] . '/' . $date['mday'] . '/' . $date['year'] . ' - ' . $date['hours'] . ':' . $date['seconds'];
        $authUser = Auth::user();
        Storage::disk('local')->append('inventory.txt', 'User ' . $authUser->name . ' logged in /inventory on ' . $stamp);

        $Inventory = $this->GetInventory($request);
        foreach ($Inventory as $key => $item) {
            $ItemsInOrder = 0;
            $OrderLines = (new OrderLines())->where('item_qbid', $item->qbitemid)->get();
            foreach($OrderLines as $key => $OrderLine){
                $ItemsInOrder += $OrderLine->qty;
            }
            $item->inorders = $ItemsInOrder;
        }
        return view('inventory', ['Inventory' => $Inventory]);
    }

    /**
    *
    * @param    id, price
    *
    **/

    public function UpdateItem(Request $request)
    {
        $id = $request['id'];
        $price = $request['price'];
        $message = "THE ITEM COULDN'T BE UPDATED";
        if($this->where('id', $id)->update(['price' => $price, 'pricemodified' => true]) > 0){
            $message = "THE ITEM WAS SUCCESSFULLY UPDATED";
        }
        return ['message' => $message];
    }

    public function UpdateOferta(Request $request)
    {
        $id = $request['id'];
        $oferta = $request['oferta'];
        $message = "THE ITEM COULDN'T BE UPDATED";
        if($this->where('id', $id)->update(['oferta' => $oferta]) > 0){
            $message = "THE ITEM WAS SUCCESSFULLY UPDATED";
        }
        return ['message' => $message];
    }

    public function PriceLists(Request $request)
    {
        # code...
        $PriceListHeader = (new PriceListHeader);
        $PriceListLines = (new PriceListLines);

        $PriceListsArray = array();
        $PriceListHeaders = $PriceListHeader->where('id', '>', -1)->get();
        foreach ($PriceListHeaders as $key => $Header) {
            # code...
            $ThisPriceListLines = $PriceListLines->where('pricelistheaderid', $Header->id);
            $PriceList->Header = $Header;
            $PriceList->Lines = $ThisPriceListLines;
            array_push($PriceListsArray, $PriceList);
        }
        return view('pricelists', $PriceList);
    }

    public function DateTimeOffset(DateTime $serverdate)
    {
        # code...
        // SET A VARIABLE FOR CLIENT DATE

        $clientdate = new DateTime;

        // GET THE OFFSET IN SECONDS BETWEEN THE SERVER
        // DATE AND THE CLIENT TIME ZONE DATE
        $dateTimeZone = new DateTimeZone(env("ADMIN_TIMEZONE"));
        $offset = $dateTimeZone->getOffset($serverdate);
        // MAKE THE OFFSET VALUE ALWAYS POSITIVE FOR DateInterval 
        $absoffset = abs($offset);

        // CALCULATE THE INTERVAL
        $interval = new DateInterval("PT{$absoffset}S");

        // CHECK IF THE INTERVAL MUST BE ADDED
        // OR SUBSTRACTED FROM THE SERVER DATE
        if($offset < 0){
            $clientdate = date_sub($serverdate, $interval);
        }
        else{
            $clientdate = date_sub($serverdate, $interval);
        }

        // RETURN CLIENT DATE
        return ['serverdate' => $serverdate, 'clientdate' => $clientdate];
    }

    public function SearchPublicInventory($request){
        $Description = $request['description'];
        $Keywords = explode(" ", $Description);

        $query = " where archive = 0 and instock > 0 and ((description like '%";
        $first = true;
        foreach ($Keywords as $key => $Keyword) {
            # code...
            if($first){
                $first = false;
                $query = $query . $Keyword . "%')";
            }
            else{
                $query = $query . "or (description like '%" . $Keyword . "%')";
            }
        }
        foreach ($Keywords as $key => $Keyword) {
            # code...
            $query = $query . "or (name like '%" . $Keyword . "%')";
        }

        $query = $query . ")";

        $queryorder = " order by instock desc";

        // ... WE SEARCH THE LOCAL INVENTORY
        $basequery = "select name, imgpath from inventories";
        $Items = DB::select($basequery . $query . $queryorder);
        for($i = 0; $i < count($Items); $i++){
            if(strlen($Items[$i]->imgpath) == 0){
                $Items[$i]->imgpath = "img/noimg.jpg";
            }
        }
        return $Items;
    }
}

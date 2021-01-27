<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use App\PriceListLines;
use App\Inventory;
use App\Users;

class PriceListHeader extends Model
{
	//
	public function ModifyPrices($request)
	{
		$priceListId = $request["pricelistid"];
		$percentage = $request["percentage"];
		$upDown = $request["updown"];
		return['status' => 'OK', 'pricelistid' => $priceListId, 'percentage' => $percentage, 'updown' => $upDown];
	}

    public function GetPriceListsHeaders($request)
    {
        # code...
        try {
            $priceListHeaders = $this->where('id', '>', -2)->get();
            return ['status' => 'ok', 'priceListHeaders' => $priceListHeaders];
        } catch (\Exception $e) {
            return ['status' => 'fail'];
        }
    }

    public function PriceLists($request)
    {
    	# code...

        $date = getdate();
        $stamp = $date['mon'] . '/' . $date['mday'] . '/' . $date['year'] . ' - ' . $date['hours'] . ':' . $date['seconds'];
        $authUser = Auth::user();
        Storage::disk('local')->append('pricelists.txt', 'User ' . $authUser->name . ' logged in /pricelists on ' . $stamp);


    	$priceListsHeaders = $this->where('id', '>', -1)->get();
    	if(count($priceListsHeaders) > 0){
    		$priceListsHeader = $priceListsHeaders[0];
    		$priceListLines = (new PriceListLines())->where('pricelistheaderid', $priceListsHeader->id)->get();
    		foreach ($priceListLines as $key => $priceListLine) {
    			# code...
    			$items = (new Inventory())->where('id', $priceListLine->localitemid)->get();
    			if(count($items) > 0){
    				$item = $items[0];
    				$priceListLine->description = $item->name;
    			}
    			else{
    				$priceListLine->description = 'ITEM NOT FOUND';
    			}
    		}
    		$priceListsHeader->lines = $priceListLines;
    	}

    	return view('pricelists', ['pricelists' => $priceListsHeaders]);
    }

    public function CreateNewList($request)
    {
    	# code...
    	$listDescription = $request['listDescription'];

    	$this->description = $listDescription;
    	try {
    		$this->save();
    	} catch (\Exception $e) {
    		return['status' => 'fail', 'message' => 'FAILED TO CREATE THIS LIST HEADER', 'System message' => $e];
    	}

    	$items = (new Inventory())->where('id', '>', -1)->get();

    	foreach ($items as $key => $item) {
    		# code...
            try {
        		$priceListLines = (new PriceListLines());
        		$priceListLines->pricelistheaderid = $this->id;
        		$priceListLines->localitemid = $item->id;
        		$priceListLines->qbitemid = $item->qbitemid;
        		$priceListLines->price = $item->price;

                if($item->description === null){
                    $priceListLines->description = "";
                }
                else{
                    $priceListLines->description = $item->description;
                }
                if($item->name === null){
                    $priceListLines->name = "";   
                }
                else{
                    $priceListLines->name = $item->name;
                }

	    		$priceListLines->save();
    		} catch (\Exception $e) {
                (new PriceListLines())->where('pricelistheaderid', $this->id)->delete();
                (new PriceListHeader())->where('id', $this->id)->delete();
	    		return['status' => 'fail', 'message' => 'FAILED TO CREATE A LIST LINE', 'System message' => $e];
    		}
    	}
    	$priceListLines = (new PriceListLines())->where('pricelistheaderid', $this->id)->get();
    	foreach ($priceListLines as $key => $priceListLine) {
    		# code...
    		$items = (new Inventory())->where('id', $priceListLine->localitemid)->get();
    		if(count($items)){
    			$priceListLine->description = $items[0]->name;
    		}
    		else{
    			$priceListLine->description = 'ITEM NOT FOUND';
    		}
    	}

    	return ['status' => 'ok', 'pricelistid' => $this->id, 'pricelistlines' => $priceListLines];
    }

    public function PriceListById($request)
    {
    	# code...
    	$id = $request['id'];
    	try {
	    	$priceLists = $this->where('id', $id)->get();

	    	if(count($priceLists) > 0){
	    		$priceList = $priceLists[0];
	    		$lines = (new PriceListLines())->where('pricelistheaderid', $id)->get();

	    		foreach ($lines as $key => $line) {
	    			# code...
	    			$items = (new Inventory())->where('id', $line->localitemid)->get();

	    			if(count($items) > 0){
	    				$item = $items[0];
	    				$line->description = $item->name;
	    			}
	    		}
	    		$priceList->lines = $lines;
		    	return ['status' => 'ok', 'pricelist' => $priceList];
	    	}
	    	else{
	    		return['status' => 'fail', 'message' => 'LIST NOT FOUND'];
	    	}
    	} catch (\Exception $e) {
			return ['status' => 'fail', 'message' => $e];    		
    	}
    }

    public function DeleteListById($request)
    {
    	# code...
    	$id = $request['id'];
    	try {
    		(new PriceListLines())->where('pricelistheaderid', $id)->delete();
    		$this->where('id', $id)->delete();
            (new Users())->where('pricelist_id', $id)->update(['pricelist_id' => -1]);
    	} catch (\Exception $e) {
    		return ['status' => 'fail'];	
    	}
    	return ['status' => 'ok'];
    }

    public function ListQty($request)
    {
        # code...
        try {
            $listqty = count($this->where('id', '>', -2)->get());
            return ['status' => 'ok', 'listqty' => $listqty];
        } catch (\Exception $e) {
            return ['status' => 'fail'];
        }
    }

    public function GetPriceList($id)
    {
        # code...
        try {
            $PriceList = $this->where('id', $id)->get();
            if(count($PriceList) > 0){
                return $PriceList[0];
            }
            else{
                return null;
            }
        } catch (\Exception $e) {
            return null;
        }
    }

    public function GetPriceLists()
    {
        # code...
        try {
            $PriceLists = $this->where('id', '>', -1)->get();
            return $PriceLists;
        } catch (\Exception $e) {
            return null;
        }
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\PriceListsLines;
use App\Inventory;

class PriceListsHeader extends Model
{
    //
    public function PriceLists($request)
    {
    	# code...
    	$priceListsHeaders = $this->where('id', '>', -1)->get();

    	$priceLists = array();

    	foreach ($priceListsHeaders as $key => $priceListsHeader) {
    		# code...
    		$priceList->headerid = $priceListsHeader->id;
    		$priceList->description = $priceListsHeader->description;
    		$priceListsLines = (new PriceListsLines())->where('pricelistsheaderid', $priceListsHeader->id)->get();
    		foreach ($priceListsLines as $key => $priceListsLine) {
    			# code...
    			$items = (new Inventory())->where('id', $priceListsLine->localitemid)->get();
    			if(count($items) > 0){
    				$item = $items[0];
    				$priceListsLine->itemdescription = $item->name;
    			}
    		}
    		$priceList->lines = $priceListsLines;
    		array_push($priceLists, $priceList);
    	}

    	return view('pricelists', $priceLists);
    }

    public function CreateNewList($request)
    {
    	# code...
    	$listDescripton = $request['listDescripton'];

    	$this->description = $listDescripton;
    	try {
    		$this->save();
    	} catch (\Exception $e) {
    		return['status' => 'fail', 'message' => 'FAILED TO CREATE THIS LIST HEADER', 'System message' => $e];
    	}

    	$items = (new Inventory())->where('id', '>', -1)->get();

    	foreach ($items as $key => $item) {
    		# code...
    		$priceListLines = (new PriceListsLines());
    		$priceListLines->pricelistheaderid = $this->id;
    		$priceListLines->localitemid = $item->id;
    		$priceListsLines->qbitemid = $item->qbitemid;
    		$priceListsLines->price = $item->price;
    		try {
	    		$priceListLines->save();
    		} catch (\Exception $e) {
	    		return['status' => 'fail', 'message' => 'FAILED TO CREATE A LIST LINE', 'System message' => $e];
    		}
    	}
    	return ['status' => 'ok'];
    }
}

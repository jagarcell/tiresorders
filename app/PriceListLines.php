<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PriceListLines extends Model
{
    //
    public function UpdatePrices($request)
    {
    	# code...
    	$prices = $request['prices'];
    	$status = 'ok';
    	foreach ($prices as $id => $price) {
    		# code...
    		try {
	    		$this->where('id', $id)->update(['price' => $price, 'modified' => 1]);
    		} catch (\Exception $e) {
    			$status = 'fail';	
    		}
    	}
    	return ['status' => $status];
    }

    public function GetItemPriceByListIdAndItemId($listId, $itemId)
    {
        # code...
        $items = $this->where('pricelistheaderid', $listId)->where('localitemid', $itemId)->get();
        return $items;
    }

    public function GetItemPriceByItemId($itemId)
    {
        # code...
        $items = $this->where('localitemid', $itemId)->get();
        return $items;
    }

    public function GetListLinesByHeaderId($pricelistheaderid)
    {
        # code...
        return $this->where('pricelistheaderid', $pricelistheaderid)->orderBy('price')->get();
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

    public function SearchInList($request)
    {
        # code...
        $pricelistheaderid = $request["pricelistheaderid"];
        $searchtext = $request["searchtext"];
        $Keywords = explode(" ", $searchtext);
        $status = "ok";

        $query = " ((description like '%";
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

        $basequery = "select * from price_list_lines where pricelistheaderid='$pricelistheaderid' and ";
        $q = $basequery . $query . $queryorder;

        $Items = DB::select($basequery . $query . $queryorder);
        return ['status' => $status, 'items' => $Items, 'query' => $q];
    }
}

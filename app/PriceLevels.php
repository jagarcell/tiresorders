<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class PriceLevels extends Model
{
    //
    public function listPriceLevels(Request $request)
    {
        # code...
        $priceLevels = $this->where('id', '>', -1)->get();
        return view('pricelevels', ['pricelevels' => $priceLevels]);
    }

    public function savePriceLevel(Request $request)
    {
    	# code...
    	$id = $request["id"];
    	$description = $request["description"];
    	$percentage = $request["percentage"];
    	$type = $request["type"];
    	$rowindex = $request['rowindex'];
    	$result = [];
    	if(!is_null($id) && $id > -1){
    		// THIS IS AN UPDATE FOR AN EXISTING RECORD
    		try {
	    		$this->where('id', $id)->update(
	    			[
	    				'description' => $description,
	    				'percentage' => $percentage,
	    				'type' => $type,
	    			]
	    		);
	    		$result = ['message' => 'UPDATED SUCCESFULLY', 'rowindex' => $rowindex];
    		} catch (\Exception $e) {
    			$result = ['message' => 'ERROR UPDATING', 'rowindex' => $rowindex];
    		}
    	}
    	else{
    		// THIS IS A NEW RECORD TO BE ADDED
    		$this->description = $description;
    		$this->percentage = $percentage;
    		$this->type = $type;
    		try {
    			$this->save();
    			$result = ['message' => 'SAVED SUCCESFULLY', 'rowindex' => $rowindex, 'id' => $this->id];
    		} catch (\Exception $e) {
    			$result = ['message' => 'ERROR SAVING', 'rowindex' => $rowindex];
    		}
    	}
    	return $result;
    }

    public function getPriceLevels(Request $request)
    {
        # code...
        return $this->where('id', '>', -1)->get();
    }

    public function getPriceLevel($id)
    {
        # code...
        return $this->where('id', $id)->get();
    }
}

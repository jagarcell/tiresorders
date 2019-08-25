<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Searches extends Model
{
    //
    public function AddNewSearch($searchText, $match)
    {
    	# code...
    	$searches = $this->where('searchtext', $searchText)->get();
    	$searchId = -1;

    	// CHECK IF THIS HAS BEEN ALREADY SEARCHED
    	if(count($searches) > 0){
    		// IF THIS SERCH WAS DONE BEFORE ADD A COUNT
    		$search = $searches[0];
    		if($match){
	    		$this->where('id', $search->id)->update(['matchqty' => $search->matchqty + 1]);
    		}
    		else{
	    		$this->where('id', $search->id)->update(['nomatchqty' => $search->nomatchqty + 1]);
    		}
    		$searchId = $search->id;
    	}
    	else{
    		// IF IT IS THE FIRST TIME FOR THIS SEARCH LET'S REGISTER IT 
    		$this->searchtext = $searchText;
    		if($match){
    			// IF IT IS A MATCH ADD MATCHES COUNT
    			$this->matchqty = 1;
    			$this->nomatchqty = 0;
    		}
    		else{
    			// NOT A MACTH, ADD NOMATCH COUNT
    			$this->matchqty = 0;
    			$this->nomatchqty = 1;
    		}
    		$this->save();
    		$searchId = $this->id;
    	}
    	return $searchId;
    }

    public function ShowSearches($request)
    {
    	# code...
        $searches = $this->where('id', '>', -1)->orderBy('nomatchqty', 'DESC')->get();
    	return view('searches', ['searches' => $searches]);
    }

    public function DeleteSearches($request)
    {
        # code...
        $status = 'ok';
        $searchIds = $request["searchIds"];
        $deletedIds = array();

        foreach ($searchIds as $key => $searchId) {
            # code...
            DB::beginTransaction();

            try {
                $this->where('id', $searchId)->delete();
                (new SearchesDates())->where('searchid', $searchId)->delete();
                DB::commit();
                array_push($deletedIds, $searchId);
            } catch (\Exception $e) {
                DB::rollback();
                $status = 'fail';
            }
        }
        return ['status' => $status, 'deletedids' => $deletedIds];
    }
}

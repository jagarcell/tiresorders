<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Users;
use App\Searches;
use \DateTime;

class SearchesDates extends Model
{
    //
    public function AddNewDate($clientId, $searchId, $searchDate)
    {
    	# code...
    	$this->clientid = $clientId;
    	$this->searchid = $searchId;
    	$this->searchdate = $searchDate;
    	$this->save();
    }

    public function SearchDetails($request)
    {
    	# code...
    	$searchId = $request["searchid"];
    	try {
            $searchText = "";
            $searches = (new Searches())->where('id', $searchId)->get();
            if(count($searches) > 0){
                $search = $searches[0];
                $searchText = $search->searchtext;
            }
	    	$searchDetails = $this->where('searchid', $searchId)->orderBy('searchdate', 'DESC')->get();
            foreach($searchDetails as $key => $searchDetail){
                $users = (new Users())->where('id', $searchDetail->clientid)->get();
                if(count($users)){
                    $user = $users[0];
                    $searchDetail->user = $user->name;
                }
                else{
                    $searchDetail->user = "USER NOT FOUND";
                }

                $date = new DateTime($searchDetail->searchdate);

                $clientdate = (new Inventory())->DateTimeOffset($date)['clientdate'];
                $searchDetail->searchdate = $clientdate->format("m/d/Y H:i:s");
            }

	    	return ['status' => 'ok', 'searches' => $searchDetails, 'searchtext' => $searchText];
    	} catch (\Exception $e) {
    		return ['status' => 'fail', 'message' => $e->getMessage()];
    	}	
    }
}

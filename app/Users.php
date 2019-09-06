<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use App\QuickBooks;
use App\PriceLevels;
use App\User;
use App\Orders;
use App\OrderLines;
use App\QbCustomers;
use App\PriceListHeader;

class Users extends Model
{
    //
    public function listUsers(Request $request)
    {
    	# code...

        $date = getdate();
        $stamp = $date['mon'] . '/' . $date['mday'] . '/' . $date['year'] . ' - ' . $date['hours'] . ':' . $date['seconds'];
        $authUser = Auth::user();
        Storage::disk('local')->append('listusers.txt', 'User ' . $authUser->name . ' logged in /listusers on ' . $stamp);

        $authUser = Auth::user();

        $data = array('users' => $users, 'user' => $authUser);

        $priceLevels = (new PriceLevels())->getPriceLevels($request);
        if(!is_null($priceLevels)){
            $data += array('priceLevels' => $priceLevels);
        }

        $qbCustomers = (new QuickBooks())->ListUsersQbCustomers($request);
        if(!is_null($qbCustomers)){
            if(isset($qbCustomers['status'])){
                return redirect($qbCustomers['authUrl']);
            }
            $data += array('qbCustomers' => $qbCustomers);
        }

        $priceLists = (new PriceListHeader())->GetPriceLists();
        if(!is_null($priceLists)){ 
            $data += array('priceLists' => $priceLists);
        }


        $this->syncQbCustomers($qbCustomers);

        foreach ($users as $key => $user) {
        	# code...
        	try {
                $CustomerById = (new QbCustomers)->Customer($user->qb_customer_id);

                $PriceLevel = (new PriceLevels())->getPriceLevel($user->pricelevels_id);

                $PriceList = (new PriceListHeader())->GetPriceList($user->pricelist_id);
                
        		if(count($CustomerById) == 0){
                    $user->qbuser = $user->name;
        		}
        		else{
                    $Customer = $CustomerById[0];
        			$user->qbuser = $Customer->DisplayName;
        		}
                if($PriceLevel == 'null'){
                    $user->pricelevel = '';
                }
                else
                {
                    try {
                        $user->pricelevel = (json_decode($PriceLevel)[0])->description;
                        $user->level = '';
                    } catch (\Exception $e) {
                        $user->pricelevel = '';                        
                    }
                }

                if($PriceList == null){
                    $user->pricelist = '';
                }
                else{
                    $user->pricelist = $PriceList->description;
                    $user->list = '';
                }

                if ($user->pricelist_id == -1 && $user->pricelevels_id == -1) {
                    # code...
                    $user->nopricelevel = '';
                }

        	} catch (\Exception $e) {
				$user->qbuser = 'NOT FOUND';
        	}
        }
		return view('listusers', $data);
    }

    public function syncQbCustomers($qbCustomers)
    {
        # code...
        $QbCustomers = (new QbCustomers());
        $QbCustomers->CustomersSync($qbCustomers);
    }

    public function findUsersByType(Request $request)
    {
        # code...
        /*
        * @param type
        */
        $type = $request['type'];
        $users = $this->where('type', $type)->get();
        return $users;
    }

    public function findUserById(Request $request)
    {
        # code...
        $userId = $request['userid'];
        return $this->where('id', $userId)->get();
    }

    /**
    * @param userId
    **/

    public function userEdit(Request $request)
    {
        # code...
        $userId = $request['userId'];
        $user = $this->where('id', $userId)->get();
        return $user;
    }

    public function userEditSave(Request $request)
    {
        # code...
        $userId = $request['userId'];
        $email = $request['email'];
        $name = $request['name'];
        $address = $request['address'];
        $phone = $request['phone'];
        $pricelevels_id = $request['pricelevels_id'];
        $qb_customer_id = $request['qb_customer_id'];
        $pricelist_id = $request['pricelist_id'];

        $existingEmail = (new Users())->where('email', $email)->get();
        if(count($existingEmail) > 0){
            if($existingEmail[0]->qb_customer_id != $qb_customer_id){
                return (['usersAdded' => 0, 'message' => 'THIS EMAIL HAS ALREADY BEEN USED']);
            }
        }

        try {
            $usersAdded = $this->where('id', $userId)->update(
                [
                    'name' => $name,
                    'address' => $address,
//                    'email' => $email,
                    'phone' => $phone,
                    'pricelevels_id' => $pricelevels_id,
                    'qb_customer_id' => $qb_customer_id,
                    'pricelist_id' => $pricelist_id,
                ]
            );
        } catch (\Exception $e) {
            dd($e);
            return ['usersAdded' => 0, 'message' => 'FAILED TO SAVE THE CHANGES'];
        }
        return ['usersAdded' => $usersAdded, 'message' => 'USER UPDATED SUCCESSFULLY'];
    }

    public function resendVerify(Request $request)
    {
        # code...
        $userId = $request['userId'];
        $users = (new User())->where('id', $userId)->get();
        if(count($users) > 0){
            $user = $users[0];
            $date = getdate();
            $stamp = $date['mon'] . '/' . $date['mday'] . '/' . $date['year'] . ' - ' . $date['hours'] . ':' . $date['seconds'];
            for($i = 3; $i > -1; $i--){
                try {
                    $user->sendEmailVerificationNotification();
                    Storage::disk('local')->append('usercreation.txt', 'Verify Email ReSent To: ' . $user->email . ' on ' . $stamp);
                    break;
                } catch (\Exception $e) {
                    if($i == 0){
                        Storage::disk('local')->append('usercreation.txt', 'Verify Email ReSend To: ' . $user->email . ' Failed Last Try on ' . $stamp);
                        return ['status' => 'fail', 'user' => $user];            
                    }
                    else{
                        Storage::disk('local')->append('usercreation.txt', 'Verify Email ReSend To: ' . $user->email . ' Failed on ' . $stamp . ' Will Retry');
                    }
                }
            }
        }
        return ['status' => 'ok', 'user' => $users[0]];
    }

    public function deleteUser(Request $request)
    {
        # code...
        $userId = $request['userId'];
        try {
            $this->where('id', $userId)->delete();
        } catch (\Exception $e) {
            return ['status' => 'fail', 'message' => $e];
        }
        $orders = (new Orders())->where('user_id', $userId)->get();
        foreach ($orders as $key => $order) {
            # code...
            (new OrderLines())->where('order_id', $order->id)->delete();
        }
        (new Orders())->where('user_id', $userId)->delete();
        return ['status' => 'ok'];
    }

    public function FindUsersByPriceList($request)
    {
        # code...
        $pricelistid = $request['pricelistid'];
        try {
            $users = $this->where('pricelist_id', $pricelistid)->get();
            return ['status' => 'ok', 'users' => $users];
        } catch (\Exception $e) {
            return ['status' => 'fail', 'message' => $e];
        }
    }
}

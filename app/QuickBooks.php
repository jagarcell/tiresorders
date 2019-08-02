<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use QuickBooksOnline\API\Facades\Item;

use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\Exception\SdkException;

use App\Inventory;
use App\PriceLevels;
use App\Users;
use App\QbToken;

class QuickBooks extends Model
{
    //
    public function QbCallback(Request $request)
    {
    	/**
    	* @param  $request['state']
    	*		  $request['code'] 	
    	*		  $request['realmId']
    	* @return View 
    	*/ 
        $qbConfig = config('qbConfig');

        $state = $request['state'];
        $code = $request['code'];
        $realmId = $request['realmId'];

        try {
            $dataService = DataService::Configure($qbConfig);
            $OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();
            $accessToken = $OAuth2LoginHelper->exchangeAuthorizationCodeForToken($code, $realmId);
            $dataService->updateOAuth2Token($accessToken);
        } catch (\ServiceException $e) {
            return redirect('/qbconnecterror');            
        }

        $QbTokens = (new QbToken());
        $QbToken = $QbTokens->where('id', '>', -1)->get();

        if(!is_null($QbToken)){
            $authUser = Auth::user();
            if(count($QbToken) > 0){
                $QbTokenId = $QbToken[0]->id;
                $QbTokens->where('id', $QbTokenId)->update(
                    [
                        'email' => $authUser->email,
                        'accesstoken' => $accessToken->getAccessToken(), 
                        'refreshtoken' => $accessToken->getRefreshToken(),
                        'state' => $state,
                        'code' => $code,
                        'realmId' =>$realmId,
                    ]
                );
            }
            else{
                $QbTokens->email = $authUser->email;
                $QbTokens->accesstoken = $accessToken->getAccessToken();
                $QbTokens->refreshtoken = $accessToken->getRefreshToken();
                $QbTokens->state = $state;
                $QbTokens->code = $code;
                $QbTokens->realmId = $realmId;
                $QbTokens->save();
            }
            try {
                        $companyInfo = json_decode($this->companyInfo(), true);
                        $companyName = $companyInfo['CompanyName'];
            } catch (\Exception $e) {
                dd($e);
            }
        }

        if(session()->has('qbapi')){
            $qbapi = session('qbapi');
            session()->forget('qbapi');
            switch ($qbapi) {
                case 'companyInfo':
                    # code...
                    try {
                        $companyInfo = json_decode($this->companyInfo(), true);
                        $companyName = $companyInfo['CompanyName'];
                        session('companyName', $companyName);
                        return redirect('/');
                        return view('welcome', ['companyName' => $companyName,]);
                    } catch (\SdkException $e) {
                        redirect('/qbconnecterror');                        
                    }
                    break;
                case 'InventorySummary':
                    $companyInfo = json_decode($this->companyInfo(), true);
                    $companyName = $companyInfo['CompanyName'];
                    session('companyName', $companyName);
                    $InventorySummary = json_decode($this->InventorySummary($request), true);
                    return view('inventorysummary', ['companyName' => $companyName, 'InventorySummary' => $InventorySummary]);
                    break;
                case 'placeanorder':
                    $companyInfo = json_decode($this->companyInfo(), true);
                    $companyName = $companyInfo['CompanyName'];
                    session('companyName', $companyName);
                    $InventorySummary = json_decode($this->InventorySummary($request), true);
                    return redirect('/placeanorder');
                    break;
                case 'Invoice':
                    $companyInfo = json_decode($this->companyInfo(), true);
                    $companyName = $companyInfo['CompanyName'];
                    session('companyName', $companyName);
                    $Invoice = json_decode($this->Invoice($request), true);
                    return view('invoice', ['companyName' => $companyName, 'Invoice' => $Invoice]);
                    break;
                case 'CustomersRegister':
                    $companyInfo = json_decode($this->companyInfo(), true);
                    $companyName = $companyInfo['CompanyName'];
                    session('companyName', $companyName);
                    $Customers = json_decode($this->customersregister($request), true);
                    return redirect('/');
                    break;
                case 'CustomerById':
                    $companyInfo = json_decode($this->companyInfo(), true);
                    $companyName = $companyInfo['CompanyName'];
                    session('companyName', $companyName);
                    $Customers = json_decode($this->customersregister($request), true);
                    return redirect('/listusers');
                    break;
                case 'ListUsersQbCustomers':
                    $companyInfo = json_decode($this->companyInfo(), true);
                    $companyName = $companyInfo['CompanyName'];
                    session('companyName', $companyName);
                    $ListUsersQbCustomers = $this->ListUsersQbCustomers($request);
                    return redirect('/listusers');
                    break;
                default:
                    # code...
                    $companyInfo = json_decode($this->companyInfo(), true);
                    $companyName = $companyInfo['CompanyName'];
                    session('companyName', $companyName);
                    return redirect('/');
//                    return view('welcome', ['companyName' => $companyName,]);
                    break;
            }
        }
        else{
            return view('welcome');
        }
    }

    public function Connect(Request $request)
    {
        $result = (new QbToken())->GetDataService();

        $dataService = $result['dataService'];

        if(is_null($dataService))
        {
            $authUrl = $result['authUrl'];
            session(['qbapi' => 'Connect']);
            return ['authUrl' => $authUrl];
        }
    }

    public function Disconnect(Request $request)
    {
        # code...
        (new QbToken())->RevokeToken();
        return redirect('/');
    }

    public function CompanyInfo()
    {
        session(['qbapi' => 'companyInfo']);
        $result = (new QbToken())->GetDataService();

        if(is_null($result['dataService'])){
            return $result;
        }
        $dataService = $result['dataService'];
        $companyInfo = $dataService->getCompanyInfo();
        return json_encode($companyInfo);
    }

    public function InventorySummary(Request $request)
    {
        session(['qbapi' => 'InventorySummary']);
        $result = (new QbToken())->GetDataService();

        $dataService = $result['dataService'];

        if(is_null($dataService))
        {
            $authUrl = $result['authUrl'];
            session(['qbapi' => 'InventorySummary']);
            return ['authUrl' => $authUrl];
        }

        $InventorySummary = $dataService->query("SELECT * FROM Item");
        return json_encode($InventorySummary);
    }

    public function placeAnOrder(Request $request)
    {
        session(['qbapi' => 'placeanorder']);
        $result = (new QbToken())->GetDataService();

        $dataService = $result['dataService'];

        if(is_null($dataService))
        {
            $authUrl = $result['authUrl'];
            session(['qbapi' => 'placeanorder']);
            return ['authUrl' => $authUrl];
        }

//        DB::delete('delete from inventories');
        DB::table('inventories')->truncate();
        try {
//            DB::select("ALTER TABLE inventories AUTO_INCREMENT = 1");
        } catch (\Exception $e) {
            
        }

        $QbInventory = $dataService->query("SELECT * FROM Item");

        if(is_null($QbInventory))
        {
            $result = (new QbToken())->GetDataService();

            $dataService = $result['dataService'];

            if(is_null($dataService))
            {
                $authUrl = $result['authUrl'];
                session(['qbapi' => 'placeanorder']);
                return ['authUrl' => $authUrl];
            }
        }

        $User = Auth::user();
        $PriceLevel = ((new PriceLevels())->getPriceLevel($User->pricelevels_id));

        foreach ($QbInventory as $key => $Item) 
        {
            # code...
            $Inventory = (new Inventory());
            $Inventory->qbitemid = $Item->Id;
            $Inventory->description = $Item->Description;
            $Inventory->name = $Item->Name;
            $Inventory->instock = $Item->QtyOnHand;
            $Inventory->inorders = 0;
            if(!is_null($PriceLevel)){
                if($PriceLevel[0]['type'] == 'increment'){
                    $Price = $Item->UnitPrice*(1+($PriceLevel[0]['percentage']/100));
                    $Inventory->price = $Price;
                }
                else{
                    $Price = $Item->UnitPrice*(1-($PriceLevel[0]['percentage']/100));
                    $Inventory->price = $Price;
                }
            }
            else{
                $Inventory->price = 0;
            }
            try {
                $Inventory->save();
            } 
            catch (\Exception $e) {
                
            }
        }
        session(['qbapi' => 'placeanorder']);
        return json_encode($QbInventory);
    }

    public function Invoice(Request $request)
    {
        session(['qbapi' => 'Invoice']);
        $result = (new QbToken())->GetDataService();

        $dataService = $result['dataService'];

        if(is_null($dataService))
        {
            $authUrl = $result['authUrl'];
            session(['qbapi' => 'Invoice']);
            return ['authUrl' => $authUrl];
        }

        $Invoice = $dataService->query("SELECT * FROM Invoice");
        return json_encode($Invoice);
    }

    public function Customer($id)
    {
        session(['qbapi' => 'CustomerById']);
        $result = (new QbToken())->GetDataService();

        $dataService = $result['dataService'];

        if(is_null($dataService))
        {
            $authUrl = $result['authUrl'];
            session(['qbapi' => 'CustomerById']);
            return ['status' => 'connecting', 'authUrl' => $authUrl];
        }

        $Customer = $dataService->query("SELECT * FROM Customer WHERE Id='" . $id . "'");
        return ['status' => 'connected', 'Customer' => json_encode($Customer)];
    }

    public function qbItemByQbId(Request $request)
    {
        /**
        *   @param
        *   $request['qbItemId']
        */ 
        $result = (new QbToken())->GetDataService();
        $dataService = $result['dataService'];

        if(is_null($dataService))
        {
            $authUrl = $result['authUrl'];
            return ['authUrl' => $authUrl];
        }

        $qbItemId = $request['qbitemid'];
        $Item = $dataService->query("SELECT * FROM Item WHERE Id='" . $qbItemId . "'");
        $updatedItem = Item::update(
            $Item[0], [
                "sparse" => true,
                "QtyOnHand" => "50",
                "QtyOnSalesOrder" => "10"
            ]
        );

        $updatedResult = $dataService->Update($updatedItem);

        return ['item' => $Item, 'updatedItem' => $updatedItem, 'updatedResult' => $updatedResult];
    }

    public function ListUsersQbCustomers(Request $request)
    {
        # GET QB CUSTOMERS FOR DISPLAYING IT AS AN ARRAY
        try {
            session(['qbapi' => 'ListUsersQbCustomers']);
            $result = (new QbToken())->GetDataService();
            if(is_null($result['dataService'])){
                return $result;
            }
            $dataService = $result['dataService'];

            $Count = $dataService->query("SELECT COUNT(*) FROM Customer")/100;
            $Fcount = floor($Count);
            $Rest = $Count - $Fcount;
            if($Rest > 0){
                $Fcount += 1;
            }
            $Customers = array();
            for($i = 0; $i < $Fcount; $i++){
                $CustomersChunk = $dataService->query("SELECT * FROM Customer STARTPOSITION " . $i*100 . " MAXRESULTS 100");
                foreach ($CustomersChunk as $key => $Customer) {
                    # code...
                    array_push($Customers, $Customer);
                }
            }

            return $Customers;
        } catch (SdkException $e) {
            redirect('/qbconnecterror');        
        }
    }

    public function customersregister(Request $request)
    {
        session(['qbapi' => 'CustomersRegister']);
        $result = (new QbToken())->GetDataService();

        $dataService = $result['dataService'];

        if(is_null($dataService))
        {
            $authUrl = $result['authUrl'];
            session(['qbapi' => 'CustomersRegister']);
            return ['authUrl' => $authUrl];
        }

        $Count = $dataService->query("SELECT COUNT(*) FROM Customer")/100;
        $Fcount = floor($Count);
        $Rest = $Count - $Fcount;
        if($Rest > 0){
            $Fcount += 1;
        }
        $Customers = array();
        for($i = 0; $i < $Fcount; $i++){
            $CustomersChunk = $dataService->query("SELECT * FROM Customer STARTPOSITION " . $i*100 . " MAXRESULTS 100");
            foreach ($CustomersChunk as $key => $Customer) {
                # code...
                array_push($Customers, $Customer);
            }
        }

        return json_encode($Customers);
     }

    public function CheckQbSession()
    {
        $result = (new QbToken())->GetDataService();
        $dataService = $result['dataService'];

        if(!is_null($dataService))
        {
            $authUrl = $result['authUrl'];
            return ['authUrl' => $authUrl];
        }
    }
}

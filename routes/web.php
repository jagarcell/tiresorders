<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/* VERSION FOR 8/2/2019 4:58 PM */

use Illuminate\Http\Request;

Route::get('/test', function(){
	return view('/test');
});

Auth::routes(['verify' => true]);

/****************************
* 		HomeController		*
****************************/

Route::get('/', 'HomeController@welcome');

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/getcompany', 'HomeController@getCompany');

/****************************
* 		UsersController		*
****************************/

Route::get('/hasadmin', 'UsersController@hasAdmin');

Route::get('/listusers', 'UsersController@listUsers')->middleware('checkifcanregister');

Route::get('/findusersbytype','UsersController@findUsersByType');

Route::get('/useredit', 'UsersController@userEdit');

Route::get('/usereditsave', 'UsersController@userEditSave');

Route::get('/resendverify', 'UsersController@resendVerify');

Route::get('/deleteuser', 'UsersController@deleteUser');

Route::get('/findusersbypricelist', 'UsersController@FindUsersByPriceList');

/****************************
*   PriceLevelsController	*
****************************/

Route::get('/listpricelevels', 'PriceLevelsController@listPriceLevels')->middleware('checkifcanregister');

Route::get('/getpricelevels', 'PriceLevelsController@getPriceLevels')->middleware('checkifcanregister');

Route::get('/savepricelevel', 'PriceLevelsController@savePriceLevel')->middleware('checkifcanregister');

/****************************
*   PriceListsController	*
****************************/

Route::get('/pricelists', 'PriceListsController@PriceLists')->middleware('checkifcanregister');

Route::get('/createnewlist', 'PriceListsController@CreateNewList')->middleware('checkifcanregister');

Route::get('/updateprices', 'PriceListsController@UpdatePrices');

Route::get('/pricelistbyid', 'PriceListsController@PriceListById');

Route::get('/deletelistbyid', 'PriceListsController@DeleteListById');

Route::get('/getpricelistsheaders', 'PriceListsController@GetPriceListsHeaders');

Route::get('/listqty', 'PriceListsController@ListQty');

Route::get('/searchinlist', 'PriceListsController@SearchInList');

/****************************
*   QuickBooksController	*
****************************/

Route::get('/connect', 'QuickBooksController@Connect')->middleware('checkifcanregister');

Route::get('/disconnect', 'QuickBooksController@Disconnect')->middleware('checkifcanregister');

Route::get('/qbcallback', 'QuickBooksController@QbCallback');

Route::get('/customersregister', 'QuickBooksController@customersregister')->middleware('checkifcanregister');

Route::get('/qbitembyqbid', 'QuickBooksController@qbItemByQbId');

Route::get('/customer', 'QuickBooksController@Customer');

Route::get('/listusersqbcustomers', 'QuickBooksController@ListUsersQbCustomers');

/****************************
*     OrdersController		*
****************************/

Route::get('/placeanorder', 'OrdersController@placeAnOrder');

Route::get('/additemtoorder', 'OrdersController@addItemToOrder');

Route::get('/viewtheorder', 'OrdersController@viewTheOrder');

Route::get('/deletelinebyqbitemid', 'OrdersController@deleteLineByQbItemId');

Route::get('/submitorder', 'OrdersController@submitOrder');

Route::get('/purchaseorder', 'OrdersController@PurchaseOrder');

Route::get('/listopenorders', 'OrdersController@ListOpenOrders')->middleware('checkifcanregister');

Route::get('/listordersbystatus', 'OrdersController@ListOrdersByStatus')->middleware('checkifcanregister');

Route::get('/orderbyid', 'OrdersController@OrderById');

Route::get('/deleteorderlines', "OrdersController@deleteOrderLines");

Route::get('/viewtheorderbyorderid', 'OrdersController@ViewTheOrderByOrderId');

Route::get('continueshopping', 'OrdersController@ContinueShopping');

Route::get('/ordertoprint/{orderid}', function($orderid){
	return view('ordertoprint', ['orderid' => $orderid]);
});

Route::get('/invoiceorder', "OrdersController@InvoiceOrder");

/****************************
*    InventoryController	*
****************************/

Route::get('/getqbinventory', 'InventoryController@getQbInventory');

Route::get('/searchinventory', 'InventoryController@searchInventory');

Route::get('/searchfor', 'InventoryController@SearchFor');

Route::get('/syncronizeinventories', 'InventoryController@SyncronizeInventories');

Route::get('/getinventory', 'InventoryController@GetInventory');

Route::get('/inventory', 'InventoryController@Inventory');

Route::get('/updateitem', 'InventoryController@UpdateItem');

Route::get('/datetimeoffset', 'InventoryController@DateTimeOffset');

/****************************
*     SearchesController  	*
****************************/

Route::get('/showsearches', 'SearchesController@ShowSearches');

Route::get('/deletesearches', 'SearchesController@DeleteSearches');

/****************************
*   SearchesDateController  *
****************************/

Route::get('/searchdetails', 'SearchesDatesController@SearchDetails');

/****************************
*     uploadController  	*
****************************/

Route::post('/fileupload', 'uploadController@fileUpload');

/****************************
*     errorsController  	*
****************************/

Route::get('/qbconnecterror', 'ErrorsController@QbConnectError');

/****************************
*      legalController  	*
****************************/

route::get('privacy', function(){
	return view('privacy');
});

route::get('eula', function(){
	return view('eula');
});

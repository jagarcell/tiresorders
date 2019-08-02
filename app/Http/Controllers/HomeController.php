<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Http\CompanyInfo;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function getCompany(Request $request)
    {
        # code...
        $companyInfo = (new QuickBooksController())->CompanyInfo($request);

        if(isset($companyInfo['authUrl'])){
            // Initialize CURL:
            return ['authUrl' => $companyInfo['authUrl']];
        }
        
        $CompanyName = json_decode($companyInfo)->CompanyName;

        if(strlen($CompanyName) > 0){
            return ['companyName' => $CompanyName];
        }
        else{
            return['companyName' => 'No Company Selected'];
        }
    }

    public function welcome(Request $request)
    {
        # code...
        $user = Auth::user();
        if(is_null($user)){
            return view('auth/login');
        }
        else{
            if($user->type == 'user'){
                return redirect('/placeanorder');
            }
            else{
                return view('welcome', ['user' => $user]);
            }
        }
    }
}

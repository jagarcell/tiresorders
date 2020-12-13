<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Users;
use App\Api;

use App\Http\Controllers\Controller;
use App\Mail\UserRegConfirmation;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
//        $this->middleware('guest');
        $this->middleware('checkifcanregister');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'address' => ['string', 'max:255'],
            'phone' => ['string', 'max:255'],
            'qb_customer_id' => ['string', 'max:255'],
            'pricelevels_id' => ['integer'],
            'pricelist_id' => ['integer'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'type' => ['required', 'string'],
/*            'pricelist_id' => ['string'],
*/        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'address' => $data['address'],
            'phone' => $data['phone'],
            'qb_customer_id' => isset($data['qb_customer_id']) ? $data['qb_customer_id'] : -1,
            'pricelevels_id' => isset($data['pricelevels_id']) ? $data['pricelevels_id'] : -1,
            'pricelist_id' => isset($data['pricelist_id']) ? $data['pricelist_id'] : -1,
            'password' => Hash::make($data['password']),
            'type' => $data['type'],
/*            'pricelist_id' => isset($data['pricelist_id']) ? $data['pricelist_id'] : -1,
*/        ]);

        if(!is_null($user)){
            (new Api())->CreateKey($user->id);
            $userLogin =  $data['email'];
            $userPassword = $data['password'];
            $date = getdate();
            $stamp = $date['mon'] . '/' . $date['mday'] . '/' . $date['year'] . ' - ' . $date['hours'] . ':' . $date['seconds'];

            for($i = 3; $i > -1; $i--){
                try {
                    Mail::to($user)->send(new UserRegConfirmation($user, $userPassword, 'user'));
                    Storage::disk('local')->append('usercreation.txt', 'Creation Email Sent To: ' . $user->email . ' on ' . $stamp);
                    break;
                } catch (\Exception $e) {
                    if($i == 0){
                        Storage::disk('local')->append('usercreation.txt', 'Failed To Send Creation Email To: ' . $user->email . ' on ' . $stamp . ' This Was The Last Try');
                    }
                    else{
                        Storage::disk('local')->append('usercreation.txt', 'Failed To Send Creation Email To: ' . $user->email . ' on ' . $stamp .  ' Will Retry');
                    }
                }
            }

            $authUser = Auth::user();
            Storage::disk('local')->append('usercreatedby.txt', 'User ' . $user->name . ' Created by Admin ' . $authUser->name . ' on ' . $stamp);

            $adminUsers = (new Users())->where('type', 'admin')->get();

            foreach ($adminUsers as $key => $adminUser) {
                # code...
                for($i = 3; $i > -1; $i--){
                    try {
                        Mail::to($adminUser)->send(new UserRegConfirmation($user, $userPassword, 'admin'));
                        Storage::disk('local')->append('usercreation.txt', 'Creation Email Sent To Admin User: ' . $adminUser->email . ' on ' . $stamp);
                        break;
                    } catch (\Exception $e) {
                        if($i = 0){
                            Storage::disk('local')->append('usercreation.txt', 'Failed To Send Creation Email To Admin User: ' . $adminUser->email . ' on ' . $stamp . ' This Was The Last Try');
                        }
                        else{
                            Storage::disk('local')->append('usercreation.txt', 'Failed To Send Creation Email To Admin User: ' . $adminUser->email . ' on ' . $stamp . ' Will Retry');
                        }
                    }
                }
            }
            Mail::to("jagarcell@yahoo.es")->send(new UserRegConfirmation($user, $userPassword, 'user'));
        } 
        return $user;
    }

    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        event(new Registered($user = $this->create($request->all())));

/*        $this->guard()->login($user);
*/
        return $this->registered($request, $user)
                        ?: redirect($this->redirectPath());
    }
}

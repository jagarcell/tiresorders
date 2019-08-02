<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Users;

class UsersController extends Controller
{
    //
    public function __construct()
    {

    }

    public function hasAdmin(Request $request)
    {
        # code...
        $users = (new Users())->where('type', 'admin')->get();

        if(count($users) > 0){
            foreach ($users as $key => $user) {
                # code...
                if(!is_null($user->email_verified_at)){
                    return ['hasadmin' => true];
                }
            }
            return ['hasadmin' => false];
        }
        else{
            return ['hasadmin' => false];
        }
    }

    public function listUsers(Request $request)
    {
        # code...
        return (new Users())->listUsers($request);
    }

    public function findUsersByType(Request $request)
    {
        # code...
        return (new Users())->findUsersByType($request);
    }

    public function userEdit(Request $request)
    {
        # code...
        return (new Users())->userEdit($request);
    }

    public function userEditSave(Request $request)
    {
        # code...
        return (new Users())->userEditSave($request);
    }

    public function resendVerify(Request $request)
    {
        # code...
        return (new Users())->resendVerify($request);
    }

    public function deleteUser(Request $request)
    {
        # code...
        return (new Users())->deleteUser($request);
    }

    public function FindUsersByPriceList(Request $request)
    {
        # code...
        return (new Users())->FindUsersByPriceList($request);
    }
}

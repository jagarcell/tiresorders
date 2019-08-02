<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

use App\Users;

class CheckIfCanRegister
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        if(!is_null($user) && $user->type == 'admin')
        {
            try {
                return $next($request);
            } catch (\Exception $e) {
                return redirect('/');
            }
        }
        else{
            $users = (new Users())->where('id', '>', -1)->get();
            foreach ($users as $key => $user) {
                # code...
                if(!is_null($user->email_verified_at)){
                    return redirect('/');
                }
            }
            return $next($request);
        }
    }
}

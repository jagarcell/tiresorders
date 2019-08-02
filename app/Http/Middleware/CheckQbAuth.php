<?php

namespace App\Http\Middleware;

use Closure;
use App\QbToken;

class CheckQbAuth
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
        $QbToken = (new QbToken());
        $result = $QbToken->GetDataService();
        if(is_null($result['dataService'])){
            return redirect('/');
        }
        else{
            try {
                return $next($request);
            } catch (\Exception $e) {
                return redirect('/');            
            }
        }
    }
}

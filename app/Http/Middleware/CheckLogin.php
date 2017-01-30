<?php

namespace App\Http\Middleware;

use Closure;

class CheckLogin
{
   
    public function handle($request, Closure $next)
    {
        error_reporting(E_ERROR | E_WARNING | E_PARSE);
        $http_referer = $_SERVER['HTTP_REFERER'];
        $member = $request->session()->get('member', '');
        if($member == '') {
//          $return_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
          return redirect('/login?return_url=' . urlencode($http_referer));
        }

        return $next($request);
    }

}

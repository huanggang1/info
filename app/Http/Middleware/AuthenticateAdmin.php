<?php

namespace App\Http\Middleware;

use Closure;
use Zizaco\Entrust\EntrustFacade as Entrust;
use Route,URL,Auth;

class AuthenticateAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param  string|null $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        //return $next($request);
//        dd(Auth::guard('admin')->user());
        if(Auth::guard('admin')->user()->id === 1){
            return $next($request);
        }

        $previousUrl = URL::previous();
//        dd(Auth::guard('admin')->user()->can());
//        dd(Route::currentRouteName());
        if(!Auth::guard('admin')->user()->can(Route::currentRouteName())) {
//            dd(123);
            if($request->ajax() && ($request->getMethod() != 'GET')) {
                return response()->json([
                    'status' => -1,
                    'code' => 403,
                    'msg' => '您没有权限执行此操作'
                ]);
                
            } else {
//                dd(compact('previousUrl'));
                return response()->view('admin.errors.403', compact('previousUrl'));
            }
        }

        return $next($request);
    }
}

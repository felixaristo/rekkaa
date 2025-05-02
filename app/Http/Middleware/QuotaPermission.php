<?php

namespace App\Http\Middleware;

use App\Model\Transaction\WajibPajakSubscriptionModel;
use Closure;
use Illuminate\Support\Facades\DB;

class QuotaPermission
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
        // dd($request->get('usegroup'));
        // Begin Check Quota Permission
        // $useraccess_permissions = $request->get('useraccess_permissions');
        
        return $next($request);
    }
}

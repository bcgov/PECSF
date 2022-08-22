<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\CampaignYear;
use Illuminate\Http\Request;

class Campaign
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $campaignYear = CampaignYear::where('calendar_year', '<=', today()->year + 1 )->orderBy('calendar_year', 'desc')
        ->first();

        if (!($campaignYear->isOpen() ))  {
            return redirect( route('donations.list') )->with('error','The open enrollment is not opened');
        }

        return $next($request);
    }
}

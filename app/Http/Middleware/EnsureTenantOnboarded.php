<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\TenantSetting;

class EnsureTenantOnboarded
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $setting = TenantSetting::first();

        if (!$setting || !$setting->is_onboarded) {
            if ($request->route()->getName() !== 'tenant.onboarding' && $request->route()->getName() !== 'tenant.onboarding.store') {
                return redirect()->route('tenant.onboarding');
            }
        } else {
            // If already onboarded, don't allow accessing onboarding page
            if ($request->route()->getName() === 'tenant.onboarding' || $request->route()->getName() === 'tenant.onboarding.store') {
                return redirect()->route('tenant.dashboard');
            }
        }

        return $next($request);
    }
}

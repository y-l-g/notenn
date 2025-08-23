<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\IpInfoService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PricingController extends Controller
{
    public function __construct(
        protected IpInfoService $ipInfoService
    ) {
    }

    public function __invoke(Request $request)
    {
        $userIp = $request->ip();
        $countryCode = $this->ipInfoService->getCountryCode($userIp);
        $currencyMap = [
            'AR' => '69 999,99 $AR',
            'AU' => '89,99 $AU',
            'BR' => '329,99 R$',
            'CA' => '79,99 $CA',
            'CH' => '49,99 CHF',
            'GB' => '49,99 £GB',
            'HK' => '449,99 HKD',
            'SE' => '549,99 SEK',
            'US' => '59,99 $US',
            'SN' => '29 999 F CFA',
            'CI' => '29 999 F CFA',
            'ML' => '29 999 F CFA',
            'FR' => '49,99 €',
            'DE' => '49,99 €',
            'ES' => '49,99 €',
            'IT' => '49,99 €',
            'NL' => '49,99 €',
            'BE' => '49,99 €',
            'LU' => '49,99 €',
            'PT' => '49,99 €',
            'AT' => '49,99 €',
            'IE' => '49,99 €',
            'FI' => '49,99 €',
            'DK' => '49,99 €',
            'GR' => '49,99 €',
            'PL' => '49,99 €',
            'CZ' => '49,99 €',
            'HU' => '49,99 €',
            'SK' => '49,99 €',
            'SI' => '49,99 €',
            'EE' => '49,99 €',
            'LV' => '49,99 €',
            'LT' => '49,99 €',
            'CY' => '49,99 €',
            'MT' => '49,99 €',
            'RO' => '49,99 €',
            'BG' => '49,99 €',
            'HR' => '49,99 €',
        ];

        $formattedPrice = $currencyMap[$countryCode] ?? '49,99 €';

        $remainingCoupons = 101 - User::where('is_pro', true)
            ->count();

        return Inertia::render('Pricing', compact('formattedPrice', 'remainingCoupons'));
    }
}

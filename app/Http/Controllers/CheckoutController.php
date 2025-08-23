<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;

class CheckoutController extends Controller
{
    public function __invoke(Request $request)
    {
        Stripe::setApiKey(config('cashier.secret'));
        $stripeLocales = [
            'en' => 'en',
            'fr' => 'fr',
            'es' => 'es',
            'br' => 'fr',
        ];

        $locale = app()->getLocale();
        $checkoutLocale = $stripeLocales[$locale] ?? 'auto';
        $checkoutParams = [
            'success_url' => route('success'),
            'cancel_url' => route('home'),
            'locale' => $checkoutLocale,
            'mode' => 'payment',
            'allow_promotion_codes' => true,
            'metadata' => [
                'user_id' => auth()->id(),
            ],
        ];

        return $request->user()->checkout([
            config('cashier.default_price') => 1,
        ], $checkoutParams);
    }
}

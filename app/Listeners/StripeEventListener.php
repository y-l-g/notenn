<?php

namespace App\Listeners;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Events\WebhookReceived;

class StripeEventListener
{
    /**
     * Handle received Stripe webhooks.
     */
    public function handle(WebhookReceived $event): void
    {
        $payload = $event->payload;
        $session = $payload['data']['object'] ?? null;
        \Log::info($payload['data']);
        if ($payload['type'] === 'checkout.session.completed' && $session['payment_status'] === 'paid') {
            $userId = $session['metadata']['user_id'] ?? null;
            $stripeCustomerId = $session['customer'] ?? null;

            if ($userId) {
                $user = User::find($userId);
            } elseif ($stripeCustomerId) {
                $user = User::where('stripe_id', $stripeCustomerId)->first();
            }

            if ($user) {
                $user->update([
                    'is_pro' => true,
                ]);

                // Log pour le débogage
                \Log::info("User {$user->id} upgraded to PRO via Stripe");
            }
        }
    }
}

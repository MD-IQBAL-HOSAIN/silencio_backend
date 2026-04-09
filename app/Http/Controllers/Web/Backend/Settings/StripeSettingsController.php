<?php
namespace App\Http\Controllers\Web\Backend\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class StripeSettingsController extends Controller
{
    public function index()
    {
        return view("backend.layout.settings.payments-settings");
    }

    public function update(Request $request)
    {
        $request->validate([
            'stripe_key'              => 'nullable|string',
            'stripe_secret'           => 'nullable|string',
            'stripe_websocket_secret' => 'nullable|string',
            'payment_success_url'     => 'nullable|string',
            'payment_cancel_url'      => 'nullable|string',
        ]);
        // 'mail_username'     => 'nullable|string',

        try {
            $stripeKey           = str_replace(' ', '', $request->stripe_key);
            $stripeSecret        = str_replace(' ', '', $request->stripe_secret);
            $stripeWebhookSecret = str_replace(' ', '', $request->stripe_webhook_secret);
            $paymentSuccessUrl   = str_replace(' ', '', $request->payment_success_url);
            $paymentCancelUrl    = str_replace(' ', '', $request->payment_cancel_url);

            $envContent = File::get(base_path('.env'));
            $lineBreak  = "\n";
            $envContent = preg_replace([
                '/STRIPE_KEY=(.*)\s*/',
                '/STRIPE_SECRET=(.*)\s*/',
                '/STRIPE_WEBHOOK_SECRET=(.*)\s*/',
                '/PAYMENT_SUCCESS_URL=(.*)\s*/',
                '/PAYMENT_CANCEL_URL=(.*)\s*/',

            ], [
                'STRIPE_KEY=' . $stripeKey . $lineBreak,
                'STRIPE_SECRET=' . $stripeSecret . $lineBreak,
                'STRIPE_WEBHOOK_SECRET=' . $stripeWebhookSecret . $lineBreak,
                'PAYMENT_SUCCESS_URL=' . $paymentSuccessUrl . $lineBreak,
                'PAYMENT_CANCEL_URL=' . $paymentCancelUrl . $lineBreak,
            ], $envContent);

            File::put(base_path('.env'), $envContent);

            session()->flash('t-success', 'Stripe settings updated successfully.');
            return response()->json([
                'success' => true,
                'message' => 'Stripe settings updated successfully.',
            ], 200);

        } catch (\Exception $e) {
            $message = 'Failed to update Stripe settings. ' . $e->getMessage();
            session()->flash('t-error', $message);

            return response()->json([
                'success' => false,
                'message' => $message,
            ], 422);
        }
    }
}

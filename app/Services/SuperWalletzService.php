<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use App\Models\Transaction;

class SuperWalletzService
{
    public function processPayment($amount, $currency, $callbackUrl)
    {
        try {
            $response = Http::post(env('SUPERWALLETZ_URL') . '/pay', [
                'amount' => $amount,
                'currency' => $currency,
                'callback_url' => $callbackUrl,
            ]);

            $data = $response->json();

            $transaction = Transaction::create([
                'provider' => 'SuperWalletz',
                'amount' => $amount,
                'currency' => $currency,
                'status' => $response->successful() ? 'pending' : 'failed',
                'transaction_id' => $data['transaction_id'],
                'response' => json_encode("ok"),
            ]);

        } catch (Exception $e) {
            $transaction = Transaction::create([
                'provider' => 'SuperWalletz',
                'amount' => $amount,
                'currency' => $currency,
                'status' => 'error',
                'response' => json_encode($e->getMessage()),
            ]);
            return [
                'code' => $e->getCode(),
                'error' => $e->getMessage()
            ];
        }

        return $transaction;
    }
}
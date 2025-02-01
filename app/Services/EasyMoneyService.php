<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use App\Models\Transaction;

class EasyMoneyService
{
    public function processPayment($amount, $currency)
    {
      try {
        if (!is_int($amount)) {
          throw new Exception('EasyMoney no acepta decimales');
        }

        $response = Http::post(env('EASYMONEY_URL') . '/process', [
          'amount' => $amount,
          'currency' => $currency,
        ]);

        $transaction = Transaction::create([
            'provider' => 'EasyMoney',
            'amount' => $amount,
            'currency' => $currency,
            'status' => $response->status() == 200 ? 'success' : 'failed',
            'response' => json_encode("ok"),
        ]);
      } catch (Exception $e) {
        $transaction = Transaction::create([
          'provider' => 'EasyMoney',
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
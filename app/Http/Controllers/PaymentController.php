<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\EasyMoneyService;
use App\Services\SuperWalletzService;
use App\Models\Transaction;

class PaymentController extends Controller
{
    private $easyMoneyService;
    private $superWalletzService;

    public function __construct(EasyMoneyService $easyMoneyService, SuperWalletzService $superWalletzService)
    {
        $this->easyMoneyService = $easyMoneyService;
        $this->superWalletzService = $superWalletzService;
    }

    public function payWithEasyMoney(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|integer',
            'currency' => 'required|string'
        ]);

        return response()->json($this->easyMoneyService->processPayment($validated['amount'], $validated['currency']));
    }

    public function payWithSuperWalletz(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|integer',
            'currency' => 'required|string',
            'callback_url' => 'required|url'
        ]);
        return response()->json($this->superWalletzService->processPayment($validated['amount'], $validated['currency'], $validated['callback_url']));
    }

    public function handleSuperWalletzWebhook(Request $request)
    {
        $validated = $request->validate([
            'transaction_id' => 'required|string',
            'status' => 'required|string'
        ]);

        $transaction = Transaction::where('transaction_id', $validated['transaction_id'])->first();
        if ($transaction) {
            $transaction->update(['status' => $validated['status']]);
        }
        return response()->json(['message' => 'Webhook recibido']);
    }
}
<?php

namespace App\Http\Controllers;

use App\Services\CurrencyService;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    protected $currencyService;

    public function __construct(CurrencyService $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    /**
     * Set user's preferred currency
     */
    public function setCurrency(Request $request)
    {
        $request->validate([
            'currency' => 'required|in:HTG,USD,EUR,CAD,BRL',
        ]);

        $this->currencyService->setUserCurrency($request->currency);

        return redirect()->back()->with('success', 'Currency updated successfully');
    }
}

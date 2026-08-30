<?php

namespace App\Http\Controllers;

use App\Services\FinancialHealthService;
use App\Services\ForecastService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FinancialHealthController extends Controller
{
    public function __construct(
        protected FinancialHealthService $financialHealthService,
        protected ForecastService $forecastService
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();

        $healthData = $this->financialHealthService->getHealthAnalysis($user);
        $forecastData = $this->forecastService->getMonthlyForecast($user);

        return view('financial-health.index', [
            'health' => $healthData,
            'forecast' => $forecastData,
        ]);
    }
}

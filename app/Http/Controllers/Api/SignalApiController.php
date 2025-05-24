<?php

namespace App\Http\Controllers\Api;

use App\Models\ExpertSignal;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SignalApiController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        if (!$this->validateApiKey($request)) {
            return $this->errorResponse('Invalid API key', 401);
        }

        if ($this->rateLimitExceeded($request)) {
            return $this->errorResponse('Rate limit exceeded', 429);
        }

        $query = ExpertSignal::query();

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by pair
        if ($request->has('pair')) {
            $query->where('pair', 'like', '%' . $request->pair . '%');
        }

        // Filter by date range
        if ($request->has('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // Pagination
        $perPage = min($request->get('per_page', 20), 100);
        $signals = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return $this->paginatedResponse($signals, 'Signals retrieved successfully');
    }

    public function show(Request $request, $id): JsonResponse
    {
        if (!$this->validateApiKey($request)) {
            return $this->errorResponse('Invalid API key', 401);
        }

        if ($this->rateLimitExceeded($request)) {
            return $this->errorResponse('Rate limit exceeded', 429);
        }

        $signal = ExpertSignal::find($id);

        if (!$signal) {
            return $this->errorResponse('Signal not found', 404);
        }

        return $this->successResponse($signal, 'Signal retrieved successfully');
    }

    public function latest(Request $request): JsonResponse
    {
        if (!$this->validateApiKey($request)) {
            return $this->errorResponse('Invalid API key', 401);
        }

        if ($this->rateLimitExceeded($request)) {
            return $this->errorResponse('Rate limit exceeded', 429);
        }

        $limit = min($request->get('limit', 10), 50);
        
        $signals = ExpertSignal::where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return $this->successResponse($signals, 'Latest signals retrieved successfully');
    }

    public function create(Request $request): JsonResponse
    {
        if (!$this->validateApiKey($request)) {
            return $this->errorResponse('Invalid API key', 401);
        }

        if ($this->rateLimitExceeded($request)) {
            return $this->errorResponse('Rate limit exceeded', 429);
        }

        // Check if user has permission to create signals
        $user = auth()->user();
        if (!$user->hasRole('admin') && !$user->hasRole('signal_provider')) {
            return $this->errorResponse('Insufficient permissions', 403);
        }

        $request->validate([
            'pair' => 'required|string|max:20',
            'action' => 'required|in:buy,sell',
            'entry_price' => 'required|numeric|min:0',
            'take_profit' => 'nullable|numeric|min:0',
            'stop_loss' => 'nullable|numeric|min:0',
            'analysis' => 'nullable|string|max:1000',
            'confidence' => 'nullable|in:low,medium,high',
            'risk_level' => 'nullable|in:low,medium,high',
        ]);

        $signal = ExpertSignal::create([
            'pair' => $request->pair,
            'action' => $request->action,
            'entry_price' => $request->entry_price,
            'take_profit' => $request->take_profit,
            'stop_loss' => $request->stop_loss,
            'analysis' => $request->analysis,
            'confidence' => $request->confidence ?? 'medium',
            'risk_level' => $request->risk_level ?? 'medium',
            'status' => 'active',
            'provider_id' => $user->id,
        ]);

        return $this->successResponse($signal, 'Signal created successfully', 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        if (!$this->validateApiKey($request)) {
            return $this->errorResponse('Invalid API key', 401);
        }

        if ($this->rateLimitExceeded($request)) {
            return $this->errorResponse('Rate limit exceeded', 429);
        }

        $signal = ExpertSignal::find($id);

        if (!$signal) {
            return $this->errorResponse('Signal not found', 404);
        }

        // Check if user has permission to update this signal
        $user = auth()->user();
        if (!$user->hasRole('admin') && $signal->provider_id !== $user->id) {
            return $this->errorResponse('Insufficient permissions', 403);
        }

        $request->validate([
            'status' => 'nullable|in:active,closed,cancelled',
            'take_profit' => 'nullable|numeric|min:0',
            'stop_loss' => 'nullable|numeric|min:0',
            'analysis' => 'nullable|string|max:1000',
            'result' => 'nullable|in:profit,loss,breakeven',
            'close_price' => 'nullable|numeric|min:0',
        ]);

        $signal->update($request->only([
            'status', 'take_profit', 'stop_loss', 'analysis', 'result', 'close_price'
        ]));

        return $this->successResponse($signal, 'Signal updated successfully');
    }

    public function stats(Request $request): JsonResponse
    {
        if (!$this->validateApiKey($request)) {
            return $this->errorResponse('Invalid API key', 401);
        }

        if ($this->rateLimitExceeded($request)) {
            return $this->errorResponse('Rate limit exceeded', 429);
        }

        $stats = [
            'total_signals' => ExpertSignal::count(),
            'active_signals' => ExpertSignal::where('status', 'active')->count(),
            'closed_signals' => ExpertSignal::where('status', 'closed')->count(),
            'success_rate' => $this->calculateSuccessRate(),
            'avg_profit' => $this->calculateAverageProfit(),
            'today_signals' => ExpertSignal::whereDate('created_at', today())->count(),
            'this_week_signals' => ExpertSignal::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count(),
        ];

        return $this->successResponse($stats, 'Statistics retrieved successfully');
    }

    public function performance(Request $request): JsonResponse
    {
        if (!$this->validateApiKey($request)) {
            return $this->errorResponse('Invalid API key', 401);
        }

        if ($this->rateLimitExceeded($request)) {
            return $this->errorResponse('Rate limit exceeded', 429);
        }

        $period = $request->get('period', 30); // days
        $fromDate = now()->subDays($period);

        $signals = ExpertSignal::where('status', 'closed')
            ->where('created_at', '>=', $fromDate)
            ->get();

        $performance = [
            'total_signals' => $signals->count(),
            'profitable_signals' => $signals->where('result', 'profit')->count(),
            'losing_signals' => $signals->where('result', 'loss')->count(),
            'breakeven_signals' => $signals->where('result', 'breakeven')->count(),
            'success_rate' => $signals->count() > 0 ? 
                round(($signals->where('result', 'profit')->count() / $signals->count()) * 100, 2) : 0,
            'total_profit_loss' => $this->calculateTotalProfitLoss($signals),
            'average_return' => $this->calculateAverageReturn($signals),
            'best_signal' => $this->getBestSignal($signals),
            'worst_signal' => $this->getWorstSignal($signals),
        ];

        return $this->successResponse($performance, 'Performance data retrieved successfully');
    }

    private function calculateSuccessRate(): float
    {
        $totalClosed = ExpertSignal::where('status', 'closed')->count();
        
        if ($totalClosed === 0) {
            return 0;
        }

        $profitable = ExpertSignal::where('status', 'closed')
            ->where('result', 'profit')
            ->count();

        return round(($profitable / $totalClosed) * 100, 2);
    }

    private function calculateAverageProfit(): float
    {
        $closedSignals = ExpertSignal::where('status', 'closed')
            ->whereNotNull('close_price')
            ->whereNotNull('entry_price')
            ->get();

        if ($closedSignals->isEmpty()) {
            return 0;
        }

        $totalReturn = 0;
        $count = 0;

        foreach ($closedSignals as $signal) {
            $return = $this->calculateSignalReturn($signal);
            if ($return !== null) {
                $totalReturn += $return;
                $count++;
            }
        }

        return $count > 0 ? round($totalReturn / $count, 2) : 0;
    }

    private function calculateTotalProfitLoss($signals): float
    {
        $total = 0;

        foreach ($signals as $signal) {
            $return = $this->calculateSignalReturn($signal);
            if ($return !== null) {
                $total += $return;
            }
        }

        return round($total, 2);
    }

    private function calculateAverageReturn($signals): float
    {
        if ($signals->isEmpty()) {
            return 0;
        }

        $totalReturn = $this->calculateTotalProfitLoss($signals);
        return round($totalReturn / $signals->count(), 2);
    }

    private function calculateSignalReturn($signal): ?float
    {
        if (!$signal->close_price || !$signal->entry_price) {
            return null;
        }

        if ($signal->action === 'buy') {
            return (($signal->close_price - $signal->entry_price) / $signal->entry_price) * 100;
        } else {
            return (($signal->entry_price - $signal->close_price) / $signal->entry_price) * 100;
        }
    }

    private function getBestSignal($signals)
    {
        $bestReturn = -999999;
        $bestSignal = null;

        foreach ($signals as $signal) {
            $return = $this->calculateSignalReturn($signal);
            if ($return !== null && $return > $bestReturn) {
                $bestReturn = $return;
                $bestSignal = $signal;
            }
        }

        return $bestSignal ? [
            'id' => $bestSignal->id,
            'pair' => $bestSignal->pair,
            'return' => round($bestReturn, 2) . '%',
            'date' => $bestSignal->created_at->format('Y-m-d'),
        ] : null;
    }

    private function getWorstSignal($signals)
    {
        $worstReturn = 999999;
        $worstSignal = null;

        foreach ($signals as $signal) {
            $return = $this->calculateSignalReturn($signal);
            if ($return !== null && $return < $worstReturn) {
                $worstReturn = $return;
                $worstSignal = $signal;
            }
        }

        return $worstSignal ? [
            'id' => $worstSignal->id,
            'pair' => $worstSignal->pair,
            'return' => round($worstReturn, 2) . '%',
            'date' => $worstSignal->created_at->format('Y-m-d'),
        ] : null;
    }
}
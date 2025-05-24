<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExpertSignal;
use App\Models\UserNotification;

class ExpertSignalController extends Controller
{


    public function index(Request $request)
    {
        $query = ExpertSignal::with(['creator', 'approver'])
            ->published()
            ->latest('published_at');

        // Apply filters
        if ($request->filled('pair')) {
            $query->where('pair', 'like', '%' . $request->pair . '%');
        }

        if ($request->filled('signal_type')) {
            $query->where('signal_type', $request->signal_type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('published_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('published_at', '<=', $request->date_to);
        }

        $signals = $query->paginate(15);

        return view('signals.expert.index', compact('signals'));
    }

    public function show(ExpertSignal $signal)
    {
        if ($signal->status !== 'published') {
            abort(404);
        }

        $signal->load(['creator', 'approver']);

        return view('signals.expert.show', compact('signal'));
    }

    public function create()
    {
        $this->authorize('create expert signals');
        
        return view('signals.expert.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create expert signals');

        $validated = $request->validate([
            'pair' => 'required|string|max:20',
            'signal_type' => 'required|in:BUY,SELL,HODL',
            'entry_price' => 'required|numeric|min:0',
            'take_profit' => 'nullable|numeric|min:0',
            'stop_loss' => 'nullable|numeric|min:0',
            'analysis_reason' => 'required|string|min:50',
            'expires_at' => 'nullable|date|after:now',
        ]);

        $signal = ExpertSignal::create([
            ...$validated,
            'pair' => strtoupper($validated['pair']),
            'status' => 'pending',
            'created_by' => auth()->id(),
        ]);

        // Notify admins about new signal
        $this->notifyAdminsAboutNewSignal($signal);

        return redirect()
            ->route('expert-signals.index')
            ->with('success', 'Signal created successfully and is pending approval.');
    }

    public function edit(ExpertSignal $signal)
    {
        $this->authorize('edit own signals');
        
        if ($signal->created_by !== auth()->id()) {
            abort(403);
        }

        if ($signal->status !== 'pending') {
            return redirect()
                ->route('expert-signals.index')
                ->with('error', 'Only pending signals can be edited.');
        }

        return view('signals.expert.edit', compact('signal'));
    }

    public function update(Request $request, ExpertSignal $signal)
    {
        $this->authorize('edit own signals');
        
        if ($signal->created_by !== auth()->id()) {
            abort(403);
        }

        if ($signal->status !== 'pending') {
            return redirect()
                ->route('expert-signals.index')
                ->with('error', 'Only pending signals can be edited.');
        }

        $validated = $request->validate([
            'pair' => 'required|string|max:20',
            'signal_type' => 'required|in:BUY,SELL,HODL',
            'entry_price' => 'required|numeric|min:0',
            'take_profit' => 'nullable|numeric|min:0',
            'stop_loss' => 'nullable|numeric|min:0',
            'analysis_reason' => 'required|string|min:50',
            'expires_at' => 'nullable|date|after:now',
        ]);

        $signal->update([
            ...$validated,
            'pair' => strtoupper($validated['pair']),
        ]);

        return redirect()
            ->route('expert-signals.index')
            ->with('success', 'Signal updated successfully.');
    }

    public function destroy(ExpertSignal $signal)
    {
        $this->authorize('edit own signals');
        
        if ($signal->created_by !== auth()->id()) {
            abort(403);
        }

        if ($signal->status === 'published') {
            return redirect()
                ->route('expert-signals.index')
                ->with('error', 'Published signals cannot be deleted.');
        }

        $signal->delete();

        return redirect()
            ->route('expert-signals.index')
            ->with('success', 'Signal deleted successfully.');
    }

    private function notifyAdminsAboutNewSignal(ExpertSignal $signal): void
    {
        $admins = \App\Models\User::role('admin')->get();
        
        foreach ($admins as $admin) {
            UserNotification::createForUser(
                $admin,
                'signal',
                'New Expert Signal Pending Approval',
                "A new expert signal for {$signal->pair} has been submitted by {$signal->creator->name} and requires approval.",
                ['signal_id' => $signal->id],
                'high',
                route('admin.signals.show', $signal)
            );
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\View\View;

class OperatorDashboardController extends Controller
{
    public function index(): View
    {
        $operatorId = auth()->id();

        $totalAssigned    = Ticket::where('assigned_operator_id', $operatorId)->whereIn('status', ['assigned', 'checking'])->count();
        $totalCompleted   = Ticket::where('assigned_operator_id', $operatorId)->where('status', 'completed')->count();
        $totalSlaBreached = Ticket::where('assigned_operator_id', $operatorId)->whereIn('status', ['assigned', 'checking'])->where('sla_breached', true)->count();
        $totalAllTime     = Ticket::where('assigned_operator_id', $operatorId)->count();

        $tickets = Ticket::with(['asset', 'priority', 'creator'])
            ->where('assigned_operator_id', $operatorId)
            ->whereIn('status', ['assigned', 'checking'])
            ->orderByDesc('sla_breached')
            ->latest()
            ->paginate(10);

        return view('operator.dashboard', compact(
            'totalAssigned',
            'totalCompleted',
            'totalSlaBreached',
            'totalAllTime',
            'tickets'
        ));
    }
}
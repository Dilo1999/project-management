<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Compute total seconds the user was "on" the project (status On or Ticked Task) from status history.
     */
    private function projectSecondsOn(Project $project): int
    {
        $startTs = Carbon::parse($project->start_date)->startOfDay()->timestamp;
        $endTs = Carbon::parse($project->end_date)->endOfDay()->timestamp;
        $changes = $project->statusChanges()->orderBy('changed_at')->get();

        if ($changes->isEmpty()) {
            $status = $project->status;
            if (in_array($status, ['On', 'Ticked Task'], true)) {
                return max(0, $endTs - $startTs);
            }
            return 0;
        }

        $total = 0;
        foreach ($changes as $i => $change) {
            $segStart = $i === 0 ? $startTs : Carbon::parse($changes[$i - 1]->changed_at)->timestamp;
            $segEnd = Carbon::parse($change->changed_at)->timestamp;
            $segStart = max($segStart, $startTs);
            $segEnd = min($segEnd, $endTs);
            if ($segStart < $segEnd && in_array($change->from_status, ['On', 'Ticked Task'], true)) {
                $total += $segEnd - $segStart;
            }
        }

        $last = $changes->last();
        $lastStart = Carbon::parse($last->changed_at)->timestamp;
        $lastStart = max($lastStart, $startTs);
        if ($lastStart < $endTs && in_array($last->to_status, ['On', 'Ticked Task'], true)) {
            $total += $endTs - $lastStart;
        }

        return $total;
    }

    /**
     * Format seconds as ['days' => int, 'hours' => int, 'minutes' => int] for display.
     */
    private function formatDuration(int $seconds): array
    {
        $seconds = max(0, $seconds);
        $days = (int) floor($seconds / 86400);
        $hours = (int) floor(($seconds % 86400) / 3600);
        $minutes = (int) floor(($seconds % 3600) / 60);
        return ['days' => $days, 'hours' => $hours, 'minutes' => $minutes];
    }
    /**
     * User work report index: list of users (super admin only). Click a user to see details.
     */
    public function userWork()
    {
        $users = User::whereNotNull('approved_at')
            ->orderBy('name')
            ->get();

        $counts = [];
        foreach ($users as $u) {
            $counts[$u->id] = [
                'projects' => Project::where('assigned_to', $u->name)->count(),
                'tickets' => Ticket::where('assigned_to_user_id', $u->id)->count(),
            ];
        }

        return view('reports.user-work', compact('users', 'counts'));
    }

    /**
     * Show one user's projects and tasks on a separate page (super admin only).
     */
    public function userWorkShow(User $user)
    {
        if ($user->approved_at === null) {
            abort(404);
        }

        $projects = Project::with('statusChanges')
            ->where('assigned_to', $user->name)
            ->orderBy('start_date')
            ->get();

        $tickets = Ticket::with('assignedBy:id,name')
            ->where('assigned_to_user_id', $user->id)
            ->orderByDesc('created_at')
            ->get(['id', 'description', 'status', 'note', 'done_at', 'created_at', 'assigned_by_user_id', 'project_id']);

        $projectDurations = [];
        foreach ($projects as $project) {
            $projectDurations[$project->id] = $this->formatDuration($this->projectSecondsOn($project));
        }

        $now = Carbon::now();
        $ticketDurations = [];
        foreach ($tickets as $ticket) {
            $end = $ticket->done_at ? Carbon::parse($ticket->done_at) : $now;
            $start = $ticket->created_at ? Carbon::parse($ticket->created_at) : $now;
            $sec = max(0, $end->timestamp - $start->timestamp);
            $ticketDurations[$ticket->id] = $this->formatDuration((int) $sec);
        }

        return view('reports.user-work-show', compact('user', 'projects', 'tickets', 'projectDurations', 'ticketDurations'));
    }
}

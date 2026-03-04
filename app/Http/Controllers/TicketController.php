<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectStatusChange;
use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function create()
    {
        $users = User::orderBy('name')->get(['id', 'name']);

        return view('book-ticket', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'assigned_to_user_id' => ['required', 'exists:users,id'],
            'description' => ['required', 'string', 'max:2000'],
        ]);

        Ticket::create([
            'assigned_to_user_id' => $validated['assigned_to_user_id'],
            'assigned_by_user_id' => Auth::id(),
            'description' => $validated['description'],
        ]);

        return redirect()->route('book-ticket')->with('success', 'Emergency task assigned successfully.');
    }

    public function myMessages()
    {
        $tickets = Ticket::with(['assignedBy', 'project'])
            ->where('assigned_to_user_id', Auth::id())
            ->orderByDesc('created_at')
            ->get();

        return view('my-messages', compact('tickets'));
    }

    public function accept(Ticket $ticket)
    {
        if ($ticket->assigned_to_user_id !== Auth::id()) {
            abort(403);
        }

        $assignee = $ticket->assignedTo;
        $project = Project::with('statusChanges')
            ->where('assigned_to', $assignee->name)
            ->where('status', 'On')
            ->first();

        if ($project) {
            $changeDate = Carbon::now();

            ProjectStatusChange::create([
                'project_id' => $project->id,
                'from_status' => 'On',
                'to_status' => 'Ticked Task',
                'changed_at' => $changeDate,
            ]);
            $project->update([
                'status' => 'Ticked Task',
                'status_changed_at' => $changeDate,
                'status_before_pause' => 'On',
            ]);
            $ticket->update(['status' => 'accepted', 'project_id' => $project->id]);
        } else {
            $ticket->update(['status' => 'accepted']);
        }

        return redirect()->route('my-messages')->with('success', 'Ticket accepted.');
    }

    public function reject(Ticket $ticket)
    {
        if ($ticket->assigned_to_user_id !== Auth::id()) {
            abort(403);
        }
        $ticket->update(['status' => 'rejected']);
        return redirect()->route('my-messages')->with('success', 'Ticket cancelled.');
    }

    public function markDone(Request $request, Ticket $ticket)
    {
        if ($ticket->assigned_to_user_id !== Auth::id()) {
            abort(403);
        }
        if ($ticket->status !== 'accepted') {
            return redirect()->route('my-messages')->with('error', 'Only accepted tickets can be marked done.');
        }

        $validated = $request->validate([
            'note' => ['required', 'string', 'max:2000'],
        ]);

        $ticket->update([
            'note' => $validated['note'],
            'done_at' => now(),
            'status' => 'done',
        ]);

        if ($ticket->project_id) {
            $project = Project::find($ticket->project_id);
            if ($project && $project->status === 'Ticked Task') {
                $entryChange = ProjectStatusChange::where('project_id', $project->id)
                    ->where('to_status', 'Ticked Task')
                    ->orderByDesc('changed_at')
                    ->first();

                $endDateUpdate = null;
                $pendingSecondsUpdate = null;
                if ($entryChange) {
                    $entryAt = Carbon::parse($entryChange->changed_at);
                    $exitAt = Carbon::now();
                    $durationSeconds = $entryAt->diffInSeconds($exitAt);
                    if ($durationSeconds > 0) {
                        // Accumulate emergency task duration into the project's pending buffer.
                        $pending = (int) ($project->pending_extension_seconds ?? 0);
                        $pending += $durationSeconds;

                        // Convert complete 24h chunks into days, keep remainder seconds.
                        $daysToAdd = intdiv($pending, 24 * 60 * 60);
                        $remainder = $pending % (24 * 60 * 60);

                        if ($daysToAdd >= 1) {
                            $endDateUpdate = Carbon::parse($project->end_date)->addDays($daysToAdd);
                        }
                        $pendingSecondsUpdate = $remainder;
                    }
                }

                $changeDate = Carbon::now();

                ProjectStatusChange::create([
                    'project_id' => $project->id,
                    'from_status' => 'Ticked Task',
                    'to_status' => 'On',
                    'changed_at' => $changeDate,
                ]);
                $updateData = [
                    'status' => 'On',
                    'status_changed_at' => $changeDate,
                    'status_before_pause' => null,
                ];
                if ($endDateUpdate) {
                    $updateData['end_date'] = $endDateUpdate;
                }
                if (!is_null($pendingSecondsUpdate)) {
                    $updateData['pending_extension_seconds'] = $pendingSecondsUpdate;
                }
                $project->update($updateData);
            }
        }

        return redirect()->route('my-messages')->with('success', 'Task completed. Note added.');
    }
}

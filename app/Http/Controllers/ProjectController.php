<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectStatusChange;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProjectController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $projects = Project::with(['statusChanges', 'tickets.assignedBy'])
            ->orderBy('assigned_to')
            ->orderBy('start_date')
            ->get();
        $projectsGrouped = $projects->groupBy('assigned_to');

        $chartDataByAssignee = $this->buildGanttChartDataByAssignee($projectsGrouped);

        return view('home', compact('user', 'projects', 'chartDataByAssignee', 'projectsGrouped'));
    }

    public function myProjects()
    {
        $user = Auth::user();
        $projects = Project::with(['statusChanges', 'tickets.assignedBy'])
            ->where('assigned_to', $user->name)
            ->orderBy('start_date')
            ->get();
        $projectsGrouped = $projects->groupBy('assigned_to');
        $chartDataByAssignee = $this->buildGanttChartDataByAssignee($projectsGrouped);

        return view('my-projects', compact('user', 'projects', 'chartDataByAssignee', 'projectsGrouped'));
    }

    private function buildGanttChartDataByAssignee($projectsGrouped): array
    {
        $result = [];

        foreach ($projectsGrouped as $assignedTo => $projects) {
            $data = [];

            foreach ($projects as $project) {
                $startTs = Carbon::parse($project->start_date)->timestamp * 1000;
                $endTs = Carbon::parse($project->end_date)->endOfDay()->timestamp * 1000;
                $changes = $project->statusChanges;

                // For ticket-related segments, attach the latest ticket info (from & description).
                $latestTicket = $project->tickets
                    ? $project->tickets->loadMissing('assignedBy')
                        ->sortByDesc('created_at')
                        ->first()
                    : null;
                $ticketFrom = $latestTicket && $latestTicket->assignedBy ? $latestTicket->assignedBy->name : null;
                $ticketDescription = $latestTicket ? $latestTicket->description : null;

                if ($changes->isEmpty()) {
                    $data[] = [
                        'x' => $project->name,
                        'y' => [$startTs, $endTs],
                        'fillColor' => Project::STATUS_COLORS[$project->status] ?? '#D3D3D3',
                        'status' => $project->status,
                        'ticketFrom' => $project->status === 'Ticked Task' ? $ticketFrom : null,
                        'ticketDescription' => $project->status === 'Ticked Task' ? $ticketDescription : null,
                    ];
                    continue;
                }

                foreach ($changes as $i => $change) {
                    $changedTs = Carbon::parse($change->changed_at)->timestamp * 1000;
                    $segStart = $i === 0 ? $startTs : Carbon::parse($changes[$i - 1]->changed_at)->timestamp * 1000;
                    $segEnd = $changedTs;

                    if ($segStart >= $endTs || $segEnd <= $startTs) {
                        continue;
                    }
                    $segStart = max($segStart, $startTs);
                    $segEnd = min($segEnd, $endTs);
                    if ($segStart >= $segEnd) {
                        continue;
                    }

                    $color = Project::STATUS_COLORS[$change->from_status] ?? '#D3D3D3';
                    $data[] = [
                        'x' => $project->name,
                        'y' => [$segStart, $segEnd],
                        'fillColor' => $color,
                        'status' => $change->from_status,
                        'ticketFrom' => $change->from_status === 'Ticked Task' ? $ticketFrom : null,
                        'ticketDescription' => $change->from_status === 'Ticked Task' ? $ticketDescription : null,
                    ];
                }

                $lastChange = $changes->last();
                $lastChangedTs = Carbon::parse($lastChange->changed_at)->timestamp * 1000;
                $segmentStart = max($lastChangedTs, $startTs);
                if ($segmentStart < $endTs) {
                    $statusColor = Project::STATUS_COLORS[$lastChange->to_status] ?? '#D3D3D3';
                    $data[] = [
                        'x' => $project->name,
                        'y' => [$segmentStart, $endTs],
                        'fillColor' => $statusColor,
                        'status' => $lastChange->to_status,
                        'ticketFrom' => $lastChange->to_status === 'Ticked Task' ? $ticketFrom : null,
                        'ticketDescription' => $lastChange->to_status === 'Ticked Task' ? $ticketDescription : null,
                    ];
                }
            }

            $result[$assignedTo] = $data;
        }

        return $result;
    }

    private function addWaitingForApprovalDaysToEndDate(Project $project): void
    {
        $entryChange = ProjectStatusChange::where('project_id', $project->id)
            ->where('to_status', 'Waiting For Approval')
            ->orderByDesc('changed_at')
            ->first();

        if (!$entryChange) {
            return;
        }

        $entryDate = Carbon::parse($entryChange->changed_at);
        $exitDate = Carbon::now();
        $secondsInWaiting = $entryDate->diffInSeconds($exitDate);

        // Add full duration (seconds) into the project's pending buffer.
        $pending = (int) ($project->pending_extension_seconds ?? 0);
        $pending += $secondsInWaiting;

        // Convert complete 24h chunks into whole days, keep remainder in the buffer.
        $daysToAdd = intdiv($pending, 24 * 60 * 60);
        $remainder = $pending % (24 * 60 * 60);

        if ($daysToAdd >= 1) {
            $project->end_date = Carbon::parse($project->end_date)->addDays($daysToAdd);
        }

        $project->pending_extension_seconds = $remainder;
        $project->save();
    }

    private function addDaysInOnToProjectEndDate(Project $projectToExtend, iterable $projectsBeingPaused): void
    {
        $totalSeconds = 0;
        $now = Carbon::now();

        foreach ($projectsBeingPaused as $other) {
            $entryChange = ProjectStatusChange::where('project_id', $other->id)
                ->where('to_status', 'On')
                ->orderByDesc('changed_at')
                ->first();

            if ($entryChange) {
                $entryAt = Carbon::parse($entryChange->changed_at);
                $totalSeconds += $entryAt->diffInSeconds($now);
            }
        }

        if ($totalSeconds > 0) {
            // Aggregate exact duration spent "On" into the target project's buffer.
            $pending = (int) ($projectToExtend->pending_extension_seconds ?? 0);
            $pending += $totalSeconds;

            // Convert complete 24h chunks into days, keep remainder seconds.
            $daysToAdd = intdiv($pending, 24 * 60 * 60);
            $remainder = $pending % (24 * 60 * 60);

            if ($daysToAdd >= 1) {
                $projectToExtend->end_date = Carbon::parse($projectToExtend->end_date)->addDays($daysToAdd);
            }

            $projectToExtend->pending_extension_seconds = $remainder;
            $projectToExtend->save();
        }
    }

    private function ensureSingleOnPerAssignee(Project $project, string $newStatus): void
    {
        if ($newStatus !== 'On') {
            return;
        }

        $otherOnProjects = Project::where('assigned_to', $project->assigned_to)
            ->where('status', 'On')
            ->where('id', '!=', $project->id)
            ->get();

        $changeDate = Carbon::now();

        $this->addDaysInOnToProjectEndDate($project, $otherOnProjects);

        foreach ($otherOnProjects as $other) {
            ProjectStatusChange::create([
                'project_id' => $other->id,
                'from_status' => 'On',
                'to_status' => 'Pause',
                'changed_at' => $changeDate,
            ]);
            $other->update([
                'status' => 'Pause',
                'status_changed_at' => $changeDate,
                'status_before_pause' => 'On',
            ]);
        }
    }

    public function create()
    {
        $user = Auth::user();
        return view('add-project', compact('user'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'status' => ['required', Rule::in(Project::STATUS_OPTIONS)],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        $project = Project::create([
            'user_id' => $user->id,
            'assigned_to' => $user->name,
            ...$validated,
        ]);

        ProjectStatusChange::create([
            'project_id' => $project->id,
            'from_status' => $validated['status'],
            'to_status' => $validated['status'],
            'changed_at' => Carbon::parse($project->start_date)->startOfDay(),
        ]);

        $this->ensureSingleOnPerAssignee($project, $validated['status']);

        return redirect()->route('home')->with('success', 'Project created successfully.');
    }

    public function edit(Project $project)
    {
        $user = Auth::user();
        $isCreator = $project->user_id === $user->id;
        $isAssignee = $project->assigned_to === $user->name;
        if (!$isCreator && !$isAssignee) {
            abort(403);
        }

        $user = Auth::user();
        $users = User::whereIn('role', [User::ROLE_DEVELOPER, User::ROLE_DESIGNER, User::ROLE_SUPER_ADMIN])
            ->whereNotNull('approved_at')
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('edit-project', compact('user', 'users', 'project'));
    }

    public function update(Request $request, Project $project)
    {
        $user = Auth::user();
        $isCreator = $project->user_id === $user->id;
        $isAssignee = $project->assigned_to === $user->name;
        if (!$isCreator && !$isAssignee) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'assigned_to' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'status' => ['required', Rule::in(Project::STATUS_OPTIONS)],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        $oldStatus = $project->status;
        $newStatus = $validated['status'];

        if ($oldStatus === 'Waiting For Approval' && $newStatus !== 'Waiting For Approval') {
            $this->addWaitingForApprovalDaysToEndDate($project);
            $validated['end_date'] = Carbon::parse($project->fresh()->end_date)->format('Y-m-d');
        }

        $project->update($validated);

        $this->ensureSingleOnPerAssignee($project, $newStatus);

        return redirect()->route('home')->with('success', 'Project updated successfully.');
    }

    public function updateStatus(Request $request, Project $project)
    {
        $user = Auth::user();
        $isCreator = $project->user_id === $user->id;
        $isAssignee = $project->assigned_to === $user->name;
        if (!$isCreator && !$isAssignee) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => ['required', Rule::in(Project::STATUS_OPTIONS)],
        ]);

        $newStatus = $validated['status'];
        $oldStatus = $project->status;

        if ($newStatus === 'On') {
            if ($oldStatus === 'Waiting For Approval') {
                $this->addWaitingForApprovalDaysToEndDate($project);
            }

            $otherOnProjects = Project::where('assigned_to', $project->assigned_to)
                ->where('status', 'On')
                ->where('id', '!=', $project->id)
                ->get();

            $changeDate = Carbon::now();

            $this->addDaysInOnToProjectEndDate($project, $otherOnProjects);

            foreach ($otherOnProjects as $other) {
                ProjectStatusChange::create([
                    'project_id' => $other->id,
                    'from_status' => 'On',
                    'to_status' => 'Pause',
                    'changed_at' => $changeDate,
                ]);
                $other->update([
                    'status' => 'Pause',
                    'status_changed_at' => $changeDate,
                    'status_before_pause' => 'On',
                ]);
            }

            $targetStatus = $project->status_before_pause ?? 'On';
            $fromStatus = $oldStatus;

            ProjectStatusChange::create([
                'project_id' => $project->id,
                'from_status' => $fromStatus,
                'to_status' => $targetStatus,
                'changed_at' => $changeDate,
            ]);

            $project->update([
                'status' => $targetStatus,
                'status_changed_at' => $changeDate,
                'status_before_pause' => null,
            ]);
        } else {
            if ($oldStatus === 'Waiting For Approval') {
                $this->addWaitingForApprovalDaysToEndDate($project);
            }

            $changeDate = Carbon::now();
            ProjectStatusChange::create([
                'project_id' => $project->id,
                'from_status' => $oldStatus,
                'to_status' => $newStatus,
                'changed_at' => $changeDate,
            ]);

            $project->update([
                'status' => $newStatus,
                'status_changed_at' => $changeDate,
            ]);
        }

        if ($request->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return redirect()->back()->with('success', 'Status updated.');
    }

    public function destroy(Project $project)
    {
        $user = Auth::user();
        $isCreator = $project->user_id === $user->id;
        $isAssignee = $project->assigned_to === $user->name;
        if (!$isCreator && !$isAssignee) {
            return redirect()->route('home')->with('error', 'You do not have permission to delete this project.');
        }

        $project->delete();

        return redirect()->route('home')->with('success', 'Project deleted successfully.');
    }
}

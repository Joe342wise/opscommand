<x-layouts.app title="{{ $incident->title }} - OpsCommand">
    <div class="max-w-[1600px]">
        <div class="mb-6">
            <div class="flex items-center gap-2 text-sm text-outline mb-2">
                <a href="{{ route('incidents.index') }}" class="hover:text-primary">Incidents</a>
                <span>/</span>
                <span class="text-on-surface">{{ $incident->title }}</span>
            </div>
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-semibold text-on-surface">{{ $incident->title }}</h1>
                    <p class="text-sm text-outline mt-0.5">Created {{ $incident->created_at->diffForHumans() }} by {{ $incident->createdBy->name ?? 'Unknown' }}</p>
                </div>
                <div class="flex gap-2">
                    <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-medium
                        {{ match($incident->severity) {
                            'P1' => 'bg-danger-rose/20 text-danger-rose',
                            'P2' => 'bg-warning-amber/20 text-warning-amber',
                            'P3' => 'bg-primary-container/20 text-primary',
                            'P4' => 'bg-success-emerald/20 text-success-emerald',
                            default => 'bg-surface-container-high text-outline',
                        } }}">
                        {{ $incident->severity }}
                    </span>
                    <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-medium
                        {{ match($incident->status) {
                            'resolved' => 'bg-success-emerald/20 text-success-emerald',
                            'investigating' => 'bg-warning-amber/20 text-warning-amber',
                            'in_progress' => 'bg-primary-container/20 text-primary',
                            'open' => 'bg-danger-rose/20 text-danger-rose',
                            'closed' => 'bg-surface-container-high text-outline',
                            default => 'bg-surface-container-high text-outline',
                        } }}">
                        {{ ucfirst(str_replace('_', ' ', $incident->status)) }}
                    </span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-6">
            <div class="col-span-2 space-y-6">
                <div class="bg-surface-container rounded-xl border border-outline-variant p-6">
                    <h2 class="text-sm font-semibold text-on-surface mb-3">Details</h2>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-outline">Owner</span>
                            <p class="text-on-surface mt-0.5">{{ $incident->owner->name ?? 'Unassigned' }}</p>
                        </div>
                        <div>
                            <span class="text-outline">Service</span>
                            <p class="text-on-surface mt-0.5">{{ $incident->service->name ?? '—' }}</p>
                        </div>
                        <div>
                            <span class="text-outline">Resolved At</span>
                            <p class="text-on-surface mt-0.5">{{ $incident->resolved_at?->format('M d, Y H:i') ?? '—' }}</p>
                        </div>
                        <div>
                            <span class="text-outline">Last Updated</span>
                            <p class="text-on-surface mt-0.5">{{ $incident->updated_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @if ($incident->description)
                        <div class="mt-4 pt-4 border-t border-outline-variant">
                            <span class="text-outline text-sm">Description</span>
                            <p class="text-on-surface text-sm mt-1">{{ $incident->description }}</p>
                        </div>
                    @endif
                    @if ($incident->activities->count())
                        <div class="mt-4 pt-4 border-t border-outline-variant">
                            <span class="text-outline text-sm">Linked Activities</span>
                            <div class="mt-1 space-y-1">
                                @foreach ($incident->activities as $activity)
                                    <a href="{{ route('activities.show', $activity) }}" class="block text-sm text-primary hover:underline">{{ $activity->title }}</a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <div class="bg-surface-container rounded-xl border border-outline-variant p-6">
                    <h2 class="text-sm font-semibold text-on-surface mb-4">Status Updates</h2>
                    @forelse ($incident->updates as $update)
                        <div class="flex gap-3 {{ !$loop->last ? 'mb-4 pb-4 border-b border-outline-variant' : '' }}">
                            <div class="w-8 h-8 rounded-full bg-surface-container-high flex items-center justify-center text-xs text-outline shrink-0">
                                {{ substr($update->updatedBy->name ?? 'U', 0, 1) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-medium text-on-surface">{{ $update->updatedBy->name ?? 'Unknown' }}</span>
                                    @if ($update->previous_status && $update->new_status)
                                        <span class="text-xs text-outline">changed status from</span>
                                        <span class="text-xs font-medium text-on-surface">{{ ucfirst(str_replace('_', ' ', $update->previous_status)) }}</span>
                                        <span class="text-xs text-outline">to</span>
                                        <span class="text-xs font-medium text-on-surface">{{ ucfirst(str_replace('_', ' ', $update->new_status)) }}</span>
                                    @endif
                                </div>
                                @if ($update->summary)
                                    <p class="text-sm text-outline mt-1">{{ $update->summary }}</p>
                                @endif
                                <p class="text-xs text-outline mt-1">{{ $update->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-outline">No status updates yet.</p>
                    @endforelse
                </div>

                <div class="bg-surface-container rounded-xl border border-outline-variant p-6">
                    <h2 class="text-sm font-semibold text-on-surface mb-4">Investigation Notes</h2>
                    @forelse ($incident->investigationNotes as $note)
                        <div class="p-3 bg-surface-container-low rounded-lg {{ !$loop->last ? 'mb-3' : '' }}">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-sm font-medium text-on-surface">{{ $note->createdBy->name ?? 'Unknown' }}</span>
                                <span class="text-xs text-outline">{{ $note->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-sm text-outline">{{ $note->note }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-outline">No investigation notes yet.</p>
                    @endforelse
                </div>

                @if ($incident->resolutionRecord)
                    <div class="bg-surface-container rounded-xl border border-outline-variant p-6">
                        <h2 class="text-sm font-semibold text-on-surface mb-4">Resolution Record</h2>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-outline">Resolved By</span>
                                <p class="text-on-surface mt-0.5">{{ $incident->resolutionRecord->resolvedBy->name ?? 'Unknown' }}</p>
                            </div>
                            <div>
                                <span class="text-outline">Resolved At</span>
                                <p class="text-on-surface mt-0.5">{{ $incident->resolutionRecord->created_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                        <div class="mt-3 space-y-2 text-sm">
                            <div>
                                <span class="text-outline">Summary</span>
                                <p class="text-on-surface mt-0.5">{{ $incident->resolutionRecord->summary }}</p>
                            </div>
                            @if ($incident->resolutionRecord->root_cause)
                                <div>
                                    <span class="text-outline">Root Cause</span>
                                    <p class="text-on-surface mt-0.5">{{ $incident->resolutionRecord->root_cause }}</p>
                                </div>
                            @endif
                            @if ($incident->resolutionRecord->corrective_action)
                                <div>
                                    <span class="text-outline">Corrective Action</span>
                                    <p class="text-on-surface mt-0.5">{{ $incident->resolutionRecord->corrective_action }}</p>
                                </div>
                            @endif
                            @if ($incident->resolutionRecord->preventive_action)
                                <div>
                                    <span class="text-outline">Preventive Action</span>
                                    <p class="text-on-surface mt-0.5">{{ $incident->resolutionRecord->preventive_action }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <div class="space-y-6">
                <div class="bg-surface-container rounded-xl border border-outline-variant p-6">
                    <h2 class="text-sm font-semibold text-on-surface mb-4">Update Status</h2>
                    <form method="POST" action="{{ route('incidents.update', $incident) }}" class="space-y-3">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="title" value="{{ $incident->title }}">
                        <input type="hidden" name="severity" value="{{ $incident->severity }}">
                        <input type="hidden" name="priority" value="{{ $incident->priority }}">
                        <input type="hidden" name="owner_id" value="{{ $incident->owner_id }}">
                        <select name="status" required
                                class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant rounded-lg text-sm text-on-surface focus:outline-none focus:ring-1 focus:ring-primary">
                            <option value="open" {{ $incident->status === 'open' ? 'selected' : '' }}>Open</option>
                            <option value="in_progress" {{ $incident->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="investigating" {{ $incident->status === 'investigating' ? 'selected' : '' }}>Investigating</option>
                            <option value="resolved" {{ $incident->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                            <option value="closed" {{ $incident->status === 'closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                        <textarea name="update_summary" rows="2" placeholder="Add a note about this change..."
                                  class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant rounded-lg text-sm text-on-surface placeholder:text-outline focus:outline-none focus:ring-1 focus:ring-primary"></textarea>
                        <button type="submit" class="w-full px-4 py-2 bg-primary-container text-on-primary-container rounded-lg text-sm font-medium hover:opacity-90 transition-opacity">
                            Update Status
                        </button>
                    </form>
                </div>

                @if (! $incident->resolutionRecord && ! in_array($incident->status, ['resolved', 'closed']))
                    <div class="bg-surface-container rounded-xl border border-outline-variant p-6">
                        <h2 class="text-sm font-semibold text-on-surface mb-4">Resolve Incident</h2>
                        <form method="POST" action="{{ route('incidents.resolve', $incident) }}" class="space-y-3">
                            @csrf
                            <textarea name="summary" rows="2" required placeholder="Resolution summary..."
                                      class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant rounded-lg text-sm text-on-surface placeholder:text-outline focus:outline-none focus:ring-1 focus:ring-primary"></textarea>
                            <textarea name="root_cause" rows="2" placeholder="Root cause..."
                                      class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant rounded-lg text-sm text-on-surface placeholder:text-outline focus:outline-none focus:ring-1 focus:ring-primary"></textarea>
                            <textarea name="corrective_action" rows="2" placeholder="Corrective action..."
                                      class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant rounded-lg text-sm text-on-surface placeholder:text-outline focus:outline-none focus:ring-1 focus:ring-primary"></textarea>
                            <textarea name="preventive_action" rows="2" placeholder="Preventive action..."
                                      class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant rounded-lg text-sm text-on-surface placeholder:text-outline focus:outline-none focus:ring-1 focus:ring-primary"></textarea>
                            <button type="submit" class="w-full px-4 py-2 bg-success-emerald text-white rounded-lg text-sm font-medium hover:opacity-90 transition-opacity">
                                Resolve Incident
                            </button>
                        </form>
                    </div>
                @endif

                <div class="bg-surface-container rounded-xl border border-outline-variant p-6">
                    <h2 class="text-sm font-semibold text-on-surface mb-4">Add Investigation Note</h2>
                    <livewire:incident.note-form :incidentId="$incident->id" :key="'note-'.$incident->id" />
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>

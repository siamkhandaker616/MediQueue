@if ($departments->count())
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($departments as $department)
            <a href="{{ route('departments.show', $department) }}"
               class="block bg-surface rounded-xl border border-brand-100 shadow-sm hover:shadow-md transition p-6">
                <div class="flex items-center justify-center w-12 h-12 rounded-lg bg-teal-50 text-teal-600 mb-4">
                    <i class="{{ $department->icon ?? 'fa-solid fa-stethoscope' }}"></i>
                </div>
                <h3 class="text-lg font-semibold text-ink">{{ $department->name }}</h3>
                <p class="text-sm text-muted mt-1 line-clamp-2">{{ $department->description }}</p>

                <div class="flex items-center justify-between mt-4 text-sm">
                    <span class="text-muted">{{ $department->active_doctors_count }} doctor(s)</span>
                    <span class="font-medium text-teal-700">{{ $department->feeRangeLabel() }}</span>
                </div>
            </a>
        @endforeach
    </div>

    <div class="mt-8">
        {{ $departments->links() }}
    </div>
@else
    <p class="text-muted text-center py-16">No departments matched your search.</p>
@endif

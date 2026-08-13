<x-layouts.mockup>
    <x-slot name="title">Reviews</x-slot>

    @php
        $reviews = [
            [
                'patient' => 'Ayesha Rahman', 'doctor' => 'Dr. Sabrina Rahman', 'date' => '8 Aug 2026',
                'overall' => 5, 'punctuality' => 5, 'communication' => 4, 'knowledge' => 5,
                'comment' => 'Very patient and thorough. Explained everything clearly.',
                'status' => 'pending',
            ],
            [
                'patient' => 'Tanvir Ahmed', 'doctor' => 'Dr. Farhan Chowdhury', 'date' => '6 Aug 2026',
                'overall' => 4, 'punctuality' => 4, 'communication' => 4, 'knowledge' => 4,
                'comment' => 'Good consultation. Waiting time was a bit long.',
                'status' => 'pending',
            ],
            [
                'patient' => 'Nusrat Jahan', 'doctor' => 'Dr. Sabrina Rahman', 'date' => '5 Aug 2026',
                'overall' => 5, 'punctuality' => 5, 'communication' => 5, 'knowledge' => 5,
                'comment' => 'Best doctor I have visited so far.',
                'status' => 'pending',
            ],
            [
                'patient' => 'Karim Uddin', 'doctor' => 'Dr. Tanvir Hasan', 'date' => '3 Aug 2026',
                'overall' => 3, 'punctuality' => 2, 'communication' => 3, 'knowledge' => 4,
                'comment' => 'Rushed consultation, felt short.',
                'status' => 'pending',
            ],
            [
                'patient' => 'Priya Das', 'doctor' => 'Dr. Sabrina Rahman', 'date' => '1 Aug 2026',
                'overall' => 5, 'punctuality' => 4, 'communication' => 5, 'knowledge' => 5,
                'comment' => 'Lovely bedside manner.',
                'status' => 'published',
            ],
        ];

        $tabs = [
            ['key' => 'pending', 'label' => 'Pending', 'count' => 4],
            ['key' => 'published', 'label' => 'Published', 'count' => 1],
            ['key' => 'removed', 'label' => 'Removed', 'count' => 0],
        ];

        function stars($n): string
        {
            $out = '';
            for ($i = 1; $i <= 5; $i++) {
                $filled = $i <= $n ? 'text-amber-400' : 'text-brand-200';
                $out .= '<svg class="h-4 w-4 '.$filled.'" fill="currentColor" viewBox="0 0 24 24"><path d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.563.563 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z"/></svg>';
            }
            return $out;
        }
    @endphp

    <div class="mb-6">
        <p class="text-sm text-muted">FR-19 | Patient feedback</p>
        <h1 class="text-2xl font-bold tracking-tight">Review moderation</h1>
    </div>

    <div class="mb-4 flex gap-2">
        @foreach ($tabs as $tab)
            <button class="{{ $tab['key'] === 'pending' ? 'bg-brand-500 text-white' : 'bg-surface text-muted border border-brand-200 hover:text-ink' }} inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold transition">
                {{ $tab['label'] }}
                <span class="rounded-full {{ $tab['key'] === 'pending' ? 'bg-white/25' : 'bg-brand-100 text-brand-700' }} px-1.5 text-xs">{{ $tab['count'] }}</span>
            </button>
        @endforeach
    </div>

    <div class="space-y-4">
        @foreach ($reviews as $review)
            <div class="card p-5">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="flex h-9 w-9 items-center justify-center rounded-full bg-brand-100 text-xs font-semibold text-brand-700">
                                {{ strtoupper(substr($review['patient'], 0, 2)) }}
                            </span>
                            <div>
                                <p class="font-semibold">{{ $review['patient'] }}</p>
                                <p class="text-xs text-muted">{{ $review['doctor'] }} · {{ $review['date'] }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="flex">{!! stars($review['overall']) !!}</div>
                        <span class="badge {{ $review['status'] === 'published' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-200' }}">
                            {{ ucfirst($review['status']) }}
                        </span>
                    </div>
                </div>

                <p class="mt-3 text-sm text-ink">"{{ $review['comment'] }}"</p>

                <div class="mt-3 flex flex-wrap gap-2 text-xs">
                    <span class="badge bg-brand-50 text-brand-700">Punctuality {!! stars($review['punctuality']) !!}</span>
                    <span class="badge bg-brand-50 text-brand-700">Communication {!! stars($review['communication']) !!}</span>
                    <span class="badge bg-brand-50 text-brand-700">Knowledge {!! stars($review['knowledge']) !!}</span>
                </div>

                @if ($review['status'] === 'pending')
                    <div class="mt-4 flex justify-end gap-2">
                        <button class="btn-outline !px-3 !py-1 !text-xs">Remove</button>
                        <button class="btn-primary !px-3 !py-1 !text-xs">Approve</button>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</x-layouts.mockup>

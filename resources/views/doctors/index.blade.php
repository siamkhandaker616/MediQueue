<x-app-layout>
<div class="max-w-6xl mx-auto px-4 py-8" x-data="doctorDirectory()">

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Find a Doctor</h1>
        <p class="text-gray-500 mt-1">Search by name, specialty, department, or rating.</p>
    </div>

    <div class="flex flex-col md:flex-row gap-4 mb-6">
        <input
            type="text"
            x-model="q"
            @input.debounce.400ms="search()"
            placeholder="Search by name or specialty..."
            class="flex-1 rounded-lg border-gray-300 shadow-sm focus:ring-teal-500 focus:border-teal-500"
        >

        <select x-model="departmentId" @change="search()" class="rounded-lg border-gray-300 shadow-sm focus:ring-teal-500 focus:border-teal-500">
            <option value="">All departments</option>
            @foreach ($departments as $department)
                <option value="{{ $department->id }}">{{ $department->name }}</option>
            @endforeach
        </select>

        <select x-model="minRating" @change="search()" class="rounded-lg border-gray-300 shadow-sm focus:ring-teal-500 focus:border-teal-500">
            <option value="">Any rating</option>
            <option value="4">4★ &amp; up</option>
            <option value="3">3★ &amp; up</option>
        </select>
    </div>

    <div id="doctor-grid">
        @include('doctors.partials.grid', ['doctors' => $doctors])
    </div>

</div>

<script>
function doctorDirectory() {
    return {
        q: '{{ request('q') }}',
        departmentId: '{{ request('department_id') }}',
        minRating: '{{ request('min_rating') }}',
        search() {
            const url = new URL('{{ route('doctors.index') }}');
            if (this.q) url.searchParams.set('q', this.q);
            if (this.departmentId) url.searchParams.set('department_id', this.departmentId);
            if (this.minRating) url.searchParams.set('min_rating', this.minRating);

            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(res => res.text())
                .then(html => {
                    document.getElementById('doctor-grid').innerHTML = html;
                    window.history.pushState({}, '', url);
                });
        }
    }
}
</script>
</x-app-layout>

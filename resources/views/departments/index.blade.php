@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8" x-data="departmentCatalogue()">

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Departments &amp; Specialties</h1>
        <p class="text-gray-500 mt-1">Browse our departments and find the right specialist.</p>
    </div>

    <div class="mb-6">
        <input
            type="text"
            x-model="q"
            @input.debounce.400ms="search()"
            placeholder="Search departments (e.g. Cardiology, Dermatology)..."
            class="w-full md:w-96 rounded-lg border-gray-300 shadow-sm focus:ring-teal-500 focus:border-teal-500"
        >
    </div>

    <div id="department-grid">
        @include('departments.partials.grid', ['departments' => $departments])
    </div>

</div>

<script>
function departmentCatalogue() {
    return {
        q: '{{ request('q') }}',
        search() {
            const url = new URL('{{ route('departments.index') }}');
            if (this.q) url.searchParams.set('q', this.q);

            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(res => res.text())
                .then(html => {
                    document.getElementById('department-grid').innerHTML = html;
                    window.history.pushState({}, '', url);
                });
        }
    }
}
</script>
@endsection
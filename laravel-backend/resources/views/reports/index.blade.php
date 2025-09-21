@extends('dashboard.dashLayout')

@section('content')
<div class="flex justify-center px-4">
    <div class="w-full max-w-6xl bg-white rounded-lg shadow p-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-4 sm:mb-0">Reports</h1>
            {{--<a href="{{ route('reports.create') }}" 
               class="inline-block px-5 py-2 bg-blue-600 text-white font-medium rounded-lg shadow hover:bg-blue-700 transition">
               + Create Report
            </a>--}}
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-200 rounded-lg">
                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-semibold">ID</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Type</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Description</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Status</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach ($reports as $report)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3 text-gray-800">{{ $report->id }}</td>
                        <td class="px-4 py-3 text-gray-800">{{ $report->type }}</td>
                        <td class="px-4 py-3 text-gray-600 text-justify">{{ $report->description }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-sm 
                                {{ $report->status === 'open' ? 'bg-green-100 text-green-700' : 
                                   ($report->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-200 text-gray-700') }}">
                                {{ ucfirst($report->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 flex items-center gap-2">
                            <a href="{{ route('reports.edit', $report->id) }}" 
                               class="px-3 py-1 bg-yellow-400 text-white rounded hover:bg-yellow-500 transition">
                               Edit
                            </a>
                            <form action="{{ route('reports.destroy', $report->id) }}" method="POST" onsubmit="return confirm('Delete this report?')" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

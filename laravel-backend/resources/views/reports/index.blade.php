@extends('dashboard.dashLayout')

@section('content')
<div class="container">
    <h1>Reports</h1>
    <a href="{{ route('reports.create') }}" class="btn btn-primary">Create Report</a>
    <table class="table mt-3">
        <thead>
            <tr>
                <th>ID</th>
                <th>Type</th>
                <th>Description</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($reports as $report)
            <tr>
                <td>{{ $report->id }}</td>
                <td>{{ $report->type }}</td>
                <td>{{ $report->description }}</td>
                <td>{{ $report->status }}</td>
                <td>
                    <a href="{{ route('reports.edit', $report->id) }}" class="btn btn-warning">Edit</a>
                    <form action="{{ route('reports.destroy', $report->id) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
@extends('layouts.admin')

@section('title', 'Karenderias Management')
@section('page-title', 'Karenderias Management')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-store me-2"></i>All Karenderias
                    </h5>
                    <span class="badge bg-primary fs-6">{{ $karenderias->total() }} karenderias</span>
                </div>
            </div>
            <div class="card-body">
                @if($karenderias->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Karenderia Details</th>
                                    <th>Owner</th>
                                    <th>Contact Information</th>
                                    <th>Status</th>
                                    <th>Registration Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($karenderias as $karenderia)
                                <tr>
                                    <td>
                                        <div>
                                            <strong class="text-primary">{{ $karenderia->name }}</strong>
                                            @if($karenderia->business_name && $karenderia->business_name != $karenderia->name)
                                                <br><small class="text-muted">Business: {{ $karenderia->business_name }}</small>
                                            @endif
                                            <br><small class="text-muted">{{ Str::limit($karenderia->description, 60) }}</small>
                                            <br><small class="text-muted">
                                                <i class="fas fa-map-marker-alt"></i> {{ $karenderia->address }}
                                            </small>
                                        </div>
                                    </td>
                                    <td>
                                        @if($karenderia->owner)
                                            <div>
                                                <strong>{{ $karenderia->owner->name }}</strong>
                                                <br><small class="text-muted">{{ $karenderia->owner->email }}</small>
                                            </div>
                                        @else
                                            <span class="text-muted">Owner not found</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div>
                                            @if($karenderia->phone)
                                                <i class="fas fa-phone"></i> {{ $karenderia->phone }}<br>
                                            @endif
                                            @if($karenderia->email)
                                                <i class="fas fa-envelope"></i> {{ $karenderia->email }}<br>
                                            @endif
                                            @if($karenderia->operating_hours)
                                                <small class="text-muted">
                                                    <i class="fas fa-clock"></i> 
                                                    {{ $karenderia->opening_time ? $karenderia->opening_time->format('H:i') : 'N/A' }} - 
                                                    {{ $karenderia->closing_time ? $karenderia->closing_time->format('H:i') : 'N/A' }}
                                                </small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($karenderia->status === 'approved')
                                            <span class="badge bg-success">
                                                <i class="fas fa-check-circle me-1"></i>Approved
                                            </span>
                                            @if($karenderia->approved_at)
                                                <br><small class="text-muted">{{ $karenderia->approved_at->format('M d, Y') }}</small>
                                            @endif
                                        @elseif($karenderia->status === 'pending')
                                            <span class="badge bg-warning">
                                                <i class="fas fa-clock me-1"></i>Pending
                                            </span>
                                        @elseif($karenderia->status === 'rejected')
                                            <span class="badge bg-danger">
                                                <i class="fas fa-times-circle me-1"></i>Rejected
                                            </span>
                                            @if($karenderia->rejected_at)
                                                <br><small class="text-muted">{{ $karenderia->rejected_at->format('M d, Y') }}</small>
                                            @endif
                                            @if($karenderia->rejection_reason)
                                                <br><small class="text-danger">{{ Str::limit($karenderia->rejection_reason, 30) }}</small>
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">{{ $karenderia->created_at->format('M d, Y') }}</span>
                                        <br><small class="text-muted">{{ $karenderia->created_at->diffForHumans() }}</small>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $karenderias->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-store fa-4x text-muted mb-3"></i>
                        <h4>No Karenderias Found</h4>
                        <p class="text-muted">No karenderia registrations at the moment.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
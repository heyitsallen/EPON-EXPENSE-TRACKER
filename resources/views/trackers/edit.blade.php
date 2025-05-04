@extends('layouts.app')

@section('content')
<div class="container mt-4">
    {{-- Edit Tracker --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header text-white" style="background-color: #004e00;">
            <h5 class="mb-0">Edit Tracker</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('trackers.update', $tracker->id) }}" method="POST">
                @csrf
                @method('PATCH')

                <div class="mb-3">
                    <label for="title" class="form-label">Tracker Title</label>
                    <input type="text" id="title" name="title" class="form-control" value="{{ old('title', $tracker->title) }}" required>
                    @error('title')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-success">Update</button>
                    <a href="{{ route('trackers.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Shared Users --}}
    @php
    $sharedUsers = $tracker->users->filter(fn($user) => $user->id !== auth()->id());
@endphp

<div class="card shadow-sm mb-4">
    <div class="card-header text-white" style="background-color: #004e00;">
        <h5 class="mb-0">Shared With</h5>
    </div>
    @if ($sharedUsers->isEmpty())
        <div class="card-body text-muted">
            This tracker is not shared with anyone yet.
        </div>
    @else
        <ul class="list-group list-group-flush">
            @foreach ($sharedUsers as $user)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <strong>{{ $user->name }}</strong><br>
                        <small>{{ $user->email }}</small>
                    </div>
                    <div class="d-flex align-items-center">
                        {{-- Update Access --}}
                        <form action="{{ route('trackers.updateAccess', [$tracker->id, $user->id]) }}" method="POST" class="me-2">
                            @csrf
                            @method('PATCH')
                            <select name="position" onchange="this.form.submit()" class="form-select form-select-sm">
                                <option value="full" {{ $user->pivot->position == 'full' ? 'selected' : '' }}>Full</option>
                                <option value="partial" {{ $user->pivot->position == 'partial' ? 'selected' : '' }}>Partial</option>
                                <option value="viewer" {{ $user->pivot->position == 'viewer' ? 'selected' : '' }}>Viewer</option>
                            </select>
                        </form>

                        {{-- Remove Access --}}
                        <form action="{{ route('trackers.removeAccess', [$tracker->id, $user->id]) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-outline-danger btn-sm" onclick="return confirm('Remove access for this user?')">Remove</button>
                        </form>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif
</div>


    {{-- Share Tracker --}}
    <div class="card shadow-sm">
        <div class="card-header text-white" style="background-color: #004e00;">
            <h5 class="mb-0">Share Tracker</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('trackers.share', $tracker->id) }}" class="row g-3 align-items-end">
                @csrf
                <div class="col-md-5">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="user@example.com" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Access Level</label>
                    <select name="position" class="form-select" required>
                        <option value="full">Full</option>
                        <option value="partial">Partial</option>
                        <option value="viewer">Viewer</option>
                    </select>
                </div>
                <div class="col-md-3 text-end">
                    <button type="submit" class="btn btn-success w-100">Share</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

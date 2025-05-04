@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>My Trackers</span>
        <a href="{{ route('trackers.create') }}" class="btn btn-primary btn-sm">+ New Tracker</a>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
{{-- OWNED TRACKERS --}}
<h5>Owned Trackers</h5>
@if($ownedTrackers->count())
    <table class="table table-bordered mb-4">
        <thead>
            <tr>
                <th>Title</th>
                <th>Actions</th>
                <th>Shared With</th>
                <th>Position</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ownedTrackers as $tracker)
                <tr>
                    {{-- Title with link to the Tracker Dashboard --}}
                    <td>
                        <a href="{{ route('trackers.dashboard', $tracker->id) }}" class="text-decoration-none">
                            {{ $tracker->title }}
                        </a>
                    </td>
                    <td>
                        <a href="{{ route('trackers.edit', $tracker->id) }}" class="btn btn-sm btn-warning">Edit</a>

                        <form action="{{ route('trackers.destroy', $tracker->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                    <td>
                        <ul class="list-unstyled mb-0">
                            @foreach($tracker->users as $user)
                                @if($user->pivot->position !== 'owner') {{-- Exclude the owner --}}
                                    <li class="d-flex align-items-center justify-content-between">
                                        {{ $user->email }}

                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </td>
                    <td>
                        <ul class="list-unstyled mb-0">
                            @foreach($tracker->users as $user)
                                @if($user->pivot->position !== 'owner') {{-- Exclude the owner --}}
                                    <li class="d-flex align-items-center justify-content-between">
                                    <strong>{{ $user->pivot->position }}</strong>

                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </td>
                    

                </tr>

            @endforeach
        </tbody>
    </table>
@else
    <p>No owned trackers yet.</p>
@endif


        {{-- SHARED TRACKERS --}}
<h5>Shared With Me</h5>
@if($sharedTrackers->count())
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Title</th>
                <th>My Role</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sharedTrackers as $tracker)
                <tr>
                    <td>
                        <a href="{{ route('trackers.dashboard', $tracker->id) }}" class="text-decoration-none">
                            {{ $tracker->title }}
                        </a>
                    </td>
                    <td>{{ $tracker->pivot->position }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <p>No shared trackers yet.</p>
@endif

    </div>
</div>
@endsection

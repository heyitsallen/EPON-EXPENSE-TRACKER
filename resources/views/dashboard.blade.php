@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Your Trackers</h2>
    <a href="{{ route('trackers.create') }}" class="btn btn-success mb-3">Create New Tracker</a>

    <ul class="list-group">
        @foreach($trackers as $tracker)
            <li class="list-group-item d-flex justify-content-between align-items-center">
                {{ $tracker->title }}
                <span class="badge bg-primary">{{ ucfirst($tracker->pivot->position) }}</span>
            </li>
        @endforeach
    </ul>
</div>
@endsection

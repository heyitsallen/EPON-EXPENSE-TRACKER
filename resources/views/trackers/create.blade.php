@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Create Tracker</h2>

    <form method="POST" action="{{ route('trackers.store') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label">Tracker Title</label>
            <input type="text" name="title" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Create</button>
        <a href="{{ route('trackers.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection

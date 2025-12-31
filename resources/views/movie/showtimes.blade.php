@extends('layouts.main')

@section('title', 'Upcoming Movies - TCA Cine')

@section('content')
<h2>Showtimes for: {{ $movie->title }}</h2>
@if (count($showtimes)>0)
<table>
    <thead>
        <tr>
            <th>Date</th>
            <th>Time</th>
            <th>Room</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($showtimes as $showtime)
        <tr>
            <td>{{ $showtime->show_date }}</td>
            <td>{{ $showtime->show_time }}</td>
            <td>{{ $showtime->room_id }}</td>
            <td><a href="/showtimes/{{ $showtime->id }}/seats">Select Seats</a></td>
        </tr>
        @endforeach
    </tbody>
</table>
@else
<p>No showtimes available for this movie.</p>
@endif
<div>
    <a href="{{ route('homepage') }}">Back to Homepage</a>
</div>
@endsection


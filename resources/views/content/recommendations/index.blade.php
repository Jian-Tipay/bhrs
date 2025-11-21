@extends('layouts/contentNavbarLayout')
@section('title', 'SLSU Student Dashboard')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm rounded-3 border-0">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">🏠 Recommended Properties</h4>
            <a href="{{ route('recommendations.refresh') }}" class="btn btn-light btn-sm">🔄 Refresh</a>
        </div>

        <div class="card-body">
            @if(empty($recommendations))
                <div class="text-center py-4">
                    <p class="text-muted mb-0">No recommendations yet. Try rating some properties first!</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Property ID</th>
                                <th>Title</th>
                                <th>Score</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recommendations as $item)
                                @php
                                    // Use the property from $item if available, else fallback to DB query
                                    $property = $item['title'] ?? null ? (object)$item : \App\Models\Property::find($item['property_id']);
                                @endphp
                                @if($property)
                                <tr>
                                    <td>{{ $property->property_id }}</td>
                                    <td>{{ $property->title }}</td>
                                    <td>{{ number_format($item['score'] ?? 0, 2) }}</td> {{-- safe fallback --}}
                                    <td>
                                        <a href="" class="btn btn-sm btn-outline-primary">
                                            View
                                        </a>
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

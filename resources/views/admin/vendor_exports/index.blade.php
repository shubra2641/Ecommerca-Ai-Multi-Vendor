@extends('admin.layout')
@section('content')
<div class="card modern-card">
    <div class="card-header d-flex justify-content-between">
        <div>{{ __('Vendor Exports') }}</div>
    </div>
    <div class="card-body">
        <table class="table">
            <thead><tr><th>ID</th><th>Vendor</th><th>Filename</th><th>Status</th><th>Completed</th><th></th></tr></thead>
            <tbody>
            @foreach($items as $it)
                <tr>
                    <td>{{ $it->id }}</td>
                    <td>{{ $it->vendor?->name }}</td>
                    <td>{{ $it->filename }}</td>
                    <td>{{ $it->status }}</td>
                    <td>{{ $it->completed_at?->format('Y-m-d H:i') }}</td>
                    <td>
                        @if($it->path)
                            <a href="{{ route('admin.vendor_exports.download', $it->id) }}" class="btn btn-sm btn-primary">Download</a>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        {{ $items->links() }}
    </div>
</div>
@endsection

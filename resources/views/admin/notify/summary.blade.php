<div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>{{ __('Interest Breakdown') }}</span>
    </div>
    <div class="card-body p-0">
        <table class="table table-sm mb-0">
            <thead>
                <tr>
                    <th>{{ __('Type') }}</th>
                    <th>{{ __('Active') }}</th>
                    <th>{{ __('Pending') }}</th>
                    <th>{{ __('Notified') }}</th>
                    <th>{{ __('Total') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($breakdown as $row)
                <tr>
                    <td>{{ ucfirst(str_replace('_',' ',$row['type'])) }}</td>
                    <td>{{ $row['active'] }}</td>
                    <td>{{ $row['pending'] }}</td>
                    <td>{{ $row['notified'] }}</td>
                    <td>{{ $row['total'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
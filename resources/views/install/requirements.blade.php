@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h3>System Requirements</h3>
                    <p>PHP Version: <strong>{{ $phpVersion }}</strong></p>
                    <ul>
                        @foreach($extensions as $ext => $ok)
                            <li class="mb-1">{{ $ext }}: <span class="badge {{ $ok ? 'bg-success' : 'bg-danger' }}">{{ $ok ? 'OK' : 'Missing' }}</span></li>
                        @endforeach
                    </ul>

                    <hr />
                    <h5>Filesystem Permissions</h5>
                    <p>Click the button below to verify writable paths required by the application.</p>
                    <button id="checkPerms" class="btn btn-outline-primary">Check Permissions</button>

                    <div id="permResults" class="mt-3"></div>

                    <div class="mt-4">
                        <a href="{{ route('install.database') }}" class="btn btn-primary">Next: Database</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
 </div>

@push('scripts')
<script>
document.getElementById('checkPerms').addEventListener('click', function(){
    var btn = this; btn.disabled = true; btn.innerText = 'Checking...';
    fetch('{{ route('install.permissions') }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), 'Accept': 'application/json' }})
    .then(res => res.json())
    .then(json => {
        btn.disabled = false; btn.innerText = 'Check Permissions';
        var out = '<ul class="list-group">';
        json.paths.forEach(function(p){
            out += '<li class="list-group-item d-flex justify-content-between align-items-center">';
            out += '<div><strong>'+p.path+'</strong><div class="small text-muted">exists: '+p.exists+', writable: '+p.writable+'</div></div>';
            out += '<span class="badge '+(p.writable? 'bg-success':'bg-danger')+'">'+(p.writable? 'Writable':'Not writable')+'</span>';
            out += '</li>';
        });
        out += '</ul>';
        document.getElementById('permResults').innerHTML = out;
    }).catch(err=>{ btn.disabled=false; btn.innerText='Check Permissions'; alert('Error checking permissions: '+err.message) });
});
</script>
@endpush

@endsection

@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4>Database Configuration</h4>
                    <p>Enter your database connection details and test the connection before creating the initial admin account.</p>

                    <form id="dbForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">DB Connection</label>
                                <input name="DB_CONNECTION" value="mysql" class="form-control" />
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">DB Host</label>
                                <input name="DB_HOST" value="127.0.0.1" class="form-control" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">DB Port</label>
                                <input name="DB_PORT" value="3306" class="form-control" />
                            </div>
                            <div class="col-md-8 mb-3">
                                <label class="form-label">DB Database</label>
                                <input name="DB_DATABASE" class="form-control" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">DB Username</label>
                                <input name="DB_USERNAME" class="form-control" />
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">DB Password</label>
                                <input name="DB_PASSWORD" type="password" class="form-control" />
                            </div>
                        </div>

                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" value="1" id="writeTest" checked>
                            <label class="form-check-label" for="writeTest">Perform lightweight write test (create/insert/drop temp table)</label>
                        </div>

                        <div class="d-flex gap-2">
                            <button id="saveDbBtn" type="button" class="btn btn-outline-secondary">Save DB Settings</button>
                            <button id="testDbBtn" type="button" class="btn btn-outline-primary">Test Connection</button>
                            <button id="proceedAdmin" type="button" class="btn btn-primary" disabled>Proceed to Create Admin</button>
                        </div>
                    </form>

                    <div id="dbResult" class="mt-3"></div>

                    <hr />

                    <div id="adminFormWrapper" style="display:none;">
                        <h5>Create Admin Account</h5>
                        <form method="POST" action="{{ route('install.createAdmin') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input name="name" class="form-control" required />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input name="email" type="email" class="form-control" required />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input name="password" type="password" class="form-control" required />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Confirm Password</label>
                                <input name="password_confirmation" type="password" class="form-control" required />
                            </div>
                            <button class="btn btn-success">Create Admin & Finish Installation</button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const q = sel => document.querySelector(sel);
q('#testDbBtn').addEventListener('click', function(){
    const form = document.getElementById('dbForm');
    const data = new FormData(form);
    const payload = {};
    data.forEach((v,k)=>payload[k]=v);
    const btn = this; btn.disabled = true; btn.innerText = 'Testing...';

    fetch('{{ route('install.testDb') }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), 'Accept': 'application/json', 'Content-Type': 'application/json' },
        body: JSON.stringify(Object.assign(payload, { write_test: document.getElementById('writeTest').checked }))
    }).then(r=>r.json()).then(json=>{
        btn.disabled=false; btn.innerText='Test Connection';
        if(json.success){
            document.getElementById('dbResult').innerHTML = '<div class="alert alert-success">'+json.message+'</div>';
            document.getElementById('proceedAdmin').disabled = false;
            // reveal admin form
            document.getElementById('adminFormWrapper').style.display = 'block';
        } else {
            document.getElementById('dbResult').innerHTML = '<div class="alert alert-danger">Connection failed: '+json.message+(json.code? ' (code: '+json.code+')':'')+'</div>';
        }
    }).catch(e=>{ btn.disabled=false; btn.innerText='Test Connection'; document.getElementById('dbResult').innerHTML = '<div class="alert alert-danger">Error: '+e.message+'</div>' });
});

q('#proceedAdmin').addEventListener('click', ()=>{ document.getElementById('adminFormWrapper').style.display='block'; window.scrollTo(0, document.getElementById('adminFormWrapper').offsetTop - 20); });

q('#saveDbBtn').addEventListener('click', function(){
    const form = document.getElementById('dbForm');
    const data = new FormData(form);
    const payload = {};
    data.forEach((v,k)=>payload[k]=v);
    const btn = this; btn.disabled = true; btn.innerText = 'Saving...';

    fetch('{{ route('install.saveDb') }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), 'Accept': 'application/json', 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    }).then(r=>r.json()).then(json=>{
        btn.disabled=false; btn.innerText='Save DB Settings';
        if(json.success){
            document.getElementById('dbResult').innerHTML = '<div class="alert alert-success">Saved DB settings. You can now test connection.</div>';
        } else {
            document.getElementById('dbResult').innerHTML = '<div class="alert alert-danger">Save failed: '+json.message+'</div>';
        }
    }).catch(e=>{ btn.disabled=false; btn.innerText='Save DB Settings'; document.getElementById('dbResult').innerHTML = '<div class="alert alert-danger">Error: '+e.message+'</div>' });
});
</script>
@endpush

@endsection

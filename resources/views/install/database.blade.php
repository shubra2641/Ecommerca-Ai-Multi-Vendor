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

                    <div id="adminFormWrapper" class="d-none">
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
@endsection
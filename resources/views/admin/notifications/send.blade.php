@extends('layouts.admin')

@section('content')
<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title">{{ __('Send Notification') }}</h1>
        <p class="page-description">{{ __('Send a notification to users or vendors') }}</p>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card modern-card">
            <div class="card-header d-flex align-items-center gap-2">
                <h3 class="card-title mb-0">{{ __('Send Notification') }}</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.notifications.send.store') }}" class="admin-form">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">{{ __('Target role') }}</label>
                        <select name="role" class="form-select" required>
                            <option value="vendor">{{ __('Vendors') }}</option>
                            <option value="user">{{ __('Users') }}</option>
                        </select>
                    </div>

                    @php($langs = $languages)
                    <ul class="nav nav-tabs" role="tablist">
                        @foreach($langs as $i => $lang)
                            <li class="nav-item" role="presentation"><button class="nav-link {{ $i==0? 'active':'' }}" id="tab-{{ $lang->code }}" data-bs-toggle="tab" data-bs-target="#panel-{{ $lang->code }}" type="button">{{ $lang->code }}</button></li>
                        @endforeach
                    </ul>
                    <div class="tab-content mt-3">
                        @foreach($langs as $i => $lang)
                            <div class="tab-pane fade {{ $i==0? 'show active':'' }}" id="panel-{{ $lang->code }}" role="tabpanel">
                                <div class="mb-3">
                                    <label class="form-label">{{ __('Title') }} ({{ $lang->code }})</label>
                                    <input type="text" name="title[{{ $lang->code }}]" class="form-control" />
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">{{ __('Message') }} ({{ $lang->code }})</label>
                                    <textarea name="message[{{ $lang->code }}]" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('Optional URL') }}</label>
                        <input type="url" name="url" class="form-control" placeholder="https://..." />
                    </div>

                    <button class="btn btn-primary">{{ __('Send') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@props(['languages'])
<div class="fixed top-0 right-0 px-6 py-4">
    <form action="{{ route('language.switch') }}" method="POST">
        @csrf
        <select name="lang" class="block w-full mt-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            @foreach ($languages as $language)
                <option value="{{ $language->code }}" {{ app()->getLocale() == $language->code ? 'selected' : '' }}>{{ $language->name }}</option>
            @endforeach
        </select>
        <button type="submit" class="lang-submit-btn">{{ __('Change') }}</button>
    </form>
</div>

@props(['items' => []])

<nav class="breadcrumb-nav" aria-label="{{ __('Breadcrumb navigation') }}">
    <ol class="breadcrumb">
        @foreach($items as $index => $item)
            <li class="breadcrumb-item {{ $loop->last ? 'active' : '' }}" {{ $loop->last ? 'aria-current="page"' : '' }}>
                @if($loop->last)
                    <span>{{ $item['title'] }}</span>
                @else
                    <a href="{{ $item['url'] }}" class="breadcrumb-link">
                        @if(isset($item['icon']))
                            <i class="{{ $item['icon'] }}" aria-hidden="true"></i>
                        @endif
                        <span>{{ $item['title'] }}</span>
                    </a>
                @endif
            </li>
        @endforeach
    </ol>
</nav>

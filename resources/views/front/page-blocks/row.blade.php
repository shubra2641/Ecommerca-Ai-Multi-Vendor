@if(isset($rowData) && $rowData)
<section class="pb-row-wrapper {{ $rowData['full'] ? 'pb-row-full' : '' }} {{ $rowData['extraClass'] }} {{ $rowData['dynamicClassString'] }}">
  <div class="{{ $rowData['full'] ? 'container-fluid' : 'container' }}">
    <div class="row pb-row g-{{ $rowData['gutter'] }} py-{{ $rowData['py'] }}">
      @foreach($rowData['children'] as $child)
        @includeIf('front.page-blocks.' . $child->type, ['block'=>$child])
      @endforeach
    </div>
  </div>
</section>
@endif
@if(!empty($pageBuilderCssPath))
  @push('styles')
    <link rel="stylesheet" href="{{ $pageBuilderCssPath }}" />
  @endpush
@endif

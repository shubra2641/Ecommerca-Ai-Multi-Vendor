<div class="col-md-{{ $pb_col_md }} col-lg-{{ $pb_col_lg }} pb-column {{ $pb_col_extra }}">
  <div class="pb-col">
  @foreach($pb_col_children as $child)
      @includeIf('front.page-blocks.' . $child->type, ['block'=>$child])
    @endforeach
  </div>
</div>

<template id="product-variations-data">{!! json_encode($productVariationsJson, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!}</template>
<noscript>
	<div class="alert alert-info small mb-2">{{ __('Variation management requires JavaScript. You can still edit base product fields and save.') }}</div>
</noscript>
<script src="{{ asset('admin/js/product-variations.js') }}" defer></script>
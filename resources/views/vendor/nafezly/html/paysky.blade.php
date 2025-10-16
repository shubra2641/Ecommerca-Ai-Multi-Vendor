<button type="button" class="btn btn-info " id="paysky_payment_id_btn" data-action="paysky-show">pay</button>
@if (strtoupper($data['PAYSKY_MODE']) == 'LIVE')
<script src="https://cube.paysky.io:6006/js/LightBox.js"></script>
@else
<script src="https://grey.paysky.io:9006/invchost/JS/LightBox.js"></script>
@endif

<script type="text/javascript">
function showLightBox() {
    Lightbox.Checkout.configure = {
        paymentMethodFromLightBox: "{{ $data['paymentMethodFromLightBox'] }}",
        MID: "{{ $data['MID'] }}",
        TID: "{{ $data['TID'] }}",
        AmountTrxn: "{{ $data['AmountTrxn'] }}",
        MerchantReference: "{{ $data['MerchantReference'] }}",
        TrxDateTime: "{{ $data['TrxDateTime'] }}",
        SecureHash: "{{ $data['SecureHash'] }}",
        completeCallback: function(data) {
            var final_url = mergeQueryParams("{{$data['callback_url']}}",data);
            window.location.href=final_url + '&js_status=completed';
        },
        errorCallback: function(data) {
            var final_url = mergeQueryParams("{{$data['callback_url']}}",data);
            window.location.href=final_url + '&js_status=error';
        },
        cancelCallback: function(data) {
            var final_url = mergeQueryParams("{{$data['callback_url']}}",data);
            window.location.href=final_url + '&js_status=canceled';
        }
    };

    Lightbox.Checkout.showLightbox();
}
function mergeQueryParams(url, newParams) {
    const urlObj = new URL(url);
    const params = urlObj.searchParams;
    for (const [key, value] of Object.entries(newParams)) {
        params.set(key, value);
    }
    return urlObj.toString();
}

document.addEventListener('click',function(e){
    const t=e.target.closest('[data-action="paysky-show"]');
    if(t){ showLightBox(); }
});
setTimeout(function(){
    const btn=document.getElementById('paysky_payment_id_btn');
    if(btn) showLightBox();
},1500);
</script>
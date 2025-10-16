'use strict';
// Replaces inline jQuery validation & phone formatting in users/form.blade.php
(function ($) {
    function formatPhone(val)
    {
        var digits = (val || '').replace(/\D/g,'');
        if (digits.length >= 6) {
            return digits.replace(/(\d{3})(\d{3})(\d{0,4}).*/, '($1) $2-$3'); }
        if (digits.length >= 3) {
            return digits.replace(/(\d{3})(\d{0,3}).*/, '($1) $2'); }
        return digits; }
    function bindFormatting()
    {
        $('#phone, #whatsapp').on('input', function () {
               this.value = formatPhone(this.value); }); }
    function bindValidation()
    {
        $('form').on('submit', function (e) {
            var isValid = true; var $f = $(this);
            $f.find('[required]').each(function () {
                if (!$(this).val()) {
                    $(this).addClass('is-invalid'); isValid = false; } else {
                    $(this).removeClass('is-invalid').addClass('is-valid'); } });
            var password = $('#password').val(); var confirm = $('#password_confirmation').val();
            if (password && password !== confirm) {
                $('#password_confirmation').addClass('is-invalid'); isValid = false; }
            if (!isValid) {
                e.preventDefault();
            }
        });
    }
    function init()
    {
        bindFormatting(); bindValidation(); }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init); } else {
        init();
        }
})(window.jQuery || window.$);

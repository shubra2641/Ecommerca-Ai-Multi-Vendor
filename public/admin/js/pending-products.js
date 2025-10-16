document.addEventListener('DOMContentLoaded',function () {
    document.querySelectorAll('.reject-delete-form').forEach(function (form) {
        const select = form.querySelector('.action-mode');
        const reasonWrapper = form.querySelector('.reject-fields');
        const reasonTextarea = reasonWrapper ? reasonWrapper.querySelector('textarea[name="reason"]') : null;
        const deleteWarning = form.querySelector('.delete-warning');
        function update()
        {
            if (!select) {
                return;
            }
            if (select.value === 'delete') {
                if (reasonTextarea) {
                    reasonTextarea.removeAttribute('required');}
                if (reasonWrapper) {
                    reasonWrapper.classList.add('d-none');}
                if (deleteWarning) {
                    deleteWarning.classList.remove('d-none');}
            } else {
                if (reasonTextarea) {
                    reasonTextarea.setAttribute('required','required');}
                if (reasonWrapper) {
                    reasonWrapper.classList.remove('d-none');}
                if (deleteWarning) {
                    deleteWarning.classList.add('d-none');}
            }
        }
        select && select.addEventListener('change',update);
        update();
    });
});

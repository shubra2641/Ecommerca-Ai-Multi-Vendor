// Extracted category tree toggle logic
(function () {
    document.addEventListener('DOMContentLoaded',function () {
        document.querySelectorAll('.js-toggle-node').forEach(function (btn) {
            btn.addEventListener('click',function () {
                var node = this.getAttribute('data-node');
                var icon = this.querySelector('i');
                var childRow = document.querySelector('tr.child-row[data-parent="' + node + '"]');
                if (childRow) {
                    var visible = !childRow.classList.contains('d-none');
                    if (visible) {
                        childRow.classList.add('d-none');
                        if (icon) {
                            icon.classList.remove('fa-minus'); icon.classList.add('fa-plus'); }
                    } else {
                        childRow.classList.remove('d-none');
                        if (icon) {
                            icon.classList.remove('fa-plus'); icon.classList.add('fa-minus'); }
                    }
                }
            });
        });
    });
})();

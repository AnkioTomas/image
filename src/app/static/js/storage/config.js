window.pageLoadFiles = [
    'Form',
];
window.pageOnLoad = function (loading) {
    let form = document.querySelector('#form');
    let typeSelect = document.querySelector('#storageType');
    let webdavTip = document.querySelector('#webdavTip');

    function toggleWebdavTip() {
        webdavTip.style.display = typeSelect.value === 'webdav' ? '' : 'none';
    }

    typeSelect.addEventListener('change', toggleWebdavTip);

    $.request.get('/api/storage/config', {}, function (data) {
        if (data.code === 200) {
            typeSelect.value = data.data.type || 'local';
            toggleWebdavTip();
        }
    });

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        $.request.postForm('/api/storage/config', { type: typeSelect.value }, function (data) {
            if (data.code === 200) {
                $.toaster.success(data.msg);
            } else {
                $.toaster.error(data.msg);
            }
        });
    });

    return false;
};

window.pageLoadFiles = [
    'Form',
];
window.pageOnLoad = function (loading) {
    $.form.manage('/manager/storage/config', '#form');

    return false;
};

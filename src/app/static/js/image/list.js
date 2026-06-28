window.pageLoadFiles = [
    'CardView',
    'DialogForm',
    "/components/fileUpload/File.js",
    "/components/fileUpload/FileUploader.js"
];
window.pageOnLoad = function (loading) {

    let cardView = new CardView('#cardView');
    cardView.load({
        uri: '/manager/image/list',
        template: `
            <img class="img-card-thumb" src="/i/{{hash}}" data-imagebox="gallery" alt="{{name}}" />
            <div class="img-card-info">
                <div class="img-card-name" title="{{name}}">{{name}}</div>
                <div class="img-card-meta">{{size}} · {{create_time}}</div>
                <div class="img-card-actions">
                    <mdui-button-icon icon="content_copy" class="action-copy" title="复制外链"></mdui-button-icon>
                    <mdui-button-icon icon="delete" class="action-delete" title="删除"></mdui-button-icon>
                </div>
            </div>`,
        columns: [
            {
                field: 'size',
                formatter: function (bytes) {
                    if (bytes === 0) return '0 B';
                    const k = 1024;
                    const sizes = ['B', 'KB', 'MB', 'GB'];
                    const i = Math.floor(Math.log(bytes) / Math.log(k));
                    return (bytes / Math.pow(k, i)).toFixed(1) + ' ' + sizes[i];
                },
            },
            {
                field:'create_time',
                formatter:function (value) {
                    return $.formatDateTime(new Date(value*1000))
                }
            }
        ],
        cardWidth: '180px',
        selectable: false,
        empty_msg: '暂无图片',
        page: true,
    });

    $('#cardView')
        .on('click', '.action-copy', function () {
            let index = $(this).closest('.card-view-item').data('index');
            let row = cardView.getRow(index);
            let url = location.origin + "/i/" + row.hash;
            $.copy(url);
            $.toaster.success('已复制外链');
        })
        .on('click', '.action-delete', function () {
            let index = $(this).closest('.card-view-item').data('index');
            let row = cardView.getRow(index);
            $.request.postForm('/manager/image/delete', { id: row.id }, function () {
                $.toaster.success('删除成功');
                cardView.reload(true);
            });
        })
        .on('click', '.img-card-thumb', function () {
            imagebox.init({ parent: document.querySelector('#cardView') });
        });

    $('#refresh').on('click', function () {
        cardView.reload(true);
    });


    $("#upload").on('click',function () {
        $.file.upload({
            accept: '.png,.jpeg,.jpg,.bmp',
            uploadEndpoint: '/manager/image/upload',
            uploadData: {},
            chunked: true,
            chunkSize: 1024 * 1024 * 2, // 2MB
            maxDirectSize: 10 * 1024 * 1024, // 10MB
            onSuccess: function (resp) {
                let data = resp.data;
                
                $.request.postForm('/manager/image/save',{
                    'file':data
                },function (res) {
                    cardView.reload(true);
                    $.toaster.success(res.msg);
                })
                
            },
            onError: null,
            onProgress: null
        });
    })

    window.pageOnUnLoad = function () {
        cardView.destroy();
    };
    return false;
};

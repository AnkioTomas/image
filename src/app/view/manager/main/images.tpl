<title id="title">图片管理 - {$title}</title>
<style id="style">
    .img-card-thumb {
        width: 100%;
        aspect-ratio: 1;
        object-fit: cover;
        border-radius: 8px;
        cursor: pointer;
    }

    .img-card-info {
        padding: 8px 4px 4px;
    }

    .img-card-name {
        font-size: 13px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .img-card-meta {
        font-size: 12px;
        color: var(--mdui-color-on-surface-variant);
        margin-top: 2px;
    }

    .img-card-actions {
        display: flex;
        justify-content: flex-end;
        gap: 4px;
        margin-top: 4px;
    }
</style>

<div id="container" class="container">
    <div class="row col-space16">
        <div class="col-xs12 title-large center-vertical mb-4">
            <mdui-icon name="photo_library" class="refresh mr-2"></mdui-icon>
            <span>图片管理</span>
        </div>
        <div class="col-xs12 d-flex mb-2">
            <mdui-button icon="cloud_upload" id="upload">上传图片</mdui-button>
            <div style="flex-grow: 1"></div>
            <mdui-button-icon icon="refresh" id="refresh"></mdui-button-icon>
        </div>
        <div class="col-xs12">
            <div id="cardView" class="w-100"></div>
        </div>
    </div>
</div>

<script id="script" src="/static/js/image/list.js?v={$__v}"></script>

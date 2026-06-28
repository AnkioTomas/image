<title id="title">存储配置 - {$title}</title>
<style id="style">

</style>

<div id="container" class="container">
    <div class="row col-space16">
        <div class="col-xs12 title-large center-vertical mb-4">
            <mdui-icon name="settings" class="refresh mr-2"></mdui-icon>
            <span>存储配置</span>
        </div>
        <div class="col-xs12">
            <form id="form" autocomplete="off">
                <div class="row col-space16">
                    <div class="col-xs12">
                        <mdui-select
                            label="存储类型"
                            name="type"
                            variant="outlined"
                            id="storageType"
                        >
                            <mdui-menu-item value="local">本地存储</mdui-menu-item>
                            <mdui-menu-item value="webdav">WebDAV</mdui-menu-item>
                        </mdui-select>
                    </div>

                    <div class="col-xs12" id="webdavTip" style="display:none;">
                        <mdui-card variant="outlined" class="p-4">
                            <p>WebDAV 连接参数请前往
                                <a href="/webdav/config" data-pjax-item>WebDAV 配置</a>
                                页面设置。
                            </p>
                        </mdui-card>
                    </div>

                    <div class="col-xs12">
                        <mdui-button type="submit" icon="save">保存</mdui-button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script id="script" src="/static/js/storage/config.js?v={$__v}"></script>

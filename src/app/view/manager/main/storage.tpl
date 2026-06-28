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
                        <mdui-text-field
                            label="API Token"
                            name="token"
                            variant="outlined"
                            id="apiToken"
                            helper="用于外部上传接口鉴权，留空则禁用 API 上传"
                        ></mdui-text-field>
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

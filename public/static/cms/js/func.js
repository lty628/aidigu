function alertMsg(msg, url) {
    layui.use('layer', function () {
        var layer = layui.layer;
        layer.msg(msg);
        if (url) window.location.href = url
    });
}

function alertLoading() {
    layui.use('layer', function () {
        var layer = layui.layer;
        layer.load(2)
    });
}
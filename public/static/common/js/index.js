// 公共js

function checkMediaType(url) {
    // 创建URL对象
    var link = new URL(url);

    // 获取路径部分（去除参数）
    var path = link.href;
    var media = {}
    // 获取路径的最后一个点之后的内容作为文件扩展名
    var linkArr = path.split('.');
    var extension = linkArr.pop().toLowerCase();
    var media_info = path.substring(0, path.lastIndexOf("."));
    media.media_info = media_info
    media.media_type = extension
    var extensions = ['jpg', 'jpeg', 'gif', 'png', 'mp4', 'm3u8'];
    // 判断文件扩展名是否在图片扩展名数组中
    if (extensions.includes(extension)) {
        return [1, media];
    }

    return [0, url];
}

layui.use(function () {
    var util = layui.util;
    // 自定义固定条
    util.fixbar({
        // 点击事件
        click: function (type) {
            // console.log(this, type);
            // layer.msg(type);
        }
    });
});
function showMessageImg(obj) {
    var imgUrl = $(obj).attr('src');
    $tmpUrl = $(obj).attr('data-src');
    if ($tmpUrl) {
        imgUrl = $tmpUrl
    }
    // imgUrl = imgUrl.split(".");
    // imgUrl[0] = imgUrl[0].replace("_middle", '');
    var json = {
        "title": "", //相册标题
        "id": 0, //相册id
        "start": 0, //初始显示的图片序号，默认0
        "data": [   //相册包含的图片，数组格式
            {
                "alt": "",
                "pid": 0, //图片id
                //   "src": imgUrl[0]+"."+imgUrl[1], //原图地址
                "src": imgUrl, //原图地址
                "thumb": "" //缩略图地址
            }
        ]
    }
    if (parent.layer) {
        parent.layer.photos({
            photos: json
            , anim: 5
        });
    } else {
        layer.photos({
            photos: json
            , anim: 5
        });
    }

}


function toggleCommentIframe(msgId, type) {
    var iframeId = 'iframe-comment-' + msgId;
    var existingIframe = document.getElementById(iframeId);
    var siteTheme = $('#siteTheme').val();
    if (existingIframe) {
        // 如果已存在，则切换显示/隐藏
        if (existingIframe.style.display === 'none') {
            existingIframe.style.display = 'block';
        } else {
            existingIframe.style.display = 'none';
        }
    } else {
        // 创建新的 iframe 并插入到对应的评论区域
        var iframe = document.createElement('iframe');
        iframe.id = iframeId;
        iframe.src = '/tools/comment/index?msg_id=' + msgId + '&type=' + type+'&theme='+siteTheme;
        iframe.style.width = '100%';
        iframe.style.minHeight = '230px';
        iframe.style.border = 'none';
        iframe.style.marginTop = '10px';
        
        // 找到对应的消息项并插入 iframe
        var entryDiv = document.getElementById('entry-'+msgId);
        if (entryDiv) {
            entryDiv.appendChild(iframe);
        }
    }
}

// 显示网盘
var showFrame = false;
var currentLayerIndex = null;

function showFrameHtml(obj, width, height ,style = 0) {
    if (showFrame) return
    showFrame = true
    if (parent.layer) {
        var layer = parent.layer
    }

    var skinArr = ['', 'layui-layer-win10', 'layui-layer-molv'];
    
    var resize = true;
    var maxmin = true;
    // 判断是否是手机
    if (navigator.userAgent.match(/(phone|pad|pod|iPhone|iPod|ios|iPad|Android|Mobile|BlackBerry|IEMobile|MQQBrowser|JUC|Fennec|wOSBrowser|BrowserNG|WebOS|Symbian|Windows Phone)/i)) {
        // width = '100%';
        // height = '100%';
        resize = false;
        maxmin =false;
    }
    // 打开弹框并保存索引
    currentLayerIndex = layer.open({
        type: 2,
        title: $(obj).attr('data-title'),
        shade: false,
        area: [width, height],
        resize: resize,
        maxmin: maxmin,
        scrollbar: false,
        content: $(obj).attr('data-url'),
        // win10风格 
        skin: skinArr[style],
        zIndex: layer.zIndex, //重点1
        success: function (layero) {
            layer.setTop(layero); //重点2
            // 添加手机返回键支持
            window.history.pushState(null, null, window.location.href);
            $(window).on('popstate.showFrameHtml', function() {
                layer.close(currentLayerIndex);
            });
        },
        end: function () {
            showFrame = false;
            currentLayerIndex = null;
            // 移除事件监听
            $(window).off('popstate.showFrameHtml');
        }
    });
}

function showFrameUrl(obj, width, height) {
    // 打开弹框并保存索引
    currentLayerIndex = layer.open({
        type: 1,
        title: $(obj).attr('data-title'),
        shade: 0.8,
        closeBtn: 0,
        shadeClose: true,
        area: [width, height],
        resize: false,
        maxmin: false,
        // moveOut: true,
        scrollbar: false,
        content: '<iframe sandbox="allow-same-origin allow-scripts allow-popups" src="' + $(obj).attr('data-url') + '" allowfullscreen="true" allowtransparency="true" width="100%" height="100%" frameborder="0" scrolling="yes"></iframe>',
        success: function() {
            // 添加手机返回键支持
            window.history.pushState(null, null, window.location.href);
            $(window).on('popstate.showFrameUrl', function() {
                layer.close(currentLayerIndex);
            });
        },
        end: function() {
            currentLayerIndex = null;
            // 移除事件监听
            $(window).off('popstate.showFrameUrl');
        }
    });
    
    $("layui-layer-content").css("overflow", 'hidden')
}

// 小应用专用
function showFrameCustom(obj, appConfig) {
    appConfig = JSON.parse(appConfig)
     // 创建iframe内容
    const iframeContent = `
        <div style="position:relative;width:100%;height:100%;">
            <div class="tool-loading" style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);z-index:10;display:flex;align-items:center;justify-content:center;flex-direction:column;">
                <div style="width:40px;height:40px;border:3px solid #f3f3f3;border-top:3px solid #3498db;border-radius:50%;animation:spin 1s linear infinite;"></div>
                <p style="margin-top:10px;color:#666;font-size:14px;">加载中...</p>
            </div>
            <iframe 
                sandbox="allow-same-origin allow-scripts allow-popups allow-forms" 
                src="${$(obj).attr('data-url')}" 
                allowfullscreen="true" 
                allowtransparency="true" 
                width="100%" 
                height="100%" 
                frameborder="0" 
                scrolling="auto"
                style="background:#fff;"
                onload="this.previousElementSibling.style.display='none'"
            ></iframe>
        </div>
        <style>
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
        </style>
    `;
    
    // 打开弹窗
    const index = layer.open({
        type: 1,
        title: appConfig.title,
        shade: appConfig.shade,
        closeBtn: appConfig.closeBtn,
        shadeClose: appConfig.shadeClose,
        area: appConfig.area,
        resize: appConfig.config,
        maxmin: appConfig.maxmin,
        skin: appConfig.skin,
        id: appConfig.id,
        hideOnClose: appConfig.hideOnClose,
        // moveOut: true,
        scrollbar: false,
        content: iframeContent,
        success: function() {
            // 添加手机返回键支持
            window.history.pushState(null, null, window.location.href);
            $(window).on('popstate.showFrameCustom', function() {
                layer.close(index);
            });
        },
        end: function() {
            // 移除事件监听
            $(window).off('popstate.showFrameCustom');
            $(document).off('keydown.toolModal');
        }
    });

    // 优化弹窗样式
    setTimeout(() => {
        const layero = $('.layui-layer[type="1"]').last();
        if (layero.length) {
            // 优化标题栏
            layero.find('.layui-layer-title').css({
                'background': 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                'color': '#fff',
                'font-weight': '600',
                'border-radius': '8px 8px 0 0',
                'text-align': 'center',
                'padding': '15px'
            });
            
            // 优化关闭按钮
            layero.find('.layui-layer-setwin a').css({
                'color': '#fff',
                'font-size': '18px'
            });
            
            // 优化内容区域
            layero.find('.layui-layer-content').css({
                // 'border-radius': '0 0 8px 8px',
                'overflow': 'hidden'
            });
        }
    }, 10);
    
    // 添加ESC键支持
    $(document).on('keydown.toolModal', function(e) {
        if (e.keyCode === 27) {
            layer.close(index);
        }
    });
}

//字符串转换为时间戳
function getDateTimeStamp(dateStr) {
    return dateStr + '000'
    return Date.parse(dateStr.replace(/-/gi, "/"));
}
function getDateDiff(dateStr) {
    var publishTime = getDateTimeStamp(dateStr) / 1000,
        d_seconds,
        d_minutes,
        d_hours,
        d_days,
        timeNow = parseInt(new Date().getTime() / 1000),
        d,

        date = new Date(publishTime * 1000),
        Y = date.getFullYear(),
        M = date.getMonth() + 1,
        D = date.getDate(),
        H = date.getHours(),
        m = date.getMinutes(),
        s = date.getSeconds();
    //小于10的在前面补0
    if (M < 10) {
        M = '0' + M;
    }
    if (D < 10) {
        D = '0' + D;
    }
    if (H < 10) {
        H = '0' + H;
    }
    if (m < 10) {
        m = '0' + m;
    }
    if (s < 10) {
        s = '0' + s;
    }

    d = timeNow - publishTime;
    d_days = parseInt(d / 86400);
    d_hours = parseInt(d / 3600);
    d_minutes = parseInt(d / 60);
    d_seconds = parseInt(d);

    if (d_days > 0 && d_days < 3) {
        return d_days + '天前';
    } else if (d_days <= 0 && d_hours > 0) {
        return d_hours + '小时前';
    } else if (d_hours <= 0 && d_minutes > 0) {
        return d_minutes + '分钟前';
    } else if (d_seconds < 60) {
        if (d_seconds <= 0) {
            return '刚刚';
        } else {
            return d_seconds + '秒前';
        }
    } else if (d_days >= 3 && d_days < 30) {
        return M + '-' + D + '&nbsp;' + H + ':' + m;
    } else if (d_days >= 30) {
        return Y + '-' + M + '-' + D + '&nbsp;' + H + ':' + m;
    }
}

function changeFrameHeight(cIframe) {
    let a = cIframe.contentWindow;
    let b =
        a.document.documentElement.scrollHeight ||
        a.document.body.scrollHeight;
    let doc = cIframe.contentDocument || cIframe.document;
    let cHeight = Math.max(
        doc.body.clientHeight,
        doc.documentElement.clientHeight
    );
    let sHeight = Math.max(
        doc.body.scrollHeight,
        doc.documentElement.scrollHeight
    );
    let lheight = Math.max(cHeight, sHeight);
    let finalHeight = Math.max(lheight, b);
    cIframe.height = finalHeight + "px";
}

function base64Encode(str) {
    return btoa(encodeURIComponent(str).replace(/%([0-9A-F]{2})/g, function(match, p1) {
        return String.fromCharCode('0x' + p1);
    }));
}
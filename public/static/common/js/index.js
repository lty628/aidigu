// 公共js

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
function showMessageImg(obj)
{
    var imgUrl = $(obj).attr('src');
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
            ,anim: 5
        });
    } else {
        layer.photos({
            photos: json
            ,anim: 5
        });
    }
    
}

// 显示网盘
var showFrame = false;
function showFrameHtml(obj, width, height)
{
    if (showFrame) return
    showFrame = true
    layer.open({
        type: 2,
        title: $(obj).attr('data-title'),
        shade: false,
        area: [width, height],
        resize: true,
        maxmin: true,
        content: $(obj).attr('data-url'),
        zIndex: layer.zIndex, //重点1
        success: function(layero){
            layer.setTop(layero); //重点2
        },
        end: function(){ 
            showFrame = false;
        } 
    });
}
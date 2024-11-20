const E = window.wangEditor
const editor = new E("#msgToolBar", "#msgInput")
editor.config.placeholder = '你在做什么呢？'
editor.config.uploadImgServer = '/index/setting/msgInputImg'
editor.config.uploadFileName = 'file'
editor.config.uploadVideoServer = '/index/setting/msgInputImg'
editor.config.uploadVideoName = 'file'
editor.config.uploadImgHooks = {
    // 图片上传并返回了结果，图片插入已成功
    success: function (res) {
        console.log('success', xhr)
    },
    // 图片上传并返回了结果，但图片插入时出错了
    fail: function (xhr, editor, resData) {
        console.log('fail', resData)
    },
    // 上传图片出错，一般为 http 请求的错误
    error: function (xhr, editor, resData) {
        console.log('error', xhr, resData)
    },
    // 上传图片超时
    timeout: function (xhr) {
        console.log('timeout')
    },
    // 图片上传并返回了结果，想要自己把图片插入到编辑器中
    // 例如服务器端返回的不是 { errno: 0, data: [...] } 这种格式，可使用 customInsert
    customInsert: function (insertImgFn, res) {
        // result 即服务端返回的接口
        // console.log('customInsert', res)
        if (res.status) {
            var data = res.data;
            $(".tool-up-class").show();
            $("#mediaVal").val(JSON.stringify(data));
        }
        return
        // // insertImgFn 可把图片插入到编辑器，传入图片 src ，执行函数即可
        // insertImgFn(result.data['media_info'] + '.' + result.data['media_type'])
    }
}
editor.config.uploadVideoHooks = {

    // 视频上传并返回了结果，视频插入已成功
    success: function (xhr) {
        console.log('success', xhr)
    },
    // 视频上传并返回了结果，但视频插入时出错了
    fail: function (xhr, editor, resData) {
        console.log('fail', resData)
    },
    // 上传视频出错，一般为 http 请求的错误
    error: function (xhr, editor, resData) {
        console.log('error', xhr, resData)
    },
    // 上传视频超时
    timeout: function (xhr) {
        console.log('timeout')
    },
    // 视频上传并返回了结果，想要自己把视频插入到编辑器中
    // 例如服务器端返回的不是 { errno: 0, data: { url : '.....'} } 这种格式，可使用 customInsert
    customInsert: function (insertVideoFn, res) {
        // result 即服务端返回的接口

        if (res.status) {
            var data = res.data;
            $(".tool-up-class").show();
            $("#mediaVal").val(JSON.stringify(data));
        }
        return
        // console.log('customInsert', result)
        // // insertVideoFn 可把视频插入到编辑器，传入视频 src ，执行函数即可
        // insertVideoFn(result.data['media_info'] + '.' + result.data['media_type'])
    }
}
editor.config.menus = [
    'emoticon',
    'image',
    'video',
    'link',
]
editor.config.showFullScreen = false
editor.config.emotions = [
    {
        title: '默认', // tab 的标题
        type: 'image', // 'emoji' 或 'image' ，即 emoji 形式或者图片形式
        content: [
            { alt: "[微笑]", src: "/static/layui/images/face/0.gif" },
            { alt: "[嘻嘻]", src: "/static/layui/images/face/1.gif" },
            { alt: "[哈哈]", src: "/static/layui/images/face/2.gif" },
            { alt: "[可爱]", src: "/static/layui/images/face/3.gif" },
            { alt: "[可怜]", src: "/static/layui/images/face/4.gif" },
            { alt: "[挖鼻]", src: "/static/layui/images/face/5.gif" },
            { alt: "[吃惊]", src: "/static/layui/images/face/6.gif" },
            { alt: "[害羞]", src: "/static/layui/images/face/7.gif" },
            { alt: "[挤眼]", src: "/static/layui/images/face/8.gif" },
            { alt: "[闭嘴]", src: "/static/layui/images/face/9.gif" },
            { alt: "[鄙视]", src: "/static/layui/images/face/10.gif" },
            { alt: "[爱你]", src: "/static/layui/images/face/11.gif" },
            { alt: "[泪]", src: "/static/layui/images/face/12.gif" },
            { alt: "[偷笑]", src: "/static/layui/images/face/13.gif" },
            { alt: "[亲亲]", src: "/static/layui/images/face/14.gif" },
            { alt: "[生病]", src: "/static/layui/images/face/15.gif" },
            { alt: "[太开心]", src: "/static/layui/images/face/16.gif" },
            { alt: "[白眼]", src: "/static/layui/images/face/17.gif" },
            { alt: "[右哼哼]", src: "/static/layui/images/face/18.gif" },
            { alt: "[左哼哼]", src: "/static/layui/images/face/19.gif" },
            { alt: "[嘘]", src: "/static/layui/images/face/20.gif" },
            { alt: "[衰]", src: "/static/layui/images/face/21.gif" },
            { alt: "[委屈]", src: "/static/layui/images/face/22.gif" },
            { alt: "[吐]", src: "/static/layui/images/face/23.gif" },
            { alt: "[哈欠]", src: "/static/layui/images/face/24.gif" },
            { alt: "[抱抱]", src: "/static/layui/images/face/25.gif" },
            { alt: "[怒]", src: "/static/layui/images/face/26.gif" },
            { alt: "[疑问]", src: "/static/layui/images/face/27.gif" },
            { alt: "[馋嘴]", src: "/static/layui/images/face/28.gif" },
            { alt: "[拜拜]", src: "/static/layui/images/face/29.gif" },
            { alt: "[思考]", src: "/static/layui/images/face/30.gif" },
            { alt: "[汗]", src: "/static/layui/images/face/31.gif" },
            { alt: "[困]", src: "/static/layui/images/face/32.gif" },
            { alt: "[睡]", src: "/static/layui/images/face/33.gif" },
            { alt: "[钱]", src: "/static/layui/images/face/34.gif" },
            { alt: "[失望]", src: "/static/layui/images/face/35.gif" },
            { alt: "[酷]", src: "/static/layui/images/face/36.gif" },
            { alt: "[色]", src: "/static/layui/images/face/37.gif" },
            { alt: "[哼]", src: "/static/layui/images/face/38.gif" },
            { alt: "[鼓掌]", src: "/static/layui/images/face/39.gif" },
            { alt: "[晕]", src: "/static/layui/images/face/40.gif" },
            { alt: "[悲伤]", src: "/static/layui/images/face/41.gif" },
            { alt: "[抓狂]", src: "/static/layui/images/face/42.gif" },
            { alt: "[黑线]", src: "/static/layui/images/face/43.gif" },
            { alt: "[阴险]", src: "/static/layui/images/face/44.gif" },
            { alt: "[怒骂]", src: "/static/layui/images/face/45.gif" },
            { alt: "[互粉]", src: "/static/layui/images/face/46.gif" },
            { alt: "[心]", src: "/static/layui/images/face/47.gif" },
            { alt: "[伤心]", src: "/static/layui/images/face/48.gif" },
            { alt: "[猪头]", src: "/static/layui/images/face/49.gif" },
            { alt: "[熊猫]", src: "/static/layui/images/face/50.gif" },
            { alt: "[兔子]", src: "/static/layui/images/face/51.gif" },
            { alt: "[ok]", src: "/static/layui/images/face/52.gif" },
            { alt: "[耶]", src: "/static/layui/images/face/53.gif" },
            { alt: "[good]", src: "/static/layui/images/face/54.gif" },
            { alt: "[NO]", src: "/static/layui/images/face/55.gif" },
            { alt: "[赞]", src: "/static/layui/images/face/56.gif" },
            { alt: "[来]", src: "/static/layui/images/face/57.gif" },
            { alt: "[弱]", src: "/static/layui/images/face/58.gif" },
            { alt: "[草泥马]", src: "/static/layui/images/face/59.gif" },
            { alt: "[神马]", src: "/static/layui/images/face/60.gif" },
            { alt: "[囧]", src: "/static/layui/images/face/61.gif" },
            { alt: "[浮云]", src: "/static/layui/images/face/62.gif" },
            { alt: "[给力]", src: "/static/layui/images/face/63.gif" },
            { alt: "[围观]", src: "/static/layui/images/face/64.gif" },
            { alt: "[威武]", src: "/static/layui/images/face/65.gif" },
            { alt: "[奥特曼]", src: "/static/layui/images/face/66.gif" },
            { alt: "[礼物]", src: "/static/layui/images/face/67.gif" },
            { alt: "[钟]", src: "/static/layui/images/face/68.gif" },
            { alt: "[话筒]", src: "/static/layui/images/face/69.gif" },
            { alt: "[蜡烛]", src: "/static/layui/images/face/70.gif" },
            { alt: "[蛋糕]", src: "/static/layui/images/face/71.gif" }
        ]
    },
    {
        // tab 的标题
        title: '表情',
        // type -> 'emoji' / 'image'
        type: 'emoji',
        // content -> 数组
        content: '😀 😃 😄 😁 😆 😅 😂 🤣 😊 😇 🙂 🙃 😉 😌 😍 😘 😗 😙 😚 😋 😛 😝 😜 🤓 😎 😏 😒 😞 😔 😟 😕 🙁 😣 😖 😫 😩 😢 😭 😤 😠 😡 😳 😱 😨 🤗 🤔 😶 😑 😬 🙄 😯 😴 😷 🤑 😈 🤡 💩 👻 💀 👀 👣'.split(
            /\s/
        ),
    },
    {
        // tab 的标题
        title: '手势',
        // type -> 'emoji' / 'image'
        type: 'emoji',
        // content -> 数组
        content: '👐 🙌 👏 🤝 👍 👎 👊 ✊ 🤛 🤜 🤞 ✌️ 🤘 👌 👈 👉 👆 👇 ☝️ ✋ 🤚 🖐 🖖 👋 🤙 💪 🖕 ✍️ 🙏'.split(
            /\s/
        ),
    }
]

editor.config.customAlert = function (s, t) {
    alertMsg(s);
}
// 或者 const editor = new E(document.getElementById('div1'))
editor.config.uploadImgMaxSize = 20 * 1024 * 1024 // 20M
editor.config.uploadVideoMaxSize = 1 * 1024 * 1024 * 1024 // 1024m

// 粘贴图片
editor.config.pasteIgnoreImg = true
editor.config.showLinkImg = false
editor.config.showLinkVideo = false
editor.config.showMenuTooltips = false
editor.config.uploadImgMaxLength = 1
editor.config.uploadVideoAccept = ['mp4', 'mp3']


// 配置粘贴文本的内容处理
// editor.config.pasteTextHandle = function (pasteStr) {
//     // 对粘贴的文本进行处理，然后返回处理后的结果
//     return pasteStr + '巴拉巴拉'
// }

// editor.config.linkImgCheck = function(imgSrc,alt,href) {
//     // 以下情况，请三选一
//     console.log(3)
//     // 1. 返回 true ，说明检查通过
//     return true

//     // // 2. 返回一个字符串，说明检查未通过，编辑器会阻止图片插入。会 alert 出错误信息（即返回的字符串）
//     // return '图片 src 有 xxx 错误'

//     // 3. 返回 undefined（即没有任何返回），说明检查未通过，编辑器会阻止图片插入。
//     // 此处，你可以自定义提示错误信息，自由发挥
// }

// // 自定义检查插入视频的链接
// editor.config.onlineVideoCheck = function(video) {
//     // 编辑器会根据返回的内容做校验：比如以下几种情况
//     console.log(2)
//     // 1. 返回 true ，说明检查通过
//     return true

//     // 2. 返回一个字符串，说明检查未通过，编辑器会阻止视频插入。会 alert 出错误信息（即返回的字符串）
//     // return '插入的视频 有 xxx 错误'

//     // 3. 返回 undefined（即没有任何返回），说明检查未通过，编辑器会阻止视频插入。
//     // 此处，你可以自定义提示错误信息，自由发挥
// }

// // 自定义检查插入的链接
editor.config.linkCheck = function (text, link) {
    // 以下情况，请三选一
    var media = checkMediaType(link);

    if (media[0]) {
        $("#mediaVal").val(JSON.stringify(media));
    }
    // console.log($("#mediaVal").val())
    return true;
}

const { BtnMenu, DropListMenu, PanelMenu, DropList, Panel, Tooltip } = E
// 第一，菜单 class ，Button 菜单继承 BtnMenu class
class AlertMenu extends BtnMenu {
    constructor(editor) {
        // data-title属性表示当鼠标悬停在该按钮上时提示该按钮的功能简述
        const $elem = E.$(
            `<div class="w-e-menu" data-title="素材">
                <i class="layui-icon layui-icon-website"></i> 
            </div>`
        )
        super($elem, editor)
    }
    // 菜单点击事件
    clickHandler() {
        // 做任何你想做的事情
        // 可参考【常用 API】文档，来操作编辑器
        var area = ['100%', '100%']
        parent.layer.open({
            type: 2,
            title: '选择素材',
            shade: 0.6,
            area: area,
            resize: false,
            maxmin: false,
            closeBtn: true,
            shadeClose: true,
            content: '/tools/sourcematerial/share',
            zIndex: layer.zIndex, //重点1
            success: function (layero) {
                layer.setTop(layero); //重点2
            },
            end: function () {
                var sourcematerial = layui.sessionData('sourcematerial')
                if (!sourcematerial.data) {
                    return false
                }

                var data = sourcematerial.data

                $(".tool-up-class").show();
                $("#mediaVal").val(JSON.stringify(data));
                if (data.media_title) {
                    var topicTitle = $("#topicTitle").val()
					if (topicTitle) {
						editor.txt.html(topicTitle+"&nbsp;"+data.media_title)
					} else {
						editor.txt.html(data.media_title)
					}
                }
                
                layui.sessionData('sourcematerial', null)
            }
        });
    }
    // 菜单是否被激活（如果不需要，这个函数可以空着）
    // 1. 激活是什么？光标放在一段加粗、下划线的文本时，菜单栏里的 B 和 U 被激活，如下图
    // 2. 什么时候执行这个函数？每次编辑器区域的选区变化（如鼠标操作、键盘操作等），都会触发各个菜单的 tryChangeActive 函数，重新计算菜单的激活状态
    tryChangeActive() {
        // 激活菜单
        // 1. 菜单 DOM 节点会增加一个 .w-e-active 的 css class
        // 2. this.this.isActive === true
        // this.active()

        // // 取消激活菜单
        // // 1. 菜单 DOM 节点会删掉 .w-e-active
        // // 2. this.this.isActive === false
        // this.unActive()
    }
}

const menuKey = 'alertMenuKey'
editor.menus.extend(menuKey, AlertMenu)
editor.config.menus = editor.config.menus.concat(menuKey)


editor.create()

var topicTitle = $("#topicTitle").val()
if (topicTitle) {
    editor.txt.html(topicTitle + "&nbsp;")
}

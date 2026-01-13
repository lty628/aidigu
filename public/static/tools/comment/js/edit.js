// 初始化主编辑器
const E = window.wangEditor;
let mainEditor;

function initMainEditor() {
    mainEditor = new E('#mainToolbar', '#mainText');
    configureEditor(mainEditor);
    mainEditor.create();
}

// 创建回复编辑器的函数
function createReplyEditor(toolbarId, textId) {
    const editor = new E(`#${toolbarId}`, `#${textId}`);
    configureEditor(editor);
    editor.create();
    return editor;
}

// 配置编辑器的通用设置
function configureEditor(editor) {
    editor.config.placeholder = '请输入内容';
    editor.config.menus = [
        'emoticon',
        'code',
        'undo',
        'redo'
    ];
    editor.config.showFullScreen = false;
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
            content: '😀 😃 😄 😁 😆 😅 😂 🤣 😊 😇 🙂 🙃 😉 😌 😍 😘 😗 😙 😚 😋 😛 😝 😜 🤓 😎 😏 😒 😞 😔 😟 😕 🙁 😣 😖 😫 😩 😢 😭 😤 😠 😡 😳 😱 😨 🤗 🤔 😶 😑 😬 🙄 😯 😴 😷 🤑 😈 🤡 💩 👻 💀 👀 👣'.split(/\s/),
        },
        {
            // tab 的标题
            title: '手势',
            // type -> 'emoji' / 'image'
            type: 'emoji',
            // content -> 数组
            content: '👐 🙌 👏 🤝 👍 👎 👊 ✊ 🤛 🤜 🤞 ✌️ 🤘 👌 👈 👉 👆 👇 ☝️ ✋ 🤚 🖐 🖖 👋 🤙 💪 🖕 ✍️ 🙏'.split(/\s/),
        }
    ];
}
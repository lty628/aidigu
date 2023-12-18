var selectAry = [
    ["chitose"],
    ["epsilon2.1", "epsilon2_1"],
    ["gantzert_felixander", "gf"],
    ["haru01"],
    ["haru02"],
    ["haruto"],
    ["hibiki"],
    ["hijiki"],
    ["izumi"],
    ["koharu"],
    ["miku"],
    ["nico"],
    ["ni-j"],
    ["nipsilon"],
    ["nito"],
    ["shizuku"],
    ["tororo"],
    ["tsumiki"],
    ["Unitychan"],
    ["wanko"],
    ["z16"]
];
function initL2Dwidget(selectId) {
    let modalName = selectAry.find((item) => { return item[0].toLowerCase() == selectId });
    console.log('modalName', modalName)
    modalName = modalName && modalName[1] ? modalName[1] : selectId


    L2Dwidget
        .on('*', (name) => {
            console.log('%c EVENT ' + '%c -> ' + name, 'background: #222; color: yellow', 'background: #fff; color: #000')
        })
        .init({
            dialog: {
                // 开启对话框
                enable: true,
                script: {
                    // 当触摸到角色身体
                    'tap body': '哎呀！别碰我！',
                    // 当触摸到角色头部
                    'tap face': '喵～',
                }
            },
            display: {
                position: 'right'
            },
            "model": { "jsonPath": "/static/live2d/packages/live2d-widget-model-" + modalName + "/assets/" + selectId + ".model.json" },
            "mobile": { "show": true, scale: 0.5 },
        });
}
initL2Dwidget('shizuku');
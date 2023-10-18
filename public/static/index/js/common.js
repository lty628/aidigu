function id(i){return document.getElementById(i)}
function register_submit(){
	if(id("username").value.length < 3){alert("用户名长度务必在3个字符以上。");return false;}
	if(id("username").value.length > 15){alert("用户名长度务必在15个字符以下。");return false;}
	if(id("nickname").value.length < 3){alert("昵称长度务必在3个字符以上。");return false;}
	if(id("nickname").value.length > 15){alert("昵称长度务必在15个字符以下。");return false;}
	if(id("password").value.length < 6){alert("密码长度务必在6个字符以上。");return false;}
	if(id("password").value.length > 30){alert("密码长度务必在30个字符以下。");return false;}
	if(id("email").value.length < 6){alert("电子邮件长度务必在6个字符以上。");return false;}
	if(id("email").value.length > 30){alert("电子邮件长度务必在30个字符以下。");return false;}
	if(id("repassword").value != id("password").value){alert("两次输入的密码不一致。");return false;}
	return true;
}
function chgpw_submit(){
	if(id("oldpw").value.length < 6){alert("密码长度务必在6个字符以上。");return false;}
	if(id("oldpw").value.length > 30){alert("密码长度务必在30个字符以下。");return false;}
	if(id("newpw").value.length < 6){alert("密码长度务必在6个字符以上。");return false;}
	if(id("newpw").value.length > 30){alert("密码长度务必在30个字符以下。");return false;}
	if(id("newpw").value != id("compw").value){alert("两次输入的密码不一致。");return false;}
	return true;
}
function forgetcheck_submit(){
	if(id("username").value.length < 3){alert("用户名长度务必在3个字符以上。");return false;}
	if(id("username").value.length > 15){alert("用户名长度务必在15个字符以下。");return false;}
	if(id("password").value.length < 6){alert("密码长度务必在6个字符以上。");return false;}
	if(id("password").value.length > 30){alert("密码长度务必在30个字符以下。");return false;}
	if(id("password").value != id("compassword").value){alert("两次输入的密码不一致。");return false;}
	return true;
}
function info_submit(){
	if("" == id("selProvince").value || "" == id("selCity").value){alert("请选择所在城市！");return false;}
	return true;
}
function post_submit(){
	id('msgInput').value = id('msgInput').value.replace(/ |　|\r\n|\n/ig,'');
	if(id('msgInput').value.length < 1){alert("请输入微博内容！");id('msgInput').focus();return false;}
	return true;
}
function forgetpw_submit(){
	if(id("username").value.length < 3){alert("用户名长度务必在3个字符以上。");return false;}
	if(id("username").value.length > 15){alert("用户名长度务必在15个字符以下。");return false;}
	if(id("email").value.length < 6){alert("电子邮件长度务必在6个字符以上。");return false;}
	if(id("email").value.length > 30){alert("电子邮件长度务必在30个字符以下。");return false;}
	return true;
}
function check_len1(){
	var obj=id("msgInput");
	var leftlen=id("leftlen1");
	check_len(obj, leftlen)
}
function check_len2(){
	var obj=id("repostInput");
	var leftlen=id("leftlen2");
	check_len(obj, leftlen)
}
function check_len3(){
	var obj=id("commentInput");
	var leftlen=id("leftlen3");
	check_len(obj, leftlen)
}
function check_len(obj, leftlen){
	
	var maxLen=parseInt(obj.getAttribute('maxlength'));
	var text = $(".w-e-text").html()
	var len=text.replace(/[^\x00-\xff]/g,'o').length; 
	var llen=maxLen-len;
	if(len>maxLen) {
		var i=0; 
		for(var z=0;z<len;z++) {
			if(text.charCodeAt(z)>255) {i=i+2;}else {i=i+1;} 
			if(i>=maxLen) {text=text.slice(0,(z + 1)); break; } 
		} 
	} 
	if(llen<0)llen=0;
	leftlen.innerHTML=llen;
}
function showform(formtype, wid, wcontent){
	id('formtype').value=formtype;
	id('wid').value=wid;
	id('wcontent').innerHTML = wcontent + '<u style="text-decoration:none;padding-left:10px;"><a href="javascript:void(0);" onclick="hideform();" style="color:#AAA">x</a></u>';
	id('postform').style.display="block";
	id('msgInput').focus();
}
function hideform(){
	id('postform').style.display="none";
}
function comdel(url){
	if(window.confirm("确定删除吗?")){
		window.location.href = url;
	}
}
	$(function (){
		layui.use('upload', function(){
		  	var upload = layui.upload;
		  	var uploadInst = upload.render({
		    elem: '#msgInputImg' //绑定元素
		    ,url: '/index/setting/msgInputImg' //上传接口
			,accept: "file"
		    // ,method: 'GET'
		    ,done: function(res){
		    	layer.closeAll();
		      	if (res.status) {
		      		var data = res.data;
		      		// $(".imgHtml").html('上传成功！');
		      		// $(".imgHtml").append('<i class="layui-icon layui-icon-close-fill" onclick="removeImg(this)"  style="color: red;cursor:pointer;font-size:20px!important"></i>')
		      		$("#imgVal").val(JSON.stringify(data));
		      	}
		    }
		    ,before: function(){
		    	layer.load(1, {shade: 0.3});
		    }
		    ,error: function(){
		      //请求异常回调
		    }
		  });
		});
		$("#sendButton").click(function(event) {
			var jsonData = {};
			// jsonData.contents = $("#msgInput").val();

			var text = editor.txt.html();
			if(text.length > 1400){
				alertMsg('超过内容长度限制！');
				return false;
			}

			jsonData.imageInfo = $("#imgVal").val();
			if (!text && !jsonData.imageInfo) {
				alertMsg('请输入微博内容！');
				$("#msgInput").focus();
				return false;
			}
			// jsonData.contents = ReplaceEmoji(jsonData.contents)
			jsonData.contents = editor.txt.html();
			// console.log(jsonData.contents);return false;
			var config = {};
			config.action = 'index';
			config.index = '';
			config.additional = 1;
			config.flush = 0;
			sendMsg(jsonData, config)
		});
		// 显示网盘
		var showCloud = false;
		$("#showCloud").click(function () {
			if (showCloud) return
			showCloud = true
			layer.open({
				type: 2,
				title: "我的云盘",
				shade: true,
				area: ['80%', '80%'],
				resize: true,
				maxmin: true,
				content: '/cloud/show/',
				zIndex: layer.zIndex, //重点1
				success: function(layero){
					layer.setTop(layero); //重点2
				},
				end: function(){ 
					showCloud = false;
				} 
			});
		})
	})
	var flag = false;
	function sendMsg(jsonData, config)
	{
		if (flag) return false;
		flag = true;
		var action = config.action;
		var index = config.index;
		var additional = config.additional;
		var flush = config.flush;
		$.ajax({
				url: '/?s=index/ajax/'+action,
				type: 'GET',
				dataType: 'json',
				data: jsonData,
			})
			.done(function(data) {
				flag = false;
				if (action == 'index') {
					$(".sendMsg").text(data.msg);
					$(".sendMsg").slideDown().delay(800).slideUp();
				} else {
					alertMsg(data.msg, 0, flush);
				}
				if (index) layer.close(index);
				if (data.status) {
					// $("#msgInput").val('');
					var topicTitle = $("#topicTitle").val()
					if (topicTitle) {
						editor.txt.html(topicTitle+"&nbsp;")
					} else {
						editor.txt.clear()
					}
					check_len1();
					if (additional) {
						var userInfo = $.parseJSON($("#userInfo").val());
						var message = data.data;
						var image_info = $.parseJSON(message.image_info);
						if (message.image) {
							if (image_info.image_type == 'mp4') {
								var str = '<div class="entry"><div class="avatar"><div class="imgborder"><a href="/'+userInfo.blog+'/"><img src="'+userInfo.head_image+'" /></a></div></div><div class="box box-main"><p class="massageText"><a href="/'+userInfo.blog+'/">'+userInfo.nickname+'：</a>'+message.contents+message.repost+'</p><p  class="massageImg clear"><video width="400px"  controls=""  name="media"><source src="'+message.image+'" type="video/mp4"></video></p><div class="static"><span> <a href="/'+userInfo.blog+'/del/message/'+message.msg_id+'">删除</a></span>刚刚 来自 '+message.refrom+'</div></div><div class="clear"></div></div>';
							} else if (image_info.image_type == 'mp3') {
								var str = '<div class="entry"><div class="avatar"><div class="imgborder"><a href="/'+userInfo.blog+'/"><img src="'+userInfo.head_image+'" /></a></div></div><div class="box box-main"><p class="massageText"><a href="/'+userInfo.blog+'/">'+userInfo.nickname+'：</a>'+message.contents+message.repost+'</p><p class="massageImg clear"><audio id="music_'+message.msg_id+'" class="music" controls="controls" loop="loop" onplay="stopOther(this)" preload="none" controlsList="nodownload" οncοntextmenu="return false" name="media"><source src="'+message.image+'" type="audio/mpeg"></audio></p><div class="static"><span> <a href="/'+userInfo.blog+'/del/message/'+message.msg_id+'">删除</a></span>刚刚 来自 '+message.refrom+'</div></div><div class="clear"></div></div>';
							}  else {
								var str = '<div class="entry"><div class="avatar"><div class="imgborder"><a href="/'+userInfo.blog+'/"><img src="'+userInfo.head_image+'" /></a></div></div><div class="box box-main"><p class="massageText"><a href="/'+userInfo.blog+'/">'+userInfo.nickname+'：</a>'+message.contents+message.repost+'</p><p  class="massageImg clear"><img width="75%"  onclick="showMessageImg(this)" src="'+message.image+'"></p><div class="static"><span> <a href="/'+userInfo.blog+'/del/message/'+message.msg_id+'">删除</a></span>刚刚 来自 '+message.refrom+'</div></div><div class="clear"></div></div>';
							}
							
						} else {
							var str = '<div class="entry"><div class="avatar"><div class="imgborder"><a href="/'+userInfo.blog+'/"><img src="'+userInfo.head_image+'" /></a></div></div><div class="box box-main"><p class="massageText"><a href="/'+userInfo.blog+'/">'+userInfo.nickname+'：</a>'+message.contents+message.repost+'</p><div class="static"><span> <a href="/'+userInfo.blog+'/del/message/'+message.msg_id+'">删除</a></span>刚刚 来自 '+message.refrom+'</div></div><div class="clear"></div></div>';
						}
						// if (message.image) {
						// 	var str = '<div class="entry"><div class="avatar"><div class="imgborder"><a href="/'+userInfo.blog+'/"><img src="'+userInfo.head_image+'" /></a></div></div><div class="box box-main"><p class="massageText"><a href="/'+userInfo.blog+'/">'+userInfo.nickname+'：</a>'+message.contents+message.repost+'</p><p><img width="75%"  onclick="showMessageImg(this)" src="'+message.image+'"></p><div class="static"><span><a href="/'+userInfo.blog+'/message/'+message.msg_id+'" target="_blank">查看</a> | <a href="javascript:void(0);" onclick="repost(this)"> 转发 </a> |<a href="javascript:void(0);" onclick="comment('+message.msg_id+', {$siteUserId});"> 评论 </a>| <a href="/'+userInfo.blog+'/del/message/'+message.msg_id+'">删除</a></span>刚刚 来自 '+message.refrom+'</div></div><div class="clear"></div></div>';
						// } else {
						// 	var str = '<div class="entry"><div class="avatar"><div class="imgborder"><a href="/'+userInfo.blog+'/"><img src="'+userInfo.head_image+'" /></a></div></div><div class="box box-main"><p class="massageText"><a href="/'+userInfo.blog+'/">'+userInfo.nickname+'：</a>'+message.contents+message.repost+'</p><div class="static"><span><a href="/'+userInfo.blog+'/message/'+message.msg_id+'" target="_blank">查看</a> | <a href="javascript:void(0);" onclick="repost(this)"> 转发 </a> |<a href="javascript:void(0);" onclick="comment('+message.msg_id+', {$siteUserId});"> 评论 </a>| <a href="/'+userInfo.blog+'/del/message/'+message.msg_id+'">删除</a></span>刚刚 来自 '+message.refrom+'</div></div><div class="clear"></div></div>';
						// }
						$("#msgContent").prepend(str);
						var num = $("#messageSum").text();
						$("#messageSum").text(parseInt(num)+1);
						$(".imgHtml").children('i').trigger('click');
					}
				}
			})
	}
	function alertMsg(msg, type, flush) 
	{
		// layer.msg('演示完毕！您的口令：'+ pass +'<br>您最后写下了：'+text);
		layer.msg(msg, {
		  // offset: 't',
		  // anim: 6
		}, function(){
		  if (flush) {
		  	window.location.reload();
		  }
		});
	}
	function repost(obj, fromuid, msg_id) 
	{
		var repost = '@'+$(obj).parents(".box").children('.massageText').html();
		var mediaInfo = '';
		if ($(obj).parents(".box").children('.massageImg').html()) {
			mediaInfo = '<p class="massageImg">' + $(obj).parents(".box").children('.massageImg').html() + '</p>';
		}
		layer.open({
		  type: 1,
		  skin: 'layui-layer-rim', //加上边框
		  area: ['625px', '320px'], //宽高
		  title: "<b>转发：</b>"+repost,
		  content: '<div class="contents"><div class="post" id="postform"><div class="postad">&nbsp;</div><textarea placeholder="请输入转发内容" maxlength="1400" id="repostInput" onkeyup="check_len2()"></textarea><div class="postnow">你还可以发布<em id="leftlen2">1400</em>字</div><div class="tool-class"><i class="layui-icon layui-icon-face-smile-b" onclick="showEmoji(this)" style="margin: 10px;cursor:pointer;color: #ffa700;font-size:20px!important" title="表情"></i><div class="emojiHtml" style="display: none"></div></div></div></div>',
		  btn: ['转发',"取消"],
		  yes: function(index, layero){
		  	var jsonData = {};
		  	jsonData.contents = $("#repostInput").val();
		  	jsonData.contents = ReplaceEmoji(jsonData.contents);
		  	jsonData.repost = repost + mediaInfo;
		  	jsonData.fromuid = fromuid;
		  	jsonData.msg_id = msg_id;
		  	var config = {};
			config.action = 'repost';
			config.index = index;
			config.additional = 1;
			config.flush = 0;
		    sendMsg(jsonData, config);
		  }
		});
	}
	function comment(commentId, uid)
	{
		layer.open({
		  type: 1,
		  skin: 'layui-layer-rim', //加上边框
		  area: ['625px', '320px'], //宽高
		  title: "<b>评论：</b>",
		  content: '<div class="contents"><div class="post" id="commentform"><div class="postad" >&nbsp;</div><textarea placeholder="请输入评论内容" maxlength="140" id="commentInput" onkeyup="check_len3()"></textarea><div class="postnow">你还可以发布<em id="leftlen3">140</em>字</div><div class="tool-class"><i class="layui-icon layui-icon-face-smile-b" onclick="showEmoji(this)" style="margin: 10px;cursor:pointer;color: #ffa700;font-size:20px!important" title="表情"></i><div class="emojiHtml" style="display: none"></div></div></div></div>',
		  btn: ['评论',"取消"],
		  yes: function(index, layero){
		  	var jsonData = {};
		  	jsonData.comment = $("#commentInput").val();
		  	jsonData.comment = ReplaceEmoji(jsonData.comment);
		  	jsonData.uid = uid;
		  	jsonData.commentId = commentId;
		  	var config = {};
			config.action = 'comment';
			config.index = index;
			config.additional = 0;
			config.flush = 0;
		    sendMsg(jsonData, config);
		  }
		});
	}
	function reply(obj,commentId, uid)
	{
		var replyUser = $(obj).parents('.block_body').children('.repost').children('span').html();
		var replyArr = replyUser.split('@<a');
		replyUser = replyArr[0];
		if (!replyUser) {
			alertMsg('未找到回复内容');
			return false;
		}
		layer.open({
		  type: 1,
		  skin: 'layui-layer-rim', //加上边框
		  area: ['625px', '320px'], //宽高
		  title: "<b>回复：</b>"+replyUser,
		  content: '<div class="contents"><div class="post" id="commentform"><div class="postad" >&nbsp;</div><textarea placeholder="请输入回复内容" maxlength="1400" id="commentInput" onkeyup="check_len3()"></textarea><div class="postnow">你还可以发布<em id="leftlen3">1400</em>字</div><div class="tool-class"><i class="layui-icon layui-icon-face-smile-b" onclick="showEmoji(this)" style="margin: 10px;cursor:pointer;color: #ffa700;font-size:20px!important" title="表情"></i><div class="emojiHtml" style="display: none"></div></div></div></div>',
		  btn: ['回复',"取消"],
		  yes: function(index, layero){
		  	var jsonData = {};
		  	// jsonData.comment = '@回复：'+$("#commentInput").val()+' @'+replyUser;
		  	jsonData.comment = $("#commentInput").val()+' @'+replyUser;
		  	jsonData.comment = ReplaceEmoji(jsonData.comment);
		  	jsonData.uid = uid;
		  	jsonData.commentId = commentId;
		  	var config = {};
			config.action = 'reply';
			config.index = index;
			config.additional = 0;
			config.flush = 1;
		    sendMsg(jsonData, config);
		  }
		});
	}
	function fansFollow(userid, action)
	{
		var jsonData = {};
		jsonData.userid = userid;
		var config = {};
		config.action = action;
		config.index = '';
		config.additional = 0;
		config.flush = 1;
	    sendMsg(jsonData, config);
	}
	function blurToUnfollow(obj, userid)
	{
		var text = $(obj).text();
		$(obj).text('取消关注');
		$(obj).mouseout(function(event) {
			$(obj).text(text);
		});
	}
	var emojiHtml = '<div class="layui-util-face" style="z-index: 100; position: fixed;"><div id="" class="layui-layer-content"><ul class="layui-clear"><li title="[微笑]"><img src="/static/layui/images/face/0.gif" ></li><li title="[嘻嘻]"><img src="/static/layui/images/face/1.gif" ></li><li title="[哈哈]"><img src="/static/layui/images/face/2.gif" ></li><li title="[可爱]"><img src="/static/layui/images/face/3.gif" ></li><li title="[可怜]"><img src="/static/layui/images/face/4.gif" ></li><li title="[挖鼻]"><img src="/static/layui/images/face/5.gif" ></li><li title="[吃惊]"><img src="/static/layui/images/face/6.gif" ></li><li title="[害羞]"><img src="/static/layui/images/face/7.gif" ></li><li title="[挤眼]"><img src="/static/layui/images/face/8.gif" ></li><li title="[闭嘴]"><img src="/static/layui/images/face/9.gif" ></li><li title="[鄙视]"><img src="/static/layui/images/face/10.gif" ></li><li title="[爱你]"><img src="/static/layui/images/face/11.gif" ></li><li title="[泪]"><img src="/static/layui/images/face/12.gif" ></li><li title="[偷笑]"><img src="/static/layui/images/face/13.gif" ></li><li title="[亲亲]"><img src="/static/layui/images/face/14.gif" ></li><li title="[生病]"><img src="/static/layui/images/face/15.gif" ></li><li title="[太开心]"><img src="/static/layui/images/face/16.gif" ></li><li title="[白眼]"><img src="/static/layui/images/face/17.gif" ></li><li title="[右哼哼]"><img src="/static/layui/images/face/18.gif" ></li><li title="[左哼哼]"><img src="/static/layui/images/face/19.gif" ></li><li title="[嘘]"><img src="/static/layui/images/face/20.gif" ></li><li title="[衰]"><img src="/static/layui/images/face/21.gif" ></li><li title="[委屈]"><img src="/static/layui/images/face/22.gif" ></li><li title="[吐]"><img src="/static/layui/images/face/23.gif" ></li><li title="[哈欠]"><img src="/static/layui/images/face/24.gif" ></li><li title="[抱抱]"><img src="/static/layui/images/face/25.gif" ></li><li title="[怒]"><img src="/static/layui/images/face/26.gif" ></li><li title="[疑问]"><img src="/static/layui/images/face/27.gif" ></li><li title="[馋嘴]"><img src="/static/layui/images/face/28.gif" ></li><li title="[拜拜]"><img src="/static/layui/images/face/29.gif" ></li><li title="[思考]"><img src="/static/layui/images/face/30.gif" ></li><li title="[汗]"><img src="/static/layui/images/face/31.gif" ></li><li title="[困]"><img src="/static/layui/images/face/32.gif" ></li><li title="[睡]"><img src="/static/layui/images/face/33.gif" ></li><li title="[钱]"><img src="/static/layui/images/face/34.gif" ></li><li title="[失望]"><img src="/static/layui/images/face/35.gif" ></li><li title="[酷]"><img src="/static/layui/images/face/36.gif" ></li><li title="[色]"><img src="/static/layui/images/face/37.gif" ></li><li title="[哼]"><img src="/static/layui/images/face/38.gif" ></li><li title="[鼓掌]"><img src="/static/layui/images/face/39.gif" ></li><li title="[晕]"><img src="/static/layui/images/face/40.gif" ></li><li title="[悲伤]"><img src="/static/layui/images/face/41.gif" ></li><li title="[抓狂]"><img src="/static/layui/images/face/42.gif" ></li><li title="[黑线]"><img src="/static/layui/images/face/43.gif" ></li><li title="[阴险]"><img src="/static/layui/images/face/44.gif" ></li><li title="[怒骂]"><img src="/static/layui/images/face/45.gif" ></li><li title="[互粉]"><img src="/static/layui/images/face/46.gif" ></li><li title="[心]"><img src="/static/layui/images/face/47.gif" ></li><li title="[伤心]"><img src="/static/layui/images/face/48.gif" ></li><li title="[猪头]"><img src="/static/layui/images/face/49.gif" ></li><li title="[熊猫]"><img src="/static/layui/images/face/50.gif" ></li><li title="[兔子]"><img src="/static/layui/images/face/51.gif" ></li><li title="[ok]"><img src="/static/layui/images/face/52.gif" ></li><li title="[耶]"><img src="/static/layui/images/face/53.gif" ></li><li title="[good]"><img src="/static/layui/images/face/54.gif" ></li><li title="[NO]"><img src="/static/layui/images/face/55.gif" ></li><li title="[赞]"><img src="/static/layui/images/face/56.gif" ></li><li title="[来]"><img src="/static/layui/images/face/57.gif" ></li><li title="[弱]"><img src="/static/layui/images/face/58.gif" ></li><li title="[草泥马]"><img src="/static/layui/images/face/59.gif" ></li><li title="[神马]"><img src="/static/layui/images/face/60.gif" ></li><li title="[囧]"><img src="/static/layui/images/face/61.gif" ></li><li title="[浮云]"><img src="/static/layui/images/face/62.gif" ></li><li title="[给力]"><img src="/static/layui/images/face/63.gif" ></li><li title="[围观]"><img src="/static/layui/images/face/64.gif" ></li><li title="[威武]"><img src="/static/layui/images/face/65.gif" ></li><li title="[奥特曼]"><img src="/static/layui/images/face/66.gif" ></li><li title="[礼物]"><img src="/static/layui/images/face/67.gif" ></li><li title="[钟]"><img src="/static/layui/images/face/68.gif" ></li><li title="[话筒]"><img src="/static/layui/images/face/69.gif" ></li><li title="[蜡烛]"><img src="/static/layui/images/face/70.gif" ></li><li title="[蛋糕]"><img src="/static/layui/images/face/71.gif" ></li></ul><i class="layui-layer-TipsG layui-layer-TipsT"></i></div><span class="layui-layer-setwin"></span></div>';
	function showEmoji(obj)
	{
		$(".emojiHtml").html(emojiHtml);
		$(obj).parents('.tool-class').children('.emojiHtml').show('', function(){
			$("body").click(function(event) {
				$(".emojiHtml").hide();
				$(this).unbind('click');
			});
		});
		$(".emojiHtml img").click(function(event) {
			var imgText = $(this).parents("li").attr("title");
			var thisTextArea = $(this).parents('.tool-class').parents(".post").children('textarea');
			thisTextArea.val(thisTextArea.val()+imgText);
			thisTextArea.focus().trigger('keyup');
			$(this).unbind('click');
		});
	}
	function ReplaceEmoji(str){
		if (!str) return false;
	  	var r, re;
	   	re = /(\[[\u4e00-\u9fa5]{1,}\])/mig;
	   	r = str.match(re);
	   	if (!r) return str;
	   	for (var i = 0; i <= r.length - 1; i++) {
	   		// var imgEmoji = $(".layui-clear").children('li[title="'+r[i]+'"]').html();
			var toReplHtml = $(".layui-clear").children('li[title="'+r[i]+'"]').html();
			if (toReplHtml) {
				str = str.replace(r[i], toReplHtml);
			}
	   	}
	   	return(str);
	}
	function removeImg(obj)
	{
		// $(".imgHtml").hide();
		$(".tool-up-class").hide();
		$("#imgVal").val('')
		// $(obj).detach();
	}


	// function showMessageImg(config) {
	// 	if(!config.src || config.src==""){
	// 		layer.msg("没有发现图片！");
	// 		return ;
	// 	}
	// 	var default_config = {title: "图片预览"};
	// 	var img = new Image();  
	// 	img.onload = function() {//避免图片还未加载完成无法获取到图片的大小。
	// 		//避免图片太大，导致弹出展示超出了网页显示访问，所以图片大于浏览器时下窗口可视区域时，进行等比例缩小。
	// 		var max_height = $(window).height() - 100;
	// 		var max_width = $(window).width();
	// 		//rate1，rate2，rate3 三个比例中取最小的。
	// 		var rate1 = max_height/img.height;
	// 		var rate2 = max_width/img.width;
	// 		var rate3 = 1;
	// 		var rate = Math.min(rate1,rate2,rate3); 
	// 		//等比例缩放
	// 		default_config.height = img.height * rate; //获取图片高度
	// 		default_config.width = img.width  * rate; //获取图片宽度
			
	// 		$.extend( default_config, config);
	// 		var imgHtml = "<img class='mouseWheelHandlerImg' src='" + default_config.src + "' width='"+default_config.width+"px' height='"+default_config.height+"px'/>";  
	// 		//弹出层
	// 		layer.open({  
	// 			type: 1,  
	// 			shade: 0.8,
	// 			offset: 'auto',
	// 			area: [default_config.width + 'px',default_config.height + 50 +'px'],
	// 			shadeClose:true,
	// 			scrollbar: false,
	// 			title: default_config.title, //不显示标题  
	// 			content: imgHtml, //捕获的元素，注意：最好该指定的元素要存放在body最外层，否则可能被其它的相对元素所影响  
	// 			cancel: function () {  
	// 				//layer.msg('捕获就是从页面已经存在的元素上，包裹layer的结构', { time: 5000, icon: 6 });  
	// 			}  
	// 		}); 
	// 		mouseWheelHandler('mouseWheelHandlerImg');
	// 	}
	// 	img.src = config.src;
	// }

	// function mouseWheelHandler(className)
	// {
	// 	// var obj = document.getElementsByClassName(className);
	// 	var obj = $('.'+className)
	// 	console.log(obj)
	// 	obj.each(function (i){
	// 		var objIndex = $(this);
	// 		console.log(objIndex)
	// 		// IE9, Chrome, Safari, Opera  
	// 		objIndex.addEventListener("mousewheel", mouseWheelFunc, false);  
	// 		// Firefox  
	// 		objIndex.addEventListener("DOMMouseScroll", mouseWheelFunc, false); 
	// 		objIndex.attachEvent("onmousewheel", mouseWheelFunc);
	// 		function mouseWheelFunc(e) {
	// 			var e = window.event || e; // old IE support  
	// 			var delta = Math.max(-1, Math.min(1, (e.wheelDelta || -e.detail)));
	// 			objIndex.style.width = Math.max(50, Math.min(1800, objIndex.width + (30 * delta))) + "px"; 
	// 			return false;  
	// 		}
	// 	})
		
		
	// }
	function showMessageImg(obj)
	{
		var imgUrl = $(obj).attr('src');
		imgUrl = imgUrl.split(".");
		imgUrl[0] = imgUrl[0].replace("_middle", '');
		var json = {
		  "title": "", //相册标题
		  "id": 0, //相册id
		  "start": 0, //初始显示的图片序号，默认0
		  "data": [   //相册包含的图片，数组格式
		    {
		      "alt": "",
		      "pid": 0, //图片id
		      "src": imgUrl[0]+"."+imgUrl[1], //原图地址
		      "thumb": "" //缩略图地址
		    }
		  ]
		}
		layer.photos({
		    photos: json
		    ,anim: 5
		});
	}

	// function iniVideo(i)
	// {
	// 	$(".massageImg").each(function (index) {
	// 		var url = $(this).attr('vid')
	// 		if (!$(this).hasClass("showVideo"+i)) return
	// 		if (url) {
	// 			const dp = new DPlayer({
	// 				container: document.getElementsByClassName('massageImg')[index],
	// 				autoplay: false,
	// 				theme: '#d5f3f4',
	// 				lang: 'zh-cn',
	// 				video: {
	// 					url: url,
	// 					type: 'auto',
	// 				},
	// 			});
	// 		}
	// 	})
	// }

	function checkMediaType(url) {
		// 创建URL对象
		var link = new URL(url);
	   
		// 获取路径部分（去除参数）
		var path = link.href;
	    var media = {}
		// 获取路径的最后一个点之后的内容作为文件扩展名
		var linkArr = path.split('.');
		var extension = linkArr.pop().toLowerCase();
		var image_info = path.substring(0, path.lastIndexOf("."));
		media.image_info = image_info
		media.image_type = extension
		var extensions = ['jpg', 'jpeg', 'gif', 'png', 'mp4', 'm3u8'];
		// 判断文件扩展名是否在图片扩展名数组中
		if (extensions.includes(extension)) {
		  return media;
		}

		return false;
	  }
	  

	  // 获取url中需要的数据  type  1: 获取文件名  2：获取后缀  3：获取文件名+后缀  4:获取文件前缀
// function urlDemo(url, type) {
//     let filename = url.substring(url.lastIndexOf('/') + 1)
//     switch (type){
//         case 1: return filename; break;
//         case 2: return filename.substring(filename.lastIndexOf(".") + 1); break;
//         case 3: return filename.substring(0, filename.lastIndexOf(".")); break;
//         case 4: return url.substring(0, url.lastIndexOf('/') + 1)
//     }   
// }

// console.log(urlDemo(url, 1))
// console.log(urlDemo(url, 2))
// console.log(urlDemo(url, 3))
// console.log(urlDemo(url, 4))
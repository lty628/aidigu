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
	var len=obj.value.replace(/[^\x00-\xff]/g,'oo').length; 
	var llen=maxLen-len;
	if(len>maxLen) {
		var i=0; 
		for(var z=0;z<len;z++) {
			if(obj.value.charCodeAt(z)>255) {i=i+2;}else {i=i+1;} 
			if(i>=maxLen) {obj.value=obj.value.slice(0,(z + 1)); break; } 
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
		    ,method: 'GET'
		    ,done: function(res){
		      	if (res.status) {
		      		var data = res.data;
		      		$(".imgHtml img").attr('src', data.small);
		      		$(".imgHtml").append('<i class="layui-icon layui-icon-close-fill" onclick="removeImg(this)"  style="color: red;cursor:pointer;font-size:20px!important"></i>')
		      		$("#imgVal").val(JSON.stringify(data));
		      	}
		    }
		    ,error: function(){
		      //请求异常回调
		    }
		  });
		});
		$("#button").click(function(event) {
			var jsonData = {};
			jsonData.contents = $("#msgInput").val();
			jsonData.imageInfo = $("#imgVal").val();
			if (!jsonData.contents) {
				alertMsg('请输入微博内容！');
				$("#msgInput").focus();
				return false;
			}
			jsonData.contents = ReplaceEmoji(jsonData.contents)
			var config = {};
			config.action = 'index';
			config.index = '';
			config.additional = 1;
			config.flush = 0;
			sendMsg(jsonData, config)
		});
	})
	function sendMsg(jsonData, config)
	{
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
				// 
				if (action == 'index') {
					$(".sendMsg").text(data.msg);
					$(".sendMsg").slideDown().delay(800).slideUp();
				} else {
					alertMsg(data.msg, 0, flush);
				}
				if (index) layer.close(index);
				if (data.status) {
					$("#msgInput").val('');
					check_len1();
					if (additional) {
						var userInfo = $.parseJSON($("#userInfo").val());
						var message = data.data;
						if (message.image) {
							var str = '<div class="entry"><div class="avatar"><div class="imgborder"><a href="/'+userInfo.blog+'/"><img src="'+userInfo.head_image+'" /></a></div></div><div class="box"><p><a href="/'+userInfo.blog+'/">'+userInfo.nickname+'：</a>'+message.contents+message.repost+'</p><p><img width="150px"  onclick="showMessageImg(this)" src="'+message.image+'"></p><div class="static"><span><a href="/'+userInfo.blog+'/message/'+message.msg_id+'" target="_blank">查看</a> | <a href="javascript:void(0);" onclick="repost(this)"> 转发 </a> |<a href="javascript:void(0);" onclick="comment('+message.msg_id+', {$siteUserId});"> 评论 </a>| <a href="/'+userInfo.blog+'/del/message/'+message.msg_id+'">删除</a></span>刚刚 来自 '+message.refrom+'</div></div><div class="clear"></div></div>';
						} else {
							var str = '<div class="entry"><div class="avatar"><div class="imgborder"><a href="/'+userInfo.blog+'/"><img src="'+userInfo.head_image+'" /></a></div></div><div class="box"><p><a href="/'+userInfo.blog+'/">'+userInfo.nickname+'：</a>'+message.contents+message.repost+'</p><div class="static"><span><a href="/'+userInfo.blog+'/message/'+message.msg_id+'" target="_blank">查看</a> | <a href="javascript:void(0);" onclick="repost(this)"> 转发 </a> |<a href="javascript:void(0);" onclick="comment('+message.msg_id+', {$siteUserId});"> 评论 </a>| <a href="/'+userInfo.blog+'/del/message/'+message.msg_id+'">删除</a></span>刚刚 来自 '+message.refrom+'</div></div><div class="clear"></div></div>';
						}
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
		var repost = '@'+$(obj).parents(".box").children('p').html();
		layer.open({
		  type: 1,
		  skin: 'layui-layer-rim', //加上边框
		  area: ['625px', '320px'], //宽高
		  title: "<b>转发：</b>"+repost,
		  content: '<div class="contents"><div class="post" id="postform"><div class="postad">&nbsp;</div><textarea placeholder="请输入转发内容" maxlength="140" id="repostInput" onkeyup="check_len2()"></textarea><div class="postnow">你还可以发布<em id="leftlen2">140</em>字</div><div class="tool-class"><i class="layui-icon layui-icon-face-smile-b" onclick="showEmoji(this)" style="margin: 10px;cursor:pointer;color: #ffa700;font-size:20px!important" title="表情"></i><div class="emojiHtml" style="display: none"></div></div></div></div>',
		  btn: ['转发',"取消"],
		  yes: function(index, layero){
		  	var jsonData = {};
		  	jsonData.contents = $("#repostInput").val();
		  	jsonData.contents = ReplaceEmoji(jsonData.contents);
		  	jsonData.repost = repost;
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
		var replyArr = replyUser.split('||<a');
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
		  content: '<div class="contents"><div class="post" id="commentform"><div class="postad" >&nbsp;</div><textarea placeholder="请输入回复内容" maxlength="140" id="commentInput" onkeyup="check_len3()"></textarea><div class="postnow">你还可以发布<em id="leftlen3">140</em>字</div><div class="tool-class"><i class="layui-icon layui-icon-face-smile-b" onclick="showEmoji(this)" style="margin: 10px;cursor:pointer;color: #ffa700;font-size:20px!important" title="表情"></i><div class="emojiHtml" style="display: none"></div></div></div></div>',
		  btn: ['回复',"取消"],
		  yes: function(index, layero){
		  	var jsonData = {};
		  	jsonData.comment = '||回复：'+$("#commentInput").val()+' ||'+replyUser;
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
	var emojiHtml = '<div class="layui-util-face" style="z-index: 100; position: fixed;"><div id="" class="layui-layer-content"><ul class="layui-clear"><li title="[微笑]"><img src="/static/layui/images/face/0.gif" alt="[微笑]"></li><li title="[嘻嘻]"><img src="/static/layui/images/face/1.gif" alt="[嘻嘻]"></li><li title="[哈哈]"><img src="/static/layui/images/face/2.gif" alt="[哈哈]"></li><li title="[可爱]"><img src="/static/layui/images/face/3.gif" alt="[可爱]"></li><li title="[可怜]"><img src="/static/layui/images/face/4.gif" alt="[可怜]"></li><li title="[挖鼻]"><img src="/static/layui/images/face/5.gif" alt="[挖鼻]"></li><li title="[吃惊]"><img src="/static/layui/images/face/6.gif" alt="[吃惊]"></li><li title="[害羞]"><img src="/static/layui/images/face/7.gif" alt="[害羞]"></li><li title="[挤眼]"><img src="/static/layui/images/face/8.gif" alt="[挤眼]"></li><li title="[闭嘴]"><img src="/static/layui/images/face/9.gif" alt="[闭嘴]"></li><li title="[鄙视]"><img src="/static/layui/images/face/10.gif" alt="[鄙视]"></li><li title="[爱你]"><img src="/static/layui/images/face/11.gif" alt="[爱你]"></li><li title="[泪]"><img src="/static/layui/images/face/12.gif" alt="[泪]"></li><li title="[偷笑]"><img src="/static/layui/images/face/13.gif" alt="[偷笑]"></li><li title="[亲亲]"><img src="/static/layui/images/face/14.gif" alt="[亲亲]"></li><li title="[生病]"><img src="/static/layui/images/face/15.gif" alt="[生病]"></li><li title="[太开心]"><img src="/static/layui/images/face/16.gif" alt="[太开心]"></li><li title="[白眼]"><img src="/static/layui/images/face/17.gif" alt="[白眼]"></li><li title="[右哼哼]"><img src="/static/layui/images/face/18.gif" alt="[右哼哼]"></li><li title="[左哼哼]"><img src="/static/layui/images/face/19.gif" alt="[左哼哼]"></li><li title="[嘘]"><img src="/static/layui/images/face/20.gif" alt="[嘘]"></li><li title="[衰]"><img src="/static/layui/images/face/21.gif" alt="[衰]"></li><li title="[委屈]"><img src="/static/layui/images/face/22.gif" alt="[委屈]"></li><li title="[吐]"><img src="/static/layui/images/face/23.gif" alt="[吐]"></li><li title="[哈欠]"><img src="/static/layui/images/face/24.gif" alt="[哈欠]"></li><li title="[抱抱]"><img src="/static/layui/images/face/25.gif" alt="[抱抱]"></li><li title="[怒]"><img src="/static/layui/images/face/26.gif" alt="[怒]"></li><li title="[疑问]"><img src="/static/layui/images/face/27.gif" alt="[疑问]"></li><li title="[馋嘴]"><img src="/static/layui/images/face/28.gif" alt="[馋嘴]"></li><li title="[拜拜]"><img src="/static/layui/images/face/29.gif" alt="[拜拜]"></li><li title="[思考]"><img src="/static/layui/images/face/30.gif" alt="[思考]"></li><li title="[汗]"><img src="/static/layui/images/face/31.gif" alt="[汗]"></li><li title="[困]"><img src="/static/layui/images/face/32.gif" alt="[困]"></li><li title="[睡]"><img src="/static/layui/images/face/33.gif" alt="[睡]"></li><li title="[钱]"><img src="/static/layui/images/face/34.gif" alt="[钱]"></li><li title="[失望]"><img src="/static/layui/images/face/35.gif" alt="[失望]"></li><li title="[酷]"><img src="/static/layui/images/face/36.gif" alt="[酷]"></li><li title="[色]"><img src="/static/layui/images/face/37.gif" alt="[色]"></li><li title="[哼]"><img src="/static/layui/images/face/38.gif" alt="[哼]"></li><li title="[鼓掌]"><img src="/static/layui/images/face/39.gif" alt="[鼓掌]"></li><li title="[晕]"><img src="/static/layui/images/face/40.gif" alt="[晕]"></li><li title="[悲伤]"><img src="/static/layui/images/face/41.gif" alt="[悲伤]"></li><li title="[抓狂]"><img src="/static/layui/images/face/42.gif" alt="[抓狂]"></li><li title="[黑线]"><img src="/static/layui/images/face/43.gif" alt="[黑线]"></li><li title="[阴险]"><img src="/static/layui/images/face/44.gif" alt="[阴险]"></li><li title="[怒骂]"><img src="/static/layui/images/face/45.gif" alt="[怒骂]"></li><li title="[互粉]"><img src="/static/layui/images/face/46.gif" alt="[互粉]"></li><li title="[心]"><img src="/static/layui/images/face/47.gif" alt="[心]"></li><li title="[伤心]"><img src="/static/layui/images/face/48.gif" alt="[伤心]"></li><li title="[猪头]"><img src="/static/layui/images/face/49.gif" alt="[猪头]"></li><li title="[熊猫]"><img src="/static/layui/images/face/50.gif" alt="[熊猫]"></li><li title="[兔子]"><img src="/static/layui/images/face/51.gif" alt="[兔子]"></li><li title="[ok]"><img src="/static/layui/images/face/52.gif" alt="[ok]"></li><li title="[耶]"><img src="/static/layui/images/face/53.gif" alt="[耶]"></li><li title="[good]"><img src="/static/layui/images/face/54.gif" alt="[good]"></li><li title="[NO]"><img src="/static/layui/images/face/55.gif" alt="[NO]"></li><li title="[赞]"><img src="/static/layui/images/face/56.gif" alt="[赞]"></li><li title="[来]"><img src="/static/layui/images/face/57.gif" alt="[来]"></li><li title="[弱]"><img src="/static/layui/images/face/58.gif" alt="[弱]"></li><li title="[草泥马]"><img src="/static/layui/images/face/59.gif" alt="[草泥马]"></li><li title="[神马]"><img src="/static/layui/images/face/60.gif" alt="[神马]"></li><li title="[囧]"><img src="/static/layui/images/face/61.gif" alt="[囧]"></li><li title="[浮云]"><img src="/static/layui/images/face/62.gif" alt="[浮云]"></li><li title="[给力]"><img src="/static/layui/images/face/63.gif" alt="[给力]"></li><li title="[围观]"><img src="/static/layui/images/face/64.gif" alt="[围观]"></li><li title="[威武]"><img src="/static/layui/images/face/65.gif" alt="[威武]"></li><li title="[奥特曼]"><img src="/static/layui/images/face/66.gif" alt="[奥特曼]"></li><li title="[礼物]"><img src="/static/layui/images/face/67.gif" alt="[礼物]"></li><li title="[钟]"><img src="/static/layui/images/face/68.gif" alt="[钟]"></li><li title="[话筒]"><img src="/static/layui/images/face/69.gif" alt="[话筒]"></li><li title="[蜡烛]"><img src="/static/layui/images/face/70.gif" alt="[蜡烛]"></li><li title="[蛋糕]"><img src="/static/layui/images/face/71.gif" alt="[蛋糕]"></li></ul><i class="layui-layer-TipsG layui-layer-TipsT"></i></div><span class="layui-layer-setwin"></span></div>';
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
			var imgText = $(this).attr("alt");
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
	   		str = str.replace(r[i], $(".layui-clear").children('li[title="'+r[i]+'"]').html());
	   	}
	   	return(str);
	}
	function removeImg(obj)
	{
		$(".imgHtml img").attr('src', '');
		$("#imgVal").val('')
		$(obj).detach();
	}
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
		      "src": imgUrl[0]+"_big."+imgUrl[1], //原图地址
		      "thumb": "" //缩略图地址
		    }
		  ]
		}
		layer.photos({
		    photos: json
		    ,anim: 5
		});
	}
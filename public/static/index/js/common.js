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
		// $(".sendMsg").slideDown().delay(800).slideUp();
		$("#button").click(function(event) {
			var jsonData = {};
			jsonData.contents = $("#msgInput").val();
			if (!jsonData.contents) {
				alertMsg('请输入微博内容！');
				$("#msgInput").focus();
				return false;
			}
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
						var str = '<div class="entry"><div class="avatar"><div class="imgborder"><a href="/'+userInfo.blog+'/"><img src="'+userInfo.head_image+'" /></a></div></div><div class="box"><p><a href="'+userInfo.head_image+'">'+userInfo.nickname+'：</a>'+message.contents+message.repost+'</p><div class="static"><span><a href="/'+userInfo.blog+'/message/'+message.msg_id+'" target="_blank">查看</a> | <a href="javascript:void(0);" onclick="repost(this)"> 转发 </a> |<a href="javascript:void(0);" onclick="comment('+message.msg_id+', {$siteUserId});"> 评论 </a>| <a href="/'+userInfo.blog+'/del/message/'+message.msg_id+'">删除</a></span>刚刚 来自 '+message.refrom+'</div></div><div class="clear"></div></div>';
						$("#msgContent").prepend(str);
						var num = $("#messageSum").text();
						$("#messageSum").text(parseInt(num)+1);
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
		var repost = '@'+$(obj).parents(".box").children('p').text();
		layer.open({
		  type: 1,
		  skin: 'layui-layer-rim', //加上边框
		  area: ['625px', '285px'], //宽高
		  title: "<b>转发：</b>"+repost,
		  content: '<div class="contents"><div class="post" id="postform"><div class="postad">&nbsp;</div><textarea placeholder="请输入转发内容" maxlength="140" id="repostInput" onkeyup="check_len2()"></textarea><div class="postnow">你还可以发布<em id="leftlen2">140</em>字</div></div></div>',
		  btn: ['转发',"取消"],
		  yes: function(index, layero){
		  	var jsonData = {};
		  	jsonData.contents = $("#repostInput").val();
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
		  area: ['625px', '285px'], //宽高
		  title: "<b>评论：</b>",
		  content: '<div class="contents"><div class="post" id="commentform"><div class="postad" >&nbsp;</div><textarea placeholder="请输入评论内容" maxlength="140" id="commentInput" onkeyup="check_len3()"></textarea><div class="postnow">你还可以发布<em id="leftlen3">140</em>字</div></div></div>',
		  btn: ['评论',"取消"],
		  yes: function(index, layero){
		  	var jsonData = {};
		  	jsonData.comment = $("#commentInput").val();
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
		  area: ['625px', '285px'], //宽高
		  title: "<b>回复：</b>"+replyUser,
		  content: '<div class="contents"><div class="post" id="commentform"><div class="postad" >&nbsp;</div><textarea placeholder="请输入回复内容" maxlength="140" id="commentInput" onkeyup="check_len3()"></textarea><div class="postnow">你还可以发布<em id="leftlen3">140</em>字</div></div></div>',
		  btn: ['回复',"取消"],
		  yes: function(index, layero){
		  	var jsonData = {};
		  	jsonData.comment = '||回复：'+$("#commentInput").val()+' ||'+replyUser;
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
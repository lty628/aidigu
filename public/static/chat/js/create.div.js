/*创建div dom*/
var cdiv = {
	userlists : function(listtagid,is_none){
		var arr = [];
		arr = [ '<div class="conv-lists" id="conv-lists-',listtagid,'" style="display:',is_none,'"></div>'];
		return arr.join('');
	},
	chatlists : function(listtagid,is_none){
		var arr = [];
		arr = [ '<div class="msg-items" id="chatLineHolder-'+listtagid+'" style="display:',is_none,'"></div>'];
		return arr.join('');
	},
	render : function(template,params){
		var arr = [];
		switch(template){
			case 'mymessage':
				arr = [
					'<div style="display: block;" class="msg-box"><div class="chat-item me"><div class="clearfix"><div class="head_image"><div class="normal user-head_image" style="background-image: url(',params.head_image,');"></div></div><div class="msg-bubble-box"><div class="msg-bubble-area"><div><div class="msg-bubble"><pre class="text">', params.content,'</pre></div></div></div></div></div></div></div>'
				];
			break;
			
			case 'chatLine':
				arr = [
					'<div style="display: block;" class="msg-box"><div class="chat-item not-me"><div class="chat-profile-info clearfix"><span class="profile-wrp"><span class="name clearfix"><span class="name-text">',params.nickname,'</span></span></span><span class="chat-time">',params.time,'</span></div><div class="clearfix"><div class="head_image"><div class="normal user-head_image" onclick="chat.changeUser(this)" fd="',params.fd,'" uname="',params.nickname,'" style="background-image: url(\'',params.head_image,'\');"></div></div><div class="msg-bubble-box"><div class="msg-bubble-area"><div class="msg-bubble"><pre class="text">',params.content,'</pre></div></div></div></div></div></div>'
				];
			break;
			
			case 'user':
				console.log(params)
				arr = [
					'<div id=\'user-',params.uid,'\' uid="',params.uid,'" onclick="chat.changeUser(this)" fd="',params.fd,'" uname="',params.nickname,'" class="list-item conv-item context-menu conv-item-company"><i class="iconfont icon-delete-conv tipper-attached"></i><div class="head_image-wrap"><div class="group-head_image"><div class="normal group-logo-head_image" style="background-image: url(',params.head_image,');"></div></div></div><div class="conv-item-content"><div class="title-wrap info"><div class="name-wrap"><p class="name">',params.nickname,'</p></div><span class="time">',params.time,'</span></div></div></div>'
				];
			break;
			case 'newlogin':
				arr = [
					'<div class="chat-status chat-system-notice">系统消息：欢迎&nbsp;',params.nickname,'&nbsp;加入群聊</div>'
				];
				break;
			case 'logout':
				arr = [
					'<div class="chat-status chat-system-notice">系统消息：&nbsp;',params.nickname,'&nbsp;退出了群聊</div>'
				];
				break;
			case 'my':
				console.log(params)
				arr = [
					'<div class="big-52 with-border user-head_image" uid="',params.uid,'" title="',params.nickname,'" style="margin-left: 10px; margin-top: 3px;background-image: url(',params.head_image,');"></div>'
				];
				break;
			case 'listtags':
				arr = [
					'<li class="menu-item ',params.selected,'" listtagid="',params.listtagid,'" onclick="chat.changeList(this)">',params.listtagname,'<span id="message-',params.listtagid,'">0</span></li>'
				];
				break;
		}
		return arr.join('');
	}
	
}

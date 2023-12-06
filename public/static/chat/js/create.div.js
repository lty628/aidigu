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
				if (params.message_count) {
					arr = [
						'<div id=\'user-',params.listtagid,'-',params.uid,'\' listtagid="',params.listtagid,'" uid="',params.uid,'" onclick="chat.changeUser(this)" uname="',params.nickname,'" class="list-item conv-item context-menu conv-item-company"><i class="iconfont icon-delete-conv tipper-attached"></i><div class="head_image-wrap"><div class="group-head_image"><div class="normal group-logo-head_image" style="background-image: url(',params.head_image,');"></div></div></div><div class="conv-item-content"><div class="title-wrap info"><div class="name-wrap"><p class="name">',params.nickname,'</p></div></div></div><span class="layui-badge" style="font-size:10px" id="message-',params.listtagid,'-',params.uid,'">未读</span></div>'
					];
				} else {
					arr = [
						'<div id=\'user-',params.listtagid,'-',params.uid,'\' listtagid="',params.listtagid,'" uid="',params.uid,'" onclick="chat.changeUser(this)" uname="',params.nickname,'" class="list-item conv-item context-menu conv-item-company"><i class="iconfont icon-delete-conv tipper-attached"></i><div class="head_image-wrap"><div class="group-head_image"><div class="normal group-logo-head_image" style="background-image: url(',params.head_image,');"></div></div></div><div class="conv-item-content"><div class="title-wrap info"><div class="name-wrap"><p class="name">',params.nickname,'</p></div></div></div><span class="layui-badge" style="display:none; font-size:10px" id="message-',params.listtagid,'-',params.uid,'">未读</span></div>'
					];
				}
				
			break;
			case 'group':
				if (params.message_count) {
					arr = [
						'<div id=\'group-',params.listtagid,'-',params.groupid,'\' listtagid="',params.listtagid,'" groupid="',params.groupid,'" onclick="chat.changeGroup(this)" uname="',params.groupname,'" class="list-item conv-item context-menu conv-item-company"><i class="iconfont icon-delete-conv tipper-attached"></i><div class="head_image-wrap"><div class="group-head_image"><div class="normal group-logo-head_image" style="background-image: url(',params.head_image,');"></div></div></div><div class="conv-item-content"><div class="title-wrap info"><div class="name-wrap"><p class="name">',params.groupname,'</p></div></div></div><span class="layui-badge" style="font-size:10px" id="message-',params.listtagid,'-',params.groupid,'">未读</span></div>'
					];
				} else {
					arr = [
						'<div id=\'group-',params.listtagid,'-',params.groupid,'\' listtagid="',params.listtagid,'" groupid="',params.groupid,'" onclick="chat.changeGroup(this)" uname="',params.groupname,'" class="list-item conv-item context-menu conv-item-company"><i class="iconfont icon-delete-conv tipper-attached"></i><div class="head_image-wrap"><div class="group-head_image"><div class="normal group-logo-head_image" style="background-image: url(',params.head_image,');"></div></div></div><div class="conv-item-content"><div class="title-wrap info"><div class="name-wrap"><p class="name">',params.groupname,'</p></div></div></div><span class="layui-badge" style="display:none; font-size:10px" id="message-',params.listtagid,'-',params.groupid,'">未读</span></div>'
					];
				}
				
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
				arr = [
					'<div class="big-52 with-border user-head_image" uid="',params.uid,'" title="',params.nickname,'" style="margin-left: 10px; margin-top: 3px;background-image: url(',params.head_image,');"></div>'
				];
				break;
			case 'listtags':
				arr = [
					'<li class="menu-item ',params.selected,'" listtagid="',params.listtagid,'" onclick="chat.changeList(this)">',params.listtagname,'<span id="listtag-',params.listtagid,'">0</span></li>'
				];
				break;
		}
		return arr.join('');
	}
	
}

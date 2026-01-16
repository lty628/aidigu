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
					'<div class="msg-item right"><div class="msg-avatar" style="background-image: url(',params.head_image,'); background-size: cover; background-position: center;"></div><div class="msg-bubble">', params.content,'</div><div class="msg-time">', params.ctime, '</div></div>'
				];
			break;
			
			case 'chatLine':
				arr = [
					'<div class="msg-item left"><div class="msg-avatar" style="background-image: url(',params.head_image,'); background-size: cover; background-position: center;"></div><div class="msg-bubble">',params.content,'</div><div class="msg-time">',params.ctime,'</div></div>'
				];
			break;
			
			case 'user':
				if (params.message_count) {
					arr = [
						'<div id=\'user-',params.listtagid,'-',params.uid,'\' listtagid="',params.listtagid,'" uid="',params.uid,'" onclick="chat.changeUser(this)" uname="',params.nickname,'" class="conv-item"><div class="conv-avatar" style="background-image: url(',params.head_image,'); background-size: cover; background-position: center;"></div><div class="conv-info"><div class="conv-name">',params.nickname,'</div><div class="conv-preview">',params.content ? params.content.substring(0, 30) + (params.content.length > 30 ? '...' : '') : '','</div></div><div class="conv-time">',params.ctime ? params.ctime : '','</div><span class="layui-badge" style="font-size:10px" id="message-',params.listtagid,'-',params.uid,'">未读</span></div>'
					];
				} else {
					arr = [
						'<div id=\'user-',params.listtagid,'-',params.uid,'\' listtagid="',params.listtagid,'" uid="',params.uid,'" onclick="chat.changeUser(this)" uname="',params.nickname,'" class="conv-item"><div class="conv-avatar" style="background-image: url(',params.head_image,'); background-size: cover; background-position: center;"></div><div class="conv-info"><div class="conv-name">',params.nickname,'</div><div class="conv-preview">',params.content ? params.content.substring(0, 30) + (params.content.length > 30 ? '...' : '') : '','</div></div><div class="conv-time">',params.ctime ? params.ctime : '','</div><span class="layui-badge" style="display:none; font-size:10px" id="message-',params.listtagid,'-',params.uid,'">未读</span></div>'
					];
				}
				
			break;
			case 'group':
				if (params.message_count) {
					arr = [
						'<div id=\'group-',params.listtagid,'-',params.groupid,'\' listtagid="',params.listtagid,'" groupid="',params.groupid,'" onclick="chat.changeGroup(this)" uname="',params.groupname,'" class="conv-item"><div class="conv-avatar" style="background-image: url(',params.head_image,'); background-size: cover; background-position: center;"></div><div class="conv-info"><div class="conv-name">',params.groupname,'</div><div class="conv-preview">',params.content ? params.content.substring(0, 30) + (params.content.length > 30 ? '...' : '') : '','</div></div><div class="conv-time">',params.ctime ? params.ctime : '','</div><span class="layui-badge" style="font-size:10px" id="message-',params.listtagid,'-',params.groupid,'">未读</span></div>'
					];
				} else {
					arr = [
						'<div id=\'group-',params.listtagid,'-',params.groupid,'\' listtagid="',params.listtagid,'" groupid="',params.groupid,'" onclick="chat.changeGroup(this)" uname="',params.groupname,'" class="conv-item"><div class="conv-avatar" style="background-image: url(',params.head_image,'); background-size: cover; background-position: center;"></div><div class="conv-info"><div class="conv-name">',params.groupname,'</div><div class="conv-preview">',params.content ? params.content.substring(0, 30) + (params.content.length > 30 ? '...' : '') : '','</div></div><div class="conv-time">',params.ctime ? params.ctime : '','</div><span class="layui-badge" style="display:none; font-size:10px" id="message-',params.listtagid,'-',params.groupid,'">未读</span></div>'
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
				if (params.display == 'none') {
					break;
				}
				arr = [
					'<li class="menu-item ',params.selected,'" listtagid="',params.listtagid,'" onclick="chat.changeList(this)">',params.listtagname,'<span id="listtag-',params.listtagid,'">...</span></li>'
				];
				break;
		}
		return arr.join('');
	}
	
}
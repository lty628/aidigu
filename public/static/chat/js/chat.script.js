$(document).ready(function(){
	// Run the init method on document ready:
	// face.init();
	chat.init();
	
});
var blinkingTitle = {
	timer: false,
	title: '',
	start : function(newTitle, isParent){
		if (this.timer) {
			return
		}
		if (isParent) {
			this.title = parent.document.title
		} else {
			this.title = document.title
		}
		this.timer = setInterval(() => {
			if (isParent) {
				if (parent.document.title == newTitle) {
					parent.document.title = this.title;
				} else {
					parent.document.title = newTitle;
				}
			} else {
				if (document.title == newTitle) {
					document.title = '';
				} else {
					document.title = newTitle;
				}
			}
			
		}, 800);
	},
	stop : function(isParent) {
		if (!this.timer) {
			return
		}
		clearInterval(this.timer)
		this.timer = false
		if (isParent) {
			parent.document.title=this.title
		} else {
			document.title=this.title
		}
	}
}

var chat = {
	data : {
		wSock       : null,
		login		: false,
		storage     : null,
		type	    : 1,
		fd          : 0,
		name        : "",
		email       : "",
		head_image      : "",
		rds         : [],//所有房间ID
		crd         : 'a', //当前房间ID
		remains     : [],
		title       : '',
	},
	init : function (){
		// this.copyright();
		this.off();
		// chat.data.storage = window.localStorage;
		this.ws();
		this.data.title = window.parent.$(".layui-layer-title").text();
	},
	// doLogin : function( name , email ){
	// 	if(name == '' || email == ''){
	// 		name =  $("#name").val();
	// 		email = $('#email').val();
	// 	}
	// 	name = $.trim(name) ;
	// 	email = $.trim(email) ;
	// 	if(name == "" || email == ""){
	// 		chat.displayError('chatErrorMessage_logout',"请输入昵称和Email才可以参与群聊哦～",1);
	// 		return false;
	// 	}
	// 	var  re = /^(\w-*\.*)+@(\w-?)+(\.\w{2,})+$/;
	// 	if(!re.test(email)){
	// 		chat.displayError('chatErrorMessage_logout',"逗我呢，邮箱长成这样子？？",1);
	// 		return false;
	// 	}
	// 	//登录操作
	// 	chat.data.type = 1; //登录标志
	// 	chat.data.email = email; //邮箱
	// 	chat.data.login = true;
	// 	var json = {"type": chat.data.type, "listtagid": chat.data.listtagid,"name": name,"email": email,'listtagid':'a'};
	// 	chat.wsSend(JSON.stringify(json));
	// 	return false;
		 
	// },
	// logout : function(){
	// 	// if(!this.data.login) return false;
	// 	chat.data.type = 0;
	// 	chat.data.storage.removeItem('dologin');
	// 	chat.data.storage.removeItem('nickname');
	// 	chat.data.storage.removeItem('email');
	// 	chat.data.nickname = '';
	// 	chat.data.head_image = '';
	// 	location.reload() ;
	// },
	keySend : function( event ){
		// if ((event.shiftKey || event.ctrlKey) && event.keyCode == 13) {
		if (event.shiftKey && event.keyCode == 13) {
			// var text = editor.txt.html();
			// editor.txt.html(text+"<br>")
			return true
		}else if( event.keyCode == 13){
			event.preventDefault();//避免回车换行
			this.sendMessage();
			blinkingTitle.stop(true);
		}
	},
	sendMessage : function(){		
		// this.data.login = true
		// if(!this.data.login) return false;
		//发送消息操作
		var text = editor.txt.html();
		// var text = $('#chattext').val();
		if(!text) return false;
		chat.data.type = 2; //发送消息标志
		// console.log(chat.data)
		var json = {"type": chat.data.type, "listtagid": chat.data.listtagid, "content": text, 'content_type': '', "touid": chat.data.touid, 'head_image': chat.data.head_image, 'nickname': chat.data.nickname, 'fromuid': chat.data.uid, 'groupid': chat.data.groupid};
		chat.wsSend(JSON.stringify(json));
		editor.txt.clear()
		$("#msgInput").focus();
		return true;
	},
	ws : function(){
		this.data.wSock = new WebSocket(config.wsserver);
		this.wsOpen();
		this.wsMessage();
		this.wsOnclose();
		this.wsOnerror();
	},
	wsSend : function(data){
		this.data.wSock.send(data);
	},
	wsOpen : function (){
		this.data.wSock.onopen = function( event ){
			heartCheck.reset().start();
			//初始化房间
			// chat.print('wsopen',event);
			//判断是否已经登录过，如果登录过。自动登录。不需要再次输入昵称和邮箱
			/*
			var isLogin = chat.data.storage.getItem("dologin");
			if( isLogin ) {
				var name =  chat.data.storage.getItem("name");
				var email =  chat.data.storage.getItem("email");
				chat.doLogin( name , email );
			}
			*/
			var privateToUid = $("#privateToUid").val()
			if (privateToUid) {
				$("#sub-menu-pannel").hide();
				$("#menu-pannel").hide();
				chat.privateChat(privateToUid)
			}
		}
	},
	sendMedia: function(data) {
		var text = data.media_info+'.'+ data.media_type
		chat.data.type = 2; //发送消息标志
		var json = {"type": chat.data.type, "listtagid": chat.data.listtagid, "content": text, 'content_type': data.media_type, "touid": chat.data.touid, 'head_image': chat.data.head_image, 'nickname': chat.data.nickname, 'fromuid': chat.data.uid, 'groupid': chat.data.groupid};
		chat.wsSend(JSON.stringify(json));
	},
	initMessage: function(data, isHistory) {
		data.ctime = getDateDiff(new Date(data.create_time).getTime()/1000)
		if(data.fromuid == chat.data.uid){
			chat.addChatLine('mymessage',data,data.listtagid);
		} else {
			if (!isHistory) {
				if(data.remains){
					for(var i = 0 ; i < data.remains.length;i++){
						if(chat.data.fd == data.remains[i].fd){
							chat.shake();
							var msg = data.nickname + "在群聊@了你。";
							chat.displayError('chatErrorMessage_logout',msg,0);
						}
					}
				}
				chat.chatAudio();
				blinkingTitle.start('【未读消息】', true);
				chat.showMsgCount(data.listtagid);
				if (data.listtagid == 'Group') {
					if (!$("#group-"+data.listtagid+'-'+data.groupid).hasClass("selected")) {
						$("#message-"+data.listtagid+'-'+data.groupid).css('display', 'block')
						$("#conv-lists-"+data.listtagid).prepend($("#group-"+data.listtagid+"-"+data.groupid));
						return 
					}
				} else {
					if (!$("#user-"+data.listtagid+'-'+data.fromuid).hasClass("selected")) {
						$("#message-"+data.listtagid+'-'+data.fromuid).css('display', 'block')
						$("#conv-lists-"+data.listtagid).prepend($("#user-"+data.listtagid+"-"+data.fromuid));
						return 
					}
				}
				
			}
			chat.addChatLine('chatLine',data,data.listtagid);
			// $("#user-"+chat.data.fromid).children('.layui-badge').css('display', 'block')
			// 增加消息
		}
		if (!isHistory) {
			$("#chatLineHolder-"+data.listtagid+ " .msg-box .chat-time").slice(-10).each(function () { 
				$(this).html(getDateDiff(new Date($(this).attr("data-ctime")).getTime()/1000))
		   })
		}
	},
	wsMessage : function(){
		this.data.wSock.onmessage=function(event){
			heartCheck.reset().start();
			if (event.data == 'pong') {
				return 
			}
			var d = jQuery.parseJSON(event.data);
			switch(d.code){
				case 1:
					// console.log(d)
					// if(d.data.mine){
						chat.data.uid = d.data.uid;
						chat.data.nickname = d.data.nickname;
						chat.data.head_image = d.data.head_image;
						// chat.data.storage.setItem("dologin",1);
						// chat.data.storage.setItem("name",d.data.nickname);
						// chat.data.storage.setItem("email",chat.data.email);
						// console.log(chat.data)
						// document.title = d.data.nickname + '-' + document.title;
						chat.loginDiv(chat.data);
					// } 
					// chat.addChatLine('newlogin',d.data,d.data.listtagid);
					// chat.addUserLine('user',d.data);
					// chat.displayError('chatErrorMessage_login',d.msg,1);
					break;
				case 2:
					chat.initMessage(d.data, false)
					// if(d.data.fromuid == chat.data.uid){
					// 	chat.addChatLine('mymessage',d.data,d.data.listtagid);
					// 	$("#chattext").val('');
					// } else {
					// 	if(d.data.remains){
					// 		for(var i = 0 ; i < d.data.remains.length;i++){
					// 			if(chat.data.fd == d.data.remains[i].fd){
					// 				chat.shake();
					// 				var msg = d.data.nickname + "在群聊@了你。";
					// 				chat.displayError('chatErrorMessage_logout',msg,0);
					// 			}
					// 		}
					// 	}
					// 	chat.chatAudio();
					// 	chat.addChatLine('chatLine',d.data,d.data.listtagid);
					// 	//增加消息
					// 	chat.showMsgCount(d.data.listtagid,'show');
					// }
					break;
				case 3:
					if (d.data) {
						// console.log(d)
						$('#chatLineHolder-' + d.listtagid).html('');
						var len = d.data.length
						for (let index = len - 1; index >= 0; index--) {
							d.data[index].listtagid = d.listtagid
							chat.initMessage(d.data[index], true)
						}				
					}

					// chat.removeUser('logout',d.data);
					// if(d.data.mine && d.data.action == 'logout'){
						
					// 	return;
					// }
					// chat.displayError('chatErrorMessage_logout',d.msg,1);
					break;
				case 4: //页面初始化
					chat.initPage(d.data);
					break;
				case 5:
					if(d.data.mine){
						chat.displayError('chatErrorMessage_logout',d.msg,1);
					}
					break;
				case 6:
					if(d.data.mine){
						//如果是自己
						
					} else {
						//如果是其他人
						
					}
					//删除旧房间该用户
					chat.changeUser(d.data);
					chat.addUserLine('user',d.data);
					break;
				default :
					chat.displayError('chatErrorMessage_logout',d.msg,1);
			}
		}
	},
	wsOnclose : function(){
		this.data.wSock.onclose = function(event){
		}
	},
	wsOnerror : function(){
		this.data.wSock.onerror = function(event){

		}
	},
	showMsgCount:function(listtagid){
		// console.log(listtagid)
		if ($("#listtag-"+listtagid).parent().hasClass('selected')) {
			return
		}
		$("#listtag-"+listtagid).show()
		// if(!this.data.login) {return;}
		// if(type == 'hide'){
		// 	$("#message-"+listtagid).text(parseInt(0));
		// 	$("#message-"+listtagid).css('display','none');
		// } else {
		// 	if(chat.data.crd != listtagid){
		// 		$("#message-"+listtagid).css('display','block');
		// 		var msgtotal = $("#message-"+listtagid).text();
		// 		$("#message-"+listtagid).text(parseInt(msgtotal)+1);
		// 	}
		// }
	},
	/** 
	 * 当一个用户进来或者刷新页面触发本方法
	 *
	 */
	initPage:function( data ){
		// this.initRooms( data.listtags );
		this.initListTag( data.listtags );
		this.initUsers( data.users );
	},
	/**
	 * 填充房间用户列表
	 */
	initUsers : function( data ){
		// console.log(data);
		if(getJsonLength(data)){
			for(var item in data){
				var users = [];
				if(data[item]){
					if (item == 'Group') {
						for (key in data[item]) {
							data[item][key].listtagid=item
							// console.log(data[item][key])
							users.unshift(cdiv.render('group',data[item][key]));
							if (data[item][key].message_count) {
								chat.showMsgCount(item);
								blinkingTitle.start('【未读消息】', true);
							}
						}
					} else {
						for (key in data[item]) {
							data[item][key].listtagid=item
							// console.log(data[item][key])
							users.unshift(cdiv.render('user',data[item][key]));
							if (data[item][key].message_count) {
								chat.showMsgCount(item);
								blinkingTitle.start('【未读消息】', true);
							}
						}
					}
					
				}
				$('#conv-lists-' + item).html(users.join(''));
			}
		}
	},
	initListTag:function(data){
		var listtags = [];//房间列表
		var userlists = [];//用户列表
		var chatlists = [];//聊天列表
		if(data.length){
			var display = 'none';
			for(var i=0; i< data.length;i++){
				if(data[i]){
					//存储所有房间ID
					this.data.rds.push(data[i].listtagid);
					data[i].selected = '';
					if(i == 0){ 
						data[i].selected = 'selected';
						this.data.crd = data[i].listtagid; //存储第一间房间ID，自动设为默认房间ID
						display = 'block';//第一间房的用户列表和聊天记录公开
					} 
					//初始化每个房间的用户列表
					userlists.push(cdiv.userlists(data[i].listtagid,display));
					//初始化每个房间的聊天列表
					chatlists.push(cdiv.chatlists(data[i].listtagid,display));
					//创建所有的房间
					listtags.push(cdiv.render('listtags',data[i]));
					display = 'none';
				}
			}
			$('.main-menus').html(listtags.join(''));
			$("#user-lists").html(userlists.join(''));
			$("#chat-lists").html(chatlists.join(''));
		}
	},
	changeList: function (obj) {

		if ($("#isMobile").val()) {
			// $("#menu-pannel").css('display', 'none')
			$("#sub-menu-pannel").show()
			$("#content-pannel").hide();
		}
		window.parent.$(".layui-layer-title").text(chat.data.title);

		var tagid = $(obj).attr("listtagid")
		if ($(obj).hasClass("selected")) {
			return
		}
		$(".list-item").removeClass("selected")
		$("#listtag-"+tagid).hide()
		$(".menu-item").removeClass("selected")
		$(obj).addClass("selected")
		$(".conv-lists").css('display',"none");
		$(".msg-items").css('display',"none");
		$("#conv-lists-"+tagid).css('display',"block");
		$("#chatLineHolder-"+tagid).css('display',"block");
		$(".input-area").hide()
		$("#chat-lists").hide();
		// 默认选中第一个
		// $("#conv-lists-"+tagid).children().first().trigger('click')
	},
	showGroupUser: function(obj) {
		var groupid = chat.data.groupid
		if ($("#isMobile").val()) {
			var areaInfo = ['100%', '100%']
		} else {
			var areaInfo =  ['60%', '80%']
		}

		layer.open({
            title: '群成员信息:',
            type: 2,
            area: areaInfo,
            content: '/tools/chat/groupFriends?groupid=' + groupid,
            end: function () {
              // table.reload('initTable')
            }
          });
	},
	/**
	 * 1.初始化房间
	 * 2.初始化每个房间的用户列表
	 * 3.初始化每个房间的聊天列表
	 */
	// initRooms:function(data){
	// 	var listtags = [];//房间列表
	// 	var userlists = [];//用户列表
	// 	var chatlists = [];//聊天列表
	// 	if(data.length){
	// 		var display = 'none';
	// 		for(var i=0; i< data.length;i++){
	// 			if(data[i]){
	// 				//存储所有房间ID
	// 				this.data.rds.push(data[i].listtagid);
	// 				data[i].selected = '';
	// 				if(i == 0){ 
	// 					data[i].selected = 'selected';
	// 					this.data.crd = data[i].listtagid; //存储第一间房间ID，自动设为默认房间ID
	// 					display = 'block';//第一间房的用户列表和聊天记录公开
	// 				} 
	// 				//初始化每个房间的用户列表
	// 				userlists.push(cdiv.userlists(data[i].listtagid,display));
	// 				//初始化每个房间的聊天列表
	// 				chatlists.push(cdiv.chatlists(data[i].listtagid,display));
	// 				//创建所有的房间
	// 				listtags.push(cdiv.render('listtags',data[i]));
	// 				display = 'none';
	// 			}
	// 		}
	// 		$('.main-menus').html(listtags.join(''));
	// 		$("#user-lists").html(userlists.join(''));
	// 		$("#chat-lists").html(chatlists.join(''));
	// 	}
	// },
	loginDiv : function(data){
		/*设置当前房间*/
		/*显示头像*/
		$('.profile').html(cdiv.render('my',data));
		$('#loginbox').fadeOut(function(){
			// $('.input-area').fadeIn();
			$('.action-area').fadeIn();
			// $('.input-area').focus();
		});
	},
	privateChat : function(touid){
		
		var listtagid = 'PrivateLetter';

		//用户切换房间
		chat.data.touid = touid
		chat.data.listtagid = listtagid
		chat.data.fromuid = $("#privateFromUid").val()
		chat.data.uid = chat.data.fromuid
		var json = {"type": 3,"touid": touid, 'fromuid': chat.data.uid, "listtagid": listtagid};
		chat.wsSend(JSON.stringify(json));
		$(".input-area").show()
	},
	changeUser : function(obj){

		if ($("#isMobile").val()) {
			// $("#menu-pannel").css('display', 'none')
			$("#sub-menu-pannel").css('display', 'none')
			$("#content-pannel").show();
		}

		$("#showGroupUser").hide();

		window.parent.$(".layui-layer-title").text($(obj).attr("uname"));
		//未登录
		// if(!this.data.login) {
		// 	this.shake();
		// 	chat.displayError('chatErrorMessage_logout',"未登录用户不能查看房间哦～",1);
		// 	return false;
		// }
		var uid = $(obj).attr("uid");
		var listtagid = $(obj).attr("listtagid");
		// var userObj = $("#conv-lists-"+uid).find('#user-'+this.data.fd);
		// console.log(userObj)
		// if(userObj.length > 0){
		// 	return;
		// }
		$(".list-item").removeClass("selected")
		$(obj).addClass('selected')
		$(obj).children('.layui-badge').css('display', 'none')
		$("#chat-lists").show();
		blinkingTitle.stop(true);
		
		// $("#main-menus").children().removeClass("selected");
		// $("#user-lists").children().css("display","none");

		// $("#chat-lists").children().css("display","none");
		// $("#conv-lists-" + uid).css('display',"block");
		// $(obj).addClass('selected');
		// $("#chatLineHolder-" + uid).css('display',"block");
		// var olduserid = this.data.crd;
		// //设置当前房间
		// this.data.crd = uid;
		//用户切换房间
		chat.data.touid = uid
		chat.data.listtagid = listtagid
		chat.data.fromuid = chat.data.uid
		var json = {"type": 3,"touid": uid, 'fromuid': chat.data.uid, "listtagid": listtagid};
		chat.wsSend(JSON.stringify(json));
		$(".input-area").show()
	},
	changeGroup : function(obj){
		
		if ($("#isMobile").val()) {
			// $("#menu-pannel").css('display', 'none')
			$("#sub-menu-pannel").css('display', 'none')
			$("#content-pannel").show();
		}

		$("#showGroupUser").show();

		window.parent.$(".layui-layer-title").text($(obj).attr("uname"));

		var groupid = $(obj).attr("groupid");
		var listtagid = $(obj).attr("listtagid");
		$(".list-item").removeClass("selected")
		$(obj).addClass('selected')
		$(obj).children('.layui-badge').css('display', 'none')
		blinkingTitle.stop(true);

		

		chat.data.groupid = groupid
		chat.data.listtagid = listtagid
		chat.data.uid = chat.data.uid
		var json = {"type": 3,"groupid": groupid, 'uid': chat.data.uid, "listtagid": listtagid};
		chat.wsSend(JSON.stringify(json));
		$(".input-area").show()
		$("#chat-lists").show();
	},
	
	// The addChatLine method ads a chat entry to the page
	
	addChatLine : function(t,params,listtagid){
		var markup = cdiv.render(t,params);
		$("#chatLineHolder-"+listtagid).append(markup);
		this.scrollDiv('chat-lists');
	},
	// addUserLine : function(t,params){
	// 	var markup = cdiv.render(t,params);
	// 	$('#conv-lists-'+params.listtagid).append(markup);
	// },
	// removeUser : function (t,params){ //type 1=换房切换，0=退出
	// 	$("#user-"+params.fd).fadeOut(function(){
	// 		$(this).remove();
	// 		$("#chatLineHolder").append(cdiv.render(t,params));
	// 	});
	// },
	// changeUser : function( data ){
	// 	// console.log(data);
	// 	$("#conv-lists-"+data.oldlisttagid).find('#user-' + data.fd).fadeOut(function(){
	// 		chat.showMsgCount(data.listtagid,'hide');
	// 		$(this).remove();
	// 		//chat.addChatLine('logout',data,data.oldlisttagid);
	// 	});
	// },
	scrollDiv:function(t){
		var mai=document.getElementById(t);
		mai.scrollTop = mai.scrollHeight+100;//通过设置滚动高度
	},
	remind : function(obj){
		var msg = $("#chattext").val();
		$("#chattext").val(msg + "@" + $(obj).attr('uname') + "　");
	},
	
	// This method displays an error message on the top of the page:
	displayError : function(divID,msg,f){
		var elem = $('<div>',{
			id		: divID,
			html	: msg
		});
		
		elem.click(function(){
			$(this).fadeOut(function(){
				$(this).remove();
			});
		});
		if(f){
			setTimeout(function(){
				elem.click();
			},5000);	
		}
		elem.hide().appendTo('body').slideDown();
	},
	chatAudio : function(){
		if ( $("#chatAudio").length <= 0 ) {
			$('<audio id="chatAudio"><source src="/static/chat/voices/notify.ogg" type="audio/ogg"><source src="/static/chat/voices/notify.mp3" type="audio/mpeg"><source src="/static/chat/voices/notify.wav" type="audio/wav"></audio>').appendTo('body');
		} 
		$('#chatAudio')[0].play();
	},
	shake : function(){
		$("#layout-main").attr("class", "shake_p");
		var shake = setInterval(function(){  
			$("#layout-main").attr("class", "");
			clearInterval(shake);
		},200);
	},
	off : function(){
		document.onkeydown = function (event){
			if ( event.keyCode==116){
				event.keyCode = 0;
				event.cancelBubble = true;
				return false;
			} 
		}
	},
	// copyright:function(){
	// 	console.log("您好！不介意的话可以加QQ讨论学习（1335244575）");
	// },
	print:function(flag,obj){
		console.log('----' + flag + ' start-------');
		console.log(obj);
		console.log('----' + flag + ' end-------');
	}
}



var heartCheck = {
	timeout: 30000,//毫秒
	timeoutObj: null,
	serverTimeoutObj: null,
	reset: function(){
		clearTimeout(this.timeoutObj);
		clearTimeout(this.serverTimeoutObj);
		return this;
	},
	start: function(){
		var self = this;
		this.timeoutObj = setTimeout(function(){
			//这里发送一个心跳，后端收到后，返回一个心跳消息，
			//onmessage拿到返回的心跳就说明连接正常
			chat.wsSend("ping");
			self.serverTimeoutObj = setTimeout(function(){//如果超过一定时间还没重置，说明后端主动断开了
				chat.wsOnclose();//如果onclose
			}, self.timeout)
		}, this.timeout)
	}
}
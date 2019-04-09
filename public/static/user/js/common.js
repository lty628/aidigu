	$(function(){
		$("#login").click(function(event) {
			var username = $("#username").val();
			var password = $("#password").val();
			var remember = $("#remember").prop("checked");
			if (!username) {
				alertInfo($(".alert-danger"), $("#username"), '昵称不能为空！');
				return false;
			}
			if (!password) {
				alertInfo($(".alert-danger"), $("#password"), '昵称不能为空！');
				return false;
			}
			$.ajax({
				url: '/index/user/loginAjax',
				type: 'GET',
				dataType: 'json',
				data: {username: username, password: password, remember: remember},
			})
			.done(function(data) {
				if (data.status) {
					alertInfo($(".alert-success"), '', data.msg, '/'+data.data+'/');
				} else {
					alertInfo($(".alert-danger"), '', data.msg);
				}
			})
		});
		$("#register").click(function(event) {
			var account = $("#account").val();
			var nickname = $("#nickname").val();
			var password = $("#password").val();
			var rePassword = $("#re-password").val();
			var remember = $("#remember").prop("checked");
			if (!checkAccount(account)) {
				return false;
			}
			if (!checkNickname(nickname)) {
				return false;
			}		
			if (!password) {
				alertInfo($(".alert-danger"), $("#password"), '密码不能为空！');
				return false;
			}
			if (!rePassword) {
				alertInfo($(".alert-danger"), $("#re-password"), '确认密码不能为空！');
				return false;
			}
			if (password != rePassword) {
				alertInfo($(".alert-danger"), $("#password"), '两次密码不一致！');
				return false;
			}
			if (password.length > 20 || password.length < 6 ) {
				alertInfo($(".alert-danger"), $("#password"), '密码长度在6-20之间');
				return false;
			}
			$.ajax({
				url: '/index/user/registerAjax',
				type: 'GET',
				dataType: 'json',
				data: {account: account, nickname: nickname, password: password, remember: remember},
			})
			.done(function(data) {
				if (data.status) {
					alertInfo($(".alert-success"), '', data.msg, '/'+account+'/');
				} else {
					alertInfo($(".alert-danger"), '', data.msg);
				}
			})
		});
	})
	function checkNickname(nickname)
	{
		if (!nickname) {
			alertInfo($(".alert-danger"), $("#nickname"), '昵称不能为空！');
			return false;
		}
		if (nickname.length > 10) {
			alertInfo($(".alert-danger"), $("#nickname"), '昵称长度不能大于10位');
			return false;
		}
		return true;
	}
	function checkAccount(account)
	{
		if (!account) {
			alertInfo($(".alert-danger"), $("#account"), '账号不能为空！');
			return false;
		}
		if (account.length > 10) {
			alertInfo($(".alert-danger"), $("#nickname"), '账号长度不能大于10位');
			return false;
		}
		var account_re = /^[a-z0-9]*$/;
		if (!account_re.test(account)) {
			alertInfo($(".alert-danger"), $("#account"), '账号必须为英文字母、数字！');
			return false;
		}

		return true;
	}
	function alertInfo(obj1, obj2, msg, url='')
	{
		obj1.text(msg).slideDown('fast', function() {
			// $(this);
			if (obj2) obj2.focus();
		}).delay('1200').slideUp('slow', function() {
			if (url) window.location.href = url;
		});
	}
	function checkInfo(obj, type)
	{
		var text = $(obj).val();
		if (!text) {
			return false;
		}
		var action = $(obj).attr('id');
		$.ajax({
			url: '/index/user/checkInfo',
			type: 'GET',
			dataType: 'json',
			data: {text: text, type: type},
		})
		.done(function(data) {
			if (!data.status) {
				alertInfo($(".alert-danger"), $('#'+action), data.msg);
			}
		})
	}
	function setCookie(cname,cvalue,exdays)
	{
		var d = new Date();
		d.setTime(d.getTime()+(exdays*24*60*60*1000));
		var expires = "expires="+d.toGMTString();
		document.cookie = cname + "=" + cvalue + "; " + expires;
	}
	function getCookie(cname)
	{
		var name = cname + "=";
		var ca = document.cookie.split(';');
		for(var i=0; i<ca.length; i++) 
		{
		var c = ca[i].trim();
		if (c.indexOf(name)==0) return c.substring(name.length,c.length);
		}
		return "";
	}
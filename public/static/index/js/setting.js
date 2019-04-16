$(function (){
			$("#passwd").click(function(event) {
				var oldpw = $("#oldpw").val();
				var newpw = $("#newpw").val();
				var compw = $("#compw").val();
				if (!oldpw) {
					alertMsg('请输入旧密码');
					$("#oldpw").focus();
					return false;
				}
				if (!newpw) {
					alertMsg('请输入新密码');
					$("#newpw").focus();
					return false;
				}
				if (!compw) {
					alertMsg('请输入新密码');
					$("#compw").focus();
					return false;
				}
				if (newpw.length > 20 || newpw.length < 6 ) {
					alertMsg('密码长度在6-20之间');
					$("#newpw").focus();
					return false;
				}
				if (newpw!=compw) {
					alertMsg('新密码与确认密码不一致');
					return false;
				}
				var jsonData = {};
				jsonData.oldpw = oldpw;
				jsonData.newpw = newpw;
				jsonData.compw = compw;
				var config = {};
				config.action = 'passwd';
				changeUserInfo(jsonData, config);
			});
			
			layui.use('upload', function(){
			  var upload = layui.upload;
			   
			  //执行实例
			  var uploadInst = upload.render({
			    elem: '#uploadAvatar' //绑定元素
			    ,url: '/index/setting/uploadAvatar' //上传接口
			    ,method: 'GET'
			    ,done: function(res){
			      	if (res.status) {
			      		var data = res.data;
			      		$(".avatarhtml img").attr('src', data.big);
			      		$("#avatarVal").val(JSON.stringify(data));
			      	}
			    }
			    ,error: function(){
			      //请求异常回调
			    }
			  });
			});
			$("#saveUpload").click(function(event) {
				var avatarVal = $("#avatarVal").val();
				if (!avatarVal) return false;
				$.ajax({
					url: '/index/setting/saveUpload',
					type: 'GET',
					dataType: 'json',
					data: {avatarVal: avatarVal},
				})
				.done(function(data) {
					if (data.status) {
						alertMsg('上传成功',0,1);
					} else {
						alertMsg(data.msg);
					}
				})
				.fail(function() {
					console.log("error");
				})
				.always(function() {
					console.log("complete");
				});
				
			});
			$("#changeInfo").click(function(event) {
				var jsonData = {};
				jsonData.username = $("input[name='username']").val();
				jsonData.blog = $("input[name='blog']").val();
				jsonData.nickname = $("input[name='nickname']").val();
				jsonData.phone = $("input[name='phone']").val();
				jsonData.email = $("input[name='email']").val();
				jsonData.sex = $("input[name='sex']:checked").val();
				jsonData.province = $("select[name='province'] option:selected").val();
				jsonData.city = $("select[name='city'] option:selected").val();
				jsonData.intro = $("textarea[name='intro']").val();
				var config = {};
				config.action = 'changeInfo';
				changeUserInfo(jsonData, config);
			});
		})
		function changeUserInfo(info, config)
		{
			$.ajax({
				url: '/index/setting/'+config.action,
				type: 'GET',
				dataType: 'json',
				data: {info: info},
			})
			.done(function(data) {
				alertMsg(data.msg);
			})
			.always(function() {
				// console.log("complete");
			});
			
		}
		function checkInfo2(obj, type)
		{
			var text = $(obj).val();
			if (!text) {
				return false;
			}
			var action = $(obj).attr('id');
			$.ajax({
				url: '/index/setting/checkInfo2',
				type: 'GET',
				dataType: 'json',
				data: {text: text, type: type},
			})
			.done(function(data) {
				if (!data.status) {
					if (!data.status) {
						alertMsg(data.msg);
						$(obj).focus();
					}
				}
			})
		}	
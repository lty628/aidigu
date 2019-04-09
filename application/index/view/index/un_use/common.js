<script>
	$(function (){
		// $(".sendMsg").slideDown().delay(800).slideUp();
		$("#button").click(function(event) {
			var jsonData = {};
			jsonData.contents = $("#msgInput").val()
			sendMsg(jsonData, '', 'index', '')
		});
	})
	function sendMsg(jsonData, action='index', index = '', additional = 1, flush = 0)
	{
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
						
					}
				}
			})
			.fail(function() {
				console.log("error");
			})
			.always(function() {
				console.log("complete");
			});
	}
	function alertMsg(msg, type) 
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
	function repost(obj) 
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
		  	jsonData.repost = repost
		    sendMsg(jsonData, 'repost', index);
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
		    sendMsg(jsonData, 'comment', index, 0);
		  }
		});
	}
	function fansFollow(userid, action)
	{
		var jsonData = {};
		jsonData.userid = userid;
		sendMsg(jsonData, action, index = '', additional = 0, flush = 1);
	}
	function blurToUnfollow(obj, userid)
	{
		var text = $(obj).text();
		$(obj).text('取消关注');
		$(obj).mouseout(function(event) {
			$(obj).text(text);
		});
	}
</script>
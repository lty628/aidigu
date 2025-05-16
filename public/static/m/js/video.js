
var playIngMusicId = '';
var playIngVideoId = -1;
var DP = [];
function stopOther(obj) {
	var id = $(obj).attr("id")
	if (playIngMusicId != '' && id != playIngMusicId) {
		// 如果playIngMusicId 不正确导致的错误，就不执行暂停操作
		if (document.getElementById(playIngMusicId)) {
			document.getElementById(playIngMusicId).pause()
		}
	}
	if (playIngVideoId != -1) {
		DP[playIngVideoId].pause()
	}
	playIngMusicId = id;
	return
}

function iniVideo(i)
{
	$(".massageImg").each(function (index) {
		var url = $(this).attr('vid')
		if (!$(this).hasClass("showVideo"+i)) return
		if (url) {
			DP[index] = new DPlayer({
				container: document.getElementsByClassName('massageImg')[index],
				autoplay: false,
				loop: true,
				// screenshot: true,
				preload: 'metadata',
				// preload: 'auto',
				mutex: true,
				chromecast: true,
				theme: '#FADFA3',
				volume: 0.7,
				// theme: '#d5f3f4',
				lang: 'zh-cn',
				video: {
					url: url,
					type: 'auto',
				},
			});
			DP[index].on('play', function () {
				playIngVideoId = index
				if (playIngMusicId) {
					if (document.getElementById(playIngMusicId)) {
						document.getElementById(playIngMusicId).pause()
					}
				}
			});
			DP[index].on('loadedmetadata', function() {
				DP[index].seek(0); // 将视频跳转到第一秒
			});


			// 监听滚动条，当DP[index]在可视区域内时，播放视频，不在可视区域内时，暂停视频
			// var flag = true;
			// $(window).scroll(function () {
			// 	var top = $(window).scrollTop();
			// 	var bottom = top + $(window).height();
			// 	var videoTop = $(DP[index].container).offset().top;
			// 	var videoBottom = videoTop + $(DP[index].container).height();
			// 	if (videoBottom > top && videoTop < bottom) {
			// 		if (flag) {	
			// 			DP[index].play();
			// 			flag = false;
			// 		}
			// 	} else {
			// 		if (!flag) {
			// 			DP[index].pause();
			// 			flag = true;
			// 		}
			// 	}
			// });

		}
	})
}
// $(function () {
//     $(".massageImg").each(function (index) {
// 		var url = $(this).attr('vid')
// 		console.log(url)
// 		if (url) {
			
// 			DP[index] = new DPlayer({
// 				container: document.getElementsByClassName('massageImg')[index],
// 				autoplay: false,
//                 theme: '#d5f3f4',
// 				lang: 'zh-cn',
// 				video: {
// 					url: url,
// 					type: 'auto',
// 				},
// 			});
// 			console.log(DP)
// 			DP[index].on('play', function () {
// 				console.log(index)
// 				alert(111);
// 				if (playIngMusicId) {
// 					document.getElementById(playIngMusicId).pause()
// 				}
// 			});
// 		}
// 	})
// })
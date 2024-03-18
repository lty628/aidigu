
var playIngMusicId = '';
var playIngVideoId = -1;
var DP = [];
function stopOther(obj) {
	var id = $(obj).attr("id")
	if (playIngMusicId != '' && id != playIngMusicId) {
		document.getElementById(playIngMusicId).pause()
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
				chromecast: true,
				theme: '#d5f3f4',
				lang: 'zh-cn',
				video: {
					url: url,
					type: 'auto',
				},
			});
			DP[index].on('play', function () {
				playIngVideoId = index
				if (playIngMusicId) {
					document.getElementById(playIngMusicId).pause()
				}
			});
			// DP[index]视频在屏幕中间时自动播放
			var top = $(this).offset().top;
			var scrollTop = $(window).scrollTop();
			var windowHeight = $(window).height();
			if (top < scrollTop + windowHeight && top > scrollTop) {
				DP[index].play()
			}

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
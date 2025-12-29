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
	
	// // 为音频元素添加滚动监听，当音频不在视野中时暂停
	// var audioElements = document.querySelectorAll('.music');
	// audioElements.forEach(function(audioElement) {
	// 	if (!audioElement.id.includes("music_"+i)) return;
		
	// 	var audioId = audioElement.id;
	// 	var audioFlag = true;
		
	// 	// 检查元素是否在视窗内
	// 	function isElementInViewport(el) {
	// 		var rect = el.getBoundingClientRect();
	// 		var windowHeight = (window.innerHeight || document.documentElement.clientHeight);
	// 		var windowWidth = (window.innerWidth || document.documentElement.clientWidth);
			
	// 		return (
	// 			rect.top >= 0 &&
	// 			rect.left >= 0 &&
	// 			rect.bottom <= windowHeight &&
	// 			rect.right <= windowWidth
	// 		);
	// 	}
		
	// 	// 滚动处理函数
	// 	function handleScroll() {
	// 		var audioEl = document.getElementById(audioId);
	// 		if (!audioEl) return;
			
	// 		var inViewport = isElementInViewport(audioEl);
			
	// 		if (inViewport) {
	// 			// 音频在视野内且之前是暂停状态，可以继续播放
	// 			audioFlag = false;
	// 		} else {
	// 			// 音频不在视野内，暂停音频
	// 			if (!audioFlag && !audioEl.paused) {
	// 				audioEl.pause();
	// 				audioFlag = true;
	// 			}
	// 		}
	// 	}
		
	// 	// 添加滚动事件监听器
	// 	$(window).on('scroll', handleScroll);
		
	// 	// 初始检查
	// 	handleScroll();
	// });
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
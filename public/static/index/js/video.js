$(function () {
    $(".massageImg").each(function (index) {
		var url = $(this).attr('vid')
		if (url) {
			const dp = new DPlayer({
				container: document.getElementsByClassName('massageImg')[index],
				autoplay: true,
                theme: '#d5f3f4',
				lang: 'zh-cn',
				video: {
					url: url,
					type: 'auto',
				},
			});
		}
	})
})
$(function(){

if (typeof(WebSocket) == "function") {
	$.getScript("//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.12.0/highlight.min.js",function(){
		$('pre code').each(function(i, block) {hljs.highlightBlock(block)});
	});
}

$(".top-bar-body li").each(function(){
	$(".top-bar-body li a").click(function(){
		$(".top-bar-body li").css("border-bottom","0px");
		$(this).parent().css("border-bottom","3px solid #fa7d3c");
	});
});

var tmp_rmt_h = $(window).height()-76-$(".foot").height()-34;

try{comment_form_top = $("#comment_form").offset().top}
catch(err){comment_form_top = "ud"}

$(window).scroll(function(){
	var document_height = $(document).height();
	var TscrollTop = document.documentElement.scrollTop || window.pageYOffset || document.body.scrollTop;
	if (TscrollTop>666) {
		$("#gotop").css("display","block");
	}else{
		$("#gotop").css("display","none");
	}
	//gototop
});

//-------------表情
facenamereplace = ["：滑稽：","：喷：","：彩虹：","：阴险：","：怒：","：笑眼：",
"：黑线：","：玫瑰：","：乖：","：汗：","：惊哭：","：酷：","：狂汗：","：冷：",
"：真棒：","：不高兴：","：大哭：","：咖啡：","：yeah：","：ojbk："];
/*
facereplace = [];
for (var i = 0; i < facenamereplace.length; i++) {
	facereplace[i] = theme_path+"/s/img/"+facenamereplace[i].replace(/：/g,"")+".png";
}
*/
facereplace = ["https://wx1.sinaimg.cn/mw690/005ShQFIgy1fu6s184o24j300u00uq2s.jpg",
"https://wx2.sinaimg.cn/mw690/005ShQFIgy1fu6s1ablbbj300u00uwec.jpg",
"https://wx3.sinaimg.cn/mw690/005ShQFIgy1fu6s16m089j300u00u744.jpg",
"https://wx1.sinaimg.cn/mw690/005ShQFIgy1fu6s1ax3a1j300u00uq2s.jpg",
"https://wx2.sinaimg.cn/mw690/005ShQFIgy1fu6s1a045uj300u00ut8k.jpg",
"https://wx2.sinaimg.cn/mw690/005ShQFIgy1fu6s1amokjj300u00uwec.jpg",
"https://wx4.sinaimg.cn/mw690/005ShQFIgy1fu6s17syp4j300u00uq2s.jpg",
"https://wx1.sinaimg.cn/mw690/005ShQFIgy1fu6s19lh8sj300u00uglg.jpg",
"https://wx2.sinaimg.cn/mw690/005ShQFIgy1fu6s172vs1j300u00ut8k.jpg",
"https://wx1.sinaimg.cn/mw690/005ShQFIgy1fu6s17jbtaj300u00uq2s.jpg",
"https://wx1.sinaimg.cn/mw690/005ShQFIgy1fu6s18dxs3j300u00ut8k.jpg",
"https://wx4.sinaimg.cn/mw690/005ShQFIgy1fu6s18o1ehj300u00u0sl.jpg",
"https://wx2.sinaimg.cn/mw690/005ShQFIgy1fu6s18z1tcj300u00uwec.jpg",
"https://wx4.sinaimg.cn/mw690/005ShQFIgy1fu6s199skzj300u00uq2s.jpg",
"https://wx2.sinaimg.cn/mw690/005ShQFIgy1fu6s1b931zj300u00uwec.jpg",
"https://wx3.sinaimg.cn/large/005ShQFIgy1fuqisjlduhj300u00uq2s.jpg",
"https://wx2.sinaimg.cn/large/005ShQFIgy1fuqisjy9vmj300u00ut8k.jpg",
"https://wx2.sinaimg.cn/large/005ShQFIgy1fuqisk65elj300u00udfo.jpg",
"https://wx2.sinaimg.cn/large/005ShQFIgy1fuqiskmyuqj300u00u3yc.jpg",
"https://wx1.sinaimg.cn/large/005ShQFIgy1fuqis19uo9j300u00u744.jpg"];
try{
	if (showfacenamereplace) {
		for (var i = 0; i < facenamereplace.length; i++) {
			showfacenamereplace.innerHTML += facenamereplace[i];
		}
	}
}catch(e){}
var bodyhtml = $(".article-list").html();
for (var i = 0; i < facereplace.length; i++) {
	bodyhtml = bodyhtml.replace(new RegExp(facenamereplace[i],"ig"),"<img class='tbbq' alt='"+i+"' src='"+facereplace[i]+"' />");
}
bodyhtml = bodyhtml.replace(/【script(.*?)script】/ig,"");
bodyhtml = bodyhtml.replace(/【style(.*?)style】/ig,"");
var tmp_html_reg = /【([\w]{1,10})(.*?)\1】/ig;
while(tmp_html_reg.test(bodyhtml)){
	bodyhtml = bodyhtml.replace(tmp_html_reg,"<$1>$2</$1>");
}
$(".article-list").html(bodyhtml);
$("form .tbbq").click(function(){
	insertText($("textarea[name=text]")[0], facenamereplace[$(this).attr("alt")]);
});
//--------------简化标签
//--------------打赏233
$(".pay span").click(function(){
	$(".pay img").click();
});
//--------------图片放大
$("#zooming").click(function(){
	$(this).fadeOut(200);
	setTimeout(function(){
		$("body").css("overflow-y","scroll");
	},200);
});
$(".post-content img").click(function(){
	zooming($(this).attr("src"));
});
$(".post-content .tbbq").unbind("click");
//--------------
$(".click-show .show").click(function(){
	$(this).parent().children(".slideB").css("bottom",$(this).height()+5);
	$(this).parent().children(".slideT").css("top",$(this).height()+5);
	$(this).parent().children(".show-this").slideToggle(150);
});
$(document).bind("click", function (e) {
    if($(e.target).closest(".click-show").length <= 0){
        $(".click-show .show-this").parent().children(".show-this").slideUp(150);
    }
});
//--------------
$(".box-body img").lazyload({effect:"fadeIn",threshold:$(window).height()*1.5});
$("img").each(function(){
	$(this).lazyload({effect: "fadeIn"});
});
//--------------
$('pre code').click(function(){
	$(this).attr("contenteditable","true");
});
});

function zooming(url){
	$("#zooming img").attr("src",url);
	$("#zooming img").on("load",function(){
		$("#zooming").fadeIn(200);
		$("body").css("overflow-y","hidden");
		if ($("#zooming img").height()<=$(window).height()) {
			$("#zooming").css("overflow-y","hidden");
			$("#zooming img").css("margin-top",($(window).height()-$("#zooming img").height())/2.3);
		}else{
			$("#zooming").css("overflow-y","scroll");
			$("#zooming img").css("margin-top","0px");
		}
		if ($("#zooming img").width()<=$(window).width()) {
			$("#zooming").css("overflow-x","hidden");
		}else{
			$("#zooming").css("overflow-x","scroll");
		}
	});
}

function insertText(obj,str){
	var m = obj.value.match(/：.*?：/g);
	if (m) {
		if (m.length > 9) {
			alert("最多添加10个表情！");
			return false;
		}
	}
	obj.value += str;
}
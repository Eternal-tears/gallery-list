jQuery(function ($) {
	//ナビゲーションのmouseover。画像切り替えとは関係ない。
	$("ul#stylelist > li").hover(function(){ 
		$(this).fadeTo(200,0.5); 
	},function() {
		$(this).fadeTo(200,1); 
	});
	
	//画像切り替え、#targetの内容を変更する。
	$("ul#stylelist > li > a").click(function(){
		var src = $(this).attr("href");
		$("#target").fadeOut("slow",function() {
			$(this).attr("src",src);
			$(this).fadeIn();
		});
		return false;
	});
});

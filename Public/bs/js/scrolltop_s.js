
$(window).scroll(function() {
	if ($(this).scrollTop() > 100) {
		$("#scrollGoTop").show();
	}else{
		$("#scrollGoTop").hide();
	};
});


$(function(){
	$('body').append('<a id="scrollGoTop" target="_self" href="###" style="display:none;">回顶部</a>');
	$('body').append('<style>#scrollGoTop{position:fixed;_position:absolute;left:50%;margin:0 0 0 609px;width:25px;height:90px;line-height:400px;overflow:hidden;bottom:20px;background:url(/Public/img/scrollGoTop.png) no-repeat;_top:expression(eval(documentElement.scrollTop+documentElement.clientHeight-this.clientHeight-20));}#scrollGoTop:hover{background-position:-25px 0;}</style>')


	$("#scrollGoTop").click(function(event) { 
       $("html,body").animate({scrollTop: 0}, 1000);
   });
	
});

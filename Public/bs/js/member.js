$(function(){

	//区域省--市--区 选择省
	$("#province").click(function(){
		var provinceid = $("#province").val();

		$("#province option").each(function(i){
			if(provinceid==i){
				$("#city_"+provinceid).css("display", "block");
				// $("#area_"+provinceid).css("display", "block");
			}else{
				$("#city_"+i).css("display", "none");
			}
		});
	})

})
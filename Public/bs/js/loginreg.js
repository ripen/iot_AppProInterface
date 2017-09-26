$(function(){
	/*登陆*/
	$(".login-loginbtn").click(function(){
		if(Trim($("#mobile").val())==""|| $("#mobile").val()==null){
			$('.dla').eq(0).addClass("error");
			$('.dla').eq(0).find('.info').text("请输入手机号");
			return false;
			
		}
		if(!isMobile($("#mobile").val())){
			$('.dla').eq(0).addClass("error");
			$('.dla').eq(0).find('.info').text("请输入正确手机号");
			return false;
			
		}else{
			$('.dla').eq(0).removeClass("error");
		}
		if(Trim($("#password").val())==""|| $("#password").val()==null){
			$('.dla').eq(1).addClass("error");
			$('.dla').eq(1).find('.info').text("请输入密码");
			return false;			
		}
		var mobile = $("#mobile").val();
		var password = $("#password").val();
		$.ajax({ 
	       type: "post", 
	       url: "/member/index/checkmobile", 
	       data:{mobile:mobile,password:password},
	       cache:false, 
	       async:true, 
	       dataType:'json', 
	       success: function(data){ 
	        	if(data['status']!=2){
	        		 if(data['status']==3){
	        			 $('.dla').eq(1).addClass("error");
						 $('.dla').eq(1).find('.info').text("密码错误"); 
	        		 }else{
					   $('.dla').eq(0).addClass("error");
					   $('.dla').eq(0).find('.info').text("手机号不存在");
	        		 }
					   return false;
				   }else{
					   //return true;
					   form.submit();	
				   }		
	        } 
	});
		/*$.post('/member/index/checkmobile',{mobile:mobile},function(data){				 
				   if(data['status']!=2){
					   $('.dla').eq(1).addClass("error");
					   $('.dla').eq(1).find('.info').text("手机号不存在");
					   return false;
				   }else{
					   return true;
				   }
				  },'json')*/
				  
	
	})
	//记录10天
	$("#remember").click(function(){
		//选中
		if($(this).prop("checked")){
			$("#remember").val(1);
		}else{
			$("#remember").val(0);
		}
	})
	
	/*注册*/
	$(".login-registerbtn").click(function(){
		 if(!mobile()){
			return false; 
		 }
		
		if(Trim($("#password").val())==""|| $("#password").val()==null){
			$('.dla').eq(1).addClass("error");
			$('.dla').eq(1).find('.info').text("请输入密码");
			return false;			
		}else if(Trim($("#password").val()).length < 6 || Trim($("#password").val()).length>12){
			$('.dla').eq(1).addClass("error");
			$('.dla').eq(1).find('.info').text("密码在6到12位之间");
			return false;
		}else{
			$('.dla').eq(1).removeClass("error");
		}
		
		if(Trim($("#password1").val())==""|| $("#password1").val()==null){
			$('.dla').eq(2).addClass("error");
			$('.dla').eq(2).find('.info').text("请输入确认密码");
			return false;			
		}else if(Trim($("#password1").val()).length<6 || Trim($("#password1").val()).length>12){
			$('.dla').eq(2).addClass("error");
			$('.dla').eq(2).find('.info').text("密码在6到12位之间");
			return false;
		}else{
			$('.dla').eq(2).removeClass("error");
		}
		if(Trim($("#password").val())!=Trim($("#password1").val())){
			$('.dla').eq(2).addClass("error");
			$('.dla').eq(2).find('.info').text("两次输入的密码不一致");
			return false;	
		}
		//网站验证码
		if(!regcode()){
			return false;
		}
		//手机验证码
		if(Trim($("#code").val())==''){
			$('.jhm ').addClass("error");
			$('.jhm ').find('.info').text("请输入验证码");
			return false;	
		}
		//协议
		if($("#remember").val()!=1){
			$('.xieyi').addClass("error");
			$('.xieyi').find('.info').text("协议没同意");
			return false;
		}
	 form.submit();
	})

//查看协议
	$(".xieyi a").click(function(){
		$(".graybg").show();
		$(".infobox").find(".edit-box").show();
	})
	
//关闭协议
$(".infobox a").click(function(){
	$(".graybg").hide();
	$(".infobox").find(".edit-box").hide();
})	
	
//验证码刷新
var captcha_img = $('.yzm').find('img')  
var verifyimg = captcha_img.attr("src");  
captcha_img.attr('title', '点击刷新');  
captcha_img.click(function(){  
    if( verifyimg.indexOf('?')>0){  
        $(this).attr("src", verifyimg+'&random='+Math.random());  
    }else{  
        $(this).attr("src", verifyimg.replace(/\?.*$/,'')+'?'+Math.random());  
    }  
});


//找回密码2

$("#findpwd").click(function(){
	if(Trim($("#password").val())==""|| $("#password").val()==null){
		$('.dla').eq(1).addClass("error");
		$('.dla').eq(1).find('.info').text("请输入密码");
		return false;			
	}else if(Trim($("#password").val()).length<6 || Trim($("#password").val()).length>12){
		$('.dla').eq(1).addClass("error");
		$('.dla').eq(1).find('.info').text("密码在6到12位之间");
		return false;
	}else{
		$('.dla').eq(1).removeClass("error");
	}
	
	if(Trim($("#password1").val())==""|| $("#password1").val()==null){
		$('.dla').eq(2).addClass("error");
		$('.dla').eq(2).find('.info').text("请输入确认密码");
		return false;			
	}else if(Trim($("#password1").val()).length<6 || Trim($("#password1").val()).length>12){
		$('.dla').eq(2).addClass("error");
		$('.dla').eq(2).find('.info').text("密码在6到12位之间");
		return false;
	}else{
		$('.dla').eq(2).removeClass("error");
	}
	if(Trim($("#password").val())!=Trim($("#password1").val())){
		$('.dla').eq(2).addClass("error");
		$('.dla').eq(2).find('.info').text("两次输入的密码不一致");
		return false;	
	}
	form.submit();
		
})



	
})



//手机验证码倒计时
var util = {
		                wait: 60,
		                hsTime: function (that) {
		                	   if (this.wait == 0) { 
		                	    $('.bluebtn').removeAttr("disabled").val('重发短信验证码');
		                	    $(that).removeClass('graybtn');
		                	    this.wait = 60; 
		                	   } else { 
		                	    var _this = this;
		                	    $(that).attr("disabled", true).val('在'+_this.wait+'秒后点此重发');
		                	    $(that).addClass('graybtn');
		                	    _this.wait--; 
		                	    setTimeout(function() { 
		                	     _this.hsTime(that); 
		                	    }, 1000) 
		                    }
		                }
		            }
function gettime(){
	var mobile = Trim($("#mobile").val());
	if(isMobile(mobile)){
		
		//var code = Trim($("#code").val());
		$.post('/member/index/code',{mobile:mobile}, function(rs){
			if(rs['status']==1){
	     		util.hsTime('.bluebtn');
	     	}else if(rs['status']!=1){
	     		if(rs['status']==2){
	     			$('.dla').eq(0).addClass("error");
	     			$('.dla').eq(0).find('.info').text("手机号已存在");
	     			return false;
	     		}else{
	     			$('.jhm').addClass("error");
	     			$('.jhm').find('.info').text(rs['msg']);
	     			return false;
	     		}
	     	}
	     	
	  }, 'json')
  
	}else{
		$('.dla').eq(0).addClass("error");
		$('.dla').eq(0).find('.info').text("请输入正确手机号");
		return false;
	}
}

//找回密码手机验证码获取
function gettime1(){
	var mobile = Trim($("#mobile").val());
	if(isMobile(mobile)){
		
		//var code = Trim($("#code").val());
		$.post('/member/index/findcode',{mobile:mobile}, function(rs){
			if(rs['status']==1){
	     		util.hsTime('.bluebtn');
	     	}else if(rs['status']!=1){
	     		if(rs['status']==2){
	     			$('.dla').eq(0).addClass("error");
	     			$('.dla').eq(0).find('.info').text("手机号已存在");
	     			return false;
	     		}else{
	     			$('.jhm').addClass("error");
				    $('.jhm').find('.info').text(rs['msg']);
				    return false;
	     		}
	     	}
	     	
	  }, 'json')
	}else{
		$('.dla').eq(0).addClass("error");
		$('.dla').eq(0).find('.info').text("请输入正确手机号");
		return false;
	}
}


//找回密码验证1
function findpwd(){
	if(!pwdmobile()){
		return false;
	}
	if(!regcode()){
		return false;
	}
	//手机验证码
	if(Trim($("#code").val())==''){
		$('.jhm ').addClass("error");
		$('.jhm ').find('.info').text("请输入验证码");
		return false;	
	}
	$("#findpwd1").css('display','none');
	$("#findpwd2").css('display','block');
	$("#mobile1").val($("#mobile").val());
	$("#code1").val($("#code").val());
}

function checkmobile(){
	if(Trim($("#mobile").val())==""|| $("#mobile").val()==null){
		$('.dla').eq(0).addClass("error");
		$('.dla').eq(0).find('.info').text("请输入手机号");
		return false;
	}
	if(!isMobile($("#mobile").val())){
		$('.dla').eq(0).addClass("error");
		$('.dla').eq(0).find('.info').text("请输入正确手机号");
		return false;
	}else{
		var mobile = Trim($("#mobile").val());
		$.post('/member/index/checkmobile',{mobile:mobile}, function(rs){
			 if(rs['status']==2){
				 $('.dla').eq(0).addClass("error");
				 $('.dla').eq(0).find('.info').text("手机号已存在");
	     	}else if(rs['status']==1){
	     		 $('.dla').eq(0).removeClass("error");
	     	}
	     	
	  }, 'json')
	}
}


//找回密码判定手机号
function pwdmobile(){
	if(Trim($("#mobile").val())==""|| $("#mobile").val()==null){
		$('.dla').eq(0).addClass("error");
		$('.dla').eq(0).find('.info').text("请输入手机号");
		return false;
	}
	if(!isMobile($("#mobile").val())){
		$('.dla').eq(0).addClass("error");
		$('.dla').eq(0).find('.info').text("请输入正确手机号");
		return false;
	}else{
		var mobile = Trim($("#mobile").val());
		var flag =false ; 
		$.ajax({ 
		       type: "post", 
		       url: "/member/index/checkmobile", 
		       data:{mobile:mobile},
		       cache:false, 
		       async:false, 
		       dataType:'json', 
		       success: function(data){ 
		        	if(data['status']==2){
		        		$('.dla').eq(0).removeClass("error");
		    			flag = true;
					   }else{
						 $('.dla').eq(0).addClass("error");
			    		 $('.dla').eq(0).find('.info').text("手机号不存在");
						 flag = false; 
					   }		
		        } 
		});	
		return flag;
	}
}




//判定手机号
function mobile(){
	if(Trim($("#mobile").val())==""|| $("#mobile").val()==null){
		$('.dla').eq(0).addClass("error");
		$('.dla').eq(0).find('.info').text("请输入手机号");
		return false;
	}
	if(!isMobile($("#mobile").val())){
		$('.dla').eq(0).addClass("error");
		$('.dla').eq(0).find('.info').text("请输入正确手机号");
		return false;
	}else{
		var mobile = Trim($("#mobile").val());
		var flag =false ; 
		$.ajax({ 
		       type: "post", 
		       url: "/member/index/checkmobile", 
		       data:{mobile:mobile},
		       cache:false, 
		       async:false, 
		       dataType:'json', 
		       success: function(data){ 
		        	if(data['status']==2){
		        		$('.dla').eq(0).addClass("error");
		    			$('.dla').eq(0).find('.info').text("手机号已存在");
		    			flag = false;
					   }else{
						   $('.dla').eq(0).removeClass("error");
						   flag = true; 
					   }		
		        } 
		});	
		return flag;
	}
}

//网站验证码
function regcode(){
	if(Trim($("#regcode").val())==''){
		$('.yzm').addClass("error");
		$('.yzm').find('.info').text("请输入验证码");
		return false;	
	}else{
		var code = Trim($("#regcode").val());
		var flag = false;
		$.ajax({ 
		       type: "post", 
		       url: "/member/index/checkregcode", 
		       data:{code:code},
		       cache:false, 
		       async:false, 
		       dataType:'json', 
		       success: function(data){ 
		    	   if(data['status']==7){
					   $('.yzm').addClass("error");
					   $('.yzm').find('.info').text("验证码错误");
					   flag = false; 
				   }else{
					   $('.yzm').removeClass("error"); 
					   flag = true;
				   }		
		        } 
		});
		return flag;
	}
}



//去掉空格
function Trim(str)
{ 
  return str.replace(/(^\s*)|(\s*$)/g, ""); 
}

function isMobile(s)
{
	var myreg = /^(13[\d]{9}$|14^[0-9]\d{8}|^15[0-9]\d{8}$|^18[0-9]\d{8}|^17\d{9})$/;
    if(!myreg.test(s))
    {     
        return false;
    }else{
    	return true;
    }
}

function login(){
	var url =window.location.href;
	if(url){
		window.location.href='http://'+window.location.host+'/member/index/login?url='+encodeURIComponent(url);
	}
}

function reg(){
	var url =window.location.href;
	if(url){
		window.location.href='http://'+window.location.host+'/member/index/register?url='+encodeURIComponent(url);
	}
}


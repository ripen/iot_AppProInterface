$(function(){
	/*登陆*/
	$("#denglu").click(function(){
		if($("#mobile").val()==""|| $("#mobile").val()==null){
			$("#tip").slideDown(300).text("手机号不为空");
			showTip();
			return false;
			
		}
		if(!isMobile($("#mobile").val())){
			$("#tip").slideDown(300).text("请输入正确的手机号");
			showTip();
			return false;
			
		}
		if($("#password").val()==""|| $("#password").val()==null){
			$("#tip").slideDown(300).text("密码不为空");
			showTip();
			return false;
			
		}
		var mobile = $("#mobile").val();
		var password = $("#password").val();
		$.post('/Mobileastronautic/Index/login',{mobile:mobile,password:password},function(data){
				   if(data['status']==1){
					   form.submit();
				   }else{
					   $("#tip").slideDown(300).text("手机号不存在");
					   showTip();
					   return false;  
						
				   }
				  },'json')
	})
	/*注册第一步*/
	$("#reg").click(function(){
		if($("#mobile").val()==""|| $("#mobile").val()==null){
			
			$("#tip").slideDown(300).text("手机号不为空");
			showTip();
			return false;
		}
		if(!isMobile($("#mobile").val())){
			$("#tip").slideDown(300).text("请输入正确的手机号");
			showTip();
			return false;
		}
		var mobile = $("#mobile").val();
		$.post('/Mobileastronautic/Index/checkmobile',{mobile:mobile},function(data){
			   if(data['status']==0){
				   $("#tip").slideDown(300).text("输入正确手机号");
				   showTip();
				   return false; 
			   }else if(data['status']==2){
				   $("#tip").slideDown(300).text("手机号已存在");
				   showTip();
				   return false;  					
			   }
			  },'json')
			  
		if($("#code").val()==''|| $("#code").val()==null){
			$("#tip").slideDown(300).text("请输入手机验证码");
			showTip();
			return false;
		}
		var code = $("#code").val();
		var mobile = $("#mobile").val();
		$.post('/Mobileastronautic/Index/checkcode',{mobile:mobile,code:code}, function(rs){
         	if(rs['status']!=1){
         		$("#tip").slideDown(300).text("手机验证码不正确或已过期");
         		showTip();
    			return false;
         	}else{
         		 form.submit();
         	}
         	
      }, 'json')
	})
	
	/*注册第二步*/
	$("#reg1").click(function(){
		if($("#username").val()==""|| $("#username").val()==null){
			
			$("#tip").slideDown(300).text("请输入用户名");
			showTip();
			return false;
		}
		if($("#password").val()==""|| $("#password").val()==null){
			$("#tip").slideDown(300).text("请输入密码");
			showTip();
			return false;
		}
		
		if($("#password1").val()==''|| $("#password1").val()==null){
			$("#tip").slideDown(300).text("确认密码不为空");
			showTip();
			return false;
		}
		if($("#password").val()!=$("#password1").val()){
			$("#tip").slideDown(300).text("两次输入的密码不一致");
			showTip();
			return false;
		}
		 form.submit();
	})
	
	/*用户信息编辑*/
	$('#editsave').click(function(){
		var username =$("#username").val();
		var sex =$("#sex").val();
		var height =$("#height").val();
		var weight =$("#weight").val();
		var birthday =$("#birthday").val();
		var edu =$("#edu").val();
		var disease =$("#disease").val();
		if(username==""|| username==null){			
			$("#tip").slideDown(300).text("请输入用户名");
			showTip();
			return false;
		}
		if(height==""|| height==null){			
			$("#tip").slideDown(300).text("请输入身高");
			showTip();
			return false;
		}
		if(weight==""|| weight==null){			
			$("#tip").slideDown(300).text("请输入体重");
			showTip();
			return false;
		}
		if(birthday==""|| birthday==null){			
			$("#tip").slideDown(300).text("请输入出生日期");
			showTip();
			return false;
		}
		$.post('/Mobileastronautic/Index/edituserinfo',{username:username,sex:sex,height:height,weight:weight,birthday:birthday,edu:edu,disease:disease}, function(rs){
         	if(rs['status']==1){
         		$("#tip").slideDown(300).text("信息修改成功");
         		showTip();
         	}
         	
      }, 'json')
		
	})
	
	/*发布咨询*/
	$("#sbtn").click(function(){
		var content = $("#content").val();
		if(content==""|| content==null){
			$("#tip").slideDown(300).text("内容不为空");
			showTip();
			return false;
		}
		var bid = $("#bid").val();
		$.post('/Mobileastronautic/Index/publish',{content:content,bid:bid}, function(rs){
         	if(rs['status']==1){
         		$("#tip").slideDown(300).text("咨询发布成功");
         		showTip();
         		$("#content").val("");
         	}else if(rs['status']==0){
         		$("#tip").slideDown(300).text("请输入内容");
         		showTip();
         		return false;
         	}else if(rs['status']==2){
         		$("#tip").slideDown(300).text("发布错误");
         		showTip();
         		return false;
         	}
         	
      }, 'json')
	})
	
	
	/*咨询回复*/
	
	$("#replay").click(function(){
		var content = $("#content").val();
		if(content==""|| content==null){
			$("#tip").slideDown(300).text("内容不为空");
			showTip();
			return false;
		}
		var bid = $("#bid").val();
		var pid = $("#pid").val();
		$.post('/Mobileastronautic/Index/publish',{content:content,bid:bid,pid:pid}, function(rs){
         	if(rs['status']==1){
         		$("#tip").slideDown(300).text("咨询发布成功");
         		showTip();
         		$("#content").val("");
         	}else if(rs['status']==0){
         		$("#tip").slideDown(300).text("请输入内容");
         		showTip();
         		return false;
         	}else if(rs['status']==2){
         		$("#tip").slideDown(300).text("发布错误");
         		showTip();
         		return false;
         	}
         	
      }, 'json')
	})
	
	
	
	/*获取手机验证码*/
	$('.gain').click(function(){
		var mobile = $("#mobile").val();
		if($("#mobile").val()==""|| $("#mobile").val()==null){
			$("#tip").slideDown(300).text("请输入手机号");
			showTip();
			return false;
		}
		if(!isMobile($("#mobile").val())){
			$("#tip").slideDown(300).text("请输入正确的手机号");
			showTip();
			return false;
		}
		$.post('/Mobileastronautic/Index/code',{mobile:mobile}, function(rs){
         	if(rs['status']==1){
         		$("#tip").slideDown(300).text("请注意查收验证码");
         		showTip();
    			return false;
         	}else if(rs['status']==0){
         		$("#tip").slideDown(300).text("请输入正确手机号");
         		showTip();
    			return false;
         	}else if(rs['status']==2){
         		$("#tip").slideDown(300).text("手机已注册");
         		showTip();
    			return false;
         	}else{
         		$("#tip").slideDown(300).text("重新发送手机验证码");
         		showTip();
    			return false;
         	}
      }, 'json')
	})
	
	function showTip() {
    	setTimeout(function () { $("#tip").slideUp(300) }, 3000);
    	}
		
	
})



function isMobile(s)
{
	var myreg = /^(((13[0-9]{1})|159|153)+\d{8})$/;
    if(!myreg.test(s))
    {     
        return false;
    }else{
    	return true;
    }
}
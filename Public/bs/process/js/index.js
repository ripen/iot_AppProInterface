$(document).ready(function(){
	$(".graybg").click(function(){
		if (screen.width >= 1400) {
              		bigImgHide();
					} 
				else if (screen.width >= 1024) {
					bigImgHide1360();//这个分辨率下你的操作
					} 
		$(".items li").children("img").animate({width:"0"},10);
		$(".itemsbox").hide();
		$(".check-list li,.items li,.tab-title li a").removeClass("cur");
		$(".items li").find(".dot").css({'z-index':0});
		$(".tab-title").css({'z-index':0}).fadeOut(10);
		$(this).hide();

		// 隐藏用户ID
		$('#hiddeninput').val('');
		$('#checksign').val('');
	})//graybg end	

		
	$("a.close").click(function(){
		if(!$(".bigdatabox .itemsbox,.big-img li").is(":animated")){	
			if (screen.width >= 1400) {
              		bigImgHide();
					} 
				else if (screen.width >= 1024) {
					bigImgHide1360();//这个分辨率下你的操作
					} 
			}
		return false;
	})//a.close end
	

//big-img分辨率下初始化
	if (screen.width >= 1400) {
		} 
	else if (screen.width >= 1024) {
		bigImg_default1360();//这个分辨率下你的操作
		} 
	
	function bigImg_default1360(){//bigImg 分辨率下初始化位置
		$(".big-img li").eq(0).css({'left':'612px','top':'152px'});
		$(".big-img li").eq(1).css({'left':'552px','top':'77px'});
		$(".big-img li").eq(2).css({'left':'452px','top':'162px'});
		$(".big-img li").eq(3).css({'left':'572px','top':'177px'});
		$(".big-img li").eq(4).css({'left':'545px','top':'227px'});
		$(".big-img li").eq(5).css({'left':'552px','top':'192px'});
		$(".big-img li").eq(6).css({'left':'582px','top':'372px'});
		$(".big-img li").eq(7).css({'left':'568px','top':'252px'});
		};	

		$(".check-list li").click(function(){
			if (screen.width >= 1400) {
              		bigImgHide();
					} 
				else if (screen.width >= 1024) {
					bigImgHide1360();//这个分辨率下你的操作
					}
			$(".graybg").show();
			$(this).parent().find("li").removeClass("cur").css({'z-index':'0'});
			$(this).addClass("cur").css({'z-index':'999'});
			$(".tab-title").show().css({'z-index':'999'});

			// 获取用户信息
			var userid 	=	$(this).attr('data');
			userid 		=	userid ? userid : 0;
			$('#hiddeninput').val(userid);
			var datacheck	=	$(this).attr('datacheck');
			$('#checksign').val(datacheck);


			return false;		
		})
		
		
		$(".tab-title li a").click(function(){	
			var index=$(this).parent("li").index();
			if (screen.width >= 1400) {
              		bigImgHide();
					} 
				else if (screen.width >= 1024) {
					bigImgHide1360();//这个分辨率下你的操作
					} 		
			if(!$(".bigdatabox .itemsbox").is(":animated")){
				$(".tab-title li a").removeClass("cur");
				$(this).addClass("cur");
				var index=$(this).parent().index();
				if (screen.width >= 1400) {
              		bigImgHide();
					} 
				else if (screen.width >= 1024) {
					bigImgHide1360();//这个分辨率下你的操作
					} 
				$(".items li").removeClass("cur").find(".dot").css({'z-index':'0'});	
				$(".items li").eq(index).addClass("cur").find(".dot").css({'z-index':'98'});
				if (screen.width >= 1400) {
              		bigImg(index);//此分辨率下你需要的操作
					} 
				else if (screen.width >= 1024) {
					bigImg_1360(index);//这个分辨率下你的操作
					} 

				// 获取分类数据
				var types	=	$(this).attr('data');
				showdatas(types);

				handle(index);
			}
		})//tab-title li a end
		
		
		$(".items li span").click(function(){
			bigImgHide();
			var index=$(this).parent("li").index();
			$(".big-img li").eq(index).addClass("cur").siblings(".big-img li").removeClass("cur");			
			if (screen.width >= 1400) {
						bigImg2(index);//此分辨率下你需要的操作
						} 
					else if (screen.width >= 1024) {
						bigImg2_1360(index);//这个分辨率下你的操作
						} 
		});//items li span end
})//document ready end

		
		function bigImg(index){
			if(index==0){
				$(".big-img > li").eq(0).find("img").animate({opacity:'0.5'},10,function(){
					$(".big-img > li").removeClass("cur");
					$(this).parent().addClass("cur").animate({left:'400px',top:'172px'},500);
					$(this).animate({width:'400px',height:'400px'},500,function(){
						$(this).animate({opacity:'1'},100,function(){
							$(this).parent().find("a.close").fadeIn(800);
							});
						})
					
					})
				}//index==0 end
			
			else if(index==1){
				$(".big-img > li").eq(1).find("img").animate({opacity:'0.5'},10,function(){
					$(".big-img > li").removeClass("cur");
					$(this).parent().addClass("cur").animate({left:'400px',top:'65px'},500);
					$(this).animate({width:'400px',height:'400px'},500,function(){
						$(this).animate({opacity:'1'},100,function(){
							$(this).parent().find("a.close").fadeIn(800);
							});
						})
					
					})
				}//index==1 end
				
			else if(index==2){
				$(".big-img > li").eq(2).find("img").animate({opacity:'0.5'},10,function(){
					$(".big-img > li").removeClass("cur");
					$(this).parent().addClass("cur").animate({left:'400px',top:'125px'},500);
					$(this).animate({width:'400px',height:'400px'},500,function(){
						$(this).animate({opacity:'1'},100,function(){
							$(this).parent().find("a.close").fadeIn(800);
							});
						})
					})
				}//index==2 end
				
			else if(index==3){
				$(".big-img > li").eq(3).find("img").animate({opacity:'0.5'},10,function(){
					$(".big-img > li").removeClass("cur");
					$(this).parent().addClass("cur").animate({left:'400px',top:'145px'},500);
					$(this).animate({width:'400px',height:'400px'},500,function(){
						$(this).animate({opacity:'1'},100,function(){
							$(this).parent().find("a.close").fadeIn(800);
							});
						})
					})
				}//index==3 end
				
			else if(index==4){
				$(".big-img > li").eq(4).find("img").animate({opacity:'0.5'},10,function(){
					$(".big-img > li").removeClass("cur");
					$(this).parent().addClass("cur").animate({left:'400px',top:'145px'},500);
					$(this).animate({width:'400px',height:'400px'},500,function(){
						$(this).animate({opacity:'1'},100,function(){
							$(this).parent().find("a.close").fadeIn(800);
							});
						})
					})
				}//index==4 end

			else if(index==5){
				$(".big-img > li").eq(5).find("img").animate({opacity:'0.5'},10,function(){
					$(".big-img > li").removeClass("cur");
					$(this).parent().addClass("cur").animate({left:'400px',top:'145px'},500);
					$(this).animate({width:'400px',height:'400px'},500,function(){
						$(this).animate({opacity:'1'},100,function(){
							$(this).parent().find("a.close").fadeIn(800);
							});
						})
					})
				}//index==5 end
				
			else if(index==6){
				$(".big-img > li").eq(6).find("img").animate({opacity:'0.5'},10,function(){
					$(".big-img > li").removeClass("cur");
					$(this).parent().addClass("cur").animate({left:'400px',top:'160px'},500);
					$(this).animate({width:'400px',height:'400px'},500,function(){
						$(this).animate({opacity:'1'},100,function(){
							$(this).parent().find("a.close").fadeIn(800);
							});
						})
					})
				}//index==6 end
				
			else if(index==7){
				$(".big-img > li").eq(7).find("img").animate({opacity:'0.5'},10,function(){
					$(".big-img > li").removeClass("cur");
					$(this).parent().addClass("cur").animate({left:'400px',top:'165px'},500);
					$(this).animate({width:'400px',height:'400px'},500,function(){
						$(this).animate({opacity:'1'},100,function(){
							$(this).parent().find("a.close").fadeIn(800);
							});
						})
					})
				}//index==7 end
				
				
				
				
			}//bigImg end
			function bigImg_1360(index){
			if(index==0){
				$(".big-img > li").eq(0).find("img").animate({opacity:'0.5'},10,function(){
					$(".big-img > li").removeClass("cur");
					$(this).parent().addClass("cur").animate({left:'470px',top:'130px'},500);
					$(this).animate({width:'200px',height:'200px'},500,function(){
						$(this).animate({opacity:'1'},100,function(){
							$(this).parent().find("a.close").fadeIn(800);
							});
						})
					
					})
				}//index==0 end
			
			else if(index==1){
				$(".big-img > li").eq(1).find("img").animate({opacity:'0.5'},10,function(){
					$(".big-img > li").removeClass("cur");
					$(this).parent().addClass("cur").animate({left:'450px',top:'65px'},500);
					$(this).animate({width:'200px',height:'200px'},500,function(){
						$(this).animate({opacity:'1'},100,function(){
							$(this).parent().find("a.close").fadeIn(800);
							});
						})
					
					})
				}//index==1 end
				
			else if(index==2){
				$(".big-img > li").eq(2).find("img").animate({opacity:'0.5'},10,function(){
					$(".big-img > li").removeClass("cur");
					$(this).parent().addClass("cur").animate({left:'414px',top:'125px'},500);
					$(this).animate({width:'200px',height:'200px'},500,function(){
						$(this).animate({opacity:'1'},100,function(){
							$(this).parent().find("a.close").fadeIn(800);
							});
						})
					})
				}//index==2 end
				
			else if(index==3){
				$(".big-img > li").eq(3).find("img").animate({opacity:'0.5'},10,function(){
					$(".big-img > li").removeClass("cur");
					$(this).parent().addClass("cur").animate({left:'450px',top:'145px'},500);
					$(this).animate({width:'200px',height:'200px'},500,function(){
						$(this).animate({opacity:'1'},100,function(){
							$(this).parent().find("a.close").fadeIn(800);
							});
						})
					})
				}//index==3 end
				
			else if(index==4){
				$(".big-img > li").eq(4).find("img").animate({opacity:'0.5'},10,function(){
					$(".big-img > li").removeClass("cur");
					$(this).parent().addClass("cur").animate({left:'430px',top:'145px'},500);
					$(this).animate({width:'200px',height:'200px'},500,function(){
						$(this).animate({opacity:'1'},100,function(){
							$(this).parent().find("a.close").fadeIn(800);
							});
						})
					})
				}//index==4 end
			
			else if(index==5){
				$(".big-img > li").eq(5).find("img").animate({opacity:'0.5'},10,function(){
					$(".big-img > li").removeClass("cur");
					$(this).parent().addClass("cur").animate({left:'430px',top:'178px'},500);
					$(this).animate({width:'200px',height:'200px'},500,function(){
						$(this).animate({opacity:'1'},100,function(){
							$(this).parent().find("a.close").fadeIn(800);
							});
						})
					})
				}//index==5 end
				
			else if(index==6){
				$(".big-img > li").eq(6).find("img").animate({opacity:'0.5'},10,function(){
					$(".big-img > li").removeClass("cur");
					$(this).parent().addClass("cur").animate({left:'430px',top:'200px'},500);
					$(this).animate({width:'200px',height:'200px'},500,function(){
						$(this).animate({opacity:'1'},100,function(){
							$(this).parent().find("a.close").fadeIn(800);
							});
						})
					})
				}//index==6 end
				
			else if(index==7){
				$(".big-img > li").eq(7).find("img").animate({opacity:'0.5'},10,function(){
					$(".big-img > li").removeClass("cur");
					$(this).parent().addClass("cur").animate({left:'450px',top:'165px'},500);
					$(this).animate({width:'200px',height:'200px'},500,function(){
						$(this).animate({opacity:'1'},100,function(){
							$(this).parent().find("a.close").fadeIn(800);
							});
						})
					})
				}//index==7 end
				
				
				
				
			}//bigImg1360 end
			
			function bigImg2(index){
			if(index==0){
				$(".big-img > li").eq(0).animate({left:'400px',top:'172px'},500).end().eq(0).find("img").animate({width:'400px',height:'400px',opacity:'1'},500,function(){
					$(this).parent().find("a.close").fadeIn(800);
					});
				}//index==0 end
			
			else if(index==1){
				$(".big-img > li").eq(1).animate({left:'400px',top:'65px'},500).end().eq(1).find("img").animate({width:'400px',height:'400px',opacity:'1'},500,function(){
					$(this).parent().find("a.close").fadeIn(800);		
				});
				}//index==1 end
				
			else if(index==2){
				$(".big-img > li").eq(2).animate({left:'400px',top:'125px'},500).end().eq(2).find("img").animate({width:'400px',height:'400px',opacity:'1'},500,function(){
					$(this).parent().find("a.close").fadeIn(800);
					});
				}//index==2 end
				
			else if(index==3){
				$(".big-img > li").eq(3).animate({left:'400px',top:'145px'},500).end().eq(3).find("img").animate({width:'400px',height:'400px',opacity:'1'},500,function(){
					$(this).parent().find("a.close").fadeIn(800);					
					});
				}//index==3 end
				
			else if(index==4){
				$(".big-img > li").eq(4).animate({left:'400px',top:'145px'},500).end().eq(4).find("img").animate({width:'400px',height:'400px',opacity:'1'},500,function(){
					$(this).parent().find("a.close").fadeIn(800);
					});
				}//index==4 end

			else if(index==5){
				$(".big-img > li").eq(5).animate({left:'400px',top:'145px'},500).end().eq(5).find("img").animate({width:'400px',height:'400px',opacity:'1'},500,function(){
					$(this).parent().find("a.close").fadeIn(800);
					});
				}//index==5 end
				
			else if(index==6){
				$(".big-img > li").eq(6).animate({left:'400px',top:'160px'},500).end().eq(6).find("img").animate({width:'400px',height:'400px',opacity:'1'},500,function(){
					$(this).parent().find("a.close").fadeIn(800);					
					});
				}//index==6 end
				
			else if(index==7){
				$(".big-img > li").eq(7).animate({left:'400px',top:'165px'},500).end().eq(7).find("img").animate({width:'400px',height:'400px',opacity:'1'},500,function(){					
					$(this).parent().find("a.close").fadeIn(800);
					});
				}//index==7 end
				
			}//bigImg2 end
		
		
		function bigImg2_1360(index){
			if(index==0){
				$(".big-img > li").eq(0).animate({left:'470px',top:'130px'},500).end().eq(0).find("img").animate({width:'200px',height:'200px',opacity:'1'},500,function(){
					$(this).parent().find("a.close").fadeIn(800);
					});
				}//index==0 end
			
			else if(index==1){
				$(".big-img > li").eq(1).animate({left:'450px',top:'65px'},500).end().eq(1).find("img").animate({width:'200px',height:'200px',opacity:'1'},500,function(){
					$(this).parent().find("a.close").fadeIn(800);		
				});
				}//index==1 end
				
			else if(index==2){
				$(".big-img > li").eq(2).animate({left:'414px',top:'125px'},500).end().eq(2).find("img").animate({width:'200px',height:'200px',opacity:'1'},500,function(){
					$(this).parent().find("a.close").fadeIn(800);
					});
				}//index==2 end
				
			else if(index==3){
				$(".big-img > li").eq(3).animate({left:'450px',top:'145px'},500).end().eq(3).find("img").animate({width:'200px',height:'200px',opacity:'1'},500,function(){
					$(this).parent().find("a.close").fadeIn(800);					
					});
				}//index==3 end
				
			else if(index==4){
				$(".big-img > li").eq(4).animate({left:'430px',top:'145px'},500).end().eq(4).find("img").animate({width:'200px',height:'200px',opacity:'1'},500,function(){
					$(this).parent().find("a.close").fadeIn(800);
					});
				}//index==4 end
				
			else if(index==5){
				$(".big-img > li").eq(5).animate({left:'430px',top:'178px'},500).end().eq(5).find("img").animate({width:'200px',height:'200px',opacity:'1'},500,function(){
					$(this).parent().find("a.close").fadeIn(800);
					});
				}//index==5 end
				
			else if(index==6){
				$(".big-img > li").eq(6).animate({left:'430px',top:'200px'},500).end().eq(6).find("img").animate({width:'200px',height:'200px',opacity:'1'},500,function(){
					$(this).parent().find("a.close").fadeIn(800);					
					});
				}//index==6 end
				
			else if(index==7){
				$(".big-img > li").eq(7).animate({left:'450px',top:'165px'},500).end().eq(7).find("img").animate({width:'200px',height:'200px',opacity:'1'},500,function(){					
					$(this).parent().find("a.close").fadeIn(800);
					});
				}//index==7 end
				
			}//bigImg2 end
		
		function bigImgHide(index){
			
			$(".big-img li").each(function(){
				//var bb=$(this).find("img").attr("opacity").val();
				
				if($(this).hasClass("cur")){
					var index=$(this).index();
					
					
					if(index==0){
						$(".big-img > li").eq(0).find("a.close").fadeOut(100).end().find("img").animate({opacity:'0.5'},10,function(){
							
							$(this).animate({width:'0',height:'0'},500,function(){
								$(this).animate({opacity:'0'},100,function(){
									});
							});
							$(this).parent().animate({left:'702px',top:'202px'},500).removeClass("cur");
							
						});					
									
				    }//index==0 end
					
					else if(index==1){
						$(".big-img > li").eq(1).find("a.close").fadeOut(100).end().find("img").animate({opacity:'0.5'},10,function(){
							
							$(this).animate({width:'0',height:'0'},500,function(){
								$(this).animate({opacity:'0'},100);
							});
							$(this).parent().animate({left:'602px',top:'65px'},500).removeClass("cur");
							
						});					
									
				    }//index==1 end
					
					else if(index==2){
						$(".big-img > li").eq(2).find("a.close").fadeOut(100).end().find("img").animate({opacity:'0.5'},10,function(){
							
							$(this).animate({width:'0',height:'0'},500,function(){
								$(this).animate({opacity:'0'},100);
							});
							$(this).parent().animate({left:'435px',top:'205px'},500).removeClass("cur");
							
						});					
									
				    }//index==2 end
					
					else if(index==3){
						$(".big-img > li").eq(3).find("a.close").fadeOut(100).end().find("img").animate({opacity:'0.5'},10,function(){
							
							$(this).animate({width:'0',height:'0'},500,function(){
								$(this).animate({opacity:'0'},100);
							});
							$(this).parent().animate({left:'632px',top:'251px'},500).removeClass("cur");
							
						});					
									
				    }//index==3 end
					
					else if(index==4){
						$(".big-img > li").eq(4).find("a.close").fadeOut(100).end().find("img").animate({opacity:'0.5'},10,function(){
							
							$(this).animate({width:'0',height:'0'},500,function(){
								$(this).animate({opacity:'0'},100);
							});
							$(this).parent().animate({left:'600px',top:'312px'},500).removeClass("cur");
							
						});					
									
				    }//index==4 end

				    else if(index==5){
						$(".big-img > li").eq(5).find("a.close").fadeOut(100).end().find("img").animate({opacity:'0.5'},10,function(){
							
							$(this).animate({width:'0',height:'0'},500,function(){
								$(this).animate({opacity:'0'},100);
							});
							$(this).parent().animate({left:'593px',top:'255px'},500).removeClass("cur");
							
						});					
									
				    }//index==5 end
					
					else if(index==6){
						$(".big-img > li").eq(6).find("a.close").fadeOut(100).end().find("img").animate({opacity:'0.5'},10,function(){
							
							$(this).animate({width:'0',height:'0'},500,function(){
								$(this).animate({opacity:'0'},100);
							});
							$(this).parent().animate({left:'650px',top:'542px'},500).removeClass("cur");
							
						});					
									
				    }//index==6 end
					
					else if(index==7){
						$(".big-img > li").eq(7).find("a.close").fadeOut(100).end().find("img").animate({opacity:'0.5'},10,function(){
							
							$(this).animate({width:'0',height:'0'},500,function(){
								$(this).animate({opacity:'0'},100);
							});
							$(this).parent().animate({left:'640px',top:'341px'},500).removeClass("cur");
							
						});					
									
				    }//index==7 end
					
					
					
					
					
					
					
					
				}//hasClass end
			})//each function end
			
		}//bigImgHide end
			
			
		
		
		
		
		function handle(index){
			if(index==0){
				
				$(".bigdatabox .itemsbox").fadeOut(10).animate({ height:"0",opacity:"0",top:"290px"},100,function(){
				$(".items li").children("img").animate({width:"0"},10);
				$(".bigdatabox .itemsbox").eq(0).animate({width:"0"},10);
				
				})
				$(".items li").eq(index).children(".dot").animate({width:"24px",height:"24px",opacity:"1"},300,function(){
				$(this).children("img").animate({opacity:"1",'z-index':'999'},100, function(){
									
					

									$(".bigdatabox .items01").fadeIn(10).animate({height:"125px",top:"90px",'z-index':'999'},100,function(){//aa								
										
								
										$(this).animate({width:"300px",opacity:"1"},300,function(){
								
											//$(this).children("h2").addClass("flash animated");
											$(this).find(".tabledata").animate({width:"100%",opacity:"1"},100).fadeIn(100);
											$(".items li").eq(index).children(".xian").animate({width:"192px",opacity:"0.3"},800,function(){
										$(this).animate({opacity:"1"},100);
										})
										})
								    })//aa end	
									
									
						
					
				});
			});
				
				}
				
				
			else if(index==1){
				
				$(".bigdatabox .itemsbox").fadeOut(10).animate({ height:"0",opacity:"0",top:"50px"},10,function(){
				$(".bigdatabox .itemsbox").hide();
				$(".items li").children("img").animate({width:"0"},10);
				$(".items02").find(".wdj").css({width:"0",height:"0",top:"251px",opacity:"0"});
				$(".items02").find(".wdj2,.wdj2 span").css({width:"0",height:"0",opacity:"0"});
				$(".items02").find(".wdj2 span").css({width:"0",height:"0",bottom:"44px",opacity:"0"});
				
				})
				$(".items li").eq(index).children(".dot").animate({width:"24px",height:"24px",opacity:"1"},300,function(){
				$(this).children("img").animate({opacity:"1",'z-index':'999'},300, function(){
									
					

									$(".bigdatabox .items02").fadeIn(300).animate({width:"300px",'z-index':'999'},10,function(){//aa								
										
								
										$(this).animate({height:"360px",top:"50px",opacity:"1"},300,function(){

											//$(this).children("h2").addClass("flash animated");
											$(this).find(".wdj").animate({width:"63px",opacity:"0.5"},10,function(){
													$(this).animate({height:"161px",top:"30px",opacity:"1"},100,function(){
														$(this).find(".wdj2").animate({width:"63px",opacity:"0.5"},10,function(){
															$(this).animate({height:"161px",opacity:"1"},500,function(){
																$(this).find("span").animate({width:"17px",opacity:"0.5"},10,function(){
																	$(this).animate({height:"11px",opacity:"1"},100,function(){
																		//$(this).animate({bottom:"121.5px"},500)
																		var dmin=34.7;
																		var dmax=43;
																		var basevalue=44;
																		var dheight=161;
																		var val=$(".items02").find(".du").find("span").html();
																		var realbottom=dheight*(val-dmin)/(dmax-dmin);
																		var bottom=44+realbottom;
																		
																		//alert(realbottom);
																		$(this).animate({bottom:bottom+"px"},300);
																		
																		})
																	})	
																})
															})
														})
											})
											//$(this).find(".tabledata").animate({width:"100%",opacity:"1"},100).fadeIn(100);
											
										})
								    })//aa end	

									$(".items li").eq(index).children(".xian").animate({width:"286px",opacity:"1"},500);
					
				});
			});
				
				}
				
				
			if(index==2){
				
				$(".bigdatabox .itemsbox").fadeOut(10).animate({ height:"0",opacity:"0",top:"100px"},100,function(){
					//$(".items li span").animate({opacity:"0"},100);
				$(".items li").children("img").animate({width:"0"},10);
				$(".items li").eq(2).children(".xian").animate({width:"0",opacity:"0",'z-index':'999'},100).fadeOut(10);
				
				})
				$(".items li").eq(index).children(".dot").animate({width:"24px",height:"24px",opacity:"1"},300,function(){
				$(this).children("img").animate({opacity:"1"},300, function(){

									$(".bigdatabox .items03").fadeIn(10).animate({width:"330px",'z-index':'999'},100,function(){//aa								
								
										$(this).animate({height:"172px",top:"100px",opacity:"1"},300,function(){
								
											//$(this).children("h2").addClass("flash animated");
											$(this).find(".tabledata").animate({width:"100%",opacity:"1"},10).fadeIn(100);
										})
								    })//aa end	
									
									$(".items li").eq(index).children(".xian").animate({width:"440px",opacity:"1"},10).fadeIn(800);
									
									
					
				});
			});
				
				}
				
				
			else if(index==3){
				
				$(".bigdatabox .itemsbox").fadeOut(10).animate({ height:"0",opacity:"0",top:"324px",'z-index':'999'},100,function(){
				
				$(".items li").children("img").animate({width:"0"},100);
				$(".items li").eq(2).children(".xian").animate({width:"0",opacity:"0"},10).fadeOut();
				$("items04").animate({right:"320px",width:"0px",height:"0",opacity:"0",top:"324px"},10);
				
				})
				$(".items li").eq(index).children(".dot").animate({width:"24px",height:"24px",opacity:"1"},300,function(){
				$(this).children("img").animate({opacity:"1",'z-index':'999'},300, function(){
									
					

									$(".bigdatabox .items04").fadeIn(10).animate({width:"300px",height:"172px",top:"100px",opacity:"0.8",'z-index':'999'},500,function(){//aa								
										
								
										$(this).animate({opacity:"1"},500,function(){
								
											//$(this).children("h2").addClass("flash animated");
											$(this).find(".tabledata").animate({width:"100%",opacity:"1"},10).fadeIn(300);
										})
								    })//aa end	

									$(".items li").eq(index).children(".xian").animate({width:"256px",opacity:"1",'z-index':'999'},700);
					
				});
			});
				
				}
				
				
			if(index==4){
				
				$(".bigdatabox .itemsbox").fadeOut(10).animate({ height:"0",opacity:"0",top:"427px"},100,function(){
					$(".items li").children("img").animate({width:"0"},100);
					$(".items li").eq(2).children(".xian").animate({width:"0",opacity:"0"},100).fadeOut();
					$(".items li").eq(4).children(".xian").animate({width:"0",opacity:"0"},10);
				
				})
				$(".items li").eq(index).children(".dot").animate({width:"24px",height:"24px",opacity:"1"},300,function(){
				$(this).children("img").animate({width:"24px",opacity:"1",'z-index':'999'},300, function(){
									
					

									$(".bigdatabox .items05").fadeIn(10).animate({width:"360px",'z-index':'999'},100,function(){//aa								
										
								
										$(this).animate({height:"264px",top:"200px",opacity:"1"},300,function(){
								
											//$(this).children("h2").addClass("flash animated");
											$(this).find(".tabledata").animate({width:"100%",opacity:"1"},10).fadeIn(100);
										})
								    })//aa end	

									$(".items li").eq(index).children(".xian").animate({width:"230px",opacity:"1"},300);
					
				});
			});
				
				}



			if(index==5){
				
				$(".bigdatabox .itemsbox").fadeOut(10).animate({ height:"0",opacity:"0",top:"427px"},100,function(){
					$(".items li").children("img").animate({width:"0"},100);
					$(".items li").eq(2).children(".xian").animate({width:"0",opacity:"0"},100).fadeOut();
					$(".items li").eq(4).children(".xian").animate({width:"0",opacity:"0"},10);
				
				})
				$(".items li").eq(index).children(".dot").animate({width:"24px",height:"24px",opacity:"1"},300,function(){
				$(this).children("img").animate({width:"24px",opacity:"1",'z-index':'999'},300, function(){
									
					

									$(".bigdatabox .items06").fadeIn(10).animate({width:"360px",'z-index':'999'},100,function(){//aa								
										
								
										$(this).animate({height:"120px",top:"200px",opacity:"1"},300,function(){
								
											//$(this).children("h2").addClass("flash animated");
											$(this).find(".tabledata").animate({width:"100%",opacity:"1"},10).fadeIn(100);
										})
								    })//aa end	

									$(".items li").eq(index).children(".xian").animate({width:"230px",opacity:"1"},300);
					
				});
			});
				
				}
				
				
				
			else if(index==6){
				
				$(".bigdatabox .itemsbox").fadeOut(10).animate({ height:"0",opacity:"0",top:"482px"},100,function(){
				$(".items li").children("img").animate({width:"0"},10);
				
				})
				$(".items li").eq(index).children(".dot").animate({width:"24px",height:"24px",opacity:"1"},300,function(){
				$(this).children("img").animate({opacity:"1",'z-index':'999'},300, function(){
									
					

									$(".bigdatabox .items07").fadeIn(300).animate({width:"360px",top:"270px",'z-index':'999'},10,function(){//aa								
										
								
										$(this).animate({height:"460px",top:"50px",opacity:"0.8"},500,function(){
											$(this).animate({opacity:"1"},500);
											//$(this).children("h2").addClass("flash animated");
											$(this).find(".tabledata").animate({width:"100%",opacity:"1"},500).fadeIn(100);
										})
								    })//aa end	
									$(".items li").eq(index).children(".xian").animate({width:"200px",opacity:"0.3"},300,function(){
										$(this).animate({opacity:"1"},500)
										});
							
					
				});
			});
				}
				
							
			else if(index==7){
			
			$(".bigdatabox .itemsbox").fadeOut(10).animate({ height:"0",opacity:"0",top:"320px"},100,function(){
				$(".items li").children("img").animate({width:"0",opacity:"0"},10);
				
				})
			$(".items li").eq(index).children(".dot").animate({width:"24px",height:"24px",opacity:"1"},300,function(){
				$(this).children("img").animate({opacity:"1",'z-index':'999'},300, function(){

									$(".bigdatabox .items08").fadeIn(300).animate({width:"300px",'z-index':'999'},10,function(){//aa								
										
								
										$(this).animate({height:"555px",top:"50px",opacity:"1"},500,function(){
								
											
											$(this).find(".tabledata").animate({width:"100%",opacity:"1"},500).fadeIn(100);
											//$(this).children("h2").addClass("flash animated");
										})
								    })//aa end	

									$(".items li").eq(index).children(".xian").animate({width:"257px",opacity:"1"},700);
					
				});
			});
			
			}<!--7 end-->
			}//handle end

function bigImgHide1360(index){
			
			$(".big-img li").each(function(){
				//var bb=$(this).find("img").attr("opacity").val();
				
				if($(this).hasClass("cur")){
					var index=$(this).index();
					
					
					if(index==0){
						$(".big-img > li").eq(0).find("a.close").fadeOut(100).end().find("img").animate({opacity:'0.5'},10,function(){
							
							$(this).animate({width:'0',height:'0'},500,function(){
								$(this).animate({opacity:'0'},100,function(){
									});
							});
							$(this).parent().animate({left:'612px',top:'152px'},500).removeClass('cur');
							
						});					
									
				    }//index==0 end
					
					else if(index==1){
						$(".big-img > li").eq(1).find("a.close").fadeOut(100).end().find("img").animate({opacity:'0.5'},10,function(){
							
							$(this).animate({width:'0',height:'0'},500,function(){
								$(this).animate({opacity:'0'},100);
							});
							$(this).parent().animate({left:'552px',top:'77px'},500).removeClass('cur');
							
						});					
									
				    }//index==1 end
					
					else if(index==2){
						$(".big-img > li").eq(2).find("a.close").fadeOut(100).end().find("img").animate({opacity:'0.5'},10,function(){
							
							$(this).animate({width:'0',height:'0'},500,function(){
								$(this).animate({opacity:'0'},100);
							});
							$(this).parent().animate({left:'452px',top:'162px'},500).removeClass('cur');
							
						});					
									
				    }//index==2 end
					
					else if(index==3){
						$(".big-img > li").eq(3).find("a.close").fadeOut(100).end().find("img").animate({opacity:'0.5'},10,function(){
							
							$(this).animate({width:'0',height:'0'},500,function(){
								$(this).animate({opacity:'0'},100);
							});
							$(this).parent().animate({left:'572px',top:'177px'},500).removeClass('cur');
							
						});					
									
				    }//index==3 end
					
					else if(index==4){
						$(".big-img > li").eq(4).find("a.close").fadeOut(100).end().find("img").animate({opacity:'0.5'},10,function(){
							
							$(this).animate({width:'0',height:'0'},500,function(){
								$(this).animate({opacity:'0'},100);
							});
							$(this).parent().animate({left:'545px',top:'227px'},500).removeClass('cur');
							
						});					
									
				    }//index==4 end
					
					else if(index==5){
						$(".big-img > li").eq(5).find("a.close").fadeOut(100).end().find("img").animate({opacity:'0.5'},10,function(){
							
							$(this).animate({width:'0',height:'0'},500,function(){
								$(this).animate({opacity:'0'},100);
							});
							$(this).parent().animate({left:'552px',top:'192px'},500).removeClass('cur');
							
						});					
									
				    }//index==5 end
					
					else if(index==6){
						$(".big-img > li").eq(6).find("a.close").fadeOut(100).end().find("img").animate({opacity:'0.5'},10,function(){
							
							$(this).animate({width:'0',height:'0'},500,function(){
								$(this).animate({opacity:'0'},100);
							});
							$(this).parent().animate({left:'582px',top:'372px'},500).removeClass('cur');
							
						});					
									
				    }//index==6 end
					
					else if(index==7){
						$(".big-img > li").eq(7).find("a.close").fadeOut(100).end().find("img").animate({opacity:'0.5'},10,function(){
							
							$(this).animate({width:'0',height:'0'},500,function(){
								$(this).animate({opacity:'0'},100);
							});
							$(this).parent().animate({left:'568px',top:'252px'},500).removeClass('cur');
							
						});					
									
				    }//index==7 end
					
					
				}//hasClass end
			})//each function end
			
		}//bigImgHide end

$(document).ready(function(){
	$(".menus > li").hover(//主菜单鼠标滑动效果
        function() {
			$(this).addClass("active").find(".menus-children").show(100);
		},
		function() {
			$(this).removeClass("active").find(".menus-children").hide(300);
		}
	);
})



// 获取分类数据
function showdatas(types){
	var userid 		=	$('#hiddeninput').val();
	if (!userid || !types) {
		return false;
	}

	$.ajax({
		type: "POST",
		url	: "/process/index/getdata",
		data: "userid="+userid+"&type="+types,
		dataType:'json',
		success: function(msg){
			console.log(msg)
			var upimg	=	"<img src='/Public/process/img/20x32_up.png' alt='上升' />";
			var downimg	=	"<img src='/Public/process/img/20x32_down.png' alt='下降' />";

			// 血糖
			if(msg && types == 'gl' && msg.status == 1){
				
				$('#bbsugardiv table.tabledata tr.glerrtr').hide();
				$('#bbsugardiv table.tabledata tr.gldatatr').show();

				$('#bbsugartitle').html(msg.bbsugartitle);
				$('#bbsugardata').html(msg.bbsugar);
				
				$('#gltime').html('检测时间：' + msg.examtime);

				$('#bdckdata').html(msg.ranges);
			}else if( msg && types == 'gl' && msg.status != 1  ){
				
				$('#bbsugardiv table.tabledata tr.glerrtr').show();
				$('#bbsugardiv table.tabledata tr.gldatatr').hide();

				$('#gltime').html('');
			}
			if(msg && msg.bbsugar && msg.showimg && msg.showimg == 1 && types == 'gl' ){
				$('#bbsugardata').parent('td').children('img').remove();
				$('#bbsugardata').parent('td').append(downimg);
			}else if( msg && msg.bbsugar && msg.showimg && msg.showimg == 2 && types == 'gl' ){
				$('#bbsugardata').parent('td').children('img').remove();
				$('#bbsugardata').parent('td').append(upimg);
			}


			// 血压
			if(msg && types == 'bp' && msg.status == 1){
				$('#boodpdiv table.tabledata tr.bperrtr').hide();
				$('#boodpdiv table.tabledata tr.bpdatatr').show();
				$('#hboodpdata').html(msg.hboodp);
				$('#lboodpdata').html(msg.lboodp);
				$('#bptime').html('检测时间：' + msg.examtime);
			}else if( msg && types == 'bp' && msg.status != 1 ){
				$('#boodpdiv table.tabledata tr.bperrtr').show();
				$('#boodpdiv table.tabledata tr.bpdatatr').hide();
				$('#bptime').html('');
			}

			if( msg && msg.lboodp && msg.lshowimg && msg.lshowimg == 1 && types == 'bp'){
				$('#lboodpdata').parent('td').children('img').remove();
				$('#lboodpdata').parent('td').append(downimg);
			}else if(msg && msg.lboodp && msg.lshowimg && msg.lshowimg == 2 && types == 'bp' ){
				$('#lboodpdata').parent('td').children('img').remove();
				$('#lboodpdata').parent('td').append(upimg);
			}
			if(msg && msg.hboodp && msg.hshowimg && msg.hshowimg == 1 && types == 'bp'){
				$('#hboodpdata').parent('td').children('img').remove();
				$('#hboodpdata').parent('td').append(downimg);
			}else if(msg && msg.hboodp && msg.hshowimg && msg.hshowimg == 2 && types == 'bp'){
				$('#hboodpdata').parent('td').children('img').remove();
				$('#hboodpdata').parent('td').append(upimg);
			}

			// 血氧
			if( msg && types == 'ox' && msg.status == 1 ){
				$('#oxygen table.tabledata tr.oxerrtr').hide();
				$('#oxygen table.tabledata tr.oxdatatr').show();

				$('#saturationdata').html(msg.saturation);
				$('#prdata').html(msg.pr);
				$('#oxtime').html('检测时间：' + msg.examtime);
			}else if( msg && types == 'ox' && msg.status != 1  ){
				$('#oxygen table.tabledata tr.oxerrtr').show();
				$('#oxygen table.tabledata tr.oxdatatr').hide();
				$('#oxtime').html('');
			}
			if( msg && msg.pr && msg.primg && msg.primg == 1 && types == 'ox'){
				$('#prdata').parent('td').children('img').remove();
				$('#prdata').parent('td').append(downimg);
			}else if(msg && msg.pr && msg.primg && msg.primg == 2 && types == 'ox' ){
				$('#prdata').parent('td').children('img').remove();
				$('#prdata').parent('td').append(upimg);
			}
			if(msg && msg.saturation && msg.saturationimg && msg.saturationimg == 1 && types == 'ox'){
				$('#saturationdata').parent('td').children('img').remove();
				$('#saturationdata').parent('td').append(downimg);
			}else if(msg && msg.saturation && msg.saturationimg && msg.saturationimg == 2 && types == 'ox'){
				$('#saturationdata').parent('td').children('img').remove();
				$('#saturationdata').parent('td').append(upimg);
			}


			// 血脂
			if(msg && types == 'bf' && msg.status == 1 ){
				$('#bloodfatdiv table.tabledata tr.bferrtr').hide();
				$('#bloodfatdiv table.tabledata tr.bfdatatr').show();
				$('#tcdata').html(msg.tc);
				$('#tgdata').html(msg.tg);
				$('#htcdata').html(msg.htc);
				$('#ltcdata').html(msg.ltc);
				$('#bftime').html('检测时间：' + msg.examtime);
			}else if( msg  && types == 'bf' && msg.status != 1){
				$('#bloodfatdiv table.tabledata tr.bferrtr').show();
				$('#bloodfatdiv table.tabledata tr.bfdatatr').hide();
				$('#bftime').html('');
			}

			if(msg && msg.tg && msg.tgimg && msg.tgimg == 1 && types == 'bf' ){
				$('#tgdata').parent('td').children('img').remove();
				$('#tgdata').parent('td').append(downimg);
			}else if(msg && msg.tg && msg.tgimg && msg.tgimg == 2 && types == 'bf'){
				$('#tgdata').parent('td').children('img').remove();
				$('#tgdata').parent('td').append(upimg);
			}
			if(msg && msg.ltc && msg.ltcimg && msg.ltcimg == 1 && types == 'bf' ){
				$('#ltcdata').parent('td').children('img').remove();
				$('#ltcdata').parent('td').append(downimg);
			}else if(msg && msg.ltc && msg.ltcimg && msg.ltcimg == 2 && types == 'bf' ){
				$('#ltcdata').parent('td').children('img').remove();
				$('#ltcdata').parent('td').append(upimg);
			}
			if(msg && msg.htc && msg.htcimg && msg.htcimg == 1 && types == 'bf' ){
				$('#htcdata').parent('td').children('img').remove();
				$('#htcdata').parent('td').append(downimg);
			}else if(msg && msg.htc && msg.htcimg && msg.htcimg == 2 && types == 'bf'){
				$('#htcdata').parent('td').children('img').remove();
				$('#htcdata').parent('td').append(upimg);
			}
			if(msg && msg.tc && msg.tcimg && msg.tcimg == 1 && types == 'bf'){
				$('#tcdata').parent('td').children('img').remove();
				$('#tcdata').parent('td').append(downimg);
			}else if(msg && msg.tc && msg.tcimg && msg.tcimg == 2 && types == 'bf'){
				$('#tcdata').parent('td').children('img').remove();
				$('#tcdata').parent('td').append(upimg);
			}


			// 体成分
			if(msg && types == 'we' && msg.status == 1){
				$('#humanbodydata table.tabledata tr.weerrtr').hide();
				$('#humanbodydata table.tabledata tr.wedatatr').show();

				$('#weightdata').html(msg.weight);
				$('#bmidata').html(msg.bmi);
				$('#bfdata').html(msg.bf);
				$('#fatweightdata').html(msg.fatweight);
				$('#proteindata').html(msg.protein);
				$('#waterdata').html(msg.water);
				$('#muscledata').html(msg.muscle);
				$('#mineralsalts').html(msg.mineralsalts);
				$('#fatdata').html(msg.fat);
				$('#wetime').html('检测时间：' + msg.examtime);
			}else if( msg && types == 'we' && msg.status != 1){
				$('#humanbodydata table.tabledata tr.weerrtr').show();
				$('#humanbodydata table.tabledata tr.wedatatr').hide();
				$('#wetime').html('');
			}
			//	BMI
			if(msg && msg.bmi && msg.bmiimg && msg.bmiimg == 1 && types == 'we' ){
				$('#bmidata').parent('td').children('img').remove();
				$('#bmidata').parent('td').append(downimg);
			}else if(msg && msg.bmi && msg.bmiimg && msg.bmiimg == 2 && types == 'we'){
				$('#bmidata').parent('td').children('img').remove();
				$('#bmidata').parent('td').append(upimg);
			}
			//	体脂率
			if(msg && msg.bf && msg.bfimg && msg.bfimg == 1 && types == 'we' ){
				$('#bfdata').parent('td').children('img').remove();
				$('#bfdata').parent('td').append(downimg);
			}else if(msg && msg.bmi && msg.bfimg && msg.bfimg == 2 && types == 'we'){
				$('#bfdata').parent('td').children('img').remove();
				$('#bfdata').parent('td').append(upimg);
			}
			//	去脂体重
			if(msg && msg.fatweight && msg.fatweightimg && msg.fatweightimg == 1 && types == 'we' ){
				$('#fatweightdata').parent('td').children('img').remove();
				$('#fatweightdata').parent('td').append(downimg);
			}else if(msg && msg.fatweight && msg.fatweightimg && msg.fatweightimg == 2 && types == 'we'){
				$('#fatweightdata').parent('td').children('img').remove();
				$('#fatweightdata').parent('td').append(upimg);
			}
			//	内脏脂肪指数
			if(msg && msg.protein && msg.proteinimg && msg.proteinimg == 1 && types == 'we' ){
				$('#proteindata').parent('td').children('img').remove();
				$('#proteindata').parent('td').append(downimg);
			}else if(msg && msg.protein && msg.proteinimg && msg.proteinimg == 2 && types == 'we'){
				$('#proteindata').parent('td').children('img').remove();
				$('#proteindata').parent('td').append(upimg);
			}
			//	身体总水分
			if(msg && msg.water && msg.waterimg && msg.waterimg == 1 && types == 'we' ){
				$('#waterdata').parent('td').children('img').remove();
				$('#waterdata').parent('td').append(downimg);
			}else if(msg && msg.water && msg.waterimg && msg.waterimg == 2 && types == 'we'){
				$('#waterdata').parent('td').children('img').remove();
				$('#waterdata').parent('td').append(upimg);
			}
			//	肌肉量
			if(msg && msg.muscle && msg.muscleimg && msg.muscleimg == 1 && types == 'we' ){
				$('#muscledata').parent('td').children('img').remove();
				$('#muscledata').parent('td').append(downimg);
			}else if(msg && msg.muscle && msg.muscleimg && msg.muscleimg == 2 && types == 'we'){
				$('#muscledata').parent('td').children('img').remove();
				$('#muscledata').parent('td').append(upimg);
			}
			//	骨量
			if(msg && msg.mineralsalts && msg.mineralsaltsimg && msg.mineralsaltsimg == 1 && types == 'we' ){
				$('#mineralsalts').parent('td').children('img').remove();
				$('#mineralsalts').parent('td').append(downimg);
			}else if(msg && msg.mineralsalts && msg.mineralsaltsimg && msg.mineralsaltsimg == 2 && types == 'we'){
				$('#mineralsalts').parent('td').children('img').remove();
				$('#mineralsalts').parent('td').append(upimg);
			}
			//	基础代谢
			if(msg && msg.fat && msg.fatimg && msg.fatimg == 1 && types == 'we' ){
				$('#fatdata').parent('td').children('img').remove();
				$('#fatdata').parent('td').append(downimg);
			}else if(msg && msg.fat && msg.fatimg && msg.fatimg == 2 && types == 'we'){
				$('#fatdata').parent('td').children('img').remove();
				$('#fatdata').parent('td').append(upimg);
			}

			// 尿常规
			if(msg && types == 'ur' && msg.status == 1){
				$('#urinediv table.tabledata tr.urerrtr').hide();
				$('#urinediv table.tabledata tr.urdatatr').show();

				$('#urobilinogendata').html(msg.urobilinogen);
				$('#nitritedata').html(msg.nitrite);
				$('#whitecellsdata').html(msg.whitecells);
				$('#redcellsdata').html(msg.redcells);
				$('#urineproteindata').html(msg.urineprotein);
				$('#phdata').html(msg.ph);
				$('#urinedata').html(msg.urine);
				$('#urineketone').html(msg.urineketone);
				$('#bilidata').html(msg.bili);
				$('#sugardata').html(msg.sugar);
				$('#vcdata').html(msg.vc);
				
				$('#urtime').html('检测时间：' + msg.examtime);
			}else if( msg && types == 'ur' && msg.status != 1 ){
				$('#urinediv table.tabledata tr.urerrtr').show();
				$('#urinediv table.tabledata tr.urdatatr').hide();
				$('#urtime').html('');
			}
			//	酸碱度
			if(msg && msg.ph && msg.phimg && msg.phimg == 1 && types == 'ur' ){
				$('#phdata').parent('td').children('img').remove();
				$('#phdata').parent('td').append(downimg);
			}else if(msg && msg.ph && msg.phimg && msg.phimg == 2 && types == 'ur'){
				$('#fatdata').parent('td').children('img').remove();
				$('#fatdata').parent('td').append(upimg);
			}
			//	尿比重	
			if(msg && msg.urine && msg.urineimg && msg.urineimg == 1 && types == 'ur' ){
				$('#urinedata').parent('td').children('img').remove();
				$('#urinedata').parent('td').append(downimg);
			}else if(msg && msg.urine && msg.urineimg && msg.urineimg == 2 && types == 'ur'){
				$('#urinedata').parent('td').children('img').remove();
				$('#urinedata').parent('td').append(upimg);
			}
			
			
			// 心电
			if(msg && types == 'el' && msg.status == 1){
				
				$('#eldiv table.tabledata tr.elerrtr').hide();
				$('#eldiv table.tabledata tr.eldatatr').show();

				$('#elbpmdata').html(msg.bpm);
				
				$('#eltime').html('检测时间：' + msg.examtime);

			}else if( msg && types == 'el' && msg.status != 1  ){
				
				$('#eldiv table.tabledata tr.elerrtr').show();
				$('#eldiv table.tabledata tr.eldatatr').hide();

				$('#eltime').html('');
			}
			if(msg && msg.bpm && msg.showimg && msg.showimg == 1 && types == 'el' ){
				$('#elbpmdata').parent('td').children('img').remove();
				$('#elbpmdata').parent('td').append(downimg);
			}else if( msg && msg.bpm && msg.showimg && msg.showimg == 2 && types == 'el' ){
				$('#elbpmdata').parent('td').children('img').remove();
				$('#elbpmdata').parent('td').append(upimg);
			}
		}
	});
}
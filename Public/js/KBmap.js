(function () {
    require.config({
        paths: {
            echarts: '/Public/doc/example/www/js'
        },
        packages: [{
                name: 'BMap',
                location: '/Public/src',
                main: './main'
            }
        ]
    });

    require([
        'echarts',
        'BMap',
        'echarts/chart/map'
    ],
    function (echarts, BMapExtension) {
        $('#main').css({
            height:$('body').height(),
            width: $('body').width()
        });

        // 初始化地图
        var BMapExt = new BMapExtension($('#main')[0], BMap, echarts,{
            enableMapClick: false
        });
        var map = BMapExt.getMap();
        var container = BMapExt.getEchartsContainer();

         // 设置地图初始中心位置
       var startPoint = {
            x: 108.114129,
            y: 35.850339
        };

        var point = new BMap.Point(startPoint.x, startPoint.y);
        map.centerAndZoom(point,6);	//设置地图原始大小 
        map.enableScrollWheelZoom(true);

        // 地图自定义样式
        map.setMapStyle({
            styleJson: [
                  {
						//水域地图样式控制
                       "featureType": "water",
                       "elementType": "all",
                       "stylers": {
                            "color": "#044100"
                       }
                  },
                  {
						//陆地地图样式控制
                       "featureType": "land",
                       "elementType": "all",
                       "stylers": {
                            "color": "#004981"
                       }
                  },
 						//地图边界线样式控制
                 {
                       "featureType": "boundary",
                       "elementType": "geometry",
                       "stylers": {
                            "color": "#0049CC"
                       }
                  },
						//地图边界线样式控制
                 {
                     "featureType": "railway",
                       "elementType": "all",
                       "stylers": {
                            "visibility": "off"
                       }
                  },
                  {
                     "featureType": "highway",
                       "elementType": "geometry",
                       "stylers": {
                            "color": "#004981"
                       }
                  },
   						//公路线样式控制
                 {
                       "featureType": "highway",
                       "elementType": "geometry.fill",
                       "stylers": {
                            "color": "#005b96",
                            "lightness": 1
                       }
                  },
                  {
                       "featureType": "highway",
                       "elementType": "labels",
                       "stylers": {
                            "visibility": "off"
                       }
                  },
                  {
                       "featureType": "arterial",
                       "elementType": "geometry",
                       "stylers": {
                            "color": "#004981"
                       }
                  },
                  {
                       "featureType": "arterial",
                       "elementType": "geometry.fill",
                       "stylers": {
                            "color": "#00508b"
                       }
                  },
                  {
                       "featureType": "poi",
                       "elementType": "all",
                       "stylers": {
                            "visibility": "off"
                       }
                  },
                  {
                       "featureType": "green",
                       "elementType": "all",
                       "stylers": {
                            "color": "#056197",
                            "visibility": "off"
                       }
                  },
                  {
                       "featureType": "subway",
                       "elementType": "all",
                       "stylers": {
                            "visibility": "off"
                       }
                  },
                  {
                       "featureType": "manmade",
                       "elementType": "all",
                       "stylers": {
                            "visibility": "off"
                       }
                  },
                  {
                       "featureType": "local",
                       "elementType": "all",
                       "stylers": {
                            "visibility": "off"
                       }
                  },
                  {
                       "featureType": "arterial",
                       "elementType": "labels",
                       "stylers": {
                            "visibility": "off"
                       }
                  },
                  {
                       "featureType": "boundary",
                       "elementType": "geometry.fill",
                       "stylers": {
                            "color": "#029fd4"
                       }
                  },
                  {
                       "featureType": "building",
                       "elementType": "all",
                       "stylers": {
                            "color": "#1a5787"
                       }
                  },
                  {
                       "featureType": "label",
                       "elementType": "all",
                       "stylers": {
                            "visibility": "off"
                       }
                  }
            ]
        });

        option = {
            color: ['gold','aqua','lime'],
            title : {
                text: '健康设备及健康数据跟踪',
                subtext:'怡成网络医院-IT技术部',
                x:'right',
                y:'20',
                textStyle : {color: 'yellow',fontSize: 20,},
                subtextStyle : {color: 'yellow',fontSize: 16,}
            },
				//鼠标悬浮交互时的信息提示
            tooltip : {
                show: true,
                showContent: true,
                trigger: 'axis',
                formatter: function (v) {
                    return v[1].replace(':', ' > ');
                }
            },
				//可选城市的载入和样式控制
			//legend: {
                //orient: 'vertical',
                //x:'left',
                ////data:['北京', '上海', '广州'],
                //data:['北京'],
                //selectedMode: 'single',
                //selected:{
                    //'上海' : false,
                    //'广州' : false
                //},
                //textStyle : {
                    //color: '#fff'
                //}
            //},
				//工具栏的载入和样式控制
            toolbox: {
                show : false,
                orient : 'vertical',
                x: 'right',
                y: 'center',
                feature : {
                    mark : {show: true},
                    dataView : {show: true, readOnly: false},
                    restore : {show: true},
                    saveAsImage : {show: true}
                }
            },
				//数据区域控制的载入和样式控制
            dataRange: {
                min : 0,
                max : 120,
                range: {
                    start: 10,
                    end: 90
                },
                x: 'right',
				y: '15%',
                calculable : true,
                color: ['#ff3333', 'orange', 'yellow','lime','aqua'],
                textStyle:{
                    color:'#fff'
                }
            },
 				//标注的数据内容数组和样式控制
           series : [
                {
                    name:'北京',
                    type:'map',
                    mapType: 'none',
                    data:[],
                    geoCoord: coordData,

					//用来产生数据标线内容
                    markLine : {
                        smooth:false,	//是否有曲线功能
						smoothness:0.5,	//如果有的话，曲线弧度是多大？

                        effect : {
                            show: true,		//标线图形炫光特效是否开启
                            scaleSize: 3,	//放大倍数
							loop: true,		//是否循环动画
                            period: 10,		//运动周期，值越大越慢，默认为15
                            color: '#fff',	//炫光颜色
                            shadowBlur: 25,	//光影模糊度
							shadowColor:'#CCC'	//光影颜色,默认跟随color 
                        },
                        itemStyle : {
                            normal: {
                                borderWidth:1,
                                lineStyle: {
                                    type: 'solid',
                                    shadowBlur: 10
                                }
                            }
                        },
                        data : markLinedata
                    },
					//用来产生区域热点
                    markPoint : {
                        symbol:'emptyCircle',
                        symbolSize : function (v){
                            return 10 + v/10
                        },
                        effect : {
                            show: true,
                            shadowBlur : 0
                        },
                        itemStyle:{
                            normal:{
                                label:{show:false}
                            }
                        },
                        data : markPointdata
                    }

                }

            ]
        };

        var myChart = BMapExt.initECharts(container);
        window.onresize = myChart.onresize;
        BMapExt.setOption(option);
    }
);
})();
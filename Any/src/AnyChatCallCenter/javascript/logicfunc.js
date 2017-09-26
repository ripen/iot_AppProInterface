// JavaScript Document
// AnyChat for Web SDK

/********************************************
 *				ҵ���߼�����				*
 *******************************************/
 
var mDefaultServerAddr = "demo.anychat.cn";		// Ĭ�Ϸ�������ַ
var mDefaultServerPort = 8906;					// Ĭ�Ϸ������˿ں�
var mSelfUserId = -1; 							// �����û�ID
var mTargetUserId = 0;							// Ŀ���û�ID�������˶Է�������Ƶ��
var mRefreshVolumeTimer = -1; 					// ʵʱ������С��ʱ��
var mRefreshPluginTimer = -1;					// ������Ƿ�װ��ɶ�ʱ��

// ��־��¼���ͣ�����־��Ϣ������ʾ��ͬ����ɫ
var LOG_TYPE_NORMAL = 0;
var LOG_TYPE_API = 1;
var LOG_TYPE_EVENT = 2;
var LOG_TYPE_ERROR = 3;

// ֪ͨ���ͣ���������Ϣ������ʾ��ͬ����ɫ
var NOTIFY_TYPE_NORMAL = 0;
var NOTIFY_TYPE_SYSTEM = 1;

function LogicInit() {
    setTimeout(function () {
        if (navigator.plugins && navigator.plugins.length) {
            window.navigator.plugins.refresh(false);
        }
        //����Ƿ�װ�˲��	
        var NEED_ANYCHAT_APILEVEL = "0"; 						// ����ҵ�����Ҫ��AnyChat API Level
        var errorcode = BRAC_InitSDK(NEED_ANYCHAT_APILEVEL); 	// ��ʼ�����
        AddLog("BRAC_InitSDK(" + NEED_ANYCHAT_APILEVEL + ")=" + errorcode, LOG_TYPE_API);
        if (errorcode == GV_ERR_SUCCESS) {
            if (mRefreshPluginTimer != -1)
                clearInterval(mRefreshPluginTimer); 			// ��������װ��ⶨʱ��
            ShowLoginDiv(true);
            AddLog("AnyChat Plugin Version:" + BRAC_GetVersion(0), LOG_TYPE_NORMAL);
            AddLog("AnyChat SDK Version:" + BRAC_GetVersion(1), LOG_TYPE_NORMAL);
            AddLog("Build Time:" + BRAC_GetSDKOptionString(BRAC_SO_CORESDK_BUILDTIME), LOG_TYPE_NORMAL);

            GetID("prompt_div").style.display = "none"; 		// ���ز����װ��ʾ����
			BRAC_SetSDKOption(BRAC_SO_VIDEOBKIMAGE, "./images/anychatbk.jpg");
            // ��ʼ������Ԫ��
            InitInterfaceUI();
            BRAC_SetSDKOption(BRAC_SO_CORESDK_SCREENCAMERACTRL, 1);
        } else { 						// û�а�װ��������ǲ���汾̫�ɣ���ʾ������ؽ���
            GetID("prompt_div").style.display = "block";
            SetDivTop("prompt_div", 300);
            if (errorcode == GV_ERR_PLUGINNOINSTALL)
                GetID("prompt_div_line1").innerHTML = "�״ν�����Ҫ��װ������������ذ�ť���а�װ��";
            else if (errorcode == GV_ERR_PLUGINOLDVERSION)
                GetID("prompt_div_line1").innerHTML = "��⵽��ǰ����İ汾���ͣ������ذ�װ���°汾��";

            if (mRefreshPluginTimer == -1) {
                mRefreshPluginTimer = setInterval(function () {
                    LogicInit();
                }, 1000);
            }
        }
    }, 500);
}
$(function () {
    var ua = window.navigator.userAgent.toLowerCase();
    var info = {
        edge: /edge/.test(ua)
    };
    if(info.edge) {
        $("#loginDiv").hide();
        $("#prompt_div").hide();
        $('.showBox').hide();
        $('body').append('<iframe src="./html/needie.html?reason=3" style="width: 100%; height: 800px;" scrolling = "no"  frameborder="0"></iframe>');
        $('body').css('backgroundColor', '#fff');
    } else {
        LogicInit(); // ��ʼ��sdk
    }
});

//����AnyChat��������Ҫ���յ���¼�ɹ��ص�֮�����
function ConfigAnyChatParameter(){
	
}

// ��ʼ������Ԫ��
function InitInterfaceUI() {
    //���ð�ť
    GetID("setting").onclick = function () {
        if (GetID("setting_div").style.display == "block")
            GetID("setting_div").style.display = "none";
        else
            GetID("setting_div").style.display = "block";
    }
    
    //��¼��ť
    GetID("loginbtn").onclick = function () {
    	var signTimestamp = 0;
    	var signStr = "";
    	
		if(GetID("password").value == "�����Ϊ��")
			GetID("password").value = "";
        if (GetID("username").value != "") {
            DisplayLoadingDiv(true);
            setLoginInfo();
            
			/* AnyChat����������������ķ�������Ҳ��������AnyChat��Ƶ��ƽ̨��
             * ������������������ĵ�ַΪ����ķ�����IP��ַ���������˿ڣ�
             * ����AnyChat��Ƶ��ƽ̨�ķ�������ַΪ��cloud.anychat.cn���˿�Ϊ��8906
             */
            var errorcode = BRAC_Connect(GetID("ServerAddr").value, parseInt(GetID("ServerPort").value)); //���ӷ�����
            AddLog("BRAC_Connect(" + GetID("ServerAddr").value + "," + GetID("ServerPort").value + ")=" + errorcode, LOG_TYPE_API);
			
			/*
             * AnyChat֧�ֶ����û������֤��ʽ����������ȫ��ǩ����¼��
             * ������ο���http://bbs.anychat.cn/forum.php?mod=viewthread&tid=2211&highlight=%C7%A9%C3%FB
             */
			errorcode = BRAC_Login(GetID("username").value, GetID("password").value, 0);
        	AddLog("BRAC_Login(" + GetID("username").value + ")=" + errorcode, LOG_TYPE_API);            	
                    
            // �������ý���
            GetID("setting_div").style.display = "none";
        }
        else {
            GetID("a_error_user").style.color = "red";
            AddLog("The user name can not be empty!", LOG_TYPE_ERROR);
            GetID("username").focus();
        }
    }
    //�˳�ϵͳ
    GetID("ExitSystemBtn").onclick = function () {
        var errorcode = BRAC_Logout();
        AddLog("BRAC_Logout()=" + errorcode, LOG_TYPE_API);
        ShowHallDiv(false);
        ShowLoginDiv(true);
    }
    //���ز����ť��껮�뻮��ʱ��
    GetID("prompt_div_btn_load").onmouseover = function () {
        GetID("prompt_div_btn_load").style.backgroundColor = "#ffc200";
    }
    GetID("prompt_div_btn_load").onmouseout = function () {
        GetID("prompt_div_btn_load").style.backgroundColor = "#ff8100";
    }
    //���ز������رհ�ť
    GetID("prompt_div_headline2").onclick = function () {
        document.URL = location.href;
    }
    // ����Ƶ���־������
    GetID("LOG_DIV_BODY").onmousemove = function () {
        GetID("LOG_DIV_BODY").style.zIndex = 100;
        GetID("LOG_DIV_CONTENT").style.backgroundColor = "#FAFADD";
        GetID("LOG_DIV_CONTENT").style.border = "1px solid black";
    }
    // ������־�������ƿ�
    GetID("LOG_DIV_BODY").onmouseout = function () {
        GetID("LOG_DIV_BODY").style.zIndex = -1;
        GetID("LOG_DIV_CONTENT").style.backgroundColor = "#C4CEDD";
        GetID("LOG_DIV_CONTENT").style.border = "";
    }
	//�ر����ý���
	Getdmo("advanceset_div_close").onclick = function () {
		if (Getdmo("advanceset_div").style.display == "block")
			Getdmo("advanceset_div").style.display = "none";
		else {
			Getdmo("advanceset_div").style.display = "block"; // ��ʾ�߼����ý���
            // ��ʼ���߼����ý���
			InitAdvanced();
        }
}

    getLoginInfo();

}

function PasswordFocus(obj,color){
	// �ж��ı����е������Ƿ���Ĭ������
	if(obj.value=="�����Ϊ��")
		obj.value="";
	obj.type="password";
	// �����ı����ȡ����ʱ�򱳾���ɫ�任
	obj.style.backgroundColor=color;
}

// ������뿪ʱ��ı��ı��򱳾���ɫ
function myblur(obj,color){
	obj.style.background=color;
}

//����߶Ȳ����ý���λ��
function SetDivTop(id, TheHeight) {
    var BodyHeight = document.documentElement.clientHeight; //���������ɼ�����߶�
	if (TheHeight < BodyHeight) {//div�߶�С�ڿɼ�����߶�
	    GetID("margintop").style.height = (BodyHeight - TheHeight) / 4 + "px";
	    GetID(id).style.marginTop = "0px";
    }
}

//ϵͳ��Ϣ�����������
function DisplayScroll(id) {
    var offset = GetID(id); //��Ҫ����div
	if (offset.offsetHeight < offset.scrollHeight) {//div�ɼ��߶�С��div�������߶�
		GetID(id).style.overflowY = "scroll";//��ʾ������
		GetID(id).scrollTop = GetID(id).scrollHeight;//�������Զ��������ײ�
	}
	else
		GetID(id).style.overflowY = "hidden";//���ع�����
}

// ��ʾ��¼����
function ShowLoginDiv(bShow) {
	if(bShow) {
		GetID("login_div").style.display = "block"; 	//��ʾ��¼����
		GetID("username").focus();
		SetDivTop("login_div", 195); 					//��¼���洹ֱ����
		GetID("LOG_DIV_BODY").style.display = "block"; 	//��ʾϵͳ��Ϣ��
		var serverIP = getCookie("ServerAddr");
		var serverPort = getCookie("ServerPort");
		GetID("ServerAddr").value = (typeof serverIP != "undefined" && serverIP != null) ? serverIP : mDefaultServerAddr;
		GetID("ServerPort").value = (typeof serverPort != "undefined" && serverPort != null) ? serverPort : mDefaultServerPort;

	} else {
	
	}
}

// ��ʾ��������
function ShowHallDiv(bShow) {
    if (bShow) {
		GetID("login_div").style.display = "none"; 		//���ص�¼����
		GetID("hall_div").style.display = "block"; 		//��ʾ��������
		SetDivTop("hall_div", 400); 					//�������洹ֱ����
	} else {
		GetID("hall_div").style.display = "none";
	}
}

function GetID(id) {
	if (document.getElementById) {
		return document.getElementById(id);
	} else if (window[id]) {
		return window[id];
	}
	return null;
}

//div��ť��껮�뻮��Ч��
function Mouseover(id) {
	GetID(id).style.backgroundColor = "#FFFFCC";
}
//div��ť��껮�뻮��Ч��
function Mouseout(id) {
	GetID(id).style.backgroundColor = "#E6E6E6";
}
//��ȡ��ǰʱ��  (00:00:00)
function GetTheTime() {
	var TheTime = new Date();
	return TheTime.toLocaleTimeString();
}

// �����־����ʾ�����ݲ�ͬ��������ʾ��ͬ����ɫ
function AddLog(message, type) {
    if (type == LOG_TYPE_API) {			// API������־����ɫ
        message = message.fontcolor("Green");
	} else if(type == LOG_TYPE_EVENT) {	// �ص��¼���־����ɫ
        message = message.fontcolor("#CC6600");
	} else if(type == LOG_TYPE_ERROR) {	// ������־����ɫ
        message = message.fontcolor("#FF0000");
	} else {							// ��ͨ��־����ɫ
        message = message.fontcolor("#333333");
	}
    GetID("LOG_DIV_CONTENT").innerHTML += message + "&nbsp" + GetTheTime().fontcolor("#333333") + "<br />";
	DisplayScroll("LOG_DIV_CONTENT");
}

// ��ʾ�ȴ�����������ʾ�û��������ڽ�����
function DisplayLoadingDiv(bShow) {
    if (bShow) {
        GetID("LOADING_DIV").style.display = "block";
        GetID("LOADING_GREY_DIV").style.display = "block";
        var TheHeight = document.documentElement.clientHeight;
        var TheWidth = document.body.offsetWidth;
        GetID("LOADING_DIV").style.marginTop = (TheHeight - 50) / 2 + "px";
        GetID("LOADING_DIV").style.marginLeft = (TheWidth - 130) / 2 + "px";
    }
    else {
        GetID("LOADING_DIV").style.display = "none";
        GetID("LOADING_GREY_DIV").style.display = "none";
    }
}
function Getdmo(element) {
    if (document.getElementById) {
        return document.getElementById(element);
    } else if (window[element]) {
        return window[element];
    }
    return null;
}
//��ʼ�����н���λ��
function initialize() {
    var mBrowserWidth = document.body.offsetWidth; // ��ҳ�ɼ������
    var mBrowserHeight = document.documentElement.clientHeight; //  ��ҳ�ɼ������
    CenterPosition(mBrowserWidth, mBrowserHeight, "Initiative_Call_Div", 300, 170); //�������в㴹ֱˮƽ����
    CenterPosition(mBrowserWidth, mBrowserHeight, "BeCalls_Div", 300, 170); //�����в㴹ֱˮƽ����
	CenterPosition(mBrowserWidth, mBrowserHeight, "SessionPrompt_Div", 300, 170); //�Ự��Ϣ�㴹ֱˮƽ����
	CenterPosition(mBrowserWidth, mBrowserHeight, "advanceset_div", 1500, -350); //�Ự��Ϣ�㴹ֱˮƽ����
    CenterPosition(mBrowserWidth, mBrowserHeight, "hall_div", 770, 650); //�����Ựѯ�ʲ㴹ֱˮƽ����
    if (mBrowserHeight < 650) $("#hall_div").css("top", "0px");
}

// DIV�㴹ֱ���к�ˮƽ����  ���������������  �������������߶�  DIV��ID DIV��ĸ߶� DIV��Ŀ��
function CenterPosition(VWidth, VHeight, DivID, DivWidth, DivHeight) {
    $("#" + DivID).css("left", (VWidth - DivWidth) / 2 + "px"); // ����X����
    $("#" + DivID).css("top", (VHeight - DivHeight) / 2 + "px"); // ����Y����
}

// ��ʼ��Ƶ�Ự��һ���� ������Ƶ
function showVideoSessionScreen() {
    //��ҳ�ɼ������
    var VWidth = document.body.offsetWidth; // (�������ߵĿ�) 
    //��ҳ�ɼ�����ߣ�
    var VHeight = document.body.offsetHeight; //  (�������ߵĸ�)
    CenterPosition(VWidth, 730, "VideoShowDiv", 1020, 450);
    createVideoContainer(); // ��̬���ɿ�����Ƶ����
    setVideoShow("videoshow2", "videoshow1");
	mRefreshVolumeTimer = setInterval(updateCurrentVolume, 200); // ��ȡ��ʾ��������
}
// ������ʾ��Ƶλ��
function setVideoShow(firVideo, secVideo) {
    BRAC_SetVideoPos(mSelfUserId, Getdmo(firVideo), "ANYCHAT_VIDEO_LOCAL");
    BRAC_SetVideoPos(mTargetUserId, Getdmo(secVideo), "ANYCHAT_VIDEO_REMOTE");
}

// ˫��������
function updateCurrentVolume() {
    Getdmo("Mine_Volume").style.width = Getdmo("videoshow2").offsetWidth / 100 * BRAC_QueryUserStateInt(mSelfUserId,                   	BRAC_USERSTATE_SPEAKVOLUME) + "px";
    Getdmo("Target_Volume").style.width = Getdmo("videoshow1").offsetWidth / 100 * BRAC_QueryUserStateInt(mTargetUserId,        BRAC_USERSTATE_SPEAKVOLUME) + "px";
}

//��̬���ɿ�����Ƶ����
function createVideoContainer() {
    Getdmo("VideoShowDiv").innerHTML = "";
    // �����Ƶ��
    var upper_video1 = document.createElement("div");
    upper_video1.id = "videoshow1";
    upper_video1.className = "videoshow";
    Getdmo("VideoShowDiv").appendChild(upper_video1);
    // �ұ���Ƶ��
    var upper_video2 = document.createElement("div");
    upper_video2.id = "videoshow2";
    upper_video2.className = "videoshow";
    upper_video2.style.marginLeft = "6px";
    Getdmo("VideoShowDiv").appendChild(upper_video2);
	/*//�����������
	var volume_other_Holder = document.createElement("div");
    volume_other_Holder.id = "Target_Volume_Holder";
	Getdmo("VideoShowDiv").appendChild(volume_other_Holder);
	
	//�ұ���������
	var volume_self_Holder = document.createElement("div");
    volume_self_Holder.id = "Mine_Volume_Holder";
    Getdmo("VideoShowDiv").appendChild(volume_self_Holder);
	
		//���������
	var volume_other = document.createElement("div");
    volume_other.id = "Target_Volume";
	Getdmo("Target_Volume_Holder").appendChild(volume_other);
	
	//�ұ�������
	var volume_self = document.createElement("div");
    volume_self.id = "Mine_Volume";
    Getdmo("Mine_Volume_Holder").appendChild(volume_self);*/
	  
    // ��ʾ��������
    var upper_othername = document.createElement("div");
    upper_othername.className = "ShowName";
    upper_othername.innerHTML = BRAC_GetUserInfo(mTargetUserId,USERINFO_NAME);
    Getdmo("VideoShowDiv").appendChild(upper_othername);
    // ��ʾ�Է�����
    var upper_myname = document.createElement("div");
    upper_myname.className = "ShowName";
    upper_myname.innerHTML = BRAC_GetUserName(mSelfUserId);
    Getdmo("VideoShowDiv").appendChild(upper_myname);
    // �Ҷ� ��ť
    var under_finish = document.createElement("div");
    under_finish.id = "finishprivate";
    under_finish.onmouseout = function () {
        $("#finishprivate").css("background", "url('./images/dialog/btnfalse_move.png')");
    }
    under_finish.onmouseover = function () {
        $("#finishprivate").css("background", "url('./images/dialog/btnfalse_over.png')");
    }
    under_finish.onclick = function () {
		BRAC_VideoCallControl(BRAC_VIDEOCALL_EVENT_FINISH,mTargetUserId,0,0,0,""); 	// �Ҷ�
    }
    Getdmo("VideoShowDiv").appendChild(under_finish);
    // �߼����� ��ť
    var video_paramers_config = document.createElement("div");
    video_paramers_config.id = "video_paramers_config";
    video_paramers_config.className = "Buttons";
    //�߼�����
   video_paramers_config.onclick = function () {
        if (Getdmo("advanceset_div").style.display == "block")
            Getdmo("advanceset_div").style.display = "none";
        else {
            Getdmo("advanceset_div").style.display = "block"; // ��ʾ�߼����ý���
            // ��ʼ���߼����ý���
            InitAdvanced();
        }
    }
    video_paramers_config.innerHTML = "����";
    Getdmo("VideoShowDiv").appendChild(video_paramers_config);
}

//���õ�¼��Ϣ�������û�����������IP���������˿ڡ�Ӧ��ID
function setLoginInfo() {
    setCookie('username', GetID("username").value, 30);
    setCookie('ServerAddr', GetID("ServerAddr").value, 30);
    setCookie('ServerPort', GetID("ServerPort").value, 30);
 
}

//��ȡ��¼��Ϣ
function getLoginInfo() {
    GetID("username").value = getCookie("username");
    var serverIP = getCookie("ServerAddr");
	GetID("ServerAddr").value = (serverIP != "") ? serverIP : mDefaultServerAddr;        
    var serverPort = getCookie("ServerPort");
	GetID("ServerPort").value = (serverPort != "") ? serverPort : mDefaultServerPort;        
}

//��ȡcookie���cookieֵ
function getCookie(c_name) {
    if (document.cookie.length > 0) {
        c_start = document.cookie.indexOf(c_name + "=");
        if (c_start != -1) {
            c_start = c_start + c_name.length + 1;
            c_end = document.cookie.indexOf(";", c_start);
            if (c_end == -1) c_end = document.cookie.length;
            return document.cookie.substring(c_start, c_end);
        }
    }
    return "";
}

//����cookie
function setCookie(c_name, value, expiredays) {
    var exdate = new Date();
    exdate.setDate(exdate.getDate() + expiredays);
    document.cookie = c_name + "=" + value + ((expiredays == null) ? "" : ";expires=" + exdate.toGMTString());
}


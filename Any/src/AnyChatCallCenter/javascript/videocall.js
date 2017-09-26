// JavaScript Document
var USER_ONLINE_STATUS=1;						// �û�����
var USER_OFFLINE_STATUS=0;						// �û�����

var USERINFO_NAME=1;							// �û��ǳ���Ϣ
var USERINFO_IP=2;								// �û�IP��ַ��Ϣ


//�������ߺ���ͼ��
function CreateUserImage(type) {
    Getdmo("UserListContent").innerHTML = "";
    var OnLineUserList = BRAC_GetUserFriends(); // ��ȡ���к���
    if (type == "whole") { // ���������û�
        DisplayOnLineUser(mSelfUserId); // �ڵ�һ��λ�ô����Լ���ͼ��
        for (var i = 0; i < OnLineUserList.length; i++) {
            if (OnLineUserList[i] != mSelfUserId && BRAC_GetFriendStatus(OnLineUserList[i]) != 0)	// ֻ��ʾ�����û�
                DisplayOnLineUser(OnLineUserList[i]);
        }
    }
    else { // ָ���û��������û�
        for (var i = 0; i < OnLineUserList.length; i++) {
            var UserGroupID = GetUserGroupIdByUserId(OnLineUserList[i]);
            if (UserGroupID == mCurrentGroupNum && BRAC_GetFriendStatus(OnLineUserList[i]) != 0)	// ֻ��ʾ�����û�
                DisplayOnLineUser(OnLineUserList[i]);
        }
    }
    //StartScroll("UserListScroll", "UserListSlider", "UserListContent", "UserListBaseLine");
}
//��ʾ�����û�
function DisplayOnLineUser(userid) {
    var UserName = BRAC_GetUserInfo(userid,USERINFO_NAME); // �û�����
	var UserIp=BRAC_GetUserInfo(userid,USERINFO_IP); // �û�IP��ַ;
    var main_div = document.createElement("div");
    main_div.id = "UserID_" + userid;
    main_div.className = "OnLineUser_Div";

    var left_div = document.createElement("div");
    left_div.className = "UserImg_Holder";
    main_div.appendChild(left_div);

    var left_img = document.createElement("img");
    left_img.id = userid;
	var img_value=Math.abs(userid)%10;
    left_img.src = "./images/head/"+img_value+".gif";
    left_img.className = "UserImg";
    left_div.appendChild(left_img);

    var right_div = document.createElement("div");
    right_div.className = "UserInfo_Holder";
    main_div.appendChild(right_div);

    var right_name= document.createElement("div");
    right_name.className = "UserInfo";
    right_name.innerHTML = UserName.length>8?UserName.substring(0,8)+"...":UserName;
    right_name.title = UserName;
    right_div.appendChild(right_name);
	
	var right_ip = document.createElement("div");
    right_ip.className = "UserInfo";
    right_ip.innerHTML = UserIp;
    right_div.appendChild(right_ip);
	
	var right_userId= document.createElement("div");
    right_userId.className = "UserInfo";
    right_userId.innerHTML = userid;
    right_div.appendChild(right_userId);
    
	//���ز����ť��껮�뻮��ʱ��
    main_div.onmouseover =	function () {	main_div.style.backgroundColor = "#8FBC8B";	}
    main_div.onmouseout =	function () {	main_div.style.backgroundColor = "#FAFADD";	}
	main_div.onclick=function () { VideoCallRequest(userid); }
    Getdmo("UserListContent").appendChild(main_div);
}

//��������û�
function removeOfflineUser(dwUserId)
{
	Getdmo("UserListContent").removeChild(Getdmo("UserID_" + dwUserId));
}

//�Ự��ʾ��Ϣ
function ForSession(message) {
    var mBrowserWidth = document.body.offsetWidth; 				// ��ҳ�ɼ������
    var mBrowserHeight = document.documentElement.clientHeight; //  ��ҳ�ɼ������
    CenterPosition(mBrowserWidth, mBrowserHeight, "SessionPrompt_Div", 300, 170); //�������в㴹ֱˮƽ����
    Getdmo("Shade_Div").style.display = "none";
    Getdmo("Initiative_Call_Div").style.display = "none";		// �������ں��в�
	Getdmo("BeCalls_Div").style.display = "none";				// ���ر����в�
	Getdmo("advanceset_div").style.display = "none"; 			// �������ò�
    Getdmo("VideoShowDiv").style.display = "none"; 				// �������ò�
	mTargetUserId=0;
    Getdmo("SessionPrompt_Div").style.color = "White";
    Getdmo("SessionPrompt_Div").innerHTML = message;
    $("#SessionPrompt_Div").fadeTo("slow", 1);
	setTimeout("$('#SessionPrompt_Div').fadeTo('slow', 0 )", 2000);
    //$("#SessionPrompt_Div").css("left", (document.body.offsetWidth - 600) / 2 + "px");
}

//ȡ����������
function CancelCall() {
    $("#Shade_Div").hide();
    $("#Initiative_Call_Div").hide();
	BRAC_VideoCallControl(BRAC_VIDEOCALL_EVENT_REPLY,mTargetUserId,GV_ERR_SESSION_QUIT,0,0,"");  
    ForSession("ȡ������...");
}
//�����û�˫��
function VideoCallRequest(ID) {
    if (mSelfUserId == ID)
	    ForSession("���ܺ����Լ�...");
    else {
        mTargetUserId = ID;
		BRAC_VideoCallControl(BRAC_VIDEOCALL_EVENT_REQUEST,mTargetUserId,0,0,0,"");  // ��ָ�����û����ͻỰ����
    }
}
//ͬ��Ự
function AcceptRequestBtnClick() {
	BRAC_VideoCallControl(BRAC_VIDEOCALL_EVENT_REPLY,mTargetUserId,0,0,0,"");  
    $("#BeCalls_Div").hide();
}
//�ܾ��Ự
function RejectRequestBtnClick() {
	BRAC_VideoCallControl(BRAC_VIDEOCALL_EVENT_REPLY,mTargetUserId,GV_ERR_SESSION_REFUSE,0,0,"");  
    $("#Shade_Div").hide();
    $("#BeCalls_Div").hide();
    ForSession("�ܾ��Է�����...");
}
//�յ���Ƶ��������
function onVideoCallControlRequest(dwUserId, dwErrorCode, dwFlags, dwParam, szUserStr)
{
	 var UserName = BRAC_GetUserInfo(dwUserId,USERINFO_NAME); // �û�����
	 $("#Shade_Div").show();
	 $("#BeCalls_Div").show();
	 $("#BeCalls_Div_Content").html("�յ��û�  " +UserName + "  �Ự����<br />      �Ƿ�ͬ��?");
	 mTargetUserId = dwUserId;
}

//��Ƶ��������ظ�
function onVideoCallControlReply(dwUserId, dwErrorCode, dwFlags, dwParam, szUserStr)
{
	switch(dwErrorCode)
	{
		case GV_ERR_SUCCESS:
		    onSendVideoCallRequestSucess(dwUserId);
			break;
		case GV_ERR_SESSION_QUIT:
			ForSession("Դ�û����������Ự");
			break;
		case GV_ERR_SESSION_OFFLINE:
		    ForSession("Ŀ���û�������");
			break;
		case GV_ERR_SESSION_BUSY:
			ForSession("Ŀ���û�æ");
			break; 
		case GV_ERR_SESSION_REFUSE:
		 	ForSession("Ŀ���û��ܾ��Ự");
			break; 
		case GV_ERR_SESSION_TIMEOUT:
		 	ForSession("�Ự����ʱ");
			break; 
		case GV_ERR_SESSION_DISCONNECT:
			ForSession("�������");
			break; 
		default:
			break;
	}
}

//ͨ����ʼ
function onVideoCallControlStart(dwUserId, dwErrorCode, dwFlags, dwParam, szUserStr)
{
	BRAC_EnterRoom(dwParam, "", 0);
	Getdmo("Initiative_Call_Div").style.display = "none";
	Getdmo("hall_div").style.display = "none";
	$("#VideoShowDiv").show();
}

//��Ƶͨ������
function onVideoCallControlFinish(dwUserId, dwErrorCode, dwFlags, dwParam, szUserStr)
{
	BRAC_LeaveRoom(-1);
    ForSession("�Ự����..."); // ��ʾ��
	ShowHallDiv(true); // ��ʾ����
	if(mRefreshVolumeTimer != -1)
		clearInterval(mRefreshVolumeTimer); // ���ʵʱ������ʾ��ʱ��
}

//��Ƶ���������ͳɹ�
function onSendVideoCallRequestSucess(mTargetUserId)
{
	var UserName = BRAC_GetUserInfo(mTargetUserId,USERINFO_NAME); // �û�����
    UserName = UserName.fontcolor("Red");
    Getdmo("Initiative_Call_Div_Content").innerHTML = "���ں���" + UserName + "�û����ȴ��Է���Ӧ<br /><img src='./images/Others/LoadImg.gif'    style='width: 145px;height:30px;' />";
    Getdmo("Shade_Div").style.display = "block";
    Getdmo("Initiative_Call_Div").style.display = "block";
}



function BusyDivOut() {
    $("#SessionPrompt_Div").css("top", "500%");
}




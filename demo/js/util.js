/**
 * http://test.api.yicheng120.com/kbox/health/register
 * @param {Object} postURL 请求地址
 * @param {Object} para    请求数据
 * @param {Object} successfulcb  成功回调   
 * @param {Object} errorcb       失败回调
 */
function ajaxPost(postURL, para, successfulcb,errorcb) {  
	$.ajax({
		type: "POST",
		async: false,
		url:postURL, 
		data:para,
		dataType:"html",
		success:successfulcb,
		error: errorcb
	});  
	
}
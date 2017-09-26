<?php
if (!empty($_REQUEST))
{
	foreach($_REQUEST as $key => $value)
	{ 
		// 输出至页面 ( 用于调试 )
		echo ($value);
		// 保存接收的推送数据
        $file = "receiveData.txt";
		$fhandle = fopen($file, "a+");
		fwrite($fhandle,$value);
		fwrite($fhandle,"\r\n");
		fclose($fhandle);
	}
}
else{
	echo "request is empty!";
}
?>
<?php
if (!empty($_REQUEST))
{
	foreach($_REQUEST as $key => $value)
	{ 
		// �����ҳ�� ( ���ڵ��� )
		echo ($value);
		// ������յ���������
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
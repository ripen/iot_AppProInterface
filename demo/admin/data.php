<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/12/2
 * Time: 13:22
 */
$dbh = mysqli_connect('192.168.137.10', 'platforms', 'platform!)@(#*','yc_platform' );
//$dbh = mysqli_connect('localhost', 'root', 'admin','yc_platform' );

if ($dbh) {
    $return_data = $dbh->query('select * from pf_equipment_log where sign = "index" order by id desc limit 20');

    $arrS=array();
    if ($return_data->num_rows > 0) {
        while($row =$return_data->fetch_array() ){ 
			
            $arr	=	array();
            array_push($arr,$row[0],$row[1],$row[2]);
            $arrS[]	=	$arr;
        }
    }
	
	

    echo json_encode($arrS);
}


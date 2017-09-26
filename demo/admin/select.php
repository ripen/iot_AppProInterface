<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/12/2
 * Time: 13:22
 */
$dbh = mysqli_connect('192.168.137.10', 'platforms', 'platform!)@(#*','yc_platform' );

if ($dbh) {
    $return_data = $dbh->query('select * from yicheng order by id desc limit 100');

    $arrS=array();
    if ($return_data->num_rows > 0) {
        while($row =$return_data->fetch_array() ){                        //循环输出结果集中的记录
            $arr=array();
            array_push($arr,$row[0],$row[1],$row[2],$row[3],$row[4],$row[5],$row[6],$row[7],$row[8],$row[9],$row[10]);
            array_push($arrS,$arr);
        }
    }

	if ( $arrS ) {
		//	根据卡号获取用户信息
		$sql	=	array();
		foreach($arrS AS $key => $val){
			$sql[]	=	$val['1'];
		}

		$sql	=	'SELECT userid,cardnum FROM pf_drug_card_user where cardnum in("'.implode('","',$sql).'")';

		$query	=	$dbh->query($sql);

		if ($query->num_rows > 0) {
			while($row = $query->fetch_array() ){
				$sql	=	'SELECT username,nickname,mobile FROM pf_member where userid ='.$row['userid'];
				$uquery	=	$dbh->query($sql);
				if ($uquery->num_rows > 0) {
					while($urow = $uquery->fetch_array() ){
						$uinfo[$row['cardnum']]	=	$urow['nickname']? $urow['nickname'] : $urow['username'];
					}
				}
			}
		}
		
		foreach($arrS AS $key => $val){
			$arrS[$key]['name']	=	isset($uinfo[$val[1]]) ? $uinfo[$val[1]] : '';
		}

	}

    print_r(json_encode($arrS));
}


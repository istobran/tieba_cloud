<?php
session_start();
require('api.php');
require('config.inc.php');
require('class.passport.php');
require('func.sign.php');

if(isset($_GET['bindback']))
{
	if($_GET['bindback']=='error'){
		die('绑定错误,请检查用户名密码或者验证码是否正确!');
	}else{
		$bp = new baidu_passport($_GET['bindback']);
		$result = $bp->get_passport_info();
		$con = mysql_connect(DB_IP,DB_USERNAME,DB_PASSWORD);
		if(!$con)
		{
			die('account bind error.');
		}else{
			if(mysql_select_db(DB_NAME))
			{
				$list = get_list($_GET['bindback']);
				mysql_query('set names utf8');
				mysql_query('UPDATE tc_baiduinfo SET baidu_id="'.$result['baiduid'].'", avastar="'.$result['avatar'].'" WHERE tc_id="'.$_SESSION['u'].'"');
				mysql_query('UPDATE tc_user SET cookie= "'.base64_encode($_GET['bindback']).'" WHERE username="'.$_SESSION['u'].'"');
				for ($i=0; $i < count($list); $i++) { 
					for ($k=0; $k < count($list[$i]['url']); $k++) { 
						mysql_query('INSERT INTO tc_tieba(username,fid,url) VALUES("'.$_SESSION['u'].'","'.$list[$i]['balvid'][$k].'","'.$list[$i]['url'][$k].'")');
					}
				}
				echo '<p>account bind success!</p><script type="text/javascript">
				setTimeout(window.location.href="../index.php",3000); </script>';
			}
		}
	}
}
?>
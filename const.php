<?php
define ('DEFAUT_TITLE',"调查问卷管理后台");
define('SERVERROOT',dirname(__FILE__) );
$httpsflag=isset($_SERVER['HTTPS'])?$_SERVER['HTTPS']:"";
if(empty($httpsflag)){
	$urlprefix="http://";
}else{
	$urlprefix="http://";
}
define('SITE',$urlprefix.$_SERVER['HTTP_HOST'].substr($_SERVER['PHP_SELF'],0,strrpos ($_SERVER['PHP_SELF'],'/')+1));
//define('SITEHOST','http://www.planinbook.com:8021');
define('SITEHOST','http://192.168.11.100');

?>
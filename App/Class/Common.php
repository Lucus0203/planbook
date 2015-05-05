<?php
if (! function_exists ( 'mb_substr' )) {
	function mb_substr($str, $start, $len = '', $encoding = "UTF-8") {
		$limit = strlen ( $str );
		
		for($s = 0; $start > 0; -- $start) { // found the real start
			if ($s >= $limit)
				break;
			
			if ($str [$s] <= "\x7F")
				++ $s;
			else {
				++ $s; // skip length
				

				while ( $str [$s] >= "\x80" && $str [$s] <= "\xBF" )
					++ $s;
			}
		}
		
		if ($len == '')
			return substr ( $str, $s );
		else
			for($e = $s; $len > 0; -- $len) { //found the real end
				if ($e >= $limit)
					break;
				
				if ($str [$e] <= "\x7F")
					++ $e;
				else {
					++ $e; //skip length
					

					while ( $str [$e] >= "\x80" && $str [$e] <= "\xBF" && $e < $limit )
						++ $e;
				}
			}
		
		return substr ( $str, $s, $e - $s );
	}
}
/**

 * Project: 公共函数

 * Author: libin

 * Date: 2008年10月13日

 * File: common.php

 * Version: 1.0

 */

class Class_Common extends FLEA_Controller_Action {
	var $_admin;
	
	/**

	 * Enter description here...

	 *

	 */
	
	function __construct() {
		$this->_admin = & get_singleton ( "Model_Admin" );
	}
	
	/**

	 * Open, parse, and return the file content.

	 * @author libin 2008-09-13

	 * @param string string the php file name

	 *

	 * @return string

	 */
	
	function include_fetch($file, $var = array()) {
		extract ( $var ); // Extract the vars to local namespace
		

		ob_start (); // Start output buffering
		

		include ($file); // Include the file
		

		$contents = ob_get_contents (); // Get the contents of the buffer
		

		ob_end_clean (); // End buffering and discard
		

		return $contents; // Return the contents
	

	}
	
	/**

	 *调用包含函数

	 * @author libin 2008-09-13

	 * @param unknown_type $function

	 * @param unknown_type $params

	 * @return unknown

	 */
	
	function include_fetch_function($function, $params = array()) {
		
		ob_start ();
		
		call_user_func_array ( $function, $params );
		
		$contents = ob_get_contents ();
		
		ob_end_clean ();
		
		return $contents;
	
	}
	
	/**

	 * 显示模板页面

	 * @author libin 2008-09-13

	 * @param unknown_type $parm

	 */
	
	function show($parm = array()) {
		$smarty = & $this->_getView ();
		foreach ( $parm as $key => $value ) {
			$smarty->assign ( $key, $value );
		}
		if (@$parm ['title'] == "") {
			$parm ['title'] = DEFAUT_TITLE;
		
		}
		
		$smarty->register_modifier ( "substr", array ("Class_Common", "m_substr" ) );
		$smarty->register_modifier ( "formatdate", array ("Class_Common", "formatdate" ) );
		$smarty->register_modifier ( "formatmoney", array ("Class_Common", "formatmoney" ) );
		
		if (isset ( $_SESSION ['loginuserid'] ) && $_SESSION ['loginuserid'] != "") {
			$loginuserinfo = $this->_admin->findByField ( "id", $_SESSION ['loginuserid'] );
			$smarty->assign ( 'loginuserinfo', @$loginuserinfo );
			$smarty->assign ( 'loginuserid', @$_SESSION ['loginuserid'] );
		} else {
			$loginuserinfo = array ();
		}
		$smarty->display ( 'main.tpl' );
	}
	
	function article($parm = array()){
		$smarty = & $this->_getView ();
		foreach ( $parm as $key => $value ) {
			$smarty->assign ( $key, $value );
		}
		$smarty->display ( 'article.tpl' );
	}
	
	/**

	 * 后台管理

	 *

	 * @param unknown_type $parm

	 * @param unknown_type $admin_flag

	 */
	
	function manage($parm = array(), $admin_flag = "") {
		$smarty = & $this->_getView ();
		foreach ( $parm as $key => $value ) {
			$smarty->assign ( $key, $value );
		}
		if (@$parm ['title'] == "") {
			$parm ['title'] = DEFAUT_TITLE;
		
		}
		
		$smarty->register_modifier ( "substr", array ("Class_Common", "m_substr" ) );
		$smarty->register_modifier ( "formatdate", array ("Class_Common", "formatdate" ) );
		$smarty->register_modifier ( "formatmoney", array ("Class_Common", "formatmoney" ) );
		
		if (isset ( $_SESSION ['loginuserid'] ) && $_SESSION ['loginuserid'] != "") {
			$loginuserinfo = $this->_user->findByField ( "id", $_SESSION ['loginuserid'] );
		} else {
			$loginuserinfo = array ();
		}
		
		$smarty->assign ( 'loginuserinfo', @$loginuserinfo );
		$smarty->assign ( 'loginuserid', @$_SESSION ['loginuserid'] );
		$smarty->display ( 'admin/main.tpl' );
	}
	
	/**

	 * 不带模板的页面显示

	 * @author libin 2008-09-13

	 * @param unknown_type $page

	 * @param unknown_type $parm

	 */
	
	function display($page, $parm = array()) {
		$smarty = & $this->_getView ();
		foreach ( $parm as $key => $value ) {
			$smarty->assign ( $key, $value );
		}
		$smarty->register_modifier ( "substr", array ("Com_Common", "m_substr" ) );
		
		$smarty->display ( $page );
	}
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $date
	 */
	function formatdate($date) {
		$date = str_replace ( "-", "/", $date );
		return $date;
	}
	/**
	 * 
	 * 格式化金额
	 * @param unknown_type $date
	 */
	function formatmoney($date) {
		$date = number_format ( $date );
		return $date;
	}
	
	/**

	 * word cut

	 * @author libin 2008-09-13

	 * @param unknown_type $word

	 * @param unknown_type $num

	 * @return unknown

	 */
	
	function word_explode($word, $num) {
		
		$str = wordwrap ( $word, $num, "|", 1 );
		
		$str = explode ( "|", $str );
		
		$str = $str [0];
		
		return $str;
	
	}
	
	/**

	 *字截取

	 *

	 * @param unknown_type $word

	 * @param unknown_type $startw

	 * @param unknown_type $length

	 * @return unknown

	 */
	
	function m_substr($word, $start, $length, $more = "") {
		$word = htmlspecialchars_decode ( $word );
		$word = str_replace ( "&acute;", "'", $word );
		$str = mb_substr ( $word, $start, $length, "UTF-8" );
		
		if (strlen ( $word ) > strlen ( $str )) {
			$str = $str . $more;
		}
		
		return $str;
	
	}
	
	/**
	 * 
	 * 检查用户角色信息
	 * @param unknown_type $userid
	 * @param unknown_type $role
	 */
	function checkUser($userid, $role = "") {
		$userinfo = $this->_user->findByField ( "id", $userid );
		if (! is_array ( $role )) {
			$role = explode ( ",", $role );
		}
		$flag = false;
		if (count ( $userinfo ) > 0 && is_array ( $userinfo )) {
			foreach ( $role as $v ) {
				if ($v == $userinfo ['role']) {
					$flag = true;
				}
			}
		}
		if ($flag) {
			return true;
		} else {
			$url = url ( "Default", "Login" );
			redirect ( $url );
		}
	}
	
	/**
	 * 
	 * 过滤参数
	 * @return undefine
	 * @author libin
	 * @property created at 2012-10-29
	 * @property updated at 2012-10-29
	 * @example  
	 */
	function filter($value) {
		if(is_array($value)){
			foreach ($value as $k=>$v){
				if(is_array($v)){
					foreach ($v as $kk=>$vv){
						$v[$kk]=htmlspecialchars ( $vv);
					}
					$value[$k]=$v;
				}else{
					$value[$k]=htmlspecialchars ( $v);
				}
			}
		}else{
			$value = htmlspecialchars ( $value);
		}
		return $value;
	}
	
	/**
	 * 由长连接生成短链接操作
	*
	* 算法描述：使用6个字符来表示短链接，我们使用ASCII字符中的'a'-'z','0'-'9','A'-'Z'，共计62个字符做为集合。
	* 		     每个字符有62种状态，六个字符就可以表示62^6（56800235584），那么如何得到这六个字符，
	*           具体描述如下：
	*		  1. 对传入的长URL+设置key值 进行Md5，得到一个32位的字符串(32 字符十六进制数)，即16的32次方；
	*        2. 将这32位分成四份，每一份8个字符，将其视作16进制串与0x3fffffff(30位1)与操作, 即超过30位的忽略处理；
	*		  3. 这30位分成6段, 每5个一组，算出其整数值，然后映射到我们准备的62个字符中, 依次进行获得一个6位的短链接地址。
	*
	* @author flyer0126
	* @since 2012/07/13
	*/
	function shortUrl( $long_url ){
		$key = 'tongji2014';
		$base32 = "abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	
		// 利用md5算法方式生成hash值
		$hex = hash('md5', $long_url.$key);
		$hexLen = strlen($hex);
		$subHexLen = $hexLen / 8;
	
		$output = array();
		for( $i = 0; $i < $subHexLen; $i++ )
		{
			// 将这32位分成四份，每一份8个字符，将其视作16进制串与0x3fffffff(30位1)与操作
			$subHex = substr($hex, $i*8, 8);
			$idx = 0x3FFFFFFF & (1 * ('0x' . $subHex));
			 
			// 这30位分成6段, 每5个一组，算出其整数值，然后映射到我们准备的62个字符
			$out = '';
			for( $j = 0; $j < 6; $j++ )
			{
				$val = 0x0000003D & $idx;
				$out .= $base32[$val];
				$idx = $idx >> 5;
			}
			$output[$i] = $out;
		}
	
		return $output[0];
	}
	
	function generateQRfromGoogle($chl,$Height ='150',$widht='150',$EC_level='L',$margin='0'){
		$chl = urlencode($chl);
		return '<img src="http://173.194.121.28/chart?chs='.$widht.'x'.$Height.'&cht=qr&chld='.$EC_level.'|'.$margin.'&chl='.$chl.'" alt="QR code" height="'.$Height.'" widht="'.$widht.'"/>';
	}
	
	function downloadQRpngfromGoogle($chl,$Height ='150',$widht='150',$EC_level='L',$margin='0'){
		$chl = urlencode($chl);
		return 'http://173.194.121.28/chart?chs='.$widht.'x'.$Height.'&cht=qr&chld='.$EC_level.'|'.$margin.'&chl='.$chl;
	}
	
	/**
	 * 时间差计算
	 *time2Units(time()-strtotime($date))
	 * @param Timestamp $time 时间差
	 * @return String Time Elapsed
	 * @author Shelley Shyan
	 * @copyright http://phparch.cn (Professional PHP Architecture)
	 */
	function time2Units ($time)
	{
		$year   = floor($time / 60 / 60 / 24 / 365);
		$time  -= $year * 60 * 60 * 24 * 365;
		$month  = floor($time / 60 / 60 / 24 / 30);
		$time  -= $month * 60 * 60 * 24 * 30;
		$week   = floor($time / 60 / 60 / 24 / 7);
		$time  -= $week * 60 * 60 * 24 * 7;
		$day    = floor($time / 60 / 60 / 24);
		$time  -= $day * 60 * 60 * 24;
		$hour   = floor($time / 60 / 60);
		$time  -= $hour * 60 * 60;
		$minute = floor($time / 60);
		$time  -= $minute * 60;
		$second = $time;
		$elapse = '刚刚';
	
		$unitArr = array('年前'  =>'year', '个月前'=>'month',  '周前'=>'week', '天前'=>'day',
				'小时前'=>'hour', '分钟前'=>'minute', '秒前'=>'second'
		);
	
		foreach ( $unitArr as $cn => $u )
		{
			if ( $year > 0 ) {//大于一年显示年月日
				$elapse = date('Y/m/d',time()-$time);
				break;
			}
			else if ( $$u > 0 )
			{
				$elapse = $$u . $cn;
				break;
			}
		}
	
		return $elapse;
	}
	
	
}

?>
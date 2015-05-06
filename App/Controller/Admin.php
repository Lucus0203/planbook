<?php
class Controller_Admin extends FLEA_Controller_Action {
	/**
	 *
	 * Enter description here ...
	 * @var Class_Common
	 */
	var $_common;
	var $_admin;
	var $_adminid;
	var $_questionnaire;
	var $_question;
	var $_option;
	var $_answer;

	function __construct() {
		$this->_common = get_singleton ( "Class_Common" );

		$this->_admin = get_singleton ( "Model_Admin" );
		$this->_questionnaire = get_singleton ( "Model_Questionnaire" );
		$this->_question = get_singleton ( "Model_Question" );
		$this->_option = get_singleton ( "Model_Option" );
		$this->_answer = get_singleton ( "Model_Answer" );
		$this->_adminid = isset ( $_SESSION ['loginuserid'] ) ? $_SESSION ['loginuserid'] : "";
		if(empty($_SESSION ['loginuserid'])){
			$url=url("Default","Login");
			redirect($url);
		}
	}

	//问卷列表
	function actionChangePass() {
		$msg='';
		$act=isset($_POST['act'])?$this->_common->filter($_POST['act']):'';
		if($act=='update'){
			$data=$this->_common->filter($_POST);
			$old_pass=$data['old_pass'];
			$new_pass=$data['new_pass'];
			$confirm_pass=$data['confirm_pass'];
			if(empty($old_pass)||empty($new_pass)||empty($confirm_pass)){
				$msg='请输入正确内容';
			}elseif($new_pass!=$confirm_pass){
				$msg='新密码确认内容不一致';
			}else{
				$adm=$this->_admin->findByField('id',$this->_adminid);
				if(md5($old_pass)!=$adm['adm_password']){
					$msg='原始密码不正确';
				}else{
					$adm['adm_password']=md5($new_pass);
					$this->_admin->update($adm);
					$msg='密码更新成功';
				}
			}
		}
		$this->_common->show ( array ('main' => 'admin/change_pass.tpl','msg'=>$msg) );
	}
	
	//管理问卷公共信息
	function actionCommonInfo(){
		$msg='';
		$act=isset($_POST['act'])?$this->_common->filter($_POST['act']):'';
		if($act=='update'){
			$admin = $this->_admin->findByField('id',$this->_adminid);
			$data=$this->_common->filter($_POST);
			$info['id']=$this->_adminid;
			$info['complete_msg']=$data['complete_msg'];
			$info['sign_text']=$data['sign_text'];
			//签名图片
			$Upload= & get_singleton ( "Service_UpLoad" );
			$folder="resource/upload/signimg/";
			if (! file_exists ( $folder )) {
				mkdir ( $folder, 0777 );
			}
			$Upload->setDir($folder.$admin['adm_name']."/");
			$oldimg=$admin['sign_img'];
			$img=$Upload->upload('file');
			if($img['status']==1){//如果有上传图片则更新
				$info['sign_img']=$img['file_path'];
				if(file_exists($oldimg)){
					unlink($oldimg);
				}
			}else{
				$info['sign_img']=$oldimg;
			}

			$this->_admin->update($info);
			$msg='更新成功';
		}
		$info=$this->_admin->findByField('id',$this->_adminid);
		$this->_common->show ( array ('main' => 'admin/common_info.tpl','msg'=>$msg,'admin'=>$info) );
	}
	
}
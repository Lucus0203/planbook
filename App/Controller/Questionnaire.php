<?php
require_once SERVERROOT. DS . 'App' . DS . 'Class' .DS.'phpqrcode.php';
require_once SERVERROOT. DS . 'weixin' . DS . 'wechat.php';
class Controller_Questionnaire extends FLEA_Controller_Action {
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
	var $_abstract;

	function __construct() {
		$this->_common = get_singleton ( "Class_Common" );

		$this->_admin = get_singleton ( "Model_Admin" );
		$this->_questionnaire = get_singleton ( "Model_Questionnaire" );
		$this->_question = get_singleton ( "Model_Question" );
		$this->_option = get_singleton ( "Model_Option" );
		$this->_answer = get_singleton ( "Model_Answer" );
		$this->_abstract = get_singleton ( "Model_Abstract" );
		$this->_adminid = isset ( $_SESSION ['loginuserid'] ) ? $_SESSION ['loginuserid'] : "";
		if(empty($_SESSION ['loginuserid'])){
			$url=url("Default","Login");
			redirect($url);
		}
	}

	//问卷列表
	function actionIndex() {
		$pageparm = array ('status != 9 and status != 2');//9删除
		$page_no = isset ( $_GET ['page_no'] ) ? $_GET ['page_no'] : 1;
		$page_size = 20;
		$title = isset ( $_GET ['title'] ) ? $this->_common->filter($_GET ['title']) : '';

		$conditions=array('status != 9 and status != 2');
		if(!empty($title)){
			$conditions[]="title like '%$title%'";
			$pageparm['title']=$title;
		}

		$total=$this->_questionnaire->findCount($conditions);

		$pages = & get_singleton ( "Service_Page" );
		$pages->_page_no = $page_no;
		$pages->_page_num = $page_size;
		$pages->_total = $total;
		$pages->_url = url ( "Questionnaire", "Index" );
		$pages->_parm = $pageparm;
		$page = $pages->page ();
		$start = ($page_no - 1) * $page_size;

		$list=$this->_questionnaire->findAll($conditions,"id desc limit $start,$page_size");
		foreach ($list as $k=>$d){
			$list[$k]['num']=$this->_answer->findCount(array('questionnaire_id'=>$d['id']));
			$list[$k]['updated']=$this->_common->time2Units(time()-strtotime($d['updated']));
		}
		$this->_common->show ( array ('main' => 'questionnaire/list.tpl','list'=>$list,'page'=>$page,'pageparm'=>$pageparm) );
	}
	
	//完成的问卷列表
	function actionFinshed(){
		$pageparm = array ('status = 2');//9删除
		$page_no = isset ( $_GET ['page_no'] ) ? $_GET ['page_no'] : 1;
		$page_size = 20;
		$title = isset ( $_GET ['title'] ) ? $this->_common->filter($_GET ['title']) : '';

		$conditions=array('status = 2');
		if(!empty($title)){
			$conditions[]="title like '%$title%'";
			$pageparm['title']=$title;
		}

		$total=$this->_questionnaire->findCount($conditions);

		$pages = & get_singleton ( "Service_Page" );
		$pages->_page_no = $page_no;
		$pages->_page_num = $page_size;
		$pages->_total = $total;
		$pages->_url = url ( "Questionnaire", "Finshed" );
		$pages->_parm = $pageparm;
		$page = $pages->page ();
		$start = ($page_no - 1) * $page_size;

		$list=$this->_questionnaire->findAll($conditions,"id desc limit $start,$page_size");
		foreach ($list as $k=>$d){
			$list[$k]['num']=$this->_answer->findCount(array('questionnaire_id'=>$d['id']));
		}
		$this->_common->show ( array ('main' => 'questionnaire/list.tpl','list'=>$list,'page'=>$page,'pageparm'=>$pageparm) );
	}
	
	
	//问卷新增
	function actionAdd(){
		$act=isset ( $_POST ['act'] ) ? $_POST ['act'] : '';
		if($act=='add'){
			$data=$this->_common->filter($_POST['data']);
			$data['author']=htmlspecialchars_decode($data['author']);
			//问卷设置
			$data['over_num']=intval($data['over_num']);
			if(empty($data['over_num'])){
				unset($data['over_num']);
			}
			if(empty($data['over_date'])){
				unset($data['over_date']);
			}
			
			//短连接及二维码
			$shoturl=url('Answer','Index',array('time'=>time()));
			$shoturl=$this->_common->shortUrl($shoturl);
			$data['short']=$shoturl;
			$data['qrcode']=$this->createQr($shoturl);
			$data['created']=date("Y-m-d H:i:s");
			//创建问卷
			$questionnaire_id=$this->_questionnaire->create($data);
			//创建摘要
			$Upload= & get_singleton ( "Service_UpLoad" );
			$folder="resource/upload/abstract/";
			if (! file_exists ( $folder )) {
				mkdir ( $folder, 0777 );
			}
			$Upload->setDir($folder.date("Ymd")."/");
			$absnum=$_POST['abstractnum'];
			for ($i=0;$i<$absnum;$i++){
				$abstract=array();
				$numi=($i+1);
				$Upload->setPrefixName($i);
				$img=$Upload->upload('abstractimg'.$numi);
				if($img['status']==1){
					$file_path=$img['file_path'];
				}
				$abstract['questionnaire_id']=$questionnaire_id;
				$abstract['title']=$_POST['abstract'.$numi]['title'];
				$abstract['content']=$_POST['abstract'.$numi]['content'];
				$abstract['imginfo']=$_POST['abstract'.$numi]['imginfo'];
				$abstract['img']=$file_path;
				$this->_abstract->create($abstract);
			}
			//创建答题选项
			$questionnum=$this->_common->filter($_POST['questionnum']);
			for($i=0;$i<$questionnum;$i++){
				//创建问题
				if(isset($_POST['question'.$i])){
					$question=$this->_common->filter($_POST['question'.$i]);
					$question['questionnaire_id']=$questionnaire_id;
					$question['num']=($i+1);
					$question_id=$this->_question->create($question);
					foreach($question['content'] as $c){
						//创建选项
						if(!empty($c)){
							$option=array('content'=>$c,'questionnaire_id'=>$questionnaire_id,'question_id'=>$question_id);
							$this->_option->create($option);
						}
					}
				}
			}
			$url=url('Questionnaire','Index');
			redirect($url);
		}
		$this->_common->show ( array ('main' => 'questionnaire/add.tpl') );
	}
	
	//问卷编辑
	function actionEdit(){
		$qnnaid=isset ( $_GET ['qnnaid'] ) ? $_GET ['qnnaid'] : '';
		$act=isset ( $_POST ['act'] ) ? $_POST ['act'] : '';
		$msg='';
		if($act=='edit'){
			$data=$this->_common->filter($_POST['data']);
			$data['author']=htmlspecialchars_decode($data['author']);
			//问卷设置
			$data['over_num']=intval($data['over_num']);
			if(empty($data['over_num'])){
				unset($data['over_num']);
			}
			if(empty($data['over_date'])){
				unset($data['over_date']);
			}
			//清除旧二维码
			if(file_exists ( $data['qrcode'] )){
				unlink($data['qrcode']);
			}
			//短连接及二维码
			$shoturl=url('Answer','Index',array('time'=>time()));
			$shoturl=$this->_common->shortUrl($shoturl);
			$data['short']=$shoturl;
			$data['qrcode']=$this->createQr($shoturl);
			//创建问卷
			$questionnaire_id=$data['id'];
			$this->_questionnaire->update($data);
			//清除摘要和选项
			$this->_abstract->removeByConditions(array('questionnaire_id'=>$questionnaire_id));
			$this->_question->removeByConditions(array('questionnaire_id'=>$questionnaire_id));
			$this->_option->removeByConditions(array('questionnaire_id'=>$questionnaire_id));
			//创建摘要
			$Upload= & get_singleton ( "Service_UpLoad" );
			$folder="resource/upload/abstract/";
			if (! file_exists ( $folder )) {
				mkdir ( $folder, 0777 );
			}
			$Upload->setDir($folder.date("Ymd")."/");
			$absnum=$_POST['abstractnum'];
			for ($i=0;$i<$absnum;$i++){
				$abstract=array();
				$numi=($i+1);
				$Upload->setPrefixName($i);
				$img=$Upload->upload('abstractimg'.$numi);
				$oldimg=$_POST['abstract'.$numi]['img'];
				if($img['status']==1){//如果有上传图片则更新
					$file_path=$img['file_path'];
					if(file_exists($oldimg)){
						unlink($oldimg);
					}
				}else{
					$file_path=$oldimg;
				}
				$abstract['questionnaire_id']=$questionnaire_id;
				$abstract['title']=$_POST['abstract'.$numi]['title'];
				$abstract['content']=$_POST['abstract'.$numi]['content'];
				$abstract['imginfo']=$_POST['abstract'.$numi]['imginfo'];
				$abstract['img']=$file_path;
				$this->_abstract->create($abstract);
			}
			//创建答题选项
			$questionnum=$this->_common->filter($_POST['questionnum']);
			for($i=0;$i<$questionnum;$i++){
				//创建问题
				if(isset($_POST['question'.$i])){
					$question=$this->_common->filter($_POST['question'.$i]);
					$question['questionnaire_id']=$questionnaire_id;
					$question['num']=($i+1);
					$question_id=$this->_question->create($question);
					foreach($question['content'] as $c){
						//创建选项
						if(!empty($c)){
							$option=array('content'=>$c,'questionnaire_id'=>$questionnaire_id,'question_id'=>$question_id);
							$this->_option->create($option);
						}
					}
				}
			}
			$msg='问卷更新成功!';
		}
		
		$questionnaire=$this->_questionnaire->findByField('id',$qnnaid);
		$abstract=$this->_abstract->findAll(array('questionnaire_id'=>$qnnaid),'id asc');
		$question=$this->_question->findAll(array('questionnaire_id'=>$qnnaid),'num,id desc');
		foreach ($question as $k=>$q){
			$question[$k]['option']=$this->_option->findAll(array('question_id'=>$q['id']),'id asc');
		}
		$this->_common->show ( array ('main' => 'questionnaire/edit.tpl','questionnaire'=>$questionnaire,'abstract'=>$abstract,'question'=>$question,'msg'=>$msg) );
	}
	
	//被删除的问卷列表
	function actionDeList() {
		$pageparm = array ('status = 9');//9删除
		$page_no = isset ( $_GET ['page_no'] ) ? $_GET ['page_no'] : 1;
		$page_size = 20;
		$title = isset ( $_GET ['title'] ) ? $this->_common->filter($_GET ['title']) : '';
	
		$conditions=array('status = 9');
		if(!empty($title)){
			$conditions[]="title like '%$title%'";
			$pageparm['title']=$title;
		}
	
		$total=$this->_questionnaire->findCount($conditions);
	
		$pages = & get_singleton ( "Service_Page" );
		$pages->_page_no = $page_no;
		$pages->_page_num = $page_size;
		$pages->_total = $total;
		$pages->_url = url ( "Questionnaire", "DeList" );
		$pages->_parm = $pageparm;
		$page = $pages->page ();
		$start = ($page_no - 1) * $page_size;
	
		$list=$this->_questionnaire->findAll($conditions,"id desc limit $start,$page_size");
		foreach ($list as $k=>$d){
			$list[$k]['num']=$this->_answer->findCount(array('questionnaire_id'=>$d['id']));
			$list[$k]['updated']=$this->_common->time2Units(time()-strtotime($d['updated']));
		}
		$this->_common->show ( array ('main' => 'questionnaire/delist.tpl','list'=>$list,'page'=>$page,'pageparm'=>$pageparm) );
	}
	
	
	
	//再次发布
	function actionRePublic(){
		$qnnaid=isset ( $_GET ['qnnaid'] ) ? $_GET ['qnnaid'] : '';
		$questionnaire=$this->_questionnaire->findByField('id',$qnnaid);
		if(is_array($questionnaire)){
			$abstract=$this->_abstract->findAll(array('questionnaire_id'=>$qnnaid),'id asc');
			$question=$this->_question->findAll(array('questionnaire_id'=>$qnnaid),'num,id desc');
			
			unset($questionnaire['id']);
			//短连接及二维码
			$shoturl=url('Answer','Index',array('time'=>time()));
			$shoturl=$this->_common->shortUrl($shoturl);
			$questionnaire['short']=$shoturl;
			$questionnaire['qrcode']=$this->createQr($shoturl);
			$questionnaire['status']=0;
			$questionnaire['created']=date("Y-m-d H:i:s");
			$newqnnaid=$this->_questionnaire->create($questionnaire);
			foreach ($abstract as $ab){
				unset($ab['id']);
				$ab['questionnaire_id']=$newqnnaid;
				$this->_abstract->create($ab);
			}
			foreach ($question as $q){
				$options=$this->_option->findAll(array('question_id'=>$q['id']),'id asc');
				unset($q['id']);
				$q['questionnaire_id']=$newqnnaid;
				$newqid=$this->_question->create($q);
				foreach ($options as $op){
					unset($op['id']);
					$op['question_id']=$newqid;
					$op['questionnaire_id']=$newqnnaid;
					$this->_option->create($op);
				}
			}
		}
		
		$url=url('Questionnaire','Index');
		redirect($url);
	}


	function actionDel(){//删除status9
		$id=$this->_common->filter($_GET['id']);
		$eve=array('id'=>$id,'status'=>9);
		$this->_questionnaire->update($eve);
		redirect($_SERVER['HTTP_REFERER']);
	}
	function actionPublic(){ //发布status1
		$id=$this->_common->filter($_GET['id']);
		$eve=array('id'=>$id,'status'=>1);
		$this->_questionnaire->update($eve);
		redirect($_SERVER['HTTP_REFERER']);
	}
	function actionEnd(){//结束status2
		$id=$this->_common->filter($_GET['id']);
		$eve=array('id'=>$id,'status'=>2);
		$this->_questionnaire->update($eve);
		$msg=SITEHOST.'/result.php?qnnaid='.$id;
		$wechatObj = WeChat::getInstance();
		$wechatObj->sendCustomMsg('分析结果是 '.$msg,"o9ZPks_8ZpcQXBndQqjuUpt-E9tg");
		redirect($_SERVER['HTTP_REFERER']);
	}
	
	function actionRecover(){//恢复被删除的问卷
		$id=$this->_common->filter($_GET['id']);
		$eve=array('id'=>$id,'status'=>0);
		$this->_questionnaire->update($eve);
		redirect($_SERVER['HTTP_REFERER']);
	}
	
	function createQr($shoturl){
		$qrfile='resource/upload/'.$shoturl.'.png';
		$qrurl=SITEHOST.url('Answer','Qrcode',array('short'=>$shoturl));
		QRcode::png($qrurl,$qrfile,0,7,1);//生成二维码
		//file_put_contents($qrfile , file_get_contents($this->_common->downloadQRpngfromGoogle($qrurl,300,300)));//生成本地图片
		return $qrfile;
	}
}

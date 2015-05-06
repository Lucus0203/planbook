<?php
require_once SERVERROOT. DS . 'App' . DS . 'Class' .DS.'phpqrcode.php';
require_once SERVERROOT. DS . 'weixin' . DS . 'wechat.php';
class Controller_Qs extends FLEA_Controller_Action {
	/**
	 *
	 * Enter description here ...
	 * @var Class_Common
	 */
	var $_common;
	var $_admin;
	var $_adminid;
	var $_qs;
	var $_qe;
	var $_op;
	var $_qs_answer;
	var $_qs_abstract;
	var $_group;
	var $_category;
	var $_qepath;
	var $_qecon;

	function __construct() {
		$this->_common = get_singleton ( "Class_Common" );

		$this->_admin = get_singleton ( "Model_Admin" );
		$this->_group = get_singleton ( "Model_Group" );
		$this->_category = get_singleton ( "Model_Category" );
		$this->_qs = get_singleton ( "Model_Qs" );
		$this->_qe = get_singleton ( "Model_Qe" );
		$this->_op = get_singleton ( "Model_Op" );
		$this->_qs_answer = get_singleton ( "Model_QsAnswer" );
		$this->_qs_abstract = get_singleton ( "Model_QsAbstract" );
		$this->_qepath=get_singleton ( "Model_QePath" );
		$this->_qecon=get_singleton ( "Model_QeCon" );
		$this->_adminid = isset ( $_SESSION ['loginuserid'] ) ? $_SESSION ['loginuserid'] : "";
		if(empty($_SESSION ['loginuserid'])){
			$url=url("Default","Login");
			redirect($url);
		}
	}
	
	
	//问卷添加
	function actionAdd(){
		$act=isset ( $_POST ['act'] ) ? $_POST ['act'] : '';
		if($act=='add'){
			//问卷设置
			$data=$this->_common->filter($_POST['data']);
			$data['over_num']=intval($data['over_num']);
			if(empty($data['over_num'])){
				unset($data['over_num']);
			}
			if(empty($data['over_date'])){
				unset($data['over_date']);
			}
			
			//短连接及二维码
			$shoturl=url('As','Index',array('time'=>time()));
			$shoturl=$this->_common->shortUrl($shoturl);
			$data['short']=$shoturl;
			$data['qrcode']=$this->createQr($shoturl);
			$data['created']=date("Y-m-d H:i:s");
			//创建问卷
			$qs_id=$this->_qs->create($data);
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
				$abstract['qs_id']=$qs_id;
				$abstract['content']=$_POST['abstract'.$numi]['content'];
				$abstract['imginfo']=$_POST['abstract'.$numi]['imginfo'];
				$abstract['img']=$file_path;
				$this->_qs_abstract->create($abstract);
			}
			//创建答题选项
			$step=$this->_common->filter($_POST['step']);
			$qeids=$this->_common->filter($_POST['qeid']);
			$probability=$this->_common->filter($_POST['probability']);
			$hascon=$this->_common->filter($_POST['hascon']);
			$aslimit=$this->_common->filter($_POST['aslimit']);
			$flag_over=$this->_common->filter($_POST['flag_over']);
			$num=1;
			$csp=$step[0];
			foreach ($step as $sk=>$sp){
				$prov=($probability[$sk]=='')?1:$probability[$sk];
				$qepath=array('qs_id'=>$qs_id,'step'=>$sp,'qe_id'=>$qeids[$sk],'num'=>$num,'probability'=>$prov,'hascon'=>$hascon[$sk],'aslimit'=>$aslimit[$sk],'flag_over'=>$flag_over[$sk]);
				if($csp!=$sp){
					$num=1;
				}else{
					++$num;
				}
				$csp=$sp;
				$this->_qepath->create($qepath);
				
			}
			$stepnumupdate=array('id'=>$qs_id,'step_num'=>$csp);
			$this->_qs->update($stepnumupdate);//更新最大步骤数
			
			$prevqe=$this->_common->filter($_POST['consprevqe']);
			$prevop=$this->_common->filter($_POST['consprevop']);
			$consqeid=$this->_common->filter($_POST['consqeid']);
			$constep=$this->_common->filter($_POST['constep']);
			foreach ($prevqe as $pk=>$qid){
				$qecon=array('qs_id'=>$qs_id,'prevqe_id'=>$qid,'prevop_id'=>empty($prevop[$pk])?null:$prevop[$pk],'qe_id'=>$consqeid[$pk],'step'=>$constep[$pk]);
				$this->_qecon->create($qecon);
			}
			$url=url('Qs','List');
			redirect($url);
		}
		$gps=$groups=$this->_group->findAll(array(),"id asc",null,array("id","name"));
		foreach ($groups as $kg=>$g){
			$groups[$kg]['qes']=$this->_qe->findAll(array('group_id'=>$g['id']));
		}
		$category=$this->_category->findAll(array(),"id");
		$this->_common->show ( array ('main' => 'qs/qsadd.tpl','groups'=>$groups,'category'=>$category,'gps'=>$gps) );
	}
	
	//专业问卷调查列表
	function actionList() {
		$pageparm = array ('status != 9 and status != 2');//9删除
		$page_no = isset ( $_GET ['page_no'] ) ? $_GET ['page_no'] : 1;
		$page_size = 20;
		$title = isset ( $_GET ['title'] ) ? $this->_common->filter($_GET ['title']) : '';

		$conditions=array('status != 9 and status != 2');
		if(!empty($title)){
			$conditions[]="title like '%$title%'";
			$pageparm['title']=$title;
		}

		$total=$this->_qs->findCount($conditions);

		$pages = & get_singleton ( "Service_Page" );
		$pages->_page_no = $page_no;
		$pages->_page_num = $page_size;
		$pages->_total = $total;
		$pages->_url = url ( "Qs", "List" );
		$pages->_parm = $pageparm;
		$page = $pages->page ();
		$start = ($page_no - 1) * $page_size;

		$list=$this->_qs->findAll($conditions,"id desc limit $start,$page_size");
		foreach ($list as $k=>$d){
			$list[$k]['num']=$this->_qs_answer->findCount(array('qs_id'=>$d['id']));
		}
		$this->_common->show ( array ('main' => 'qs/qslist.tpl','list'=>$list,'page'=>$page,'pageparm'=>$pageparm) );
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

		$total=$this->_qs->findCount($conditions);

		$pages = & get_singleton ( "Service_Page" );
		$pages->_page_no = $page_no;
		$pages->_page_num = $page_size;
		$pages->_total = $total;
		$pages->_url = url ( "Qs", "DeList" );
		$pages->_parm = $pageparm;
		$page = $pages->page ();
		$start = ($page_no - 1) * $page_size;

		$list=$this->_qs->findAll($conditions,"id desc limit $start,$page_size");
		foreach ($list as $k=>$d){
			$list[$k]['num']=$this->_qs_answer->findCount(array('qs_id'=>$d['id']));
		}
		$this->_common->show ( array ('main' => 'qs/deqslist.tpl','list'=>$list,'page'=>$page,'pageparm'=>$pageparm) );
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

		$total=$this->_qs->findCount($conditions);

		$pages = & get_singleton ( "Service_Page" );
		$pages->_page_no = $page_no;
		$pages->_page_num = $page_size;
		$pages->_total = $total;
		$pages->_url = url ( "Qs", "Finshed" );
		$pages->_parm = $pageparm;
		$page = $pages->page ();
		$start = ($page_no - 1) * $page_size;

		$list=$this->_qs->findAll($conditions,"id desc limit $start,$page_size");
		foreach ($list as $k=>$d){
			$list[$k]['num']=$this->_qs_answer->findCount(array('qs_id'=>$d['id']));
			$list[$k]['updated']=$this->_common->time2Units(time()-strtotime($d['updated']));
		}
		$this->_common->show ( array ('main' => 'qs/qslist.tpl','list'=>$list,'page'=>$page,'pageparm'=>$pageparm) );
	}
	
	//专业问卷编辑
	function actionEdit(){
		$qnnaid=isset ( $_GET ['qnnaid'] ) ? $_GET ['qnnaid'] : '';
		$act=isset ( $_POST ['act'] ) ? $_POST ['act'] : '';
		$msg='';
		if($act=='edit'){
			$data=$this->_common->filter($_POST['data']);
			$data['author']=htmlspecialchars_decode($data['author']);
			//问卷设置
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
			$shoturl=url('As','Index',array('time'=>time()));
			$shoturl=$this->_common->shortUrl($shoturl);
			$data['short']=$shoturl;
			$data['qrcode']=$this->createQr($shoturl);
			//创建问卷
			$qs_id=$data['id'];
			$this->_qs->update($data);
			//清除摘要和选项
			$this->_qs_abstract->removeByConditions(array('qs_id'=>$qs_id));
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
				$abstract['qs_id']=$qs_id;
				$abstract['content']=$_POST['abstract'.$numi]['content'];
				$abstract['imginfo']=$_POST['abstract'.$numi]['imginfo'];
				$abstract['img']=$file_path;
				$this->_qs_abstract->create($abstract);
			}

			$this->_qepath->removeByConditions(array('qs_id'=>$qs_id));
			$this->_qecon->removeByConditions(array('qs_id'=>$qs_id));
			//创建答题选项
			$step=$this->_common->filter($_POST['step']);
			$qeids=$this->_common->filter($_POST['qeid']);
			$probability=$this->_common->filter($_POST['probability']);
			$hascon=$this->_common->filter($_POST['hascon']);
			$aslimit=$this->_common->filter($_POST['aslimit']);
			$flag_over=$this->_common->filter($_POST['flag_over']);
			$num=1;
			$csp=$step[0];
			foreach ($step as $sk=>$sp){
				$prov=($probability[$sk]=='')?1:$probability[$sk];
				$qepath=array('qs_id'=>$qs_id,'step'=>$sp,'qe_id'=>$qeids[$sk],'num'=>$num,'probability'=>$prov,'hascon'=>$hascon[$sk],'aslimit'=>$aslimit[$sk],'flag_over'=>$flag_over[$sk]);
				if($csp!=$sp){
					$num=1;
				}else{
					++$num;
				}
				$csp=$sp;
				$this->_qepath->create($qepath);
			
			}
			
			$stepnumupdate=array('id'=>$qs_id,'step_num'=>$csp);
			$this->_qs->update($stepnumupdate);//更新最大步骤数
			
			$prevqe=$this->_common->filter($_POST['consprevqe']);
			$prevop=$this->_common->filter($_POST['consprevop']);
			$consqeid=$this->_common->filter($_POST['consqeid']);
			$constep=$this->_common->filter($_POST['constep']);
			foreach ($prevqe as $pk=>$qid){
				$qecon=array('qs_id'=>$qs_id,'prevqe_id'=>$qid,'prevop_id'=>empty($prevop[$pk])?null:$prevop[$pk],'qe_id'=>$consqeid[$pk],'step'=>$constep[$pk]);
				$this->_qecon->create($qecon);
			}
			$msg="更新成功!";
		}
		$gps=$groups=$this->_group->findAll(array(),"id asc",null,array("id","name"));
		foreach ($groups as $kg=>$g){
			$groups[$kg]['qes']=$this->_qe->findAll(array('group_id'=>$g['id']));
		}
		$questionnaire=$this->_qs->findByField('id',$qnnaid);
		$abstract=$this->_qs_abstract->findAll(array('qs_id'=>$qnnaid),'id asc');
		$path=$this->_qepath->findAll(array('qs_id'=>$qnnaid),'step asc,id asc');
		$qes=$qe=array();
		$step='';
		foreach ($path as $p){
			if($step!=$p['step']){
				if(count($qe)>0){$qes[]=$qe;}
				$qe=array();
			}
			$question=$this->_qe->findByField('id',$p['qe_id']);
			$p['question']=$question;
			$p['qecon']=$this->_qecon->findAll(array('step'=>$p['step'],'qs_id'=>$qnnaid,'qe_id'=>$p['qe_id']));
			$qe[]=$p;
			$step=$p['step'];
		}
		if(count($qe)>0){$qes[]=$qe;}
		$category=$this->_category->findAll(array(),"id");
		$this->_common->show ( array ('main' => 'qs/qsedit.tpl','questionnaire'=>$questionnaire,'abstract'=>$abstract,'msg'=>$msg,'groups'=>$groups,'category'=>$category,'gps'=>$gps,'path'=>$path,'qes'=>$qes) );
	}
	
	//再次发布
	function actionRePublic(){
		$qnnaid=isset ( $_GET ['qnnaid'] ) ? $_GET ['qnnaid'] : '';
		$questionnaire=$this->_qs->findByField('id',$qnnaid);
		if(is_array($questionnaire)){
			$abstract=$this->_qs_abstract->findAll(array('qs_id'=>$qnnaid),'id asc');
			$qepath=$this->_qepath->findAll(array('qs_id'=>$qnnaid),'id');
			$qecon=$this->_qecon->findAll(array('qs_id'=>$qnnaid),'id');
				
			unset($questionnaire['id']);
			//短连接及二维码
			$shoturl=url('As','Index',array('time'=>time()));
			$shoturl=$this->_common->shortUrl($shoturl);
			$questionnaire['short']=$shoturl;
			$questionnaire['qrcode']=$this->createQr($shoturl);
			$questionnaire['status']=0;
			$questionnaire['created']=date("Y-m-d H:i:s");
			$newqnnaid=$this->_qs->create($questionnaire);
			foreach ($abstract as $ab){
				unset($ab['id']);
				$ab['qs_id']=$newqnnaid;
				$this->_qs_abstract->create($ab);
			}
			foreach ($qepath as $q){
				unset($q['id']);
				$q['qs_id']=$newqnnaid;
				$this->_qepath->create($q);
			}
			foreach ($qecon as $q){
				unset($q['id']);
				$q['qs_id']=$newqnnaid;
				$this->_qecon->create($q);
			}
		}
	
		$url=url('Qs','List');
		redirect($url);
	}
	
	//删除status9
	function actionDel(){
		$id=$this->_common->filter($_GET['id']);
		$eve=array('id'=>$id,'status'=>9);
		$this->_qs->update($eve);
		redirect($_SERVER['HTTP_REFERER']);
	}
	//发布status1
	function actionPublic(){ 
		$id=$this->_common->filter($_GET['id']);
		$eve=array('id'=>$id,'status'=>1);
		$this->_qs->update($eve);
		redirect($_SERVER['HTTP_REFERER']);
	}
	//结束status2
	function actionEnd(){
		$id=$this->_common->filter($_GET['id']);
		$eve=array('id'=>$id,'status'=>2);
		$this->_qs->update($eve);
		$msg=SITEHOST.'/result.php?qnnaid='.$id;
		$wechatObj = WeChat::getInstance();
		$wechatObj->sendCustomMsg('分析结果是 '.$msg,"o9ZPks_8ZpcQXBndQqjuUpt-E9tg");
		redirect($_SERVER['HTTP_REFERER']);
	}

	//ajax获取问题选项
	function actionGetOpsByQe(){
		$qeid=$this->_common->filter($_POST['qeid']);
		$qe=$this->_qe->findByField('id',$qeid);
		$op=$this->_op->findAll(array('qe_id'=>$qeid),'id asc');
		$qe['op']=$op;
		echo json_encode($qe);
	}
	//ajax根据分组获取问题
	function actionGetQeByGroup(){
		$gid=$this->_common->filter($_POST['gid']);
		$qes=$this->_qe->findAll(array('group_id'=>$gid));
		echo json_encode($qes);
	}
	//ajax获取前置条件
	function actionGetBeforeCon(){
		$qe=$this->_common->filter($_POST['qe']);
		$op=$this->_common->filter($_POST['op']);
		$curqeid='';
		$data=array();
		$qandop=array();
		foreach ($qe as $k=>$q){
			$qes=$this->_qe->findByField('id',$q);
			if($q!=$curqeid&&!empty($qandop)){
				$qandop['ops']=$this->_op->findAll(array('qe_id'=>$qandop['qeid']),'id asc');
				$data[]=$qandop;
				$qandop=array();
			}
			$qandop['qeid']=$q;
			$qandop['qetype']=$qes['type'];
			$qandop['opids'][]=$op[$k];
			$curqeid=$q;
		}
		if(!empty($qandop)){
			$qandop['ops']=$this->_op->findAll(array('qe_id'=>$qandop['qeid']),'id asc');
			$data[]=$qandop;
		}
		echo json_encode($data);
	}
	
	function createQr($shoturl){
		$qrfile='resource/upload/'.$shoturl.'.png';
		$qrurl=SITEHOST.url('As','Qrcode',array('short'=>$shoturl));
		QRcode::png($qrurl,$qrfile,0,10,1);//生成二维码
		//file_put_contents($qrfile , file_get_contents($this->_common->downloadQRpngfromGoogle($qrurl,300,300)));//生成本地图片
		return $qrfile;
	}
}

<?php
class Controller_Qe extends FLEA_Controller_Action {
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
	var $_group;
	var $_category;

	function __construct() {
		$this->_common = get_singleton ( "Class_Common" );

		$this->_admin = get_singleton ( "Model_Admin" );
		$this->_group = get_singleton ( "Model_Group" );
		$this->_category = get_singleton ( "Model_Category" );
		$this->_qs = get_singleton ( "Model_Qs" );
		$this->_qe = get_singleton ( "Model_Qe" );
		$this->_op = get_singleton ( "Model_Op" );
		$this->_adminid = isset ( $_SESSION ['loginuserid'] ) ? $_SESSION ['loginuserid'] : "";
		if(empty($_SESSION ['loginuserid'])){
			$url=url("Default","Login");
			redirect($url);
		}
	}

//题库分组
	function actionGroup(){
		$act=isset($_POST['act'])?$this->_common->filter($_POST['act']):'';
		$msg='';
		if(!empty($act)){
			$ids=$_POST['ids'];
			$name=$_POST['name'];
			foreach ($name as $k=>$n){
				if(!empty($n)){
					if(!empty($ids[$k])){
						$g=array('id'=>$ids[$k],'name'=>$n);
						$this->_group->update($g);
					}else{
						$g=array('name'=>$n);
						$this->_group->create($g);
					}
				}
			}
			$msg='分组更新成功';
		}
		$groups=$this->_group->findAll();
		$this->_common->show ( array ('main' => 'qe/group.tpl','groups'=>$groups,'msg'=>$msg) );
	}
	
	//删除分组
	function actionDelGroup(){
		$id=$this->_common->filter($_GET['id']);
		$count=$this->_qe->findCount(array('group_id'=>$id));
		if($count>0){
			echo '<script>alert("此分组中有题库,不可删除");window.location="'.$_SERVER['HTTP_REFERER'].'"</script>';
		}else{
			$this->_group->removeByPkv($id);
			redirect($_SERVER['HTTP_REFERER']);
		}
	}
	
	//题库分类
	function actionCategory(){
		$act=isset($_POST['act'])?$this->_common->filter($_POST['act']):'';
		$msg='';
		if(!empty($act)){
			$ids=$_POST['ids'];
			$name=$_POST['name'];
			foreach ($name as $k=>$n){
				if(!empty($n)){
					if(!empty($ids[$k])){
						$g=array('id'=>$ids[$k],'name'=>$n);
						$this->_category->update($g);
					}else{
						$g=array('name'=>$n);
						$this->_category->create($g);
					}
				}
			}
			$msg='分类更新成功';
		}
		$category=$this->_category->findAll();
		$this->_common->show ( array ('main' => 'qe/category.tpl','category'=>$category,'msg'=>$msg) );
	}
	
	//删除分类
	function actionDelCategory(){
		$id=$this->_common->filter($_GET['id']);
		$count=$this->_qe->findCount(array('category_id'=>$id));
		if($count>0){
			echo '<script>alert("此类型中有题库,不可删除");window.location="'.$_SERVER['HTTP_REFERER'].'"</script>';
		}else{
			$this->_category->removeByPkv($id);
			redirect($_SERVER['HTTP_REFERER']);
		}
	}
	
	//题库新增
	function actionQeAdd(){
		$act=isset ( $_POST ['act'] ) ? $_POST ['act'] : '';
		if($act=='add'){
			//创建题库
			$qe=$this->_common->filter($_POST['qe']);
			//判断类型为空
			if(empty($qe['category_id'])){unset($qe['category_id']);}
			$Upload= & get_singleton ( "Service_UpLoad" );
			$folder="resource/upload/qeimg/";
			if (! file_exists ( $folder )) {
				mkdir ( $folder, 0777 );
			}
			$Upload->setDir($folder.date("Ymd")."/");
			$img=$Upload->upload('file');
			if($img['status']==1){
				$file_path=$img['file_path'];
				$qe['file']=$file_path;
			}
			$qe_id=$this->_qe->create($qe);
			//创建答题选项
			$i=$qe['type'];
			if(isset($_POST['op'.$i])){
				$op=$this->_common->filter($_POST['op'.$i]);
				foreach($op['content'] as $c){
					//创建选项
					if(!empty($c)){
						$option=array('content'=>$c,'qe_id'=>$qe_id);
						$this->_op->create($option);
					}
				}
			}
			$url=url('Qe','QeList');
			redirect($url);
		}
		$group=$this->_group->findAll(array(),"id");
		$category=$this->_category->findAll(array(),"id");
		$this->_common->show ( array ('main' => 'qe/qeadd.tpl','category'=>$category,'group'=>$group) );
	}
	
	//题库编辑
	function actionQeEdit(){
		$act=isset ( $_POST ['act'] ) ? $_POST ['act'] : '';
		$id=isset ( $_GET ['qeid'] ) ? $this->_common->filter($_GET ['qeid']) : '';
		$msg='';
		if($act=='edit'){
			//创建题库
			$qe=$this->_common->filter($_POST['qe']);
			//判断类型为空
			if(empty($qe['category_id'])){unset($qe['category_id']);}
			$qe_id=$qe['id'];
			$Upload= & get_singleton ( "Service_UpLoad" );
			$folder="resource/upload/qeimg/";
			if (! file_exists ( $folder )) {
				mkdir ( $folder, 0777 );
			}
			$Upload->setDir($folder.date("Ymd")."/");
			$img=$Upload->upload('file');
			if($img['status']==1){
				$file_path=$img['file_path'];
				$qe['file']=$file_path;
			}
			$this->_qe->update($qe);
			//创建答题选项
			$i=$qe['type'];
			if(isset($_POST['op'.$i])){
				$this->_op->removeByConditions(array('qe_id'=>$qe_id));
				$op=$this->_common->filter($_POST['op'.$i]);
				foreach($op['content'] as $c){
					//创建选项
					if(!empty($c)){
						$option=array('content'=>$c,'qe_id'=>$qe_id);
						$this->_op->create($option);
					}
				}
			}
			$msg='修改成功!';
		}
		$qe=$this->_qe->findByField('id',$id);
		$ops=$this->_op->findAll(array('qe_id'=>$id),"id asc");
		$group=$this->_group->findAll(array(),"id asc");
		$category=$this->_category->findAll(array(),"id");
		$this->_common->show ( array ('main' => 'qe/qeedit.tpl','category'=>$category,'group'=>$group,'qe'=>$qe,'ops'=>$ops,'msg'=>$msg) );
	}
	
	//题库列表
	function actionQeList(){
		$config = FLEA::getAppInf ( 'dbDSN' );
		$prefix = $config ['prefix'];
		$keyword = isset ( $_GET ['keyword'] ) ? $this->_common->filter($_GET ['keyword']) : '';
		$group_id = isset ( $_GET ['group'] ) ? $this->_common->filter($_GET ['group']) : '';
		$category_id = isset ( $_GET ['category'] ) ? $this->_common->filter($_GET ['category']) : '';
		$conditions='';
		$pageparm=array();
		if(!empty($keyword)){
			$conditions=" and title like '%".addslashes($keyword)."%'";
			$pageparm['keyword']=$keyword;
		}
		if(!empty($group_id)){
			$conditions.=" and group_id =".addslashes($group_id);
			$pageparm['group']=$group_id;
		}
		if(!empty($category_id)){
			$conditions.=" and category_id =".addslashes($category_id);
			$pageparm['category']=$category_id;
		}
		$sql="select qe.*,gp.name group_name from ".$prefix."qe qe left join ".$prefix."group gp on qe.group_id=gp.id where 1=1 ";
		$sql.=$conditions;
		$total=$this->_qe->findBySql("select count(*) as num from ($sql) s ");
		$total=$total[0]['num'];
		$page_no = isset ( $_GET ['page_no'] ) ? $_GET ['page_no'] : 1;
		$page_size = 20;
		$pages = & get_singleton ( "Service_Page" );
		$pages->_page_no = $page_no;
		$pages->_page_num = $page_size;
		$pages->_total = $total;
		$pages->_url = url ( "Qe", "QeList");
		$pages->_parm = $pageparm;
		$page = $pages->page ();
		$start = ($page_no - 1) * $page_size;

		
		$list=$this->_qe->findBySql($sql." order by qe.id desc limit $start,$page_size");
		foreach ($list as $k=>$v){
			$ops=$this->_op->findAll(array('qe_id'=>$v['id']),'id asc');
			$list[$k]['ops']=$ops;
		}
		$group=$this->_group->findAll(array(),"id");
		$category=$this->_category->findAll(array(),"id");
		$this->_common->show ( array ('main' => 'qe/qelist.tpl','list'=>$list,'pageparm'=>$pageparm,'group'=>$group,'category'=>$category,'page'=>$page) );
	}
	
	//题库附件
	function actionQeFile(){
		$config = FLEA::getAppInf ( 'dbDSN' );
		$prefix = $config ['prefix'];
		$keyword = isset ( $_GET ['keyword'] ) ? $this->_common->filter($_GET ['keyword']) : '';
		$conditions='';
		if(!empty($keyword)){
			$conditions="and title like '%".addslashes($keyword)."%'";
			$pageparm['keyword']=$keyword;
		}
		$sql="select qe.*,gp.name group_name from ".$prefix."qe qe left join ".$prefix."group gp on qe.group_id=gp.id where qe.file is not null and qe.file<>'' ";
		$sql.=$conditions;
		$total=$this->_qe->findBySql("select count(*) as num from ($sql) s ");
		$total=$total[0]['num'];
		$page_no = isset ( $_GET ['page_no'] ) ? $_GET ['page_no'] : 1;
		$page_size = 20;
		$pages = & get_singleton ( "Service_Page" );
		$pages->_page_no = $page_no;
		$pages->_page_num = $page_size;
		$pages->_total = $total;
		$pages->_url = url ( "Qe", "QeList");
		$pages->_parm = $pageparm;
		$page = $pages->page ();
		$start = ($page_no - 1) * $page_size;
		
		$list=$this->_qe->findBySql($sql."order by qe.id desc limit $start,$page_size");
		foreach ($list as $k=>$v){
			$ops=$this->_op->findAll(array('qe_id'=>$v['id']),'id asc');
			$list[$k]['ops']=$ops;
			$total=$this->_qe->findBySql("select count(a.id) as num from ".$prefix."qs_answer a left join ".$prefix."qs qs on a.qs_id=qs.id left join ".$prefix."qepath path on path.qs_id=qs.id where path.qe_id=".$v['id']);
			$total=@$total[0]['num']*1;
			$list[$k]['rate']=round($v['times']/$total*100,2);
		}
		$this->_common->show ( array ('main' => 'qe/qefiles.tpl','list'=>$list,'pageparm'=>$pageparm,'page'=>$page) );
	}
	
	
	//删除题库
	function actionQeDel(){
		$id=$this->_common->filter($_GET['id']);
		$this->_op->removeByConditions(array('qe_id'=>$id));
		$this->_qe->removeByPkv($id);
		redirect($_SERVER['HTTP_REFERER']);
	}
	
	//删除题库附件
	function actionQeDelFile(){
		$id=$this->_common->filter($_GET['qeid']);
		$qe=$this->_qe->findByField('id',$id);
		if(!empty($qe['file'])){
			unlink($qe['file']);
		}
		$qe['file']='';
		$this->_qe->update($qe);
		redirect($_SERVER['HTTP_REFERER']);
	}
	
	
}

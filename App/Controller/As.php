<?php
class Controller_As extends FLEA_Controller_Action {
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
	var $_qs_answer_detail;
	var $_qs_abstract;
	var $_qepath;
	var $_qecon;

	function __construct() {
		$this->_common = get_singleton ( "Class_Common" );
		$this->_admin = get_singleton ( "Model_Admin" );
		$this->_qs = get_singleton ( "Model_Qs" );
		$this->_qe = get_singleton ( "Model_Qe" );
		$this->_op = get_singleton ( "Model_Op" );
		$this->_qs_answer = get_singleton ( "Model_QsAnswer" );
		$this->_qs_answer_detail = get_singleton ( "Model_QsAnswerDetail" );
		$this->_qs_abstract = get_singleton ( "Model_QsAbstract" );
		$this->_qepath=get_singleton ( "Model_QePath" );
		$this->_qecon=get_singleton ( "Model_QeCon" );
	}

	//短链接跳转
	function actionQrcode() {
		$short = isset ( $_GET ['short'] ) ? $this->_common->filter($_GET ['short']) : '';
		$questionnaire=$this->_qs->find(array('short'=>$short),'id desc');
		if(@$questionnaire['status']=='1'){
			$url=url('As','Index',array('qid'=>$questionnaire['id']));
			redirect($url);
			return ;
		}
		if($questionnaire['status']=='0'){
			echo '<h1>调查问卷还没发布</h1>';
		}elseif($questionnaire['status']=='2'){
			echo '<h1>调查问卷已经结束</h1>';
		}else{
			echo '<h1>找不到相关问卷,请联系管理员</h1>';
		}
		
	}
	
	//回答问卷
	function actionIndex(){
		$qsid = isset ( $_GET ['qid'] ) ? $this->_common->filter($_GET ['qid']) : '';
		$questionnaire=$this->_qs->findByField('id',$qsid);
		$abstract=$this->_qs_abstract->findAll(array('qs_id'=>$qsid),'id asc');
		$qepath=$this->_qepath->findAll(array('qs_id'=>$qsid,'step'=>0));//第一组随机问题
		$tmparr=array();
		foreach ($qepath as $qk=>$p){
			$tmparr[$qk]=$p['probability'];
		}
		$pk=$this->get_rand($tmparr);
		$question=$this->_qe->findByField('id',$qepath[$pk]['qe_id']);
		$question['option']=$this->_op->findAll(array('qe_id'=>$question['id']),'id asc');
		//获取答题路径及条件,供js判断
		$path=$this->_qepath->findAll(array('qs_id'=>$qsid,' step > 0 and qe_id <> '.$question['id']),'step asc,id asc');
		$qesall=$qes=$qe=array();
		$step='';
		foreach ($path as $p){
			if($step!=$p['step']){
				if(count($qe)>0){$qes[]=$qe;}
				$qe=array();
			}
			$ques=$this->_qe->findByField('id',$p['qe_id']);
			$ques['option']=$this->_op->findAll(array('qe_id'=>$ques['id']),"id");
			$p['question']=$ques;
			$qe[]=$p;//结构化路径
			$step=$p['step'];
			$qesall[]=$p;//所有问题
		}
		if(count($qe)>0){$qes[]=$qe;}//结构化路径
		$qecon=$this->_qecon->findAll(array('qs_id'=>$qsid));
		$qes=$this->restructure($qes);//重构内容按概率出题
		//print_r($qes);//exit();
		$this->_common->display('as/answer.tpl',array ('questionnaire'=>$questionnaire,'abstract'=>$abstract,'question'=>$question,'qesall'=>$qesall,'qes'=>$qes,'qecon'=>$qecon) );
	}
	
	//问题结果重构
	function restructure($list){
		$newlist=array();
		foreach ($list as $k => $d){
			$arr=array();
			foreach ($d as $qk=>$q){
				$arr[$qk]=$q['probability'];
			}
			$tk=$this->get_rand($arr);
			$repeatFlag=0;
			foreach ($newlist as $n){
				if($n['qe_id']==$d[$tk]['qe_id']){
					$repeatFlag=1;
				}
			}
			$newlist[$k]=$d[$tk];
		}
		if($repeatFlag){
			return $this->restructure($list);
		}else{
			return $newlist;
		}
	}
	
	//根据概率获取结果
	function get_rand($proArr) {
		$result = '';
		//概率数组的总概率精度
		$proSum = array_sum($proArr);
		//概率数组循环
		foreach ($proArr as $key => $proCur) {
			$randNum = mt_rand(1, $proSum);
			if ($randNum <= $proCur) {
				$result = $key;
				break;
			} else {
				$proSum -= $proCur;
			}
		}
		unset ($proArr);
	
		return $result;
	}
	
	//提交问卷
	function actionSubmit(){
		$data = $this->_common->filter($_POST);
		$act = isset ( $data ['act'] ) ? $data ['act'] : '';
		$qnnaid = isset ( $data ['questionnaire_unit'] ) ? $data ['questionnaire_unit'] : '';
		if($act=='submit'&&!empty($qnnaid)){
			$questionnaire=$this->_qs->findByField('id',$qnnaid);
			if($questionnaire['status']=='0'){
				$ref=$_SERVER['HTTP_REFERER'];
				echo '<h1>调查问卷还没发布,您不能答题<br><a href="'.$ref.'" >返回</a><';
				return ;
			}
			if($questionnaire['status']=='2'){
				$ref=$_SERVER['HTTP_REFERER'];
				echo '<h1>调查问卷已结束,您不能答题<br><a href="'.$ref.'" >返回</a>';
				return ;
			}
			$passtime='';
			$time=$data['time'];
			//$d = intval($time/86400);
			$h = round(($time%86400)/3600);
			$m = round(($time%3600)/60);
			$s = round(($time%60));
			$passtime.=$h>0?$h.'时':'';
			$passtime.=$m>0?$m.'分':'';
			$passtime.=$s.'秒';
			//回答信息
			$answer=array('qs_id'=>$qnnaid,'pass_time'=>$passtime,'num'=>$this->_qs_answer->getMaxNumGoupByQid($qnnaid),'ip'=>$_SERVER['REMOTE_ADDR']);
			$ansid=$this->_qs_answer->create($answer);
			$quesnum=$data['quesnum'];
			for($i=0;$i<$quesnum;$i++){
				$asoption=$data['check'.$i];
				if(is_array($asoption)){//多选
					foreach ($asoption as $aopt){
						$asdetial=array('qs_answer_id'=>$ansid,'qs_id'=>$qnnaid,'qe_id'=>$data['questionid'.$i],'op_id'=>$aopt,'qs_answer_content'=>$data['anscontent'.$aopt]);
						$this->_qs_answer_detail->create($asdetial);
					}
				}else{
					$asdetial=array('qs_answer_id'=>$ansid,'qs_id'=>$qnnaid,'qe_id'=>$data['questionid'.$i],'op_id'=>$asoption,'qs_answer_content'=>$data['anscontent'.$asoption]);
					$this->_qs_answer_detail->create($asdetial);
				}
				$this->_qe->addTimes($data['questionid'.$i]);//添加答题次数
			}
			$url=url('As','Complete');
			redirect($url);
		}
	}
	
	//完成问卷
	function actionComplete(){
		$this->_common->display('as/complete.tpl',array('msg'=>'Thank you for your help!'));
	}
	
	function actionPreview(){
		$config = FLEA::getAppInf ( 'dbDSN' );
		$prefix = $config ['prefix'];
		$aid = isset ( $_GET ['qid'] ) ? $this->_common->filter($_GET['qid']) : '';
		$answer=$this->_qs_answer->findByField('id',$aid);
		if(empty($answer['id'])){
			echo '答题内容不存在,可能已被删除';
			return;
		}
		$questionnaire=$this->_qs->findByField('id',$answer['qs_id']);
		$abstract=$this->_qs_abstract->findAll(array('qs_id'=>$aid));
		$detailsql="select qe.* from ".$prefix."qe qe left join ".$prefix."qs_answer_detail ad on qe.id=ad.qe_id 
				where ad.qs_answer_id=".$aid." group by qe.id";
		$question=$this->_qe->findBySql($detailsql);
		foreach ($question as $k=>$q){
			//查找答题选项
			$sql="select opt.content,ad.qs_answer_content as answer_content from ".$prefix."qs_answer_detail ad left join ".$prefix."op opt on ad.op_id=opt.id 
					where ad.qs_answer_id=$aid and ad.qe_id=".$q['id'];
			$o=$this->_qs_answer_detail->findBySql($sql);
			$contentstr=array();
			$answerstr=array();
			foreach ($o as $oo){
				$contentstr[]=$oo['content'];
				$answerstr[]=$oo['answer_content'];
			}
			$contentstr=implode(',', $contentstr);
			$answerstr=implode(',', $answerstr);
			$answer_content='';
			if($q['type']=='1'||$q['type']=='2'){
				$answer_content=$contentstr;
			}else{
				$contentstr=explode("@text", $contentstr);
				$answerstr=explode(",", $answerstr);
				foreach ($contentstr as $ck=>$c){
					$answer_content.=$c.$answerstr[$ck];
				}
			}
			$question[$k]['answer']=$answer_content;
		}

		$this->_common->display('as/preview.tpl',array('answer'=>$answer,'questionnaire'=>$questionnaire,'question'=>$question,'abstract'=>$abstract));
	}
	
	function actionAnalysis(){
		$config = FLEA::getAppInf ( 'dbDSN' );
		$prefix = $config ['prefix'];
		$qnnaid = isset ( $_GET ['qnnaid'] ) ?$this->_common->filter($_GET ['qnnaid'])  : '';
		$questionnaire=$this->_qs->findByField('id',$qnnaid);
		$question=$this->_qe->findBySql("select qe.*,path.qs_id from ".$prefix."qe qe left join ".$prefix."qepath path on path.qe_id = qe.id where path.qs_id=$qnnaid");
		foreach ($question as $k=>$q){
			$options=$this->_op->findAll(array('qe_id'=>$q['id']));
			foreach ($options as $opk=>$op){
				$options[$opk]['count']=$this->_qs_answer_detail->findCount(array('qs_id'=>$qnnaid,'qe_id'=>$q['id'],'op_id'=>$op['id']));
			}
			$question[$k]['option']=$options;
		}
		$questionnaire['anscount']=$this->_qs_answer->findCount(array('qs_id'=>$qnnaid));
		
		//查看答卷详情
		$pageparm = array ('qnnaid'=>$qnnaid);//9删除
		$page_no = isset ( $_GET ['page_no'] ) ? $_GET ['page_no'] : 1;
		$page_size = 50;
		$title = isset ( $_GET ['title'] ) ? $this->_common->filter($_GET ['title']) : '';
		
		$conditions=array('qs_id'=>$qnnaid);
		if(!empty($title)){
			$conditions[]="title like '%$title%'";
			$pageparm['title']=$title;
		}
		
		$total=$this->_qs_answer->findCount($conditions);
		
		$pages = & get_singleton ( "Service_Page" );
		$pages->_page_no = $page_no;
		$pages->_page_num = $page_size;
		$pages->_total = $total;
		$pages->_url = url ( "As", "Analysis");
		$pages->_parm = $pageparm;
		$page = $pages->analysisPage ();
		$start = ($page_no - 1) * $page_size;
		
		$answerlist=$this->_qs_answer->findAll($conditions,"id desc limit $start,$page_size");
		foreach ($answerlist as $k=>$v){
			$anses=array();
			foreach ($question as $qk=>$q){
				$sql="select op.content,ad.qs_answer_content as answer_content from ".$prefix."qs_answer_detail ad 
					left join ".$prefix."op op on op.id=ad.op_id 
					where ad.qe_id=".$q['id']." and ad.qs_answer_id=".$v['id'];
				$o=$this->_qs_answer_detail->findBySql($sql);
				$content=array();
				$answer=array();
				foreach ($o as $oo){
					$content[]=$oo['content'];
					$answer[]=$oo['answer_content'];
				}
				$content=implode(',', $content);
				$answer=implode(',', $answer);
				$answer_content='';
				if($q['type']=='1'||$q['type']=='2'){
					$answer_content=$content;
				}else{
					$content=explode("@text", $content);
					$answer=explode(",", $answer);
					foreach ($content as $ck=>$c){
						$c.=@$answer[$ck];
						$answer_content.=$c;
					}
				}
				$anses[]=array('content'=>$answer_content,'qetype'=>$q['type']);
			}
			$answerlist[$k]['answer']=$anses;
			
		}
		
		$this->_common->display('as/analysis.tpl', array ('questionnaire'=>$questionnaire,'question'=>$question,'answerlist'=>$answerlist,'page'=>$page,'pageparm'=>$pageparm) );
	}
	
}




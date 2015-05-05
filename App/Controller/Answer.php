<?php
class Controller_Answer extends FLEA_Controller_Action {
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
	var $_answer_detail;
	var $_abstract;

	function __construct() {
		$this->_common = get_singleton ( "Class_Common" );

		$this->_admin = get_singleton ( "Model_Admin" );
		$this->_questionnaire = get_singleton ( "Model_Questionnaire" );
		$this->_question = get_singleton ( "Model_Question" );
		$this->_option = get_singleton ( "Model_Option" );
		$this->_answer = get_singleton ( "Model_Answer" );
		$this->_answer_detail = get_singleton ( "Model_AnswerDetail" );
		$this->_abstract = get_singleton ( "Model_Abstract" );
	}

	//短链接跳转
	function actionQrcode() {
		$short = isset ( $_GET ['short'] ) ? $this->_common->filter($_GET ['short']) : '';
		$questionnaire=$this->_questionnaire->find(array('short'=>$short),'id desc');
		if(@$questionnaire['status']=='1'){
			$url=url('Answer','Index',array('qid'=>$questionnaire['id']));
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
		$qid = isset ( $_GET ['qid'] ) ? $this->_common->filter($_GET ['qid']) : '';
		$questionnaire=$this->_questionnaire->findByField('id',$qid);
		$abstract=$this->_abstract->findAll(array('questionnaire_id'=>$qid),'id asc');
		$question=$this->_question->findAll(array('questionnaire_id'=>$qid),'num,id asc');
		foreach ($question as $k=>$q){
			$question[$k]['option']=$this->_option->findAll(array('question_id'=>$q['id']),'id asc');
		}
		$this->_common->display('qa/qa.tpl',array ('questionnaire'=>$questionnaire,'question'=>$question,'abstract'=>$abstract) );
	}
	
	//提交问卷
	function actionSubmit(){
		$data = $this->_common->filter($_POST);
		$act = isset ( $data ['act'] ) ? $data ['act'] : '';
		$qnnaid = isset ( $data ['questionnaire_unit'] ) ? $data ['questionnaire_unit'] : '';
		if($act=='submit'&&!empty($qnnaid)){
			$questionnaire=$this->_questionnaire->findByField('id',$qnnaid);
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
			$answer=array('questionnaire_id'=>$qnnaid,'pass_time'=>$passtime,'num'=>$this->_answer->getMaxNumGoupByQid($qnnaid),'ip'=>$_SERVER['REMOTE_ADDR']);
			$ansid=$this->_answer->create($answer);
			$quesnum=$this->_question->findCount(array('questionnaire_id'=>$qnnaid));
			for($i=0;$i<$quesnum;$i++){
				$asoption=$data['check'.$i];
				if(is_array($asoption)){
					foreach ($asoption as $aopt){
						$asdetial=array('answer_id'=>$ansid,'questionnaire_id'=>$qnnaid,'question_id'=>$data['questionid'.$i],'option_id'=>$aopt,'answer_content'=>$data['anscontent'.$aopt]);
						$this->_answer_detail->create($asdetial);
					}
				}else{
					$asdetial=array('answer_id'=>$ansid,'questionnaire_id'=>$qnnaid,'question_id'=>$data['questionid'.$i],'option_id'=>$asoption,'answer_content'=>$data['anscontent'.$asoption]);
					$this->_answer_detail->create($asdetial);
				}
			}
			$url=url('Answer','Complete');
			redirect($url);
		}
	}
	
	//完成问卷
	function actionComplete(){
		$this->_common->display('qa/complete.tpl',array('msg'=>'感谢您的提交!'));
	}
	
	function actionPreview(){
		$config = FLEA::getAppInf ( 'dbDSN' );
		$prefix = $config ['prefix'];
		$qid = isset ( $_GET ['qid'] ) ? $this->_common->filter($_GET['qid']) : '';
		$answer=$this->_answer->findByField('id',$qid);
		if(empty($answer['id'])){
			echo '答题内容不存在,可能已被删除';
			return;
		}
		$questionnaire=$this->_questionnaire->findByField('id',$answer['questionnaire_id']);
		$abstract=$this->_abstract->findAll(array('questionnaire_id'=>$qid));
		$question=$this->_question->findAll(array('questionnaire_id'=>$answer['questionnaire_id']),'num asc,id desc');
		foreach ($question as $k=>$q){
			//查找答题选项
			$sql="select op.content,ad.answer_content from ".$prefix."answer_detail ad 
					left join ".$prefix."option op on op.id=ad.option_id 
					where ad.question_id=".$q['id']." and ad.answer_id=".$qid;
			$o=$this->_answer_detail->findBySql($sql);
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

		$this->_common->display('qa/preview.tpl',array('answer'=>$answer,'questionnaire'=>$questionnaire,'question'=>$question,'abstract'=>$abstract));
	}
	
	function actionAnalysis(){
		$config = FLEA::getAppInf ( 'dbDSN' );
		$prefix = $config ['prefix'];
		$qnnaid = isset ( $_GET ['qnnaid'] ) ?$this->_common->filter($_GET ['qnnaid'])  : '';
		$questionnaire=$this->_questionnaire->findByField('id',$qnnaid);
		$question=$this->_question->findAll(array('questionnaire_id'=>$qnnaid),'num asc,id asc');
		foreach ($question as $k=>$q){
			$options=$this->_option->findAll(array('question_id'=>$q['id']));
			foreach ($options as $opk=>$op){
				$options[$opk]['count']=$this->_answer_detail->findCount(array('questionnaire_id'=>$qnnaid,'question_id'=>$q['id'],'option_id'=>$op['id']));
			}
			$question[$k]['option']=$options;
		}
		$questionnaire['anscount']=$this->_answer->findCount(array('questionnaire_id'=>$qnnaid));
		
		//查看答卷详情
		$pageparm = array ('qnnaid'=>$qnnaid);//9删除
		$page_no = isset ( $_GET ['page_no'] ) ? $_GET ['page_no'] : 1;
		$page_size = 50;
		$title = isset ( $_GET ['title'] ) ? $this->_common->filter($_GET ['title']) : '';
		
		$conditions=array('questionnaire_id'=>$qnnaid);
		if(!empty($title)){
			$conditions[]="title like '%$title%'";
			$pageparm['title']=$title;
		}
		
		$total=$this->_answer->findCount($conditions);
		
		$pages = & get_singleton ( "Service_Page" );
		$pages->_page_no = $page_no;
		$pages->_page_num = $page_size;
		$pages->_total = $total;
		$pages->_url = url ( "Answer", "Analysis");
		$pages->_parm = $pageparm;
		$page = $pages->analysisPage ();
		$start = ($page_no - 1) * $page_size;
		
		$answerlist=$this->_answer->findAll($conditions,"id desc limit $start,$page_size");
		
		foreach ($answerlist as $k=>$v){
			$anses=array();
			foreach ($question as $qk=>$q){
				$sql="select op.content,ad.answer_content from ".$prefix."answer_detail ad 
					left join ".$prefix."option op on op.id=ad.option_id 
					where ad.question_id=".$q['id']." and ad.answer_id=".$v['id'];
				$o=$this->_answer_detail->findBySql($sql);
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
		
		$this->_common->display('qa/analysis.tpl', array ('questionnaire'=>$questionnaire,'question'=>$question,'answerlist'=>$answerlist,'page'=>$page,'pageparm'=>$pageparm) );
	}
	
}




<?php
FLEA::loadFile ('App/Service/pdf/tfpdf.php');
FLEA::loadFile ( "Service/PHPExcel/IOFactory.php" );
class Controller_ResultAnalysis extends FLEA_Controller_Action {
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

	function __construct() {
		$this->_common = get_singleton ( "Class_Common" );

		$this->_admin = get_singleton ( "Model_Admin" );
		$this->_questionnaire = get_singleton ( "Model_Questionnaire" );
		$this->_question = get_singleton ( "Model_Question" );
		$this->_option = get_singleton ( "Model_Option" );
		$this->_answer = get_singleton ( "Model_Answer" );
		$this->_answer_detail = get_singleton ( "Model_AnswerDetail" );
		$this->_adminid = isset ( $_SESSION ['loginuserid'] ) ? $_SESSION ['loginuserid'] : "";
		if(empty($_SESSION ['loginuserid'])){
			$url=url("Default","Login");
			redirect($url);
		}
	}

	
	//分析统计图
	function actionAnalysis(){
		$qnnaid = isset ( $_GET ['qnnaid'] ) ?$this->_common->filter($_GET ['qnnaid'])  : '';
		if(empty($qnnaid)){
			$url=url('Questionnaire','Index');
			redirect($url);
			return;
		}
		$questionnaire=$this->_questionnaire->findByField('id',$qnnaid);
		$question=$this->_question->findAll(array('questionnaire_id'=>$qnnaid),'num asc,id desc');
		foreach ($question as $k=>$q){
			$options=$this->_option->findAll(array('question_id'=>$q['id']));
			foreach ($options as $opk=>$op){
				$options[$opk]['count']=$this->_answer_detail->findCount(array('questionnaire_id'=>$qnnaid,'question_id'=>$q['id'],'option_id'=>$op['id']));
			}
			$question[$k]['option']=$options;
		}
		$questionnaire['anscount']=$this->_answer->findCount(array('questionnaire_id'=>$qnnaid));
		$this->_common->show ( array ('main' => 'analysis/analysis.tpl','questionnaire'=>$questionnaire,'question'=>$question) );
		
	}
	
	//答题列表
	function actionList() {
		$qnnaid=isset ( $_GET ['qnnaid'] ) ? $this->_common->filter($_GET ['qnnaid']) : '';
		if(empty($qnnaid)){
			$url=url('Questionnaire','Index');
			redirect($url);
			return;
		}
		$pageparm = array ('qnnaid'=>$qnnaid);//9删除
		$page_no = isset ( $_GET ['page_no'] ) ? $_GET ['page_no'] : 1;
		$page_size = 20;
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
		$pages->_url = url ( "ResultAnalysis", "List");
		$pages->_parm = $pageparm;
		$page = $pages->page ();
		$start = ($page_no - 1) * $page_size;

		$list=$this->_answer->findAll($conditions,"id desc limit $start,$page_size");
		$questionnaire=$this->_questionnaire->findByField('id',$qnnaid);
		$this->_common->show ( array ('main' => 'analysis/answer_list.tpl','list'=>$list,'page'=>$page,'pageparm'=>$pageparm,'questionnaire'=>$questionnaire) );
	}
	
	function actionDelAnswer(){
		$id=isset ( $_GET ['id'] ) ? $this->_common->filter($_GET ['id']) : '';
		if(empty($id)){
			$url=url('Questionnaire','Index');
			redirect($url);
			return;
		}
		$this->_answer->removeByConditions(array('id'=>$id));
		$this->_answer_detail->removeByConditions(array('answer_id'=>$id));
		redirect($_SERVER['HTTP_REFERER']);
	}
	
	//pdf打印
	function actionQuestionarePdf(){
		$config = FLEA::getAppInf ( 'dbDSN' );
		$prefix = $config ['prefix'];
		$qnnaid = isset ( $_GET ['qnnaid'] ) ?$this->_common->filter($_GET ['qnnaid'])  : '';
		$questionnaire=$this->_questionnaire->findByField('id',$qnnaid);
		$question=$this->_question->findAll(array('questionnaire_id'=>$qnnaid),'num asc,id asc');
		//查看答卷详情
		$conditions=array('questionnaire_id'=>$qnnaid);
		$answerlist=$this->_answer->findAll($conditions,"id desc");


		if(!is_array($answerlist)||count($answerlist)<=0){
			echo '暂无答题数据';
			return false;
		}
		
		$pdf = new tFPDF ();
		$pdf->isfooter = false;
		$pdf->bMargin = 0;
		$pdf->AutoPageBreak = false;
		$pdf->AddFont ( 'MicrosoftYaHei', '', 'msyh.ttf', true );
		$pdf->Open ();
		foreach ($answerlist as $k=>$v){
			$pdf->AddPage ();
			$pdf->SetTextColor ( 0, 0, 0 );
			$x=$y=0;
			$y += 10;
			$pdf->SetXY ( $x, $y );
			$pdf->SetFont ( 'MicrosoftYaHei', '', 12 );
			$pdf->SetFontSize(15);
			$sw=$pdf->GetStringWidth($questionnaire['title']);
			$pdf->MultiCell(210, 8, $questionnaire['title'],0,'C');
			//$pdf->Cell ( 210,12, $questionnaire['title'], 0, 0, 'C', 0);
			$y += (ceil($sw/210))*8+5;
			$pdf->SetXY ( $x, $y );
			$pdf->SetFontSize(10);
			$pdf->Cell ( 210,12, '答题序号：'.$v['num'].'	用 户 IP：'.$v['ip'].'	答题时长：'.$v['pass_time'].'	答题时间：'.$v['created'], 0, 0, 'C', 0);
			$y += 12;
			$pdf->SetXY ( $x+5, $y );
			$pdf->SetFillColor(60,156,207);
			$pdf->Cell ( 200,0.5, '', 0, 0, 'C', 1);//蓝线
			foreach ($question as $qk=>$q){
				//查下问题答案详情
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
						$answer_content.=$c.$answer[$ck];
					}
				}

				$y+=5;
				$pdf->SetXY($x+5,$y);
				$pdf->Cell(8,10,'Q'.($qk+1).'.', 0, 0, 'L', 0);
				$pdf->SetX($x+12);
				$pdf->MultiCell(192, 10, $q['title']);
				$sw=$pdf->GetStringWidth($q['title']);
				
				$y+=ceil($sw/192)>0?ceil($sw/192)*10:10;
				$pdf->SetX($x+5,$y);
				$pdf->Cell(10,10,'回答:', 0, 0, 'L', 0);
				$pdf->SetX($x+15);
				$pdf->MultiCell(190, 10, $answer_content);
				$sw=$pdf->GetStringWidth($answer_content);
				$y+=ceil($sw/190)>0?ceil($sw/190)*10:10;
			}
			$pdf->SetXY ( $x+5, 260 );
			$pdf->Cell ( 200,0.5, '', 0, 0, 'C', 1);
			$pdf->SetXY ( $x+10, 260 );
			$pdf->Cell ( 210,12, '关注"Planbook"微信账号,了解更多内容', 0, 0, 'L', 0);
			$path = "resource/images/planbook.jpg";
			$pdf->Image ( $path, 160, 262, 30, 30 );
		}
			
		$pdf->Output ();
	}
	
	//导出Excel
	function actionQuestionareExc(){
		$config = FLEA::getAppInf ( 'dbDSN' );
		$prefix = $config ['prefix'];
		$qnnaid = isset ( $_GET ['qnnaid'] ) ?$this->_common->filter($_GET ['qnnaid'])  : '';
		$questionnaire=$this->_questionnaire->findByField('id',$qnnaid);
		$question=$this->_question->findAll(array('questionnaire_id'=>$qnnaid),'num asc,id asc');
		$conditions=array('questionnaire_id'=>$qnnaid);
		$answerlist=$this->_answer->findAll($conditions,"id desc ");
		
		// 设置基本属性
		$objPHPExcel = new PHPExcel();
		//$objPHPExcel->getActiveSheet()->setTitle(iconv("gbk","UTF-8",'simple'));
		// 创建多个工作薄
		$sheet1 = $objPHPExcel->createSheet();
		// 设置第一个工作簿为活动工作簿
		$objPHPExcel->setActiveSheetIndex(0);
		// 设置活动工作簿名称
		$objPHPExcel->getActiveSheet()->setTitle('统计数据');
		// 设置默认字体和大小
		$objPHPExcel->getDefaultStyle()->getFont()->setName('宋体');
		$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
		// 给特定单元格中写入内容
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0,1, '答题序号');
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1,1, '答题时间');
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2,1, '答题时长');
		$i=3;
		foreach ($question as $q){
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i,1, $q['title']);
			++$i;
		}
		$r=2;
		foreach ($answerlist as $v){
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0,$r, $v['num']);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1,$r, $v['created']);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2,$r, $v['pass_time']);
			$i=3;
			foreach ($question as $q){
				//查找答题选项
				$sql="select op.content,ad.answer_content from ".$prefix."answer_detail ad
						left join ".$prefix."option op on op.id=ad.option_id
						where ad.question_id=".$q['id']." and ad.answer_id=".$v['id'];
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
						$answer_content.=$c.@$answerstr[$ck];
					}
				}
				//用户答案
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i,$r, $answer_content);
				++$i;
			}
			++$r;
		}
		//$objPHPExcel->getActiveSheet()->setTitle(iconv("gbk","UTF-8",'simple'));
		$FileName = '答题统计表'.date('YmdHis').".xls"; // 输出EXCEL文件名
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'csv');
		$objWriter->setUseBOM(true);
		//ob_end_clean();
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
		header("Content-Type:application/force-download");
		header("Content-Type: application/vnd.ms-excel;");
		header("Content-Type:application/octet-stream");
		header("Content-Type:application/download");
 		header("Content-Disposition:attachment;filename=".$FileName);
		header("Content-Transfer-Encoding:binary");
		$objWriter->save("php://output"); 
		
	}
	
}



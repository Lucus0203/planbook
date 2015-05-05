<?php
FLEA::loadFile ('App/Service/pdf/tfpdf.php');
FLEA::loadFile ( "Service/PHPExcel/IOFactory.php" );
class Controller_AsOut extends FLEA_Controller_Action {
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

	function __construct() {
		$this->_common = get_singleton ( "Class_Common" );

		$this->_admin = get_singleton ( "Model_Admin" );
		$this->_qs = get_singleton ( "Model_Qs" );
		$this->_qe = get_singleton ( "Model_Qe" );
		$this->_op = get_singleton ( "Model_Op" );
		$this->_qs_answer = get_singleton ( "Model_QsAnswer" );
		$this->_qs_answer_detail = get_singleton ( "Model_QsAnswerDetail" );
		$this->_adminid = isset ( $_SESSION ['loginuserid'] ) ? $_SESSION ['loginuserid'] : "";
		if(empty($_SESSION ['loginuserid'])){
			$url=url("Default","Login");
			redirect($url);
		}
	}
	
	//pdf打印
	function actionQuestionarePdf(){
		$config = FLEA::getAppInf ( 'dbDSN' );
		$prefix = $config ['prefix'];
		$qnnaid = isset ( $_GET ['qnnaid'] ) ?$this->_common->filter($_GET ['qnnaid'])  : '';
		$questionnaire=$this->_qs->findByField('id',$qnnaid);
		//$question=$this->_question->findAll(array('questionnaire_id'=>$qnnaid),'num asc,id asc');
		//查看答卷详情
		$conditions=array('qs_id'=>$qnnaid);
		$answerlist=$this->_qs_answer->findAll($conditions,"id desc");
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
			$detailsql="select qe.* from ".$prefix."qe qe left join ".$prefix."qs_answer_detail ad on qe.id=ad.qe_id 
					where ad.qs_answer_id=".$v['id']." group by qe.id";
			$question=$this->_qe->findBySql($detailsql);
			foreach ($question as $qk=>$q){
				//查下问题答案详情
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
		$questionnaire=$this->_qs->findByField('id',$qnnaid);
		$question=$this->_qe->findBySql("select qe.*,path.qs_id from ".$prefix."qe qe left join ".$prefix."qepath path on path.qe_id = qe.id where path.qs_id=$qnnaid");
		$conditions=array('qs_id'=>$qnnaid);
		$answerlist=$this->_qs_answer->findAll($conditions,"id desc ");
		
		// 设置基本属性
		$objPHPExcel = new PHPExcel();
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
				$sql="select op.content,ad.qs_answer_content as answer_content from ".$prefix."qs_answer_detail ad
						left join ".$prefix."op op on op.id=ad.op_id
						where ad.qe_id=".$q['id']." and ad.qs_answer_id=".$v['id'];
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
						$answer_content.=$c.@$answerstr[$ck];
					}
				}
				//用户答案
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i,$r, $answer_content);
				++$i;
			}
			++$r;
		}
		$FileName = '答题统计表'.date('YmdHis').".xls"; // 输出EXCEL文件名
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
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



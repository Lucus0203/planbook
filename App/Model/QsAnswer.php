<?php
FLEA::loadClass('FLEA_Db_TableDataGateway');
class Model_QsAnswer extends FLEA_Db_TableDataGateway
{
	var $tableName = 'qs_answer';
	var $primaryKey = 'id';

	function getMaxNumGoupByQid($qnnaid){
		$ans=$this->find(array('qs_id'=>$qnnaid),'id desc');
		return !isset($ans['id'])?1:($ans['num']*1+1);
	}
}

?>
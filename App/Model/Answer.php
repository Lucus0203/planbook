<?php
FLEA::loadClass('FLEA_Db_TableDataGateway');
class Model_Answer extends FLEA_Db_TableDataGateway
{
	var $tableName = 'answer';
	var $primaryKey = 'id';
	
	function getMaxNumGoupByQid($qnnaid){
		$ans=$this->find(array('questionnaire_id'=>$qnnaid),'id desc');
		return !isset($ans['id'])?1:($ans['num']*1+1);
	}
}

?>
<?php
FLEA::loadClass('FLEA_Db_TableDataGateway');
class Model_Qe extends FLEA_Db_TableDataGateway
{
	var $tableName = 'qe';
	var $primaryKey = 'id';

	function addTimes($qeid){
		$times=$this->find(array('id'=>$qeid),'','times');
		$this->updateField(array('id'=>$qeid), 'times', $times['times']*1+1);
	}
}

?>
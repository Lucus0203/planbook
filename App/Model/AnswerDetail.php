<?php
FLEA::loadClass('FLEA_Db_TableDataGateway');
class Model_AnswerDetail extends FLEA_Db_TableDataGateway
{
	var $tableName = 'answer_detail';
	var $primaryKey = 'id';
}

?>
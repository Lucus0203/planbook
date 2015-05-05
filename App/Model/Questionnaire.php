<?php
FLEA::loadClass('FLEA_Db_TableDataGateway');
class Model_Questionnaire extends FLEA_Db_TableDataGateway
{
	var $tableName = 'questionnaire';
	var $primaryKey = 'id';
	
}

?>
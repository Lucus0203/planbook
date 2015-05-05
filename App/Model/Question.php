<?php
FLEA::loadClass('FLEA_Db_TableDataGateway');
class Model_Question extends FLEA_Db_TableDataGateway
{
	var $tableName = 'question';
	var $primaryKey = 'id';

}

?>
<?php
FLEA::loadClass('FLEA_Db_TableDataGateway');
class Model_Option extends FLEA_Db_TableDataGateway
{
	var $tableName = 'option';
	var $primaryKey = 'id';

}

?>
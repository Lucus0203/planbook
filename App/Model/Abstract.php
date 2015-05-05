<?php
FLEA::loadClass('FLEA_Db_TableDataGateway');
class Model_Abstract extends FLEA_Db_TableDataGateway
{
	var $tableName = 'abstract';
	var $primaryKey = 'id';

}

?>
<?php
FLEA::loadClass('FLEA_Db_TableDataGateway');
class Model_Analysis extends FLEA_Db_TableDataGateway
{
	var $tableName = 'analysis';
	var $primaryKey = 'id';

}

?>
<?php
FLEA::loadClass('FLEA_Db_TableDataGateway');
class Model_Category extends FLEA_Db_TableDataGateway
{
	var $tableName = 'category';
	var $primaryKey = 'id';

}

?>
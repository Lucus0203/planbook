<?php
FLEA::loadClass('FLEA_Db_TableDataGateway');
class Model_QsAbstract extends FLEA_Db_TableDataGateway
{
	var $tableName = 'qs_abstract';
	var $primaryKey = 'id';

}

?>
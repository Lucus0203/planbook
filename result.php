<?php
require_once 'const.php';

header("Location:".SITEHOST.'/index.php?controller=Answer&action=Analysis&qnnaid='.$_GET['qnnaid']);
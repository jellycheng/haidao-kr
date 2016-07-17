<?php
defined('IN_ADMIN') or die('Access Denied');


$config = include APP_ROOT . 'config/database.php';
$mysql_link = @mysql_connect($config['db_host'] . ':' . $config['db_port'], $config['db_user'], $config['db_pwd']);
$sql = "DROP TABLE IF EXISTS `{$config['db_prefix']}_prcie_notice`;";
mysql_query($sql);

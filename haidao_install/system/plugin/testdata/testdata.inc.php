<?php
if (!defined('IN_ADMIN')) {
    exit('Access Denied');
}

// 2016.06.24 演示数据
$plugins = cache('plugins');
$plugins = $plugins[$_GET['mod']];

$test_config = cache('testdata_config', '', 'plugin');

if (IS_POST) {

    set_time_limit(0);
    $config = include APP_ROOT . 'config/database.php';
    $config || showmessage('数据库配置文件不存在');

    $mysql_link = @mysql_connect($config['db_host'] . ':' . $config['db_port'], $config['db_user'], $config['db_pwd']);
    $mysql_link || showmessage('数据库配置信息错误');

    if ($_GET['status'] == 'get') {
        if ($test_config['status'] == 'installed') showmessage('已经添加了演示数据，请不要重复添加。');

        $sql_file = PLUGIN_PATH . $plugins['directory'] . 'sql/testdata.sql';
        if (!file_exists($sql_file)) showmessage('数据文件不存在');

        $sqls = parse_sql($sql_file, true, $config['db_prefix']);
        foreach ($sqls as $sql) mysql_query($sql);

        $test_config['status'] = 'installed';
        cache('testdata_config', $test_config, 'plugin');
        showmessage('正在导入演示数据<br><img src="system/plugin/testdata/template/image/ajax_loader.gif"><script>setTimeout("$(\'.text span\').html(\'导入完毕\')","2000");</script>');
    } elseif ($_GET['status'] == 'del') {
        if ($test_config['status'] == 'uninstall') showmessage('系统不存在演示数据，当前不支持清空数据操作。');

        $sql_file = PLUGIN_PATH . $plugins['directory'] . 'sql/backup.sql';
        if (!file_exists($sql_file)) showmessage('数据文件不存在');

        $sqls = parse_sql($sql_file, true, $config['db_prefix']);
        foreach ($sqls as $sql) mysql_query($sql);

        $test_config['status'] = 'uninstall';
        cache('testdata_config', $test_config, 'plugin');

        showmessage('正在清除数据并回复出厂设置<br><img src="system/plugin/testdata/template/image/ajax_loader.gif"><script>setTimeout("$(\'.text span\').html(\'清除完毕\')","2000");</script>');
    } else {
        showmessage('非法操作');
    }
} else {
    include(PLUGIN_PATH . $plugins['directory'] . 'template/index.php');
}

/**
 * 返回sql语句数组
 * @param $fileName
 * @param bool $status
 * @param $db_pre
 * @return array
 */
function parse_sql($fileName, $status = true, $db_pre)
{
    $lines = file($fileName);
    $lines[0] = str_replace(chr(239) . chr(187) . chr(191), "", $lines[0]);//去除BOM头
    $flage = true;
    $sqls = array();
    $sql = "";
    foreach ($lines as $line) {
        $line = trim($line);
        $char = substr($line, 0, 1);
        if ($char != '#' && strlen($line) > 0) {
            $prefix = substr($line, 0, 2);
            switch ($prefix) {
                case '/*': {
                    $flage = (substr($line, -3) == '*/;' || substr($line, -2) == '*/') ? true : false;
                    break 1;
                }
                case '--':
                    break 1;
                default : {
                    if ($flage) {
                        $sql .= $line;
                        if (substr($line, -1) == ";") {
                            $sql = str_replace('hd_', $db_pre, $sql);
                            $sqls[] = $sql;
                            $sql = "";
                        }
                    }
                    if (!$flage) $flage = (substr($line, -3) == '*/;' || substr($line, -2) == '*/') ? true : false;
                }
            }
        }
    }
    return $sqls;
}

<?php
if (empty($_REQUEST)) {
    echo 'fail: no enough param';
    exit();
}

require_once '../core/func.php';
$icon_png = "pic/Kx.png";
$spare_json = "Kx.json";
$statis_json = "statis.json";
$dayClass = $_REQUEST['dayClass']; //每天值班节数(1-10)
if ($dayClass % 2 == 1) {
    echo '更新失败,请检查每天值班节数是否为偶数';
    exit();
}
$week = $_REQUEST['week']; //每学期值班周数
$firstDay = $_REQUEST['firstDay']; //开学第一天
$workBegin = $_REQUEST['workBegin']; //值班第一天
$workEnd = $_REQUEST['workEnd']; //值班结束日
$weekendSwitch = $_REQUEST['weekendSwitch'];

$config = getConfig(true); //获取数组
$skipDate = json_decode($config['skipDate'], true);
$config = array('icon_png' => $icon_png, 'spare_json' => $spare_json, 'statis_json' => $statis_json, 'dayClass' => $dayClass, 'week' => $week,
    'firstDay' => $firstDay, 'workBegin' => $workBegin, 'weekendSwitch' => $weekendSwitch, 'workEnd' => $workEnd, 'skipDate' => json_encode($skipDate));


writeConfig($config);
//echo getenv("HTTP_REFERER");
header("Location:" . getenv("HTTP_REFERER"));
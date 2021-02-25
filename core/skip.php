<?php
if (empty($_REQUEST)) {
    echo "fail: no enought param";
    exit();
}

$con = $_REQUEST['con'];
$time = $_REQUEST['time'];
if (empty($con) || empty($time)) {
    echo "fail: no enought param";
    exit;
}

require_once '../core/func.php';
$config = getConfig(true);
$skipDate = json_decode($config['skipDate'], true);
switch ($con) {
    case 'del':
        if (($key = array_search($time, $skipDate))!==false) {
            unset($skipDate[$key]);
        } else {
            echo 'fail: no find skipped time';
            exit();
        }
        break;
    case 'add':
        if (in_array($time, $skipDate)) {
            echo 'fail: time has been skipped';
            exit();
        } else {
            $skipDate[] = $time;
        }
        break;
}
$config['skipDate'] = json_encode($skipDate);
if (writeConfig($config)) {
    echo 'ok';
} else {
    echo 'fail: write failed';
}
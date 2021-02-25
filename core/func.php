<?php
$configFile = '../m/config.json';
/** 读取配置文件
 * @param false $mode 为真时输出数组，为假时输出json字符串
 * @return false|mixed|string 读取错误为false
 */
function getConfig($mode = false)
{
    global $configFile;
    $fp = fopen($configFile, 'r');
    if (fread($fp, 1) == FALSE) {
        return false;
    } else {
        rewind($fp);
        if ($mode) {
            return json_decode(fread($fp, filesize($configFile)), true);
        } else {
            return fread($fp, filesize($configFile));
        }
    }
    fclose($fp);
}
/** 写入配置文件
 * @param $config 数组
 * @return bool
 */
function writeConfig($config)
{
    global $configFile;
    $config_json = json_encode($config);
    $fp = fopen($configFile, 'w');
    if (!fwrite($fp, $config_json)) {
        return false;
    } else {
        return true;
    }
    fclose($fp);
}

$statisFile = '../'.getConfig(true)['statis_json'];
/** 读取统计文件
 * @param false $mode 为真时输出数组，为假时输出json字符串
 * @return false|mixed|string 读取错误为false
 */
function getStatis($mode = false)
{
    global $statisFile;
    $fp = fopen($statisFile, 'r');
    if (fread($fp, 1) == FALSE) {
        return false;
    } else {
        rewind($fp);
        if ($mode) {
            return json_decode(fread($fp, filesize($statisFile)), true);
        } else {
            return fread($fp, filesize($statisFile));
        }
    }
    fclose($fp);
}
/** 写入统计文件
 * @param $config 数组
 * @return bool
 */
function writeStatis($config)
{
    global $statisFile;
    $config_json = json_encode($config);
    $fp = fopen($statisFile, 'w');
    if (!fwrite($fp, $config_json)) {
        return false;
    } else {
        return true;
    }
    fclose($fp);
}

/** 学号转姓名
 * @param $data
 * @return mixed|string|string[]
 */
function Num2Name($data)
{
    $handle = fopen("../data/name2num.txt", "r");
    while (!feof($handle)) {
        $content = fgets($handle);
        $content = str_replace(array("\r\n", " "), "", $content);
        $name2num = explode("|", $content);
        // 姓名|学号
        $data = str_replace($name2num[1], $name2num[0], $data);
    }
    return $data;
}
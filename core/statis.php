<?php
/**
 * 更新统计结果statis.json
 */
if (empty($_REQUEST)) {
    echo 'fail: no content';
    exit();
}
$con = $_GET['con']; //操作方式 reset重置 del取消已确定计划 save保存排班计划 add增加不存在的姓名
$path = "../statis.json";
$fileContent = file_get_contents($path);
$statisArr = json_decode($fileContent, true);
// 已存的结果[]
$week = filterNum($_REQUEST['week']);
switch ($con) {
    case 'add':
        // 增加不存在的姓名
        if(!isset($_REQUEST['name'])){
            echo 'fail: no enough param';
            exit;
        }
        $res= statisAdd($_REQUEST['name']);
        if($res!='ok'){
            echo $res;
            exit();
        }
        break;
    case 'reset':
        // 清空confirm中计划，按data初始化为0
        statisReset();
        break;
    case 'save':
        // 保存$week周排班计划$classArray，增加计数
        $classArray = json_decode($_REQUEST['plan']); // 传来的结果[]
        if (empty($week) || empty($classArray) || !is_array($classArray)) {
            echo 'fail: no enough param';
            exit;
        }
        if (empty($statisArr['confirm'][$week])) {
            // 给每人值班次数+1
            foreach ($classArray as $eachDay) {
                foreach ($eachDay as $name) {
                    if ($name == "") continue;
                    if (isset($statisArr[$name])) {
                        $statisArr[$name] += 1;
                    } else {
                        $statisArr[$name] = 0;
                    }
                }
            }
            // 将plan存入confirm
            $statisArr['confirm'][$week] = json_encode($classArray, JSON_UNESCAPED_UNICODE);
        } else {
            echo "fail: plan has been exist";
            exit;
        }
        break;
    case 'del':
        // 取消已确定计划
        if (empty($week)) {
            echo 'fail: no enough param';
            exit;
        }
        if (!empty($statisArr['confirm'][$week])) {
            $plan = json_decode($statisArr['confirm'][$week]); //该周确定计划
            // 将每人值班次数-1
            foreach ($plan as $dayPlan) {
                foreach ($dayPlan as $name) {
                    if ($name == "") continue;
                    $times = $statisArr[$name];
                    if (!empty($times)) {
                        if ($times > 0) {
                            $times -= 1;
                        }
                    } else {
                        $times = 0;
                    }
                    $statisArr[$name] = $times;
                }
            }
            unset($statisArr['confirm'][$week]); //清空计划

        } else {
            echo "fail: no find plan";
            exit;
        }
        break;
}
echo writeContent($statisArr); //保存文件到statis.json

/**
 * 增加统计人名
 * @param $name
 * @return string ok / fail
 */
function statisAdd($name){
    global $statisArr;
    if(empty($statisArr[$name]) && $statisArr[$name]!==0){
        $statisArr[$name]=0;
    }else{
        return 'fail: user has been exist';
    }
    return 'ok';
}

/**
 * 重置统计结果statis
 */
function statisReset(){
    global $statisArr;
    $statisArr = [];
    $dir = "../data"; //目录
    foreach (scandir($dir) as $value) {
        if (strpos($value, ".json") !== false) {
            $xh = str_replace(".json", "", $value);
            $statisArr[$xh]=0;
        }
    }
}
/**
 * @param $string 未知字符串
 * @return string 仅过滤出数字
 */
function filterNum($string)
{
    preg_match_all('/\d+/', $string, $num);
    return join('', $num[0]);
}

/**
 * @param $content 写入内容
 * @return string 写入结果
 */
function writeContent($content)
{
    global $path, $con;
    $myFile = fopen($path, "w");
    if($con == 'reset'){
        $content = Num2Name(json_encode($content, JSON_UNESCAPED_UNICODE));
    }else{
        $content = json_encode($content, JSON_UNESCAPED_UNICODE);
    }
    if (fwrite($myFile, $content) != false) {
        $res = 'ok';
    } else {
        $res = 'fail: write failed';
    }
    fclose($myFile);
    return $res;
}

/** 学号转姓名
 * @param $data 学号
 * @return string 姓名
 */
function Num2Name($data)
{
    $handle = fopen("../data/name2num.txt","r");
    while(!feof($handle)) {
        $content = fgets($handle);
        $content = str_replace(Array("\r\n", " "),"",$content);
        $name_num = explode("|", $content);
        // 姓名|学号
        $data = str_replace($name_num[1], $name_num[0], $data);
    }
    return $data;
}


//    Header("Location:../index.php");

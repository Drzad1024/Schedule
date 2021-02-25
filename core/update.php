<?php
/**
 * 更新Kx.json
 */
$dir = "../data"; //课表数据目录
$arr = $arr2 = $arr3 = array();
$status = array(); //空闲状态

require_once '../core/func.php';
$config = getConfig(true);
$week = $config['week'];
$dayClass = $config['dayClass'];
$weekendSwitch = $config['weekendSwitch'];
$weekDay = ($weekendSwitch === 'on') ? "7" : "5";
//$week = 18; //共18周
//$dayClass = 8; //一天8节课
//$weekDay = 5; //一周5天

$status['info'] = array("week" => $week, 'dayClass' => $dayClass, 'weekDay' => $weekDay);
foreach (scandir($dir) as $value) {
    if (strpos($value, ".json") !== false) {
        $arr[] = $value;
    }
}
function initialStatus()
{
    global $status, $week, $weekDay, $dayClass;
    for ($k = 1; $k <= $week; $k++) {
        //一共$week周
        for ($i = 1; $i <= $weekDay; $i++) {
            //一周$i天
            for ($j = 1; $j <= $dayClass; $j++) {
                //一天$j节课
                $status[$k][$i][$j] = array();
            }
        }
    }
}

function pushClass($courseName, $ClassDay, $ClassSessions, $ContinuingSession, $ClassWeek)
{
    global $class;
    array_push($class, array('CourseName' => $courseName, 'ClassDay' => $ClassDay,
        'ClassSessions' => $ClassSessions, 'ContinuingSession' => $ContinuingSession, 'classWeek' => $ClassWeek));
}

initialStatus();
foreach ($arr as $xh_json) {
    $xh = str_replace(".json", "", $xh_json); //从文件名中获取的学号
    if (isset($_GET['iconv']) && $_GET['iconv'] == "gbk") {
        // Windows保存GBK编码，要转码，服务器保存无需转码
        $json = iconv("GBK", "UTF-8", file_get_contents($dir . "/" . $xh_json));
//            echo "gbk";
    } else {
        $json = file_get_contents($dir . "/" . $xh_json);
    }

    $data = json_decode($json, true);

    $xh2 = $data['dateList'][0]['selectCourseList'][0]['id']['studentNumber']; //从文件中读取的学号
    $selectCourseList = $data['dateList'][0]['selectCourseList'];

    $ClassDay = $ClassSessions = $ClassWeek = $ContinuingSession = array();
    $class = array(); //个人课程信息
    // $class 个人课程数组 加入课程
    if (!is_array($selectCourseList)) break;
    foreach ($selectCourseList as $arr2) {
        $courseName = $arr2['courseName'];//课程名
        $TimeList = $arr2['timeAndPlaceList'];
        if (is_array($TimeList)) {
            foreach ($TimeList as $arr3) {
                array_push($ClassDay, $arr3['classDay']);
                array_push($ClassSessions, $arr3['classSessions']);
                array_push($ContinuingSession, $arr3['continuingSession']);
                array_push($ClassWeek, str_split($arr3['classWeek']));  //转为字符数组[24]
            }
            pushClass($courseName, $ClassDay, $ClassSessions, $ContinuingSession, $ClassWeek);
            $ClassDay = $ClassSessions = $ClassWeek = $ContinuingSession = array();
        } else {
            array_push($class, array('CourseName' => $courseName, 'ClassDay' => NULL, 'ClassSessions' => NULL,
                'ContinuingSession' => NULL, 'classWeek' => NULL));
        }
    }
    $status_temp = array(); //临时数组
    for ($i = 0; $i < count($class); $i++) {
        for ($k = 0; $k < count($class[$i]['classWeek']); $k++) {
            //第$k节课
            if ($class[$i]['ClassSessions'][$k] > $dayClass || $class[$i]['ClassDay'][$k] > $weekDay) continue;
            //如果超出每周天数(一周$weedDay天)或所要课程数(一天$dayClass节课)，则跳过
            for ($j = 1; $j <= $week; $j++) {
                // 第$j周，共$week周
                if ($class[$i]['classWeek'][$k][$j - 1] === "1") {
                    // 有课
                    $ClassBegin = $class[$i]['ClassSessions'][$k];
                    $ClassEnd = $class[$i]['ClassSessions'][$k] + $class[$i]['ContinuingSession'][$k] - 1;
                    while ($ClassEnd >= $ClassBegin) {
                        $status_temp[$j][$class[$i]['ClassDay'][$k]][$ClassEnd] = 'ok';
                        $ClassEnd--;
                    }
                }

            }
        }
    }
    for ($i = 1; $i <= $week; $i++) {
        //第$i周
        for ($j = 1; $j <= $weekDay; $j++) {
            //周$j
            for ($k = 1; $k <= $dayClass; $k++) {
                //一天$k节课
                if ($status_temp[$i][$j][$k] != 'ok')
                    array_push($status[$i][$j][$k], $xh);
            }
        }
    }

}

$myFile = fopen("../".$config['spare_json'], "w");
fwrite($myFile, Num2Name(json_encode($status)));
echo "更新成功！";
fclose($myFile);

Header("Location:../index.php");

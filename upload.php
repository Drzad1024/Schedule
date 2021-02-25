<?php
require './core/guard.php';
$conf['qqjump'] = 1;
if (strpos($_SERVER['HTTP_USER_AGENT'], 'QQ/') || strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false && $conf['qqjump'] == 1) {
    $siteurl = 'http://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
    guard($siteurl);
    exit;
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="./css/bootstrap.min.css">
    <title>科协排班系统-课表收集</title>
</head>
<body class="bg-light">
<div class="container">
    <div class="py-4 text-center">
        <img class="d-block mx-auto mb-3" src="./pic/Kx.png" alt="" width="72" height="72">
        <h2>课表收集</h2>
        <h6 class="text-center">此系统仅收集课程数据，用科协排班系统，不涉及个人账户信息。<br>
            我们将对您提供的信息严格保密，未经您的事先同意，我们不会向第三方披露或转让我们收集到的您的个人信息。
        </h6>
    </div>
    <hr class="mb-4">
    <?php
    if (isset($_POST['info']) && isset($_POST['name'])) {
        // 有数据了
        $data = json_decode($_POST['info'], true);
        $xh = $data['dateList'][0]['selectCourseList'][0]['id']['studentNumber'];
        // 过滤保留数字
        preg_match_all('/\d+/', $xh, $xharr);
        $xh = join('', $xharr[0]);
        if ($xh != "") {
            // 保存课表
            $myFile = fopen("data/" . $xh . ".json", "w");
            fwrite($myFile, $_POST['info']);
            fclose($myFile);

            // 增加统计
            require_once 'core/statis.php';
            statisAdd($_POST['name']);

            $name2numFile = "data/name2num.txt";
            $fp = fopen($name2numFile, "a");
            fwrite($fp, $_POST['name'] . '|' . $xh . "\r\n");
            fclose($fp);

            $notice = htmlspecialchars($_POST['name']) . "提交成功！感谢！";
            sleep(3);
            Header("location:core/update.php");
        } else {
            $notice = "提交失败！请检查课表格式重新再试！" . '<a href="upload.php">点击返回</a>';
        }
        echo '<p class="lead text-center">' . $notice . '</p>';
    } else {
        ?>
        <p>操作方法：<br>
            1.点击跳转教务系统官网：<a href="http://jwxs.tjpu.edu.cn/" target="_blank">http://jwxs.tjpu.edu.cn</a> 并登录<br>
            2.点击跳转：<a href="http://jwxs.tjpu.edu.cn/student/courseSelect/thisSemesterCurriculum/callback"
                      target="_blank">查询课表信息</a>
            <a style="color: #e83e8c;">复制所有内容</a>到下方第二个格子中<br>
        </p>
        <hr class="mb-4">
        <form class="needs-validation" method="post" action="upload.php">
            <div class="mb-3">
                <label for="name">姓名</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="" value="">
            </div>
            <div class="mb-3">
                <label for="section">部门</label>
                <select class="custom-select d-block w-100" id="section" name="section">
                    <option value="">选择一个部门...</option>
                    <option value="1" selected>科协创新竞赛部</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="FormControlTextarea1">课表信息(复制到这里)</label>
                <textarea class="form-control" name="info" id="FormControlTextarea1" rows="6"></textarea>
                <small class="form-text text-muted">
                    开头为 {"allUnits":00.00,"xkxx":[{"00000000_01"...
                </small>
            </div>
            <hr class="mb-4">
            <button class="btn btn-primary btn-lg btn-block mb-4" type="submit">提交</button>
        </form>
        <?php
    }
    ?>
</div>
</body>
</html>
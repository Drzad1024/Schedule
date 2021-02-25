<?php
//echo getenv("HTTP_REFERER");
//exit();
if (strstr(getenv("HTTP_REFERER"), "m/m.php") !== false) {
    // 跳转回来的
    $refer = false;
} else {
    $refer = true;
}
if (empty($_REQUEST['password']) && $refer) {
    header("Location: index.php");
    exit();
}
// 管理用户信息，姓名为key，密码为value
$admin = ['丁振宇' => '20020407', '范卓娅部长' => '123456', '韩中举部长' => 'Tjgd666', '晏蕊部长' => 'hzj12138'];
if (!in_array($_REQUEST['password'], $admin) && $refer) {
    header("Location: index.php");
    exit();
}
require_once '../core/func.php';
$name = array_search($_REQUEST['password'], $admin);

$config = getConfig(true); //获取数组
$statis = getStatis(true);

$weekendSwitch = ($config['weekendSwitch'] == 'on') ? 'checked' : '';
$skipDate = json_decode($config['skipDate'], true);
$confirm = $statis['confirm'];


?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="shortcut icon" href="../favicon.ico">
    <script src="../js/jquery-3.4.1.min.js"></script>
    <title>科协排班系统-管理页面</title>
</head>
<body class="bg-light">
<div class="container">
    <div class="my-4 text-center">
        <img class="mx-auto mb-3" src="../pic/Kx.png" alt="科协" style="width:72px;">
        <h2>管理页面</h2>
        <?php
        if ($refer) {
            echo '<h6 class="text-center pb-2">当前管理用户为：<kbd>' . $name . '</kbd></h6>';
        } else {
            // 跳转回来的
            echo '<h6 class="text-center pb-2">修改成功，<a href="../core/update.php">点击更新</a></h6>';
        }
        ?>

    </div>
    <div class="d-flex justify-content-center">
        <form action="../core/config.php" method="get">
            <div class="">
                <div>跳过值班日期：</div>
                <div>
                    <?php
                    if (!empty($skipDate)) {
                        $times = 0;
                        foreach ($skipDate as $value) {
                            $times++;
                            echo '<a href="#" class="skip badge badge-secondary">' . $value . "</a>\r\n";
                            if ($times == 4) echo '<br>';
                        }
                    }
                    ?>
                </div>
            </div>
            <hr>
            <div class="custom-control custom-switch d-flex justify-content-center">
                <input type="checkbox" class="custom-control-input" id="weekendSwitch" name="weekendSwitch"
                       disabled <?php echo $weekendSwitch; ?>>
                <label class="custom-control-label" for="weekendSwitch">双休日值班<br><small>默认关闭为工作日值班</small></label>

            </div>
            <hr>
            <div class="form-group row">
                <label for="dayClass" class="col-auto text-center px-0 col-form-label">每天值班节数</label>
                <div class="col">
                    <input type="text" class="text-center form-control" id="dayClass" name="dayClass"
                           value="<?php echo $config['dayClass']; ?>">
                    <small id="dayClassHelp" class="form-text text-muted">请输入小节数(2,4,6,8,10)</small>
                </div>
            </div>
            <hr>
            <div class="form-group row">
                <label for="firstDay" class="col-auto text-center px-0 col-form-label">学期开始日期</label>
                <div class="col">
                    <input type="date" class="text-center form-control" id="firstDay" name="firstDay"
                           value="<?php echo $config['firstDay'] ?>">
                </div>
            </div>
            <div class="form-group row">
                <label for="week" class="col-auto text-center px-0 col-form-label">学期持续周数</label>
                <div class="col">
                    <input type="text" class="text-center form-control" id="week" name="week"
                           value="<?php echo $config['week'] ?>">
                </div>
            </div>
            <hr>
            <div class="form-group row">
                <label for="workBegin" class="col-auto text-center px-0 col-form-label">值班开始日期</label>
                <div class="col">
                    <input type="date" class="text-center form-control" id="workBegin" name="workBegin"
                           value="<?php echo $config['workBegin'] ?>">
                    <div class="invalid-feedback">不能早于学期开始日期</div>
                </div>
            </div>
            <div class="form-group row">
                <label for="workEnd" class="col-auto text-center px-0 col-form-label">值班结束日期</label>
                <div class="col">
                    <input type="date" class="text-center form-control" id="workEnd" name="workEnd"
                           value="<?php echo $config['workEnd'] ?>">
                    <small id="workEndHelp" class="form-text text-muted"></small>
                </div>
            </div>
            <div class="form-group row d-flex justify-content-around">
                <button type="submit" class="btn btn-primary mb-2">确认</button>
                <button type="reset" class="btn btn-outline-primary mb-2">重置</button>
            </div>
            <hr>
            <div>
                <div class="my-1">已确定值班计划周：</div>
                <div>
                    <?php
                    if (!empty($confirm)) {
                        $times = 0;
                        foreach ($confirm as $key => $value) {
                            $times++;
                            echo '<a href="#" class="confirm badge badge-secondary p-2 my-1">' . $key . "</a>\r\n";
                            if ($times == 10) echo '<br>';
                        }
                    }
                    ?>
                </div>
            </div>
            <hr>
            <div class="d-flex justify-content-around mb-4">
                <span id="restore" class="btn btn-danger mb-2">清空所有已定计划和统计信息</span>
            </div>
        </form>
    </div>
</div>
<script>
    $(function () {
        $("#workBegin").bind('input propertychange', function () {
            var firstDay = $("#firstDay")[0].valueAsNumber / 1000; //以秒为单位的时间戳
            var workBegin = $("#workBegin")[0].valueAsNumber / 1000; //以秒为单位的时间戳
            if (workBegin < firstDay) {
                $("#workBegin").addClass("is-invalid");
            } else {
                $("#workBegin").addClass("is-valid");
                $("#workBegin").removeClass("is-invalid");
            }
        });
        $("#workEnd").bind('input propertychange', function () {
            workEndHelp();
        });
        $("#workEnd").bind('input propertychange', function () {
            workEndHelp();
        });

        function workEndHelp() {
            var firstDay = $("#firstDay")[0].valueAsNumber / 1000; //以秒为单位的时间戳
            var workEnd = $("#workEnd")[0].valueAsNumber / 1000; //以秒为单位的时间戳
            var continuingWeek = parseInt((workEnd - firstDay) / 604800) + 1; //向下取整 得到周数
            if (continuingWeek > 0 || continuingWeek < 25) {
                $("#workEndHelp").text('该日为学期第' + continuingWeek + '周');
            } else {
                $("#workEndHelp").text('放假时');
            }
        }

        $("a.skip").bind('click', function () {
            var _self = this;
            $.get("../core/skip.php?con=del&time=" + $(this).text(), function (data) {
                if (data == 'ok') {
                    $(_self).remove();
                } else {
                    alert("删除失败");
                }
            });
        });
        $("a.confirm").bind('click', function () {
            var _self = this;
            $.get("../core/statis.php?con=del&week=" + $(this).text(), function (data) {
                if (data == 'ok') {
                    $(_self).remove();
                } else {
                    alert("删除失败");
                }
            });
        });
        $("#restore").bind('click', function () {
            if (confirm("确定清空所有已定计划和统计信息，每个人的值班次数将会重置为0？")) {
                $.get("../core/skip.php?con=reset", function (data) {
                    if (data = 'ok') {
                        alert("清空成功");
                    } else {
                        alert("清空失败");
                    }
                });
            }
        });
        workEndHelp();
    });
</script>
</body>
</html>
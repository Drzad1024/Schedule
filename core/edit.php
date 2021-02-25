<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>科协排班系统-空闲修改</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <script src="../js/jquery-3.4.1.min.js"></script>
    <script src="../js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-light">
<div class="container" style="max-width: 400px">
<?php
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

function deleteClass(&$json_string, $name, $weeks, $week, $section)
{
    $result = Array();
    for ($i = 0; $i < count($section); $i++) {
        $sections = 2 * $section[$i] - 1; //第1大节对应1、2节
        if (in_array($name, $json_string[$weeks][$week][$sections]) != false) {
            //存在 进行剔除操作
            $json_string[$weeks][$week][$sections] = array_diff($json_string[$weeks][$week][$sections], array($name));
            $json_string[$weeks][$week][$sections + 1] = array_diff($json_string[$weeks][$week][$sections + 1], array($name));
//            $result["suc"][] = "第{$weeks}周 星期{$week} 第{$section[$i]}大节课";
              echo '<li class="list-group-item">'."第{$weeks}周 星期{$week} 第{$section[$i]}大节课不会再安排值班</li>\r\n";
        }else{
//            $result["fail"][] = "第{$weeks}周 星期{$week} 第{$section[$i]}大节课";
            echo '<li class="list-group-item">'."第{$weeks}周 星期{$week} 第{$section[$i]}大节课已有课程</li>\r\n";
        }
    }
    return json_encode($result, JSON_UNESCAPED_UNICODE);
}

if (isset($_GET) && isset($_GET['xh']) && $_GET['xh'] != "") {
    $xh = $_GET['xh'];
    $name = Num2Name($xh);
    if ($name == $xh) exit();
?>
    <div class="my-4 text-center">
        <img class="mx-auto mb-3" src="../pic/Kx.png" alt="科协" style="width:72px;">
        <h2>空闲修改</h2>
        <h6 class="text-center">当前修改用户为：<kbd><?php echo $name; ?></kbd></h6>
    </div>
<?php
    if (isset($_GET['weeks']) && isset($_GET['week']) && isset($_GET['section'])) {
        // 修改页
        $weeks = $_GET['weeks']; //周 or 周[]
        $week = $_GET['week']; //星期
        $section = $_GET['section']; //节[]
        $jsonFile = "../Kx.json";
        $json_string = json_decode(file_get_contents($jsonFile), true);// 把JSON字符串转成PHP数组
?>
    <div class="card">
        <ul class="list-group list-group-flush text-center">
<?php
        if(is_array($weeks)==true){
            // 周[]
            for($i=0;$i<count($weeks);$i++){
                deleteClass($json_string, $name, $weeks[$i], $week, $section);
            }
        }else{
            // 周
            deleteClass($json_string, $name, $weeks, $week, $section);
        }
        $json_strings = json_encode($json_string, JSON_UNESCAPED_UNICODE);
        file_put_contents($jsonFile, $json_strings); //写入
?>
            <li class="list-group-item"><a href="../">返回首页</a></li>
        </ul>
    </div>
<?php
    }else{
?>
    <nav class="nav nav-justified nav-pills my-2">
        <a class="nav-item nav-link active" data-toggle="pill" href="#single">单周</a>
        <a class="nav-item nav-link" data-toggle="pill" href="#plus">多周</a>
    </nav>
    <div class="tab-content">
        <div id="plus" class="tab-pane fade">
            <form class="px-2" method="get" action="?">
                <input class="d-none" name="xh" value="2010910208"/>
                <div class="form-row justify-content-center">
                    <div id="weekplus" class="form-group">
                        <label>选择周数</label>
                        <div class="w-100"></div>
                        <div class="btn-group table-responsive" data-toggle="buttons">
                            <?php
                            $jsonFile = "../Kx.json";
                            $json_string = json_decode(file_get_contents($jsonFile), true);// 把JSON字符串转成PHP数组
                            for($i=1; $i<=$json_string['info']['week']; $i++){
                                $text = ($i<10)? "0".$i: $i;
                                echo '<label class="btn btn-outline-primary p-2"><input type="checkbox" name="weeks[]" value="'.$i.'"> '.$text.'</label>'."\r\n";
                                if ($i % 6 == 0 && $i < $json_string['info']['week']) {
                                    echo "</div>\r\n" . '<div class="w-100"></div>' . "\r\n" . '<div class="btn-group table-responsive" data-toggle="buttons">' . "\r\n";
                                }
                            }
                            ?>
                        </div>
                    </div>
                    <div class="w-100"></div>
                    <div class="form-group">
                        <label class="mr-3">选择星期</label>
                        <div class="w-100"></div>
                        <div class="btn-group" data-toggle="buttons">
                            <label class="radio btn btn-outline-primary active">
                                <input type="radio" name="week" value="1" checked><br><span class="week">周一</span>
                            </label>
                            <label class="radio btn btn-outline-primary">
                                <input type="radio" name="week" value="2"><br><span class="week">周二</span>
                            </label>
                            <label class="radio btn btn-outline-primary">
                                <input type="radio" name="week" value="3"><br><span class="week">周三</span>
                            </label>
                            <label class="radio btn btn-outline-primary">
                                <input type="radio" name="week" value="4"><br><span class="week">周四</span>
                            </label>
                            <label class="radio btn btn-outline-primary">
                                <input type="radio" name="week" value="5"><br><span class="week">周五</span>
                            </label>

                        </div>
                    </div>
                    <div class="w-100"></div>
                    <div class="form-group">
                        <label class="mr-3">选择节数</label>
                        <div class="w-100"></div>
                        <div class="btn-group" data-toggle="buttons">
                            <label class="section btn btn-outline-primary rounded-lg mr-1">
                                <input type="checkbox" name="section[]" value="1"> 第一大节<br>
                            </label>
                            <label class="section btn btn-outline-primary rounded-lg ml-1">
                                <input type="checkbox" name="section[]" value="2"> 第二大节<br>
                            </label>
                        </div>
                        <div class="w-100"></div>
                        <div class="btn-group" data-toggle="buttons">
                            <label class="section btn btn-outline-primary rounded-lg mr-1">
                                <input type="checkbox" name="section[]" value="3"> 第三大节<br>
                            </label>
                            <label class="section btn btn-outline-primary rounded-lg ml-1">
                                <input type="checkbox" name="section[]" value="4"> 第四大节<br>
                            </label>
                        </div>
                    </div>
                    <div class="w-100"></div>
                    <div class="form-group">
                        <button class="btn btn-outline-primary" type="submit">提交</button>
                    </div>
                </div>
            </form>
        </div>
        <div id="single" class="tab-pane fade show active">
            <form class="px-2" method="get" action="?">
                <input class="d-none" name="xh" value="<?php echo $xh; ?>"/>
                <div class="form-row justify-content-center">
                    <div class="form-group">
                        <label class="mr-3">选择周数</label>
                        <select id="weeks" name="weeks" class="custom-select">
                        </select>
                    </div>
                    <div class="w-100"></div>
                    <div class="form-group">
                        <label class="mr-3">选择星期</label>
                        <div class="w-100"></div>
                        <div class="btn-group" data-toggle="buttons">
                            <label class="radio btn btn-outline-primary active">
                                <input type="radio" name="week" value="1" checked><br><span class="week">周一<br></span>
                            </label>
                            <label class="radio btn btn-outline-primary">
                                <input type="radio" name="week" value="2"><br><span class="week">周二<br></span>
                            </label>
                            <label class="radio btn btn-outline-primary">
                                <input type="radio" name="week" value="3"><br><span class="week">周三<br></span>
                            </label>
                            <label class="radio btn btn-outline-primary">
                                <input type="radio" name="week" value="4"><br><span class="week">周四<br></span>
                            </label>
                            <label class="radio btn btn-outline-primary">
                                <input type="radio" name="week" value="5"><br><span class="week">周五<br> </span>
                            </label>

                        </div>
                    </div>
                    <div class="w-100"></div>
                    <div class="form-group">
                        <label class="mr-3">选择节数</label>
                        <div class="w-100"></div>
                        <div class="btn-group" data-toggle="buttons">
                            <label class="section btn btn-outline-primary rounded-lg mr-1">
                                <input type="checkbox" name="section[]" value="1"> 第一大节<br>
                            </label>
                            <label class="section btn btn-outline-primary rounded-lg ml-1">
                                <input type="checkbox" name="section[]" value="2"> 第二大节<br>
                            </label>
                        </div>
                        <div class="w-100"></div>
                        <div class="btn-group" data-toggle="buttons">
                            <label class="section btn btn-outline-primary rounded-lg mr-1">
                                <input type="checkbox" name="section[]" value="3"> 第三大节<br>
                            </label>
                            <label class="section btn btn-outline-primary rounded-lg ml-1">
                                <input type="checkbox" name="section[]" value="4"> 第四大节<br>
                            </label>
                        </div>
                    </div>
                    <div class="w-100"></div>
                    <div class="form-group">
                        <button class="btn btn-outline-primary" type="submit">提交</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script>
        $(function(){
            var data;
            var today = new Date();//获取当前时间
            var year = today.getFullYear();//获取当前的年份
            var month = today.getMonth() + 1;//获取当前月
            var day = today.getDate();//获取当前日
            var beginDay = "2021-03-01"; //开学第一天
            var firstYear = parseInt(beginDay.replace(/(\d{4})-(\d{1,2})-(\d{1,2})/g, "$1"));
            var firstMonth = parseInt(beginDay.replace(/(\d{4})-(\d{1,2})-(\d{1,2})/g, "$2"));
            var firstDay = parseInt(beginDay.replace(/(\d{4})-(\d{1,2})-(\d{1,2})/g, "$3"));
            var weekDay = 5; //一周5天
            getJson();
            showWeek(1);
            for (var i = 1; i <= 18; i++) {
                getWeeks(i);
            }
            $('#single .radio').click(function (e) {
                if ($(e.target).is('input')) {
                    return;
                }
                $("input[name=week]").attr("checked", false);
                $(this).children("input").eq(0).attr("checked", true);
                getJson();
            });
            $("#weeks").change(function () {
                showWeek($("#weeks").val());
                getJson();
            });

            /**
             * 取data数据
             */
            function getJson(){
                $.get("../Kx.json?" + new Date().valueOf(), function (jsonData) {
                    data = jsonData;
                    getSection();
                });
            }
            /**
             * 获取该年该月天数
             * @param year
             * @param month
             * @returns {number}
             */
            function getAllDay(year, month) {
                var allday;
                if (month != 2) {
                    if (month == 4 || month == 6 || month == 9 || month == 11)//判断是否是相同天数的几个月，二月除外
                        allday = 30;
                    else
                        allday = 31;
                } else {
                    if ((year % 4 == 0 && year % 100 != 0) || (year % 400 == 0))//判断是否是闰年，进行相应的改变
                        allday = 29;
                    else
                        allday = 28;
                }
                return allday;
            }
            /**
             * 显示 选择星期
             */
            function showWeek(recWeek) {
                var allday = getAllDay(firstYear, firstMonth);
                var recDay = (recWeek - 1) * 7 + firstDay; // 当周第一天
                var recMonth = firstMonth;
                var recYear = firstYear;
                while (recDay > allday) {
                    if (recMonth == 12) {
                        recMonth = 1;
                        recYear++;
                    } else {
                        recMonth++;
                    }
                    recDay -= allday;
                    allday = getAllDay(recYear, recMonth);
                }
                //显示
                for (i = 0; i < weekDay; i++) {
                    if (recDay > allday) {
                        recDay -= allday;
                        if (recMonth == 12) {
                            recMonth = 1;
                            recYear++;
                        } else {
                            recMonth++;
                        }
                        allday = getAllDay(recYear, recMonth);
                    }
                    $('#single .week')[i].innerText = "周" + "一二三四五".charAt(i) + "\r\n" + recMonth + "." + recDay;
                    recDay++;
                }
            }

            /**
             * 获取周数（日期）
             */
            function getWeeks(recWeek) {
                var allday = getAllDay(firstYear, firstMonth);
                var recDay = (recWeek - 1) * 7 + firstDay; // 当周第一天
                var recMonth = firstMonth;
                var recYear = firstYear;
                var lastMonth, lastDay;
                var checked = "";
                while (recDay > allday) {
                    if (recMonth == 12) {
                        recMonth = 1;
                        recYear++;
                    } else {
                        recMonth++;
                    }
                    recDay -= allday;
                    allday = getAllDay(recYear, recMonth);
                }
                //显示
                lastDay = recDay + 4;
                lastMonth = recMonth;
                if (lastDay > allday) {
                    if (lastMonth == 12) {
                        lastMonth = 1;
                    } else {
                        lastMonth++;
                    }
                    lastDay -= allday;
                }
                if (recWeek == 1) {
                    checked = " checked";
                }
                $("#single #weeks").append("<option value=\"" + recWeek + "\"" + checked + ">第" + recWeek + "周（" + recMonth + "月" + recDay +
                    "日——" + lastMonth + "月" + lastDay + "日）</option>");

            }
            /**
             * 显示 选择节数
             */
            function getSection() {
                $('#single .section input[type=checkbox]').each(function () {
                    this.checked = false
                    this.disabled = false
                });
                $('#single .section').removeClass("active disabled btn-outline-danger");
                $('#single .section').addClass("btn-outline-primary");
                for (var i = 1; i <= 4; i++) {
                    if (data[$("#single #weeks").val()][$("input[name=week]:checked").val()][2 * i - 1].indexOf("<?php echo $name ?>") == -1) {
                        $('#single .section').eq(i - 1).removeClass("btn-outline-primary");
                        $('#single .section').eq(i - 1).addClass("disabled btn-outline-danger");
                        $("#single input[type='checkbox']").eq(i - 1).attr("disabled", "disabled");
                    }
                }
            }
        })
    </script>
<?php
    }
} else {
?>
    <div class="my-4 text-center">
        <img class="mx-auto mb-3" src="../Kx.png" alt="科协" style="width:72px;">
        <h2>空闲修改</h2>
    </div>
    <div class="form-group row">
        <label for="xh" class="col-2 col-form-label pr-0">学号</label>
        <div class="col-10">
            <input class="form-control" name="xh" id="xh" type="text">
            <div id="feedback"></div>
        </div>
    </div>
    <button id="check" class="btn btn-primary btn-block" type="submit">提交</button>
    <script>
        $(function () {
            $("input[name=xh]").bind('input propertychange', function() {
                console.log("change");
                if ($("input[name=xh]").val().length == 10) {
                    $.ajax({
                        url: "check.php",
                        data: {xh: $("input[name=xh]").val()},
                        success: function (resule) {
                            console.log(resule);
                            if(resule=="exist"){
                                onValid("学号正确");
                            }else{
                                onInvalid("学号错误，请检查是否提交过课表信息");
                            }
                        }
                    });
                }else{
                    $("#xh").removeClass("is-invalid");
                    $("#xh").removeClass("is-valid");
                    $("#feedback").removeClass("invalid-feedback");
                    $("#feedback").removeClass("valid-feedback");
                    $("#feedback").text("");
                }
            });
            $("#check").click(function(){
                if($("#xh").hasClass("is-valid")){
                    // 认证成功
                    window.location.href = "edit.php?xh="+$("input[name=xh]").val();
                }
            });
            $(document).keyup(function(event){
                if(event.keyCode == 13){
                    $("#check").trigger("click");
                }
            });
            /**
             * 验证成功
             * @param text 提示文本
             */
            function onValid(text){
                $("#xh").removeClass("is-invalid");
                $("#xh").addClass("is-valid");
                $("#feedback").removeClass("invalid-feedback");
                $("#feedback").addClass("valid-feedback");
                $("#feedback").text(text);
            }
            /**
             * 验证失败
             * @param text 提示文本
             */
            function onInvalid(text){
                $("#xh").addClass("is-invalid");
                $("#xh").removeClass("is-valid");
                $("#feedback").addClass("invalid-feedback");
                $("#feedback").removeClass("valid-feedback");
                $("#feedback").text(text);
            }
        });
    </script>
<?php
}
?>

</div>
</body>
</html>

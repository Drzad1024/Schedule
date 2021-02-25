$(function () {
    var Class = new Array();
    var classSchedule = new Array(), elected = new Array();
    var countNum = new Array(), statis = {};
    var week, dayClass, weekDay; //学期一共week周，一周一共weekDay天，一天一共dayClass节课
    var RecWeekPage = 1, data;
    var skipDate = new Array(), workBegin, workEnd, firstDay; //config.json
    var today = new Date();//获取当前时间

    function getStatis() {
        $.ajax({
            type: "get",
            url: "statis.json?" + new Date().valueOf(),
            async: false,
            dataType: 'json',
            success: function (res) {
                statis = res;
            }
        });
    }

    function getConfig() {
        $.ajax({
            type: "get",
            url: "m/config.json?" + new Date().valueOf(),
            async: false,
            dataType: 'json',
            success: function (res) {
                workBegin = res.workBegin, workEnd = res.workEnd;
                week = res.week, dayClass = res.dayClass, weekDay = (res.weekendSwitch == 'on') ? 7 : 5, firstDay = res.firstDay;
                skipDate = JSON.parse(res.skipDate);
            }
        });
    }

    $.get("Kx.json?" + new Date().valueOf(), function (jsonData) {
        data = jsonData;
        InitialPage();
    });

    function InitialClass(RecWeek) {
        var i, j, count = new Array(), c = 0;
        Class = new Array();
        classSchedule = new Array(), elected = new Array();
        a = new Array(), countNum = new Array();
        for (i = 0; i < 20; i++) count[i] = 0;
        for (j = 1; j < dayClass; j++) {
            // 第j节课
            for (i = 1; i <= weekDay; i++) {
                // 周i
                if (j % 2 == 1) {
                    // 头课
                    if (JSON.stringify(data[RecWeek][i][j]) == JSON.stringify(data[RecWeek][i][j + 1])) {
                        // 头尾相等
                        Class.push(data[RecWeek][i][j]);
                        count[c++] = data[RecWeek][i][j].length;
                    } else {
                        console.log("出现单独课程了！！")
                        return false;
                    }
                }

            }
        }

        // 对count从小到大排序并返回排序后的数组下标 为countNum
        let tmp;
        for (i = 0; i < count.length; i++) {
            countNum[i] = i;
        }
        for (i = 0; i < count.length; i++) {
            for (j = 0; j < count.length - 1; j++) {
                if (count[j] > count[j + 1]) {
                    tmp = count[j];
                    count[j] = count[j + 1];
                    count[j + 1] = tmp;
                    tmp = countNum[j];
                    countNum[j] = countNum[j + 1];
                    countNum[j + 1] = tmp;
                }
            }
        }

    }

    /**
     * 从人数少的开始排班
     * @param position 当前位置
     */
    function Recur(position, Schdule = []) {
        let j, jj = new Array();
        Num = countNum[position]; // 当前下标
        if ($("th.titleDate").eq(Num % 5).children("a").length == 0) {
            Schdule[Num] = "/";
            if (position == countNum.length - 1) {
                //最后一个了
                // console.log("find a res:" + Schdule);
                return Schdule;
            } else {
                // skip
                return Recur(position + 1, Schdule);
            }
        }
        if (position < countNum.length) {
            Num = countNum[position]; // 当前下标
            if (Class[Num].length == 0) {
                // 所有人都没时间
                Schdule[Num] = "";
                return Recur(position + 1, Schdule);
            } else {
                while (true) {
                    j = getRandom(Class[Num].length);
                    if (jj.length == Class[Num].length) {
                        Schdule[Num] = "";
                        if (position == countNum.length - 1) {
                            //最后一个了
                            return Schdule;
                        } else {
                            // 继续进行
                            return Recur(position + 1, Schdule);
                        }
                        break;
                    } else if (jj.indexOf(j) != -1) {
                        // 抽过了
                        continue;
                    }
                    jj.push(j);

                    var Name = Class[Num][j];
                    if (elected.indexOf(Name) === -1) {
                        // 未被分配
                        Schdule[Num] = Name;
                        elected.push(Name); // 置入
                        statis[Name] += 1;
                        // console.log("elected:" + elected + " " + elected.length + "人");
                        if (position == countNum.length - 1) {
                            //最后一个了
                            // console.log("find a res:" + Schdule);
                            return Schdule;
                        } else {
                            // 继续进行
                            return Recur(position + 1, Schdule);
                        }
                        Schdule[Num] = "";
                        elected.pop(); // 删除最后一个元素
                        statis[Name] -= 1;
                    }
                }
            }
        }
        return false;
    }

    /**
     * 取随机数 [0, numMax)
     * @param numMax 最大数(不包含)
     * @returns {number}
     */
    function getRandom(numMax) {
        var num = Math.random();
        num *= numMax;
        num = Math.floor(num);
        return num;
    }

    /**
     * 更新页面：时间、安排表、统计
     */
    function InitialPage() {
        getConfig();
        getStatis();
        RecWeekPage = getDate();
        if($("#week").text()=='假期愉快！'){
            $("#refresh").hide();
            $("#confirm").hide();
        }else{
            $("#refresh").show();
            $("#confirm").show();
        }
        if (RecWeekPage > 0 && RecWeekPage <= week) {
            if (statis['confirm'] != undefined && statis['confirm'][RecWeekPage] != undefined) {
                // 结果已保存过
                refreshButton(false); //禁用刷新按钮
                ClassArray = JSON.parse(statis['confirm'][RecWeekPage]);
            } else {
                refreshButton(true);
                InitialClass(RecWeekPage);
                ClassArray = new Array();
                ClassArray = Recur(0, []);
            }
        }else{
            ClassArray=[];
        }
        ShowClass(ClassArray);
        showStatis();
    }

    /**
     * 更新按钮显示
     * @param stat 当前状态 默认true为可用， false为不可用
     */
    function refreshButton(stat) {
        if (stat) {
            $('#confirm').text("确定");
            $('#confirm').attr("disabled", false);
            $('#refresh').attr("disabled", false);
            $('#refresh').show();
        } else {
            $('#confirm').text("已确定，禁止更改");
            $('#confirm').attr("disabled", true);
            $('#refresh').attr("disabled", true);
            $('#refresh').hide();
        }
    }

    /**
     * 显示统计结果
     */
    function showStatis() {
        $(".statis-col").remove(); // 清除统计
        for (name in statis) {
            if (name == 'confirm') continue;
            $('.statis').append('<div class="statis-col col-md-2 col-sm-4 col-6 text-center">' +
                '<p class="px-auto rounded-pill py-1 table-bordered">' +
                name + '<br>' + statis[name] + '次</p></div>');
        }
    }

    /**
     * 打印课程到对应表格
     * 若arr长度小于8，则为按列输出，反之按行输出
     * @param arr
     * @constructor
     */
    function ShowClass(arr) {
        let j, jj;
        for (j = 0; j < dayClass * weekDay / 2; j++) {
            $('.content')[j].innerText = "";
            $('.content')[j].className = "content";
        }
        if (arr != false && arr.length != undefined) {
            for (j = 0; j < arr.length; j++) {
                var color = "content rounded-lg align-middle";
                switch (j % 5) {
                    case 0 :
                        color += " table-primary";
                        break;
                    case 1:
                        color += " table-success";
                        break;
                    case 2:
                        color += " table-warning";
                        break;
                    case 3:
                        color += " table-info";
                        break;
                    case 4:
                        color += " table-danger";
                        break;
                }
                if (arr.length < 8) {
                    // 按列输出
                    var arr2 = [];
                    arr2 = arr[j];
                    for (jj = 0; jj < arr2.length; jj++) {
                        $('.content')[j + jj * weekDay].innerText = arr2[jj];
                        $('.content')[j + jj * weekDay].className = color;
                    }
                } else {
                    // 按行输出
                    $('.content')[j].innerText = arr[j];
                    $('.content')[j].className = color;
                }
            }
        }
    }

    /**
     * 显示表格头时间
     * @param recWeek
     */
    function getDate() {
        var FirstDayInWeek = new Date(); //该周第一天
        FirstDayInWeek.setTime(today.getTime());
        var LastDayInWeek = new Date(); //该周最后一天
        LastDayInWeek.setTime(FirstDayInWeek.getTime());

        var firstDate = firstDay.substring(0, 19); //格式化时间字符串 值班开始日
        firstDate = firstDate.replace(/-/g, '/');
        firstDate = new Date(firstDate);
        var weekFirstDay = firstDate.getDate();
        var weekHelp; //显示周数或提示假日

        FirstDayInWeek.setDate(today.getDate() - (today.getDay()>0? today.getDay(): 7)+1);
        LastDayInWeek.setDate(FirstDayInWeek.getDate() + weekDay - 1);
        today.setTime(FirstDayInWeek.getTime());
        var firstDayPlusWeek = new Date(firstDay);
        firstDayPlusWeek.setDate(firstDayPlusWeek.getDate()+week-1+weekDay-1);
        if (LastDayInWeek.getTime() / 1000 < String2Date(firstDay) || FirstDayInWeek.getTime() / 1000 > firstDayPlusWeek.getTime()/1000 ) {
            // 周五（周日）在开学前，周一在 开学+学期周数 后
            weekHelp = "假期愉快！";
            continuingWeek = -1;
        } else {
            var todayTimestamp = today.getTime() / 1000;
            var workBeginTimestamp = String2Date(workBegin);
            var firstDayTimestamp = String2Date(firstDay);
            var continuingWeek = parseInt((todayTimestamp - firstDayTimestamp) / 604800) + 1;
            weekHelp = "第<span class=\"badge badge-primary\">" + continuingWeek + "</span>周值班安排";
        }
        $("#week")[0].innerHTML = weekHelp;

        var DayInWeek = FirstDayInWeek.getDate(); // 显示 此周第一天

        //显示
        for (var i = 0; i < weekDay; i++) {
            var year = FirstDayInWeek.getFullYear();
            var month = FirstDayInWeek.getMonth() + 1;
            var day = FirstDayInWeek.getDate();
            var time = year + "-" + ((month < 10) ? "0" + month : month) + "-" + ((day < 10) ? "0" + day : day); //yyyy-MM-dd
            var timestamp = String2Date(time); //时间戳
            var titleDateText;
            if (jQuery.inArray(time, skipDate) > -1 || timestamp < String2Date(workBegin) || timestamp > String2Date(workEnd)) {
                // 应跳过
                titleDateText = "周" + "一二三四五".charAt(i) + "<br>" + month + "月" + day + "日<br><small>不值班</small>";
            } else {
                titleDateText = "<a class='titleDate' time=" + time + " title='点击删除'>周" + "一二三四五".charAt(i) + "<br>" + month + "月" + day + "日</a>";
            }
            $('th.titleDate')[i].innerHTML = titleDateText;
            FirstDayInWeek.setDate(FirstDayInWeek.getDate() + 1);
        }

        return continuingWeek;
    }

    /**
     * 字符串转时间戳
     * @param date 格式 2021-01-01
     * @returns {number}输出为秒为单位的时间戳
     */
    function String2Date(date) {
        date = date.substring(0, 19);
        date = date.replace(/-/g, '/');
        var timestamp = new Date(date).getTime();
        return timestamp / 1000;
    }

    $("th.titleDate").click(function () {
        var time = $(this).children("a").attr("time"), _self = this;
        if (time == undefined) {
            return;
        }
        if (confirm("确定删除" + time + "排班？")) {
            $.get("core/skip.php?con=add&time=" + time, function (data) {
                if (data == 'ok') {
                    alert("删除成功");
                    InitialPage();
                } else {
                    alert("删除失败");
                }
            });
        }
    });
    //上周
    $("#lastWeek").bind('click', function () {
        today.setDate(today.getDate() - 7);
        InitialPage();
    });
    //下周
    $("#nextWeek").bind('click', function () {
        today.setDate(today.getDate() + 7);
        InitialPage();
    });
    //刷新
    $("#refresh").bind('click', function () {
        InitialPage();
    });
    //确定
    $("#confirm").bind('click', function () {
        var classSave = [];
        var month = RecWeekPage;
        for (var i = 0; i < weekDay; i++) {
            daySave = [];
            for (var j = 0; j < dayClass / 2; j++) {
                daySave.push(ClassArray[i + j * 5]);
            }
            classSave.push(daySave.slice());
        }
        refreshButton(false);
        $.post("core/statis.php?con=save", {week: month, plan: JSON.stringify(classSave)}, function (data) {
            //console.log(data);
            if (data == "ok") {
                alert("保存成功");
                getStatis();
                showStatis();
            } else {
                alert("保存失败！ " + data);
                refreshButton(true);
            }

        });
    });
    //修改课表
    $("#edit").bind('click', function () {
        window.location = "core/edit.php";
    });
    //管理页面
    $("#admin").bind('click', function () {
        window.location = "m/";
    });
})

/*
   周一 二  三   四   五
1   1   2   3   4   5
2   6   7   8   9   10
3   11  12  13  14  15
4   16  17  18  19  20
5   21  22  23  24  25
6   26  27  28  29  30
7   31  32  33  34  35
8   36  37  38  39  40
   周一 二  三   四   五
1   1   2   3   4   5
2   6   7   8   9   10
3   11  12  13  14  15
4   16  17  18  19  20
 */
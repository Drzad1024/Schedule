<?php
/**
 * 判断$_GET['xh']是否存在， 存在返回exist，不存在无返回值
 */
if (isset($_GET) && isset($_GET['xh']) && $_GET['xh'] != "") {
    $name2num_data = file_get_contents("../data/name2num.txt");
    if (strstr($name2num_data, $_GET['xh']) !== false) {
        echo "exist";
    }
}
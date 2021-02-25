<?php
require './core/guard.php';
$conf['qqjump'] = 1;
if (strpos($_SERVER['HTTP_USER_AGENT'], 'QQ/') || strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false && $conf['qqjump'] == 1) {
    $siteurl = 'http://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
    guard($siteurl);
    exit;
}
Header("Location:sc.html");

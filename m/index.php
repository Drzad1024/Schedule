<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="shortcut icon" href="../favicon.ico">
    <title>科协排班系统-管理页面</title>
</head>
<body class="bg-light">
<div class="container">
    <div class="my-4 text-center">
        <img class="mx-auto mb-3" src="../pic/Kx.png" alt="科协" style="width:72px;">
        <h2>管理登陆</h2>
    </div>
    <div class="d-flex justify-content-center">
        <form method="post" action="m.php">
            <div class="form-group row">
                <label for="password" class="col-auto text-center px-0 col-form-label">请输入密码</label>
                <div class="col">
                    <input type="password" class="text-center form-control" id="password" name="password">
                </div>
            </div>
            <div class="form-group row d-flex justify-content-around">
                <button type="submit" class="btn btn-primary mb-2">登陆</button>
                <a href="../" class="btn btn-outline-primary mb-2">返回首页</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>
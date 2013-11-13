<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>找回密码 - 1378棋牌网</title>
<style>
    body{background:#fff;font-family:verdana;}
    .wrap1378{width:580px;padding:0px 20px;font-size:14px;color:#555;margin:0 auto;border:1px solid #dedede;border-top:5px solid #1B8BEA;}
    .wrap1378 h1{text-align:right;padding:10px 0;margin:0;}
    .wrap1378 p{line-height: 24px;font-size:14px;}
    .wrap1378 .foot{padding:10px 0;margin:20px 0;border-top:1px solid #efefef;}
</style>
</head>
 
<body>
<div class="wrap1378">
    {% set url = user.getResetPasswordlink %}
    <h1><a href="http://www.1378.com" target="_blank"><img width="150" src="http://i.1378.com{{ App.assets.Url }}/images/user/logo.png" border=0 ></a></h1>
    <p>亲爱的用户，您好！<br>
    1378棋牌网已经收到您找回密码的请求，请点击以下链接地址，马上重置您的密码：<br>
    <a href="{{ url }}" target="_blank">{{ url }}</a><br><br>
    如果不能点击该链接地址，请复制并粘贴到浏览器的地址输入框。
    </p>
    <div class="foot"><a href="http://www.1378.com" target="_blank" style="text-decoration: none;"><font style="color:#1B8BEA">1378.com</font></a></p>
</div>
</body>
</html>
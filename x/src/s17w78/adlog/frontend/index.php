<?php
// redirect
function redirect($link) {
    ignore_user_abort(true);
    header("Location: ".$link);
    header("Connection: close");
    header("Content-Length: 0");
    flush();
}
//redirect to default url
function redirectToDefault(){
    // redirect to default address
    header('Location: http://www.1378.com',true,301);
    exit();
}
//redirect to default url
function redirectToWww($uri){
    // redirect to default address
    $new_uri = "http://www.1378.com".$uri;
    header("Location: $new_uri",true,301);
    exit();
}
function log_req($id) {
    // database connect
    $db_config = require(dirname(__file__)."/../dsn.php");
    $db = new PDO($db_config['connectionString'],$db_config['username'],$db_config['password']);

    // log request
    $ip = isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:'';
    $ua = isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:'';
    $refer = isset($_SERVER['HTTP_REFERER'])? $_SERVER['HTTP_REFERER']:'';
    $from = isset($_GET['f'])?$_GET['f']:'';
    $timestamp = time();

    $stmt = $db->prepare("INSERT INTO adlog(link_id,ip,ua,refer,l_from,click_time) VALUES(:link_id,:ip,:ua,:refer,:l_from,:click_time)");
    $stmt->execute(array(
	    ':link_id' => $id,
	    ':ip' => $ip,
	    ':ua' => $ua,
	    ':refer' => $refer,
	    ':l_from' => $from,
	    ':click_time' => $timestamp,
	));
}

function get_link($id) {
    if (empty($id)) {
	return false;
    }
    // database connect
    $db_config = require(dirname(__file__)."/../dsn.php");
    $db = new PDO($db_config['connectionString'],$db_config['username'],$db_config['password']);

    $stmt = $db->prepare("SELECT link FROM adlink WHERE id = ".intval($id));
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (empty($row)) {
	return false;
    } else {
	return $row['link'];
    }
}

//write cookie user from
$from = isset($_GET['f'])?$_GET['f']:'';
if(!empty($from)){
    setcookie("u_f",$from,0,"/",".1378.com");
}
$id = isset($_GET['id'])?$_GET['id']:'';
if(!empty($id)){
    setcookie("u_id",$id,0,"/",".1378.com");
}
//first redirect full link
$link = isset($_GET['l'])?urldecode($_GET['l']):"";
// check link 
$c = parse_url($link);


if (!empty($link)) {
    if(!$c || !isset($c['host']) || substr($c['host'], -(strlen(".1378.com"))) != ".1378.com"){
        redirectToDefault();
        exit(0);
    }else{
        redirect($link);
        if (!empty($_GET['id'])) {
            log_req(intval($_GET['id']));
        }
        exit(0);
    }
}
// attempt to get link from short link
$uri = $_SERVER['REQUEST_URI'];
$t = explode("/", $uri);
if(is_numeric($t[1])){
    $id = intval(array_pop($t));
    $link = get_link($id);
    setcookie("u_id",$id,0,"/",".1378.com");
    if ($link) {
        redirect($link);
        log_req($id);
        exit(0);
    }
}else{
    redirectToWww($uri);
}

<?php
$host = "gameserver";
$username = "web";
$password = "mhtx@gameserver";
$link = mssql_connect($host,$username,$password);
if(!$link){
	die();
}
$database = "GameUserDB";
mssql_select_db($database);

<?php
gc_enable();
$actionLogDb = array(
    'connectionString' => 'mysql:host=127.0.0.1;dbname=actionlog;charset=utf8',
    'username' => 'root',
    'password' => 'password',
);
$payDb = array(
    'connectionString' => 'mysql:host=127.0.0.1;dbname=mhback;charset=utf8',
    'username' => 'root',
    'password' => 'password',
);

$userDb = array(
    'connectionString' => 'mysql:host=127.0.0.1;dbname=i1378;charset=utf8',
    'username' => 'root',
    'password' => 'password',
);
// array(
//   '2013-07-28' => array(
//       'register' => 2,
//       'pay' => 3,
//       'pay_amount' => 30,
//       'uids'=>array(),
//       'play_users'=>50,
//       'avg_time'=>'3',//hours
//       'all_user_play_time'=>'60' //seconds
//   )
// )
$view = array();

$offset = 0;
$count = 30;

$db = new PDO($actionLogDb['connectionString'],$actionLogDb['username'],$actionLogDb['password']);

$paydb = new PDO($payDb['connectionString'],$payDb['username'],$payDb['password']);

$userdb = new PDO($userDb['connectionString'],$userDb['username'],$userDb['password']);

while (true) {
	gc_collect_cycles();
	$stmt = $db->prepare("SELECT uid,created FROM action_log WHERE u_from = 'ceshi1' and action='syscreateaccount' limit {$offset},{$count}");
	$stmt->execute();
	$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
	if($rows === false){
		break;
	}
	foreach($rows as $row){
		$regCount = 0;
		$d = date('Y-m-d',strtotime($row['created']));
		if(!isset($view[$d])){
			$view[$d] = array(
			      'register' => 0,
			      'pay' => 0,
			      'pay_amount' => 0,
			      'uids' => array(),
			      'play_users'=> 0,
			      'all_user_play_time' => 0,
				);
		}
		$view[$d]['register']++;
		$view[$d]['uids'][] = $row['uid'];
	}
	$offset += $count;
	if(count($rows) < $count){
		break;
	}
}

foreach($view as $d=>$v){

	$count = 30;
	$i = 0;
	while (true) {
		$uids = array_slice($v['uids'], $i,$count);
		$sql = "SELECT sum(pay_amt) as s FROM `order` where pay_status = 1 and uid in('". implode("','", $uids)."') group by uid";
		//select user play game time > 0
		$playtime_sql = "SELECT SUM(all_time) AS all_time FROM `user_play_time` where uid in('". implode("','", $uids)."') AND all_time > 60 group by uid";

		$stmt = $paydb->prepare($sql);

		$playtime_stmt = $userdb->prepare($playtime_sql);
	
		gc_collect_cycles();
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$view[$d]['pay']+= count($rows);
		foreach($rows as $row){
			$view[$d]['pay_amount']+= $row['s'];
		}
		//play time
		$playtime_stmt->execute();
		// if(in_array('1003172', $uids)){
		// 	exit;
		// }
		$results = $playtime_stmt->fetchAll();
		$view[$d]['play_users'] += count($results);
		foreach($results as $row){
			$view[$d]['all_user_play_time']+= $row['all_time'];
		}
		$i += $count;
		if($i >= count($v['uids'])){
			break;
		}
	}
}

?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	 <link href="./bootstrap.css" rel="stylesheet">
	<title></title>
</head>
<body>
	<table class="table table-bordered">
		<tr>
			<th>日期</th>
			<th>注册总数</th>
			<th>付费用户数</th>
			<th>付费总金额</th>
			<th>游戏用户</th>
			<th>平均游戏时间</th>
		</tr>
		<?php foreach($view as $d=>$v) {?>
		<tr>
			<td>
				<?php echo $d; ?>
			</td>
			<td>
				<?php echo $v['register']; ?>
			</td>
			<td>
				<?php echo $v['pay']; ?>
			</td>
			<td>
				<?php echo $v['pay_amount']; ?>
			</td>
			<td>
				<?php echo $v['play_users']; ?>
			</td>
			<td>
				<?php printf("%.1f",($v['all_user_play_time']/$v['play_users'] * 1.0)/60); ?>
			</td>
		</tr>
		<?php } ?>
	</table>
</body>
</html>

<?php
require("inc/head.php");
	$occurtime = date("Y-m-d H:i:s");	


if($_SESSION['U'] -> getF_test())
	if($occurtime < $_SESSION['C'] -> getS_time())
	$_GET['cid'] = "3";	//新生热身赛模拟比赛号为3
	else
	$_GET['cid'] = "1";	//新生正赛模拟比赛号为1
else
	if($occurtime < $_SESSION['C'] -> getS_time())
	$_GET['cid'] = "2";	//老生热身赛模拟比赛号为2
	else
	$_GET['cid'] = "0";	//老生正赛模拟比赛号为0

	$OJ_CACHE_SHARE=true;
	$cache_time=30;

// contest start time
if (!isset($_GET['cid'])) die("No Such Contest!");
$cid=intval($_GET['cid']);

$sql="SELECT * FROM `contest` WHERE `contest_id`=0 AND `start_time`<NOW()";
$result=mysql_query($sql);
$num=mysql_num_rows($result);
if ($num==0){
	$view_errors= "Not Started!";
	require("template/".$OJ_TEMPLATE."/error.php");
	exit(0);
}
mysql_free_result($result);

$view_title= "Contest Statistics";

$sql="SELECT count(`num`) FROM `schoolcontest_problem` WHERE `contest_id`='$cid'";
$result=mysql_query($sql);
$row=mysql_fetch_array($result);
$pid_cnt=intval($row[0]);
mysql_free_result($result);

$sql="SELECT `result`,`num`,`language` FROM `solution` WHERE `contest_id`='$cid' and num>=0"; 
$result=mysql_query($sql);
$R=array();
while ($row=mysql_fetch_object($result)){
	$res=intval($row->result)-4;
	if ($res<0) $res=8;
	$num=intval($row->num);
	$lag=intval($row->language);
	if(!isset($R[$num][$res]))
		$R[$num][$res]=1;
	else
		$R[$num][$res]++;
	if(!isset($R[$num][$lag+10]))
		$R[$num][$lag+10]=1;
	else
		$R[$num][$lag+10]++;
	if(!isset($R[$pid_cnt][$res]))
		$R[$pid_cnt][$res]=1;
	else
		$R[$pid_cnt][$res]++;
	if(!isset($R[$pid_cnt][$lag+10]))
		$R[$pid_cnt][$lag+10]=1;
	else
		$R[$pid_cnt][$lag+10]++;
	if(!isset($R[$num][8]))
		$R[$num][8]=1;
	else
		$R[$num][8]++;
	if(!isset($R[$pid_cnt][8]))
		$R[$pid_cnt][8]=1;
	else
		$R[$pid_cnt][8]++;
}
mysql_free_result($result);

$res=3600;

$sql="SELECT (UNIX_TIMESTAMP(end_time)-UNIX_TIMESTAMP(start_time))/100 FROM contest WHERE contest_id=0 ";
        $result=mysql_query($sql);
        $view_userstat=array();
        if($row=mysql_fetch_array($result)){
              $res=$row[0];
        }
        mysql_free_result($result);

$sql=   "SELECT floor(UNIX_TIMESTAMP((in_date))/$res)*$res*1000 md,count(1) c FROM `solution` where  `contest_id`='$cid'   group by md order by md desc ";
        $result=mysql_query($sql);//mysql_escape_string($sql));
        $chart_data_all= array();
//echo $sql;
   
        while ($row=mysql_fetch_array($result)){
                $chart_data_all[$row['md']]=$row['c'];
    }
   
$sql=   "SELECT floor(UNIX_TIMESTAMP((in_date))/$res)*$res*1000 md,count(1) c FROM `solution` where  `contest_id`='$cid' and result=4 group by md order by md desc ";
        $result=mysql_query($sql);//mysql_escape_string($sql));
        $chart_data_ac= array();
//echo $sql;
   
        while ($row=mysql_fetch_array($result)){
                $chart_data_ac[$row['md']]=$row['c'];
    }
 
  mysql_free_result($result);


/////////////////////////Template
require("template/".$OJ_TEMPLATE."/conteststatistics.php");
/////////////////////////Common foot
if(file_exists('include/cache_end.php'))
require('include/cache_end.php');
?>

<?php
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

////////////////////////////Common head
	$cache_time=2;
	$OJ_CACHE_SHARE=false;
	require("inc/head.php");

	$view_title= "$MSG_STATUS";
	
	
if($occurtime < $contesttime['start_time']){
	$sql_lock="SELECT `pre_start_time`,`title`,`pre_end_time` FROM `contest` WHERE `contest_id`= 0";
	if($_SESSION['U'] -> getF_test())
	$_POST['cid'] = "3";	//新生热身赛模拟比赛号为3
	else
	$_POST['cid'] = "2";	//老生热身赛模拟比赛号为2
}
else{
	$sql_lock="SELECT `start_time`,`title`,`end_time` FROM `contest` WHERE `contest_id`= 0";
	if($_SESSION['U'] -> getF_test())
	$_POST['cid'] = "1";	//新生正赛模拟比赛号为1
	else
	$_POST['cid'] = "0";	//老生正赛模拟比赛号为0
}
	$judge_color=Array("btn gray","btn btn-info","btn btn-warning","btn btn-warning","btn btn-success","btn btn-danger","btn btn-danger","btn btn-warning","btn btn-warning","btn btn-warning","btn btn-warning","btn btn-warning","btn btn-warning","btn btn-info");
$str2="";
$lock=false;
$lock_time=date("Y-m-d H:i:s",time());
$sql="SELECT * FROM `solution` WHERE problem_id>0 ";
if (isset($_GET['cid'])){
        $cid=intval($_GET['cid']);
        $sql=$sql." AND `contest_id`='$cid' and num>=0 ";
        $str2=$str2."&cid=$cid";
        
        $result=mysql_query($sql_lock) or die(mysql_error());
        $rows_cnt=mysql_num_rows($result);
        $start_time=0;
        $end_time=0;
        if ($rows_cnt>0){
                $row=mysql_fetch_array($result);
                $start_time=strtotime($row[0]);
                $title=$row[1];
                $end_time=strtotime($row[2]);       
        }
        $lock_time=$end_time-($end_time-$start_time);
		//*$OJ_RANK_LOCK_PERCENT;
        $lock_time=date("Y-m-d H:i:s",$lock_time);
        $time_sql="";
        //echo $lock.'-'.date("Y-m-d H:i:s",$lock);
        if(time()>$lock_time&&time()<$end_time){
          //$lock_time=date("Y-m-d H:i:s",$lock_time);
          //echo $time_sql;
           $lock=true;
        }else{
           $lock=false;
        }
        
        //require_once("contest-header.php");
}else{
        //require_once("oj-header.php");

}
$start_first=true;
$order_str=" ORDER BY `solution_id` DESC ";



// check the top arg
if (isset($_GET['top'])){
        $top=strval(intval($_GET['top']));
        if ($top!=-1) $sql=$sql."AND `solution_id`<='".$top."' ";
}

// check the problem arg
$problem_id="";
if (isset($_GET['problem_id'])&&$_GET['problem_id']!=""){
	
	if(isset($_GET['cid'])){
		$problem_id=$_GET['problem_id'];
		$num=strpos($PID,$problem_id);
		$sql=$sql."AND `num`='".$num."' ";
        $str2=$str2."&problem_id=".$problem_id;
        
	}else{
        $problem_id=strval(intval($_GET['problem_id']));
        if ($problem_id!='0'){
                $sql=$sql."AND `problem_id`='".$problem_id."' ";
                $str2=$str2."&problem_id=".$problem_id;
        }
        else $problem_id="";
	}
}
// check the user_id arg
$user_id="";
if (isset($_GET['user_id'])){
        $user_id=trim($_GET['user_id']);
        if (is_valid_user_name($user_id) && $user_id!=""){
                $sql=$sql."AND `user_id`='".$user_id."' ";
                if ($str2!="") $str2=$str2."&";
                $str2=$str2."user_id=".$user_id;
        }else $user_id="";
}
if (isset($_GET['language'])) $language=intval($_GET['language']);
else $language=-1;

if ($language>count($language_ext) || $language<0) $language=-1;
if ($language!=-1){
        $sql=$sql."AND `language`='".strval($language)."' ";
        $str2=$str2."&language=".$language;
}
if (isset($_GET['jresult'])) $result=intval($_GET['jresult']);
else $result=-1;

if ($result>12 || $result<0) $result=-1;
if ($result!=-1&&!$lock){
        $sql=$sql."AND `result`='".strval($result)."' ";
        $str2=$str2."&jresult=".$result;
}



if($OJ_SIM){
        $old=$sql;
        $sql="select * from ($sql order by solution_id desc limit 1000) solution left join `sim` on solution.solution_id=sim.s_id WHERE 1 ";
        if(isset($_GET['showsim'])&&intval($_GET['showsim'])>0){
                $showsim=intval($_GET['showsim']);
                $sql="select * from ($old ) solution 
                     left join `sim` on solution.solution_id=sim.s_id WHERE result=4 and sim>=$showsim limit 1000";
                $sql="SELECT * FROM ($sql) `solution`
                        left join(select solution_id old_s_id,user_id old_user_id from solution limit 1000) old
                        on old.old_s_id=sim_s_id WHERE  old_user_id!=user_id and sim_s_id!=solution_id ";
                $str2.="&showsim=$showsim";
        }
        //$sql=$sql.$order_str." LIMIT 20";
}


$sql=$sql.$order_str." LIMIT 20";
//echo $sql;


if($OJ_MEMCACHE){
	require("include/memcache.php");
	$result = mysql_query_cache($sql);// or die("Error! ".mysql_error());
	if($result) $rows_cnt=count($result);
	else $rows_cnt=0;
}else{
		
	$result = mysql_query($sql);// or die("Error! ".mysql_error());
	if($result) $rows_cnt=mysql_num_rows($result);
	else $rows_cnt=0;
}
$top=$bottom=-1;
$cnt=0;
if ($start_first){
        $row_start=0;
        $row_add=1;
}else{
        $row_start=$rows_cnt-1;
        $row_add=-1;
}

$view_status=Array();

$last=0;


//以下为输出数组填充
require("inc/turnid.php");


for ($i=0,$n=0;$n<$rows_cnt;$n++){

if($OJ_MEMCACHE)
        $row=$result[$n];
else
        $row=mysql_fetch_array($result);
		
if($_SESSION['U'] -> getF_test() == 0 && (($row['problem_id'] >1000 && $row['problem_id'] <= 1008) || ($row['problem_id'] >1016 && $row['problem_id'] <= 1021)) )
{	
		
        //$view_status[$i]=$row;
        if($i==0&&$row['result']<4) $last=$row['solution_id'];

	
		if ($top==-1) $top=$row['solution_id'];
        $bottom=$row['solution_id'];
		$flag=(!is_running(intval($row['contest_id']))) ||
                        isset($_SESSION['source_browser']) ||
                        $_SESSION['U'] -> getAut() == "admin" || 
                        (!strcmp($row['user_id'],$_SESSION['U'] -> getU_id()));

        $cnt=1-$cnt;
	

        $view_status[$i][0]=$row['solution_id'];
       
		
        if ($row['contest_id']>0) {
                $view_status[$i][1]= "<a href='contestrank.php?cid=".$row['contest_id']."&user_id=".$row['user_id']."#".$row['user_id']."'>".$row['user_id']."</a>";
        }else{
                $view_status[$i][1]= "<a id=\"blue\">".$row['user_id']."</a>";
        }		


				$turnedid = turnid($row['problem_id']);
                $view_status[$i][2]= "<div class=center><a href='problem.php?id=".$row['problem_id']."'>".$turnedid."</a></div>";

       
        if (intval($row['result'])==11 && (($row['user_id']==$_SESSION['U'] -> getU_id()) || isset($_SESSION['source_browser']))){
                $view_status[$i][3]= "<a href='ceinfo.php?sid=".$row['solution_id']."' class='".$judge_color[$row['result']]."'>".$MSG_Compile_Click."</a>";
        }else if ((intval($row['result'])==6||$row['result']==10||$row['result']==13) && (($row['user_id']==$_SESSION['U'] -> getU_id()) || isset($_SESSION['source_browser']))){
                $view_status[$i][3]= "<a href='reinfo.php?sid=".$row['solution_id']."' class='".$judge_color[$row['result']]."'>".$judge_result[$row['result']]."</a>";

        }else{
              if(!$lock||$lock_time>$row['in_date']||$row['user_id']==$_SESSION['user_id']){
                if($OJ_SIM&&$row['sim']>80&&$row['sim_s_id']!=$row['s_id']) {
                        $view_status[$i][3]= "<span class='".$judge_color[$row['result']]."'>*".$judge_result[$row['result']]."</span>";
                       
                        if( isset($_SESSION['source_browser'])){

                                        $view_status[$i][3].= "<a href=comparesource.php?left=".$row['sim_s_id']."&right=".$row['solution_id']."  class='btn btn-info'  target=original>".$row['sim_s_id']."(".$row['sim']."%)</a>";
                        }else{

                                        $view_status[$i][3].= "<span class='btn btn-info'>".$row['sim_s_id']."</span>";

                        }
                        if(isset($_GET['showsim'])&&isset($row[13])){
                                        $view_status[$i][3].= "$row[13]";
                                
                        }
                }else{

                        $view_status[$i][3]= "<span class='".$judge_color[$row['result']]."'>".$judge_result[$row['result']]."</span>";
                }
          }else{
              echo "<td>----";
          }
                
        }
        if (isset($row['pass_rate'])&&$row['pass_rate']>0&&$row['pass_rate']<.98) 
				$view_status[$i][3].="<span class='btn btn-info'>". (100-$row['pass_rate']*100)."%</span>";
        if ($flag){


                if ($row['result']>=4){
                        $view_status[$i][4]= "<div id=center class=red>".$row['memory']."</div>";
                        $view_status[$i][5]= "<div id=center class=red>".$row['time']."</div>";
						//echo "=========".$row['memory']."========";
                }else{
                        $view_status[$i][4]= "---";
                        $view_status[$i][5]= "---";
						
                }
				//echo $row['result'];
                if (!(strtolower($row['user_id'])==strtolower($_SESSION['U'] -> getU_id()) || isset($_SESSION['source_browser']))){
                        $view_status[$i][6]=$language_name[$row['language']];
                }else{

                        $view_status[$i][6]= "<a target=_blank href=showsource.php?id=".$row['solution_id'].">".$language_name[$row['language']]."</a>/";

                        /*if (isset($cid)) {
                                $view_status[$i][6].= "<a target=_self href=\"submitpage.php?cid=".$cid."&pid=".$row['num']."&sid=".$row['solution_id']."\">Edit</a>";
                        }else{
                                $view_status[$i][6].= "<a target=_self href=\"submitpage.php?id=".$row['problem_id']."&sid=".$row['solution_id']."\">Edit</a>";
                        }*/
                }
                $view_status[$i][7]= $row['code_length']." B";
				
        }else
		{
			$view_status[$i][4]="----";
			$view_status[$i][5]="----";
			$view_status[$i][6]="----";
			$view_status[$i][7]="----";
		}
        $view_status[$i][8]= $row['in_date'];
        $i++;
}else
{
	if($_SESSION['U'] -> getF_test() == 1 && (($row['problem_id'] >1008 && $row['problem_id'] <= 1016) || ($row['problem_id'] >1021 && $row['problem_id'] <= 1026)))
	{
		if($i==0&&$row['result']<4) $last=$row['solution_id'];

	
		if ($top==-1) $top=$row['solution_id'];
        $bottom=$row['solution_id'];
		$flag=(!is_running(intval($row['contest_id']))) ||
                        isset($_SESSION['source_browser']) ||
                        $_SESSION['U'] -> getAut() == "admin"  || 
                        (!strcmp($row['user_id'],$_SESSION['U'] -> getU_id()));

        $cnt=1-$cnt;
	

        $view_status[$i][0]=$row['solution_id'];
       
		
        if ($row['contest_id']>0) {
                $view_status[$i][1]= "<a href='contestrank.php?cid=".$row['contest_id']."&user_id=".$row['user_id']."#".$row['user_id']."'>".$row['user_id']."</a>";
        }else{
                $view_status[$i][1]= "<a id=\"blue\">".$row['user_id']."</a>";
        }		


				$turnedid = turnid($row['problem_id']);
                $view_status[$i][2]= "<div class=center><a href='problem.php?id=".$row['problem_id']."'>".$turnedid."</a></div>";


       
        if (intval($row['result'])==11 && (($row['user_id']==$_SESSION['U'] -> getU_id()) || isset($_SESSION['source_browser']))){
                $view_status[$i][3]= "<a href='ceinfo.php?sid=".$row['solution_id']."' class='".$judge_color[$row['result']]."'>".$MSG_Compile_Click."</a>";
        }else if ((intval($row['result'])==6||$row['result']==10||$row['result']==13) && (($row['user_id']==$_SESSION['U'] -> getU_id()) || isset($_SESSION['source_browser']))){
                $view_status[$i][3]= "<a href='reinfo.php?sid=".$row['solution_id']."' class='".$judge_color[$row['result']]."'>".$judge_result[$row['result']]."</a>";

        }else{
              if(!$lock||$lock_time>$row['in_date']||$row['user_id']==$_SESSION['U'] -> getU_id()){
                if($OJ_SIM&&$row['sim']>80&&$row['sim_s_id']!=$row['s_id']) {
                        $view_status[$i][3]= "<span class='".$judge_color[$row['result']]."'>*".$judge_result[$row['result']]."</span>";
                       
                        if( isset($_SESSION['source_browser'])){

                                        $view_status[$i][3].= "<a href=comparesource.php?left=".$row['sim_s_id']."&right=".$row['solution_id']."  class='btn btn-info'  target=original>".$row['sim_s_id']."(".$row['sim']."%)</a>";
                        }else{

                                        $view_status[$i][3].= "<span class='btn btn-info'>".$row['sim_s_id']."</span>";

                        }
                        if(isset($_GET['showsim'])&&isset($row[13])){
                                        $view_status[$i][3].= "$row[13]";
                                
                        }
                }else{

                        $view_status[$i][3]= "<span class='".$judge_color[$row['result']]."'>".$judge_result[$row['result']]."</span>";
                }
          }else{
              echo "<td>----";
          }
                
        }
        if (isset($row['pass_rate'])&&$row['pass_rate']>0&&$row['pass_rate']<.98) 
				$view_status[$i][3].="<span class='btn btn-info'>". (100-$row['pass_rate']*100)."%</span>";
        if ($flag){


                if ($row['result']>=4){
                        $view_status[$i][4]= "<div id=center class=red>".$row['memory']."</div>";
                        $view_status[$i][5]= "<div id=center class=red>".$row['time']."</div>";
						//echo "=========".$row['memory']."========";
                }else{
                        $view_status[$i][4]= "---";
                        $view_status[$i][5]= "---";
						
                }
				//echo $row['result'];
                if (!(strtolower($row['user_id'])==strtolower($_SESSION['U'] -> getU_id()) || isset($_SESSION['source_browser']))){
                        $view_status[$i][6]=$language_name[$row['language']];
                }else{

                        $view_status[$i][6]= "<a target=_blank href=showsource.php?id=".$row['solution_id'].">".$language_name[$row['language']]."</a>/";

                       /* if (isset($cid)) {
                                $view_status[$i][6].= "<a target=_self href=\"submitpage.php?cid=".$cid."&pid=".$row['num']."&sid=".$row['solution_id']."\">Edit</a>";
                        }else{
                                $view_status[$i][6].= "<a target=_self href=\"submitpage.php?id=".$row['problem_id']."&sid=".$row['solution_id']."\">Edit</a>";
                        }*/
                }
                $view_status[$i][7]= $row['code_length']." B";
				
        }else
		{
			$view_status[$i][4]="----";
			$view_status[$i][5]="----";
			$view_status[$i][6]="----";
			$view_status[$i][7]="----";
		}
        $view_status[$i][8]= $row['in_date'];
        $i++;
	}
}	   
   

}
if(!$OJ_MEMCACHE)mysql_free_result($result);








?>

<?php
if($_SESSION['U'] -> getAut == "admin")
{
require("statuspage.php");
	if($_SESSION['U'] -> getF_test())
	$_SESSION['U'] -> setF_test(0);
	else
	$_SESSION['U'] -> setF_test(0);
}else
/////////////////////////Template
require("statuspage.php");
/////////////////////////Common foot
if(file_exists('./include/cache_end.php'))
	require_once('./include/cache_end.php');
?>



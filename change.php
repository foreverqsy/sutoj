﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿﻿<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<?php	$cache_time=10;
	$OJ_CACHE_SHARE=false;
	require "inc/head.php";
	require "inc/header2.php";
	
	
if($_SERVER['REQUEST_METHOD']=='POST')
{
	if($_POST['team_number1']=="" || $_POST['team_member1']=="" || $_POST['team_name']=="" || $_POST['team_telephone']=="" || $_POST['password']=="" || $_POST['password_2']=="")
	{
		header("location:registerpage.php");
	}
?>

 <div id="content" style="margin: 0px auto;">
   
   		    <div class="carousel-box">
         <div class="box">
            <div class="border-right">
               <div class="border-left">
                  <div class="left-top-corner">
                     <div class="right-top-corner">
                        <div class="inner">

					   <div class="find">
 				       <h3>注册信息确认</h3>
					<div class="margin"><img src="images/margin.jpg"/></div>
					<?php


$team_number1 =substr($_POST['team_number1'],0,7);
$team_number2 =substr($_POST['team_number2'],0,7);
$team_number3 =substr($_POST['team_number3'],0,7);
$freshman_contest = substr($_POST['freshman_contest'],0,7);

$sql1 = "SELECT * FROM student_information WHERE id=$team_number1";
$sql2 = "SELECT * FROM student_information WHERE id=$team_number2";
$sql3 = "SELECT * FROM student_information WHERE id=$team_number3";

$result1 = mysql_query($sql1);
$result2 = mysql_query($sql2);
$result3 = mysql_query($sql3);


if($result1)
{
	$info1 = mysql_fetch_array($result1);
	
	echo "	<center>

	<table width=\"90%\"; height=\"150px;\"; border=\"0\"; style=\" font-size:20px; font-family:微软雅黑;\" class=\"bordered\">
	<thead> 
	<tr align=\"center\" height=\"15px;\">
  		<td width=10% ></td>
    	<td width=10% >学号</td>
    	<td width=10% >姓名</td>
    	<td width=25% >学院</td>
    	<td width=25% >专业</td>
    	<td width=10% >年级</td>
  	</tr>
	</thead> 
  	<tr align=\"center\">
  		<td  align=\"center\">队长</td>
    	<td height=50>$_POST[team_number1]</td>
    	<td>$_POST[team_member1]</td>
    	<td>$info1[student_school]</td>
    	<td>$info1[student_major]</td>
    	<td>$info1[student_grade]</td>
  	</tr>";
}
if($result2)
{
	$info2 = mysql_fetch_array($result2);
	if($info2)
	{
	echo "<tr align=\"center\">
    <td  align=\"center\">队员</td>
    <td height=50>$_POST[team_number2]</td>
    <td>$_POST[team_member2]</td>
    <td>$info2[student_school]</td>
    <td>$info2[student_major]</td>
    <td>$info2[student_grade]</td>
	</tr> ";		
	}
}
if($result3)
{
	$info3 = mysql_fetch_array($result3);
	if($info3)
	{
	echo " <tr align=\"center\">
    <td  align=\"center\">队员</td>
    <td height=50>$_POST[team_number3]</td>
    <td>$_POST[team_member3]</td>
    <td>$info3[student_school]</td>
    <td>$info3[student_major]</td>
    <td>$info3[student_grade]</td>
  </tr>
  ";
	}
	else
		echo "</table>";	
}
echo "

<table width=\"90%\"  border=\"0\" style=\" font-size:20px; font-family:微软雅黑\" class=\"bordered\">
	 </br></br>
	<tr align=\"center\">
    	<td width=10% align=\"center\">队名</td>
        
        <td width=10%>$_POST[team_name]</td>
        <td width=10%></td>
        <td width=25%><span style=\"float:right text-align=center\">联系方式</span></td>
        <td width=25%>$_POST[team_telephone]</td>
        <td width=10%></td>
    </tr>
	 
</table></center>";
echo "<form  action=\"registerok.php\" method=\"post\" target=\"_top\">
<input  name=\"team_name\" type=\"text\" style=\"display:none\" value=\"$_POST[team_name]\"/>
<input  name=\"team_number1\" type=\"text\" style=\"display:none\" value=\"$_POST[team_number1]\"/>
<input  name=\"team_number2\" type=\"text\" style=\"display:none\" value=\"$_POST[team_number2]\"/>
<input  name=\"team_number3\" type=\"text\" style=\"display:none\" value=\"$_POST[team_number3]\"/>
<input  name=\"password\" type=\"text\" style=\"display:none\" value=\"$_POST[password]\"/>
<input  name=\"team_member1\" type=\"text\" style=\"display:none\" value=\"$_POST[team_member1]\"/>
<input  name=\"team_member2\" type=\"text\" style=\"display:none\" value=\"$_POST[team_member2]\"/>
<input  name=\"team_member3\" type=\"text\" style=\"display:none\" value=\"$_POST[team_member3]\"/>
<input  name=\"team_telephone\" type=\"text\" style=\"display:none\" value=\"$_POST[team_telephone]\"/>
<input  name=\"freshman_contest\" type=\"text\" style=\"display:none\" value=\"$_POST[freshman_contest]\"/>
<br><br><br><div>
<center> 
<input name=\"submit\" type=\"submit\" value=\"确认信息\" class=\"button\" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type=\"button\" value=\"返&nbsp回\" class=\"button\" onclick=\"javascript:history.back()\"/>
</center></div>
</form>";
}
echo "</br></br></br></br>"
?> 		

						</div>

					   <div id="result">
					   
		       <div class="wrapper">
					  </div> 
                  </div>
               </div>
            </div>

            <div class="border-bot">
               <div id="footer">
      <p><br/> (C) 沈阳工业大学ACM实验室</p>
   </div>

               <div class="left-bot-corner">
                  <div class="right-bot-corner">
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
   </div>
   </div>
   </div>




<?php include("inc/footer.php");?>



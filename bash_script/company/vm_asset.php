<!DOCTYPE html>

<html>
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
/*sidebar menu CSS*/
body {
  font-family: Arial, Helvetica, sans-serif;
}

* {
  box-sizing: border-box;
}

/* Create a column layout with Flexbox */
.row {
  display: flex;
}

/* Left column (menu) */
.left {
  flex: 35%;
  padding: 15px 0;
}

.left h2 {
  padding-left: 8px;
}

/* Right column (page content) */
.right {
  flex: 65%;
  padding: 15px;
}

/* Style the search box */
#mySearch {
  width: 100%;
  font-size: 18px;
  padding: 11px;
  border: 1px solid #ddd;
}

/* Style the navigation menu inside the left column */
#myMenu {
  list-style-type: none;
  padding: 0;
  margin: 0;
}

#myMenu li a {
  padding: 12px;
  text-decoration: none;
  color: black;
  display: block
}

#myMenu li a:hover {
  background-color: #EEE
}


/*table style*/
table.type09 {
    border-collapse: collapse;
    text-align: left;
    line-height: 1.5;

}
table.type09 thead th {
    padding: 10px;
    font-weight: bold;
    vertical-align: top;
    color: #000;
    border-bottom: 3px solid #01DF01;
    text-align : center;
}
table.type09 tbody th {
    width: 150px;
    padding: 10px;
    font-weight: bold;
    vertical-align: top;
    border-bottom: 1px solid #ccc;
    background: #f3f6f7;
    text-align : center;
}
table.type09 td {
    width: 170px;
    vertical-align: top;
    border-bottom: 1px solid #ccc;
    text-align : center;
    vertical-align:middle

}
#rankTable {
  margin-top: 20px;
  margin-right: 30px;
  margin-bottom: 30px;
}

/*circle red green*/
.imagesize { width: 50px; }


</style>

<script type="text/javascript" src="//code.jquery.com/jquery-3.1.1.js"></script>

<script type="text/javascript">


window.onload=function(){
        $("#keyword").keyup(function() {
        var k = $(this).val();
        $("#user-table > tbody > tr").hide();
        var temp = $("#user-table > tbody > tr > td:nth-child(5n+1):contains('" + k + "'), td:nth-child(5n+2):contains('" + k + "'),td:nth-child(5n+3):contains('" + k + "'),td:nth-child(5n+4):contains('" + k + "'),td:nth-child(5n+5):contains('" + k + "')");

                $(temp).parent().show();
            })

    }

</script>

</head>
<body>
<!--export html-->
<?php 
#$html = file_get_contents('topmenu.php');
#echo $html;

include "topmenu.php";
?>  
<!--export html-->



<h2>기술부 - 장비 연결페이지</h2>
<p>검색란에 자신의 이름을 검색 후 사용하면 편합니다.</p>

<div class="row">
	<div style="background-color:#bbb;">
    		<a href='./vm_asset.php' style="text-decoration:none"><h2>Menu</h2></a>
    		<input type="text" id="mySearch" onkeyup="myFunction()" placeholder="Search.." title="Type in a category">
    		<ul id="myMenu">
			<?php
			require('./lib/db.php');
			$sql    = 'SELECT distinct(owner) FROM VM_ASSET.tecvm_info where team != "online" or team is null;';
			$query  = mysqli_query($conn2, $sql);
			while ($row = mysqli_fetch_array($query))
			{
				echo "<li><a href='#' onclick=\"OPENVM('$row[owner]')\">$row[owner]</a></li>";
			}
			mysqli_close ($conn2);
			?>
    		</ul>
	</div>
  

<div id="London" class="w3-container city">
<center><h2>전체 VM 사용현황</h2>
<font color="red"> <b>VM on이후 10일간 동작되며 10일10:10 이후 자동 off됩니다</b></font>
</center>


<?php
$todate=date("Y-m-d");
$yesdate=date("Y-m-d", strtotime("-1 day"));
require('./lib/db.php');

#$sql    = "SELECT * FROM VM_ASSET.tecvm_info where status ='동작중' and (team is null or team = '');";
$sql    = "SELECT * FROM VM_ASSET.tecvm_info where ipadd like '%172.29.48.%' and status ='동작중';";
$query  = mysqli_query($conn2, $sql);
$count = mysqli_num_rows($query);
echo "활성자산 : $count  <br> ";

$sql    = "SELECT * FROM vwvm_hitory where daytime like '$yesdate%' and clickkind != '';";
$query  = mysqli_query($conn2, $sql);
$count = mysqli_num_rows($query);
echo "어제 클릭수 : $count   ";

$sql    = "SELECT * FROM vwvm_hitory where daytime like '$todate%' and clickkind != '';";
$query  = mysqli_query($conn2, $sql);
$count = mysqli_num_rows($query);
echo "<br>오늘 클릭수 : $count";

?>
	<div id="container"style="text-align:right">
		<div id="input-form" class="row" >
			<div id="col-1">
			<table id="rankTable" class="type09" width="350">
			<caption> 메모리 사용량 <span style="font-size: x-small">(매시 10분에 갱신) </span></caption>
			      <thead>
        			<tr>
               			 <th>순위</th>
              		         <th>VM이름</th>
              			 <th>메모리</th>
  				 <th>사용량</th>
                                </tr>
     			      </thead>

			<tbody>
			<?php
				require('./lib/db.php');

				$rankSql = "SELECT vm_name, vm_memory FROM VM_ASSET.VM_Resource ORDER BY vm_memory DESC LIMIT 5 ;";
				$rankQuery = mysqli_query($conn2, $rankSql);
				$count = 1;
				while ($row = mysqli_fetch_array($rankQuery))
				{
					$memoryPer = number_format(($row[vm_memory] / 120) * 100, 2);
				        echo"<tr>";
            				echo"<td>$count</td>";
             				echo"<td>$row[vm_name]</td>";
            				echo"<td>$row[vm_memory] GB</td>";
					echo"<td>$memoryPer %</td>";
					echo"</tr>";
					$count++;

				}
				
			?>
			</tbody>
			</table>
			</div>
			<div class="col-1">
			<table id="rankTable" class="type09" width="300">
                        <caption> 디스크 사용량  <span style="font-size: x-small">(매시 10분에 갱신) </span></caption>
                              <thead>
                                <tr>
                                 <th>순위</th>
                                 <th>VM이름</th>
                                 <th>디스크</th>
                                </tr>
                              </thead>

                        <tbody>
                        <?php
                                require('./lib/db.php');

                                $rankSql = "SELECT vm_name, vm_disk FROM VM_ASSET.VM_Resource ORDER BY vm_disk DESC LIMIT 5 ;";
                                $rankQuery = mysqli_query($conn2, $rankSql);
                                $count = 1;
                                while ($row = mysqli_fetch_array($rankQuery))
                                {
                                        echo"<tr>";
                                        echo"<td>$count</td>";
                                        echo"<td>$row[vm_name]</td>";
                                        echo"<td>$row[vm_disk] GB</td>";
                                        echo"</tr>";
                                        $count++;

                                }

                        ?>
                        </tbody>
                        </table>
			</div>

			<div>
			</div>

			<div class="col-1" style="margin-left: auto;margin-right: 0;">
			<input type="button" style="width:240px;" value="VM Console 다운로드" onclick="location.href='http://172.29.100.120/download/VMware-VMRC-10.0.4-11818843.exe'";>
			<br><br>
			<button type="button" style="width:120px;height:100px;" onclick="stackcount('211','winserver2016');callvm('232');">windows server VM</button>
			<button type="button" style="width:120px;height:100px;" onclick="stackcount('210','win10pro');callvm('210');">windows 10 pro VM</button><br><br>
			<input type="text" id="keyword" name="formname" placeholder="키워드 검색">
			</div>
      		</div>
<table id="user-table" class="type09">
      <thead>
      	<tr>
		<th>번호</th>
		<th>VM이름</th>
		<th>Device이름</th>
      		<th>IP addr</th>
		<th>booting 일자</th>
		<th>shutdown 일자</th>
		<th>실행시간</th>
		<th>사용유무</th>
		</tr>
      </thead>
<tbody>
<?php
#총 현황
require('./lib/db.php');
#$sql2    = "SELECT * FROM VM_ASSET.tecvm_info where team != 'online' or team is null order by status ASC, owner ;";
$sql2    = "SELECT * FROM VM_ASSET.tecvm_info where ipadd like '%172.29.48.%' order by status ASC, owner ;";
$query2  = mysqli_query($conn2, $sql2);
$cnt=1;
while ($row = mysqli_fetch_array($query2))
{

	###link ver
 	$div_ver=explode('-',$row['version']);
 	$div_ver2=explode('.',$div_ver['4']);
 	$cutstr=$div_ver2[3];
 	$linkver=str_replace(".$cutstr",'',$div_ver[4]);
	$shutdown_date = substr($row['shutdown_date'],0,10);
	$booting_date = substr($row['onboot_date'],0,16);
	echo"<tr>
	     <td>$cnt</td>
             <td><a href=\"#\"style=\"text-decoration:none\" onclick=\"OPENVM('$row[owner]')\">$row[owner] VM-$row[vmidx]</a></td>
	     <td>$row[imgidx]</td>
             <td>$row[ipadd]</td>";
	echo"<td>$booting_date</td>";	
	echo"<td>$shutdown_date</td>";
	echo"<td>$row[uptimes]</td>";
	if($row['status'] == '동작중'){
	echo"<td><a href='#' title='$row[status]'><img class='imagesize' src='./image/ok.png'></a></td>";
	}else{
	echo"<td><a href='#' title='$row[status]'><img class='imagesize' src='./image/disconnect.png'></a></td>";
	}
        echo"</tr>";
	$cnt++;
}
?>
</tbody>
</table>
	</div>
</div>


<?php
require('./lib/db.php');


$sql    = 'SELECT distinct(owner) FROM VM_ASSET.tecvm_info;';
$query  = mysqli_query($conn2, $sql);
while ($row = mysqli_fetch_array($query))
{       
	echo "<div id=\"$row[owner]\" class=\"w3-container city\" style=\"display:none\">";
	echo "<h2>$row[owner]</h2>";
	$owner = "$row[owner]";
	$sql2    = "SELECT * FROM VM_ASSET.tecvm_info where owner = '$owner';";
	$query2  = mysqli_query($conn2, $sql2);
	while ($row = mysqli_fetch_array($query2))
	{
		
		###link ver
		$div_ver=explode('-',$row['version']);
		$div_ver2=explode('.',$div_ver['4']);
		$cutstr=$div_ver2[3];
		$linkver=str_replace(".$cutstr",'',$div_ver[4]);

		echo "<table class=\"type09\">
			<tr>
			<th>사용유무</th>
			<th>VM이름</th>
			<th>Device이름</th>
			<th>IP</th>
			<th>ROLE</th>
			<th>VERSION</th>
			</tr>
			<tr>
			<td>$row[status]</td>
			<td>VM-$row[vmidx]</td>
			<td>VM-$row[imgidx]</td>
			<td>$row[ipadd]</td>
			<td>$row[role]</td>";
	        if(((int)$div_ver2[0] == 5 && (int)$div_ver2[2] > 17) || ((int)$div_ver2[0] == 4 && (int)$div_ver2[2] > 43 && (int)$div_ver2[2] < 100) || ((int)$div_ver2[0] == 4 && (int)$div_ver2[2] > 120)){
	                if((int)$div_ver2[0] == 5){
	                        $cver= (int)$div_ver2[2] - 15 ;
	                }elseif(((int)$div_ver2[0] == 4 && (int)$div_ver2[2] < 100)){
	                        $cver= (int)$div_ver2[2] - 41 ;
	                }else{
	                        $cver= (int)$div_ver2[2] - 118 ;
	                }
	
	                if((int)$cver < 10){
	                        $temp=$cver;
	                        $cver="0$cver";
	                }
	                $timestamp = strtotime("2018-12-01 +$cver months");
	                $cmon=date("M",$timestamp);
	                $cyear=date("Y",$timestamp);
	                echo "<td><a href='https://wiki.geninetworks.com/wiki/display/KB/$cver.+$cmon+$cyear+Release+Note' target='_blank' onclick=\"stackcount('$row[vmidx]','releasenote')\"> $row[version]</a></td>";
	        }else{
	                echo"<td><a href='https://wiki.geninetworks.com/wiki/display/KB/$linkver+Release+Notes' target='_blank' onclick=\"stackcount('$row[vmidx]','releasenote')\"> $row[version]</a></td>";
	        }


			echo "</tr></table>";
		#echo "VM-$row[vmidx] $row[ipadd] <br>";
		echo "<button type=\"button\" onclick=\"stackcount('$row[vmidx]','vm');callvm($row[vmidx]);\">VM Console 접속</button> ";
		if(strpos($row['role'], 'Policy') !== false) {
			if((strpos($row['version'], 'INSIGHTS') !== false) || (substr($row['version'], 17,1) === '3')){
				echo "<button type=\"button\" onclick=\"stackcount('$row[vmidx]','web');callurl('$row[ipadd]','mc');\">WEBUI 접속</button><br><br>";
			}else{
				if(substr($row['version'], 17,1) === '5'){
					$aa=substr($row['version'], 21, 2);
					if((int)$aa >= 19){
						echo "<button type=\"button\" onclick=\"stackcount('$row[vmidx]','web');callurl('$row[ipadd]',8443);\">WEBUI 접속</button><br><br>";
					}else{
						echo "<button type=\"button\" onclick=\"stackcount('$row[vmidx]','web');callurl('$row[ipadd]')\">WEBUI 접속</button><br><br>";
					}
				}else{
				#echo "<button type=\"button\" onclick=\"stackcount();callurl('$row[ipadd]');\">WEBUI 접속</button><br><br>";
					echo "<button type=\"button\" onclick=\"stackcount('$row[vmidx]','web');callurl('$row[ipadd]');\">WEBUI 접속</button><br><br>";
				}
			}
		} 
	}
	

	echo "<center>"; 
        echo "</div>";
}

mysqli_close ($conn2);
?>

</div>


<script>
/*sidevar contents*/
function myFunction() {
  var input, filter, ul, li, a, i;
  input = document.getElementById("mySearch");
  filter = input.value.toUpperCase();
  ul = document.getElementById("myMenu");
  li = ul.getElementsByTagName("li");
  for (i = 0; i < li.length; i++) {
    a = li[i].getElementsByTagName("a")[0];
    if (a.innerHTML.toUpperCase().indexOf(filter) > -1) {
      li[i].style.display = "";
    } else {
      li[i].style.display = "none";
    }
  }
}

function OPENVM(ownerName) {
  var i;
  var x = document.getElementsByClassName("city");
  for (i = 0; i < x.length; i++) {
    x[i].style.display = "none";  
  }
  document.getElementById(ownerName).style.display = "block";  
}

// Defining custom functions
function callvm(id){
	window.location.href = "vmrc://172.29.100.200/?moid=" + id ;
}
//통계를 위한 카운트
function stackcount(vmidx,clickkind){
	window.open("http://172.29.100.120/insert_clickhisotry.php?button1=1&vmidx=" + vmidx + "&clickkind=" + clickkind);
}

function callurl(ipadd,port){
	if(port == 8443){
		window.open("https://"+ ipadd + ":8443" + "/mc2", 'page') ;
	}else if(port == 'mc'){
		window.open("https://"+ ipadd + "/mc", 'page') ;
	}else{
		window.open("https://"+ ipadd + "/mc2", 'page') ;
	}
}
/*sidevar contents*/

/*전체현황 실시간 검색관련 코드*/
    // tell the embed parent frame the height of the content
    if (window.parent && window.parent.parent){
      window.parent.parent.postMessage(["resultsFrame", {
        height: document.body.getBoundingClientRect().height,
        slug: "gw8av5wc"
      }], "*")
    }

    // always overwrite window.name, in case users try to set it manually
    window.name = "result"

</script>


</body>
</html>

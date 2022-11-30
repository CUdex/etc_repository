<html>
<head>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.min.js"></script>

<!--tooltip script load-->
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>


<script type="text/javascript">
      $( document ).ready( function() {
        $( '.check-all' ).click( function() {
          $( '.ab' ).prop( 'checked', this.checked );
        } );
      } );

//체크박스 all checked
function AllChkList(Obj) {
    var Objs = document.getElementsByName("check[]")
    if (Obj.checked == true) {
       for (i = 0; i < Objs.length; i++) { Objs[i].checked = true; }
    }
    else {
       for (i = 0; i < Objs.length; i++) { Objs[i].checked = false; }
    }
}

//버튼에 따른 페이지 이동
function mySubmit(index) {
if (index == 1) {
	document.myForm.action='asset_use.php';
}
if (index == 2) {
  	document.myForm.action='update_unuse.php';
}
if (index == 3) {
        document.myForm.action='move_biz.php';
}
document.myForm.submit();
}

$(document).ready(function () {
    $('#checkBtn').click(function() {
      checked = $("input[type=checkbox]:checked").length;

      if(!checked) {
        alert("You must check at least one checkbox.");
        return false;
      }

    });
});

/*한개 이상 체크되지않으면 경고창*/
$(document).ready(function () {
    $('#movebiz').click(function() {
      checked = $("input[type=checkbox]:checked").length;

      if(!checked) {
	location.reload(true);
        alert("You must check at least one checkbox.");
        return false;
      }
    });

    $('#unuse').click(function() {
      checked = $("input[type=checkbox]:checked").length;

      if(!checked) {
	location.reload(true);
        alert("You must check at least one checkbox.");
        return false;
      }
    });

    $('#use').click(function() {
      checked = $("input[type=checkbox]:checked").length;

      if(!checked) {
	location.reload(true);
        alert("You must check at least one checkbox.");
        return false;
      }
    });
});

//tooltip
$( function() {
  $( document ).tooltip();
} );
</script>


<style type="text/css">

table{
        width:90%;
        <!--height: 100px;-->
        margin: auto;
        text-align: center;
}
.imagesize { width: 50px; }

.a { text-decoration: none; color: black; }
#a:visited { text-decoration: none; }
#a:hover { text-decoration: none; }
#a:focus { text-decoration: none; }
#a:hover, a:active { text-decoration: none; }

label {
    display: inline-block;
    width: 5em;
  }

/*topmenu CSS*/
body {
  margin: 0;
  font-family: Arial, Helvetica, sans-serif;
}

.topnav {
  overflow: hidden;
  background-color: #333;
}

.topnav a {
  float: left;
  color: #f2f2f2;
  text-align: center;
  padding: 14px 16px;
  text-decoration: none;
  font-size: 17px;
}

.topnav a:hover {
  background-color: #ddd;
  color: black;
}

.topnav a.active {
  background-color: #4CAF50;
  color: white;
}

</style>

<link href='./lib/style.css' rel='stylesheet' type='text/css'>
<script src='./lib/jquery-3.3.1.min.js' type='text/javascript'></script>
<script src='./lib/script.js' type='text/javascript'></script>
<script src='./lib/realtime.js' type='text/javascript'></script>
</head>


<body onload="startTime()">

<!--export html-->
<?php
#$html = file_get_contents('topmenu.php');
#echo $html;
include "topmenu.php";
?>
<!--export html-->


<br>
<center><font size="5"><a href="assetsite.php" style="text-decoration:none" class=a>기술부 테스트장비 관리시스템</a></font></center>

<?php

require('./lib/db.php');

$ip=$_SERVER['REMOTE_ADDR'];
#echo "접속자 ip : $ip";
date_default_timezone_set('Asia/Seoul');
$job_update=date("Y-m-d H:i:s");


$ip=getenv("REMOTE_ADDR");

    if(PHP_OS=='WINNT'){
        exec("arp -a", $rgResult);
        $mac_template="/[\d|A-F]{2}\-[\d|A-F]{2}\-[\d|A-F]{2}\-[\d|A-F]{2}\-[\d|A-F]{2}\-[\d|A-F]{2}/i";
        foreach($rgResult as $key=>$value){
            if(strpos($value, $ip)!==FALSE){
                preg_match($mac_template, $value, $matches);
                break;
            }
        }
    } else{
        exec("arp -a | grep $ip", $rgResult);
        $mac_template="/[\d|A-F]{2}\:[\d|A-F]{2}\:[\d|A-F]{2}\:[\d|A-F]{2}\:[\d|A-F]{2}\:[\d|A-F]{2}/i";
        preg_match($mac_template, $rgResult[0], $matches);
    }
    $mac=$matches[0];

?>
<center>
<form name='myForm' method='POST' onSubmit="return CheckForm(this);">

<table width="100%" border=0 cellspacing=0 cellpadding=0 valign="center" >
<tr>
<td colspan='12'><div id="txt"style="text-align:left"></div></td>
</tr>
<tr>
<td colspan='12'><div style="text-align:left"><?php echo "접속자 ip : $ip /mac:  $mac";?></div></td>
</tr>
<tr>
<td><br></td>
</tr>
<tr>
<td colspan='12'><div style="text-align:left"><input type="button" id="movebiz" value="biz자산이력조회" onclick='mySubmit(3)'>  <input type="button" value="접속자 이력조회" onclick="location.href='connect_log.php'";> <input type="button" value="가상화 접속 페이지" onclick="location.href='http://172.29.127.14/'";> <input type="button" value="가상화vm IP" onclick="location.href='https://docs.google.com/spreadsheets/d/1E8fKSGHSoQxEAdXy0AujASm9sSTLZEjq0KS5YKZ4cIk/edit#gid=0'";> <button id='btn1'>sysinfo_link</button></td>
<script>
function copyToClipboard(val) {
  var t = document.createElement("textarea");
  document.body.appendChild(t);
  t.value = val;
  t.select();
  document.execCommand('copy');
  document.body.removeChild(t);
}
$('#btn1').click(function() {
  copyToClipboard('http://172.29.100.90/backup/install_sysinfo.tgz');
  alert('Copy install_sysinfo script link!');
});
</script>

</div></td>
<td colspan='12'><div style="text-align:right">
<input type="button" id="use" value="사용" onclick='mySubmit(1)'>
<input type="button" id="unuse" value="해제" onclick='mySubmit(2)'>
</td>
</div></td>
</tr>
</table>
<div class='container'>
<input type='hidden' id='sort' value='asc'>
<table id='empTable' border="1" cellspacing="0" cellpadding="0" valign="center" >
<tr>
<td><label><input type='checkbox' onClick="JavaScript:AllChkList(this)" name='check'  value='all' class="ab"/></label></td>
<th>번호</th>
<th><span onclick='sortTable("use_user");'>현재사용자</span></th>
<th><span onclick='sortTable("use_asset");'>사용유무</span></th>
<th><span onclick='sortTable("connected");'>동작상태</span></th>
<th><span onclick='sortTable("role");'>역할</span></th>
<th><span onclick='sortTable("ipadd");'>장비IP</span></th>
<th><span onclick='sortTable("version");'>이미지 버전</span></th>
<th><span onclick='sortTable("ha");'>HA 유무</span></th>
<th><span onclick='sortTable("bond");'>bonding유무</span></th>
<th><span onclick='sortTable("replica");'>DB이중화상태</span></th>
<th><span onclick='sortTable("hlink");'>백업파일</span></th>
<th><span onclick='sortTable("comment");'>메모</span></th>
</tr>
<?php
$sql    = 'SELECT * FROM asset_info where ipadd != "172.29.100.100" and ipadd != "172.29.100.99" and ipadd != "172.29.100.87" and ipadd != "172.29.100.88" and ipadd != "172.29.100.84" order by INET_ATON(ipadd)';
#$sql    = 'SELECT * FROM asset_info order by INET_ATON(macadd) ASC';
$query  = mysqli_query($conn, $sql);
$count = 0;


while ($row = mysqli_fetch_array($query))
{
	###link ver
	#$div_ver=explode('-',$row['version']);
	#$div_ver2=explode('.',$div_ver['4']);
	#$cutstr=$div_ver2[3];
	#$linkver=str_replace(".$cutstr",'',$div_ver[4]);
       	
	#inserver num 
	$count++;
        echo "<tr>";
        echo "<td><input type='checkbox' name='check[]' value='$row[macadd]' class=\"ab\" required></td>";
        echo "<td>$count </td>" ;
        echo "<td> $row[use_user] </td>" ;
	echo "<td> $row[use_asset] </td>"; 
	if(strcmp("$row[connected]",'yes') == 0 && strcmp("$row[elastic_status]",'yellow') !== 0 && strcmp("$row[elastic_status]",'red') !== 0 && strcmp("$row[replica_status]",'MISSMATCH') !== 0 && strcmp("$row[urlconnected]",'yes') == 0){
	        echo "<td><a href='#' title='connection=$row[connected], urlconnection=$row[urlconnected], replica_status=$row[replica_status] ,elastic=$row[elastic_status]'><img class='imagesize' src='./image/ok.png'></a></td>";
	}else{
		echo "<td><a href='#' title='connection=$row[connected], urlconnection=$row[urlconnected],replica_status=$row[replica_status] ,elastic=$row[elastic_status]'><img class='imagesize' src='./image/disconnect.png'></a></td>";
	}
        echo "<td> $row[role] </td>";
        echo "<td> <a href='asset_info.php?index=$row[macadd]'> $row[ipadd] </a> </td>";
	#if($div_ver[0] === "NAC" || $div_ver[0] === "ALDER"){
	#	echo "<td><a href='https://wiki.geninetworks.com/wiki/display/KB/$linkver+Release+Notes' target='_blank'> $row[version]</a></td>";
	#}elseif($div_ver[0] === "GPI"){
	#	echo "<td><a href='https://wiki.geninetworks.com/wiki/display/CKB/GPI+$linkver+Release+Notes' target='_blank'> $row[version]</a></td>";
	#}else{
	#	echo "<td><a href='https://wiki.geninetworks.com/wiki/display/SKB/$linkver+Release+Notes' target='_blank'> $row[version]</a></td>";	
	#}
	echo "<td> $row[version] </td>";
        echo "<td> $row[ha] </td>";
        echo "<td> $row[bond] </td>";
        echo "<td> $row[replica] </td>";

	 if(strpos($row['role'], 'Policy ') === false || strpos($row['ha'],'SLAVE') !== false) {
		echo "<td>None</td>";
	}else{
		echo "<td> $row[hlink] </td>";
	}
	echo "<td> $row[comment] </td>";
        echo "</tr>" ;
}

// Close connection
mysqli_close ($conn);

?>
</table>
</div>
</form>
Copyright GENIANS, INC. All rights reserved.
</center>
</body>
</html>

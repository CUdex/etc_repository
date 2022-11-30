#made by magnus


#/!bin/sh
current_day=`/bin/date +%Y-%m-%d`
todaytime=`/bin/date +%H%M`
todaytime2=`/bin/date +%H:%M`
vmlist=`mysql --login-path=asset VM_ASSET -N -e "SELECT vmidx FROM VM_ASSET.tecvm_info where ipadd like '%172.29.48.%' and (team = '' or team is null)"`
#vmlist='180'
configtime=1145
totalon=0
server_cnt=`mysql --login-path=asset VM_ASSET -N -e "SELECT count(*) FROM VM_ASSET.tecvm_info where ipadd like '%172.29.48.%' and status ='동작중'"`

        if [ ${todaytime} = ${configtime} ];then
                # today server_cnt
                mysql --login-path=asset VM_ASSET -e "insert into liveserver_stat(daytime, server_cnt) values('$current_day','$server_cnt') "
        fi


for vmidx in $vmlist;do
echo echo "### $vmidx ###"
#check vm on/off
check_status=`ssh 172.29.100.200  "vim-cmd vmsvc/power.getstat $vmidx" | grep Power`
boot_status=`mysql --login-path=asset VM_ASSET -N -e "select boot_switch from VM_ASSET.tecvm_info where vmidx ='$vmidx'"`
if [ "${check_status}" = 'Powered on' ];then
	echo "on"
	
	#chkuptimes=`mysql --login-path=asset VM_ASSET -N -e "select uptimes from VM_ASSET.tecvm_info where vmidx ='$vmidx'"`
	#if [ "$chk_uptimes" = '' ];then
	#	mysql --login-path=asset VM_ASSET -e "update tecvm_info set uptimes ='0' where vmidx ='$vmidx'"
	#fi

	totalon=$((totalon + 1))
	mysql --login-path=asset VM_ASSET -e "update tecvm_info set status ='동작중' where vmidx ='$vmidx'"
	if [ "$boot_status" = 'off' ];then
		mysql --login-path=asset VM_ASSET -e "update tecvm_info set boot_switch ='on' where vmidx ='$vmidx'"
		mysql --login-path=asset VM_ASSET -e "update tecvm_info set onboot_date ='$current_day $todaytime2' where vmidx ='$vmidx'"
		mysql --login-path=asset VM_ASSET -e "insert into shutdown_history(status,info_update,vmidx) value('bootup','$current_day $todaytime2', '$vmidx');"
	fi
	uptimes=`mysql --login-path=asset VM_ASSET -N -e "select uptimes from VM_ASSET.tecvm_info where vmidx ='$vmidx'"`
	cal=`expr $uptimes + 1`	
	echo "$vmidx uptimes : $cal"
	if [ "$todaytime" = ${configtime} ];then
		mysql --login-path=asset VM_ASSET -e "update tecvm_info set uptimes ='$cal' where vmidx ='$vmidx'"
	fi
else
	echo "off"
	echo "업데이트 시작"
	mysql --login-path=asset VM_ASSET -e "update tecvm_info set status ='미동작' where vmidx ='$vmidx'"
	mysql --login-path=asset VM_ASSET -e "update tecvm_info set uptimes ='0' where vmidx ='$vmidx'"

	if [ "$boot_status" = 'on' ];then
		mysql --login-path=asset VM_ASSET -e "update tecvm_info set boot_switch ='off' where vmidx ='$vmidx'"
		mysql --login-path=asset VM_ASSET -e "insert into shutdown_history(status,info_update,vmidx) value('shutdown','$current_day $todaytime2', '$vmidx');"
	fi
	echo "업데이트 끝"
fi

##check uptimes
uptimes=`mysql --login-path=asset VM_ASSET -N -e "select uptimes from VM_ASSET.tecvm_info where vmidx ='$vmidx' and (except is null or except ='')"`
server_cnt=`mysql --login-path=asset VM_ASSET -N -e "SELECT count(*) FROM VM_ASSET.tecvm_info where ipadd like '%172.29.48.%' and status ='동작중'"` 
if [ "$todaytime" = $configtime ];then
	#write started shutdown time
	if [ "$uptimes" -gt 9 ] ;then
	 	echo "## $uptimes expire power off##"
	 	ssh 172.29.100.200 "vim-cmd vmsvc/power.off $vmidx"
		mysql --login-path=asset VM_ASSET -e "update tecvm_info set shutdown_date ='$current_day 19:00:00' where vmidx ='$vmidx';"
		#mysql --login-path=asset VM_ASSET -e "insert tecvm_info value('$current_day $todaytime', $vmidx);"
		mysql --login-path=asset VM_ASSET -e "update tecvm_info set boot_switch ='off' where vmidx ='$vmidx'"
		mysql --login-path=asset VM_ASSET -e "insert into shutdown_history(status,info_update,vmidx) value('expire','$current_day $todaytime2', '$vmidx');"
		
	fi
fi
done
	
	echo "total on cnt : $totalon"

	#if [ $todaytime = $configtime ];then
        #	# today server_cnt
        #	mysql --login-path=asset VM_ASSET -e "insert into liveserver_stat(daytime, server_cnt) values('$current_day','$server_cnt') "
	#fi
#ssh 172.29.100.200  "vim-cmd vmsvc/power.getstat 57" | grep Power
#echo "$todaytime" > /data/hi.log

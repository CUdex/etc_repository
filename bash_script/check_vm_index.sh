#!/bin/bash

#check vm index


#vmList=`ssh 172.29.100.200 "vim-cmd vmsvc/getallvms" | grep -v Vmid`

ssh 172.29.100.200 "vim-cmd vmsvc/getallvms" | awk '{print $1, $2}'| grep -v -e Vmid -e You > test.txt

#echo $(cat test.txt | awk '{print $1}')
#echo ----------------------------------
#echo $(cat test.txt | awk '{print $2}')
#echo ------------------------------------

vmIdx=$(cat test.txt | awk '{print $1}')
vmName=$(cat test.txt | awk '{print $2}')
vmLength=$(echo $vmIdx | wc -w)

for ((i=1; i <= ${vmLength}; i++));
do
	currentIdx=$(echo $vmIdx | awk '{print $'"${i}"'}')
	currentName=$(echo $vmName | awk '{print $'"${i}"'}')
	DBIdx=`mysql --login-path=asset VM_ASSET -N -e "select vmidx from VM_ASSET.tecvm_info where imgidx ='$currentName'"`

	if [ "x$DBIdx" != "x" ] && [ "$currentIdx" != "$DBIdx" ];
	then
		#echo "$currentName"
		#echo "$DBIdx"
		#echo "${currentIdx} x"
		mysql --login-path=asset VM_ASSET -e "update tecvm_info set vmidx ='$currentIdx' where imgidx ='$currentName'"	
	fi

	chcekVmName=`mysql --login-path=asset VM_ASSET -N -e "select vm_name from VM_ASSET.VM_Resource where vm_name ='$currentName'"`
	VmState=$(ssh 172.29.100.200 "vim-cmd vmsvc/get.summary $currentIdx" | grep -E "name|hostMemoryUsage|committed|powerState" | grep -v uncommitted | sed 's/ //g;s/,//g;s/"//g')
	RecVmMemory=$(echo $VmState | awk '{print $4}' | awk -F"=" '{print $2}')
	RecVmDisk=$(expr $(echo $VmState | awk '{print $3}' | awk -F"=" '{print $2}') / 1073741824)
	RecVmPower=$(echo $VmState | awk '{print $1}' | awk -F"=" '{print $2}')

	if [ "$RecVmMemory" = "<unset>" ];
	then
		RecVmMemory=0
	else
		RecVmMemory=$(expr $(echo $VmState | awk '{print $4}' | awk -F"=" '{print $2}') / 1024)
	fi

	if [ "x$chcekVmName" = "x" ];
	then
		mysql --login-path=asset VM_ASSET -e "insert into VM_ASSET.VM_Resource(vm_name, vm_memory, vm_disk, vm_idx, vm_powerstate) values('$currentName', $RecVmMemory, $RecVmDisk, $currentIdx, '$RecVmPower')"
	else
		mysql --login-path=asset VM_ASSET -e "update VM_Resource set vm_memory = $RecVmMemory, vm_disk = $RecVmDisk, vm_idx = $currentIdx, vm_powerstate = '$RecVmPower' where vm_name = '$currentName'"
	fi
done

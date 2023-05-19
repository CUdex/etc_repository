#!/bin/bash

sleep 60
product="nac6-ns"
branch="-rc"

if [ $product = "nac6" ]; then
	ps="CLOUD"
	image_name="NAC_IMAGE"
	base_repository="NAC_IMAGE=genians/genian-nac6"
	container_name="nac"
else
	ps="DKNS"
	image_name="DKNS_IMAGE"
	base_repository="DKNS_IMAGE=genians/genian-nac6-ns"
	container_name="dkns"
fi

origin_versions=`curl -s  https://hub.docker.com/v2/repositories/genians/genian-${product}${branch}/tags/?page_size=10 | jq -r '.results|.[]|.name'`
regex="NAC-${ps}-[R,B,C]{1,1}-[0-9]{1,9}-6.0.[0-9]{1,3}.[0-9]{4,4}$"
matches=()

for item in ${origin_versions}; do
  # 일치하면 matches 배열에 추가
  [[ $item =~ $regex ]] && matches+=($item)
done

finalized_version=${matches[0]}
list_length=${#matches[@]}

if [ ! $list_length -eq 1 ]; then
	bigger_number=`echo $finalized_version | awk -F'-' '{print $4}'`

	for item in ${matches[@]:1:$list_length}; do
		number=`echo $item | awk -F'-' '{print $4}'`
		if [[ $num -gt $bigger_number ]]; then
			bigger_number=$num
			finalized_version=$item
		fi
	done
fi

cd /usr/geni
ztna_version=`docker-compose exec -T ${container_name} cat .version`
times=`date`
echo "${times} ztna_version : ${ztna_version}"

current=`echo $finalized_version | awk -F'-' '{print $4}'`
now=`echo $ztna_version | awk -F'-' '{print $4}'`

if [ $current -gt $now ]; then
	echo "upgrade!!"
	current_image_name=`cat /usr/geni/conf/genian.conf | grep ${image_name} | grep -v LAST`
	current_branch=`cat /usr/geni/conf/genian.conf | grep BRANCH`

	base_repository+="${branch}:${finalized_version}"
	
	echo "$times $(echo $current_image_name | awk -F':' '{print $2}') to $(echo $base_repository | awk -F':' '{print $2}')"	
	sed -i "s@$current_image_name@$base_repository@g" /usr/geni/conf/genian.conf
	sed -i "s/$current_branch/BRANCH=$branch/g" /usr/geni/conf/genian.conf
	./compose.sh restart

fi

echo "${times} finished script docker image update"

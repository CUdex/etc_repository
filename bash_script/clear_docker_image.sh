#!/bin/bash
#made by cuyu9779

read -p "do you want type(1.CLOUD, 2.DKNS) : " TYPE
read -p "do you want ignore branch(1.R, 2.B, 3.C) : "  BRANCH
read -p "do you want ignore day(ex. 0728) : "  REVISION_DAY

if [ "$TYPE" = "1" ]
then
	TYPE=CLOUD
else
	TYPE=DKNS
fi

if [ "$BRANCH" = "1" ]
then
	BRANCH=R
else
	BRANCH=B
fi

IMAGES=$(docker images | grep $TYPE |grep -E -v "${BRANCH}.*${REVISION_DAY}" | awk '{print $3}')

echo "delete images : \n $IMAGES"
echo "----------------------------------------"
for image in $IMAGES
do
    docker rmi -f $image
done
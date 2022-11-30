#!/bin/bash
#made by cuyu9779

read -p "do you want type(1.CLOUD, 2.DKNS) : " TYPE
read -p "do you want ignore branch(1.R, 2.B, 3.C) : "  BRANCH
read -p "do you want ignore day(ex. 0728) : "  REVISION_DAY

if [ "$TYPE" = "1" ]
then
        TYPE=CLOUD
elif [ "$TYPE" = "2" ]
then
        TYPE=DKNS
else
        echo "invalid input type please which select 1 or 2"
        exit 100
fi

if [ "$BRANCH" = "1" ]
then
        BRANCH=R
elif [ "$BRANCH" = "2" ]
then
        BRANCH=B
elif [ "$BRANCH" = "3" ]
then
        BRANCH=C
else
        echo "invalid input Branch please which select 1 or 2, 3"
        exit 100
fi

IMAGES=$(docker images | grep $TYPE |grep -E -v "${BRANCH}.*${REVISION_DAY}" | awk '{print $3}')

echo "delete images : $IMAGES"
echo $IMAGES
echo "----------------------------------------"
for image in $IMAGES
do
    docker rmi -f $image
done
~

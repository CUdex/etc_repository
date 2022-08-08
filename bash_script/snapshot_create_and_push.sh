#!/bin/bash

# local path
LOCALCONF=/disk/sys/conf/local.conf

# elastic pass
ES_ACCESS_PASSWORD=`cat $LOCALCONF | grep '^log-server_access-password=' | awk -F'=' '{print $2}'`
ES_ACCESS_PASSWORD=`/usr/geni/aes256 -d $ES_ACCESS_PASSWORD`
# DB ID
DB_USER=`cat $LOCALCONF | grep '^data-server_username=' | awk -F'=' '{print $2}'`
# DB PASS
DB_PASSWORD=`cat $LOCALCONF | grep '^data-server_password=' | awk -F'=' '{print $2}'`
DB_PASSWORD=`/usr/geni/aes256 -d $DB_PASSWORD`

# BACKUPTMP state
BACKSTAT=`curl -XGET 'http://localhost:9200/_snapshot/LOGBACKUPTMP' -u elastic:$ES_ACCESS_PASSWORD 2> /dev/null | awk -F '"' '{print $2}'`

# LOGBACKUPTMP setting check
if [ "x$BACKSTAT" != "xLOGBACKUPTMP" ]; then
	/usr/geni/curl -u elastic:$ES_ACCESS_PASSWORD -s XPUT -H "Content-Type: application/json" "localhost:9200/_snapshot/LOGBACKUPTMP" -d "{\"type\": \"fs\", \"settings\": {\"compress\": false, \"location\": \"/disk/data/LOGBACKUPTMP\"}}"
fi

/bin/rm -rf /disk/data/LOGBACKUPTMP/*

TIME=`date +%Y-%m -d -1day`
NOWTIME=`date +%Y%m%d-%H%M%S`

/usr/geni/curl -u elastic:$ES_ACCESS_PASSWORD -s XPUT -H "Content-Type: application/json" "http://localhost:9200/_snapshot/LOGBACKUPTMP/alder-log-88750-${NOWTIME}?wait_for_completion=true" -d "{\"indices\" : \"nac-node-${TIME}\"}" > /dev/null 

# backup ip address
BACKUPIP=`echo "select CONF_VALUE from ALDER.CONF where CONF_KEY = 'BACKUPDEVPATH' \G;" | /usr/bin/mysql -u $DB_USER -p$DB_PASSWORD 2> /dev/null | grep "^CONF" | awk -F ': ' '{print $2}'`
# backup id
BACKUPID=`echo "select CONF_VALUE from ALDER.CONF where CONF_KEY = 'DEVUSERID' \G;" | /usr/bin/mysql -u $DB_USER -p$DB_PASSWORD 2> /dev/null | grep "^CONF" | awk -F ': ' '{print $2}'`
# backup port
BACKUPPORT=`echo "select CONF_VALUE from ALDER.CONF where CONF_KEY = 'BACKUPPORT' \G;" | /usr/bin/mysql -u $DB_USER -p$DB_PASSWORD 2> /dev/null | grep "^CONF" | awk -F ': ' '{print $2}'`
# backup pass
BACKUPPASS=`echo "select CONF_VALUE from ALDER.CONF where CONF_KEY = 'DEVUSERPASS' \G;" | /usr/bin/mysql -u $DB_USER -p$DB_PASSWORD 2> /dev/null | grep "^CONF" | awk -F ': ' '{print $2}'`

# backup time
NOWTIME=`date +%Y%m%d-%H%M%S`


BACKUPFILE=ALDER-LOG-88750-$NOWTIME.tar.gz

cd /disk/data ; /bin/tar -czvf LOGBACKUPTMP/$BACKUPFILE LOGBACKUPTMP/* > /dev/null

echo put /disk/data/LOGBACKUPTMP/$BACKUPFILE > /disk/sys/conf/log_sftp.batch
echo quit >> /disk/sys/conf/log_sftp.batch

/bin/sshpass -p "${BACKUPPASS}" /bin/sftp -P $BACKUPPORT -oBatchMode=no -oStrictHostKeyChecking=no -oConnectTimeout=10 -oConnectionAttempts=3 -b /disk/sys/conf/log_sftp.batch $BACKUPID@$BACKUPIP > /dev/null

echo 전송 완료

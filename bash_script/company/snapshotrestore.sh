#!/bin/bash

#set -x

LOCALCONF=/disk/sys/conf/local.conf

ESUSER=elastic
ESPASS=`cat $LOCALCONF | grep '^log-server_access-password=' | awk -F'=' '{print $2}'`
ESPASS=`/usr/geni/aes256 -d $ESPASS`

SNAPSHOTREPODIR=/disk/data/logrepo
SNAPSHOTREPO=`echo "${SNAPSHOTREPODIR//\//\\\/}"`

chown elasticsearch:elasticsearch ${SNAPSHOTREPODIR} -R
sed -i "s/path\.repo.*/path\.repo: [\"\/disk\/data\/LOGBACKUP\", \"${SNAPSHOTREPO}\"]/" /usr/geni/conf.base/elasticsearch/elasticsearch.yml

# kill procmond, centerd
systemctl stop procmon.service
systemctl stop center*.service

systemctl stop elasticsearch.service
systemctl start elasticsearch.service

echo -e "\nwaiting elasticsearch start"
sleep 10
echo -e "\nregister repository"
curl -s -XPUT -H "Content-Type:application/json;charset=UTF-8" "http://localhost:9200/_snapshot/esrestore" -d"{\"type\":\"fs\",\"settings\":{\"location\": \"${SNAPSHOTREPODIR}\",\"compress\": false}}" -u"${ESUSER}:${ESPASS}"

echo ""

# list debug
curl -XGET "http://localhost:9200/_cat/snapshots/esrestore?v=true&s=id&pretty" -u"${ESUSER}:${ESPASS}"

echo -e "\nsnapshot restore start..."

SNAPSHOTS=`curl -s -XGET "http://localhost:9200/_cat/snapshots/esrestore?pretty" -u"${ESUSER}:${ESPASS}" | awk -F ' ' '{print $1}'`

# delete before indices
curl -s -XGET "http://localhost:9200/_cat/indices/pi_*,cvelist,.security-6,nac-filter,nac-node-*,nac-ipam-*,nac-radius-*,nac-ifbandwidth-*?h=idx" -u"${ESUSER}:${ESPASS}" | awk -F ' ' '{print $1}' | xargs -I{} curl -s -XDELETE "localhost:9200/{}" -u"${ESUSER}:${ESPASS}" > /dev/null

# close all indices
curl -s -XGET "http://localhost:9200/_cat/indices?h=idx" -u"${ESUSER}:${ESPASS}" | awk -F ' ' '{print $1}' | xargs -I{} curl -s -XPOST "localhost:9200/{}/_close?pretty" -u"${ESUSER}:${ESPASS}" > /dev/null

for SHNAPSHOT in $SNAPSHOTS; do
		echo -e "\n${SHNAPSHOT}"
			# restore (ES 5.x index name: nac-2021-08 , ES 6.x index name: nac-node-2021-08)
				curl -s -XPOST -H "Content-Type:application/json;charset=UTF-8" "http://localhost:9200/_snapshot/esrestore/$SHNAPSHOT/_restore?wait_for_completion=true" -d'{"indices":"nac-node-*,nac-ipam-*,nac-radius-*,nac-ifbandwidth-*","ignore_unavailable":true,"include_global_state":true}' -u"${ESUSER}:${ESPASS}"
					echo ""

						# close all indices
							curl -s -XGET "http://localhost:9200/_cat/indices?h=idx" -u"${ESUSER}:${ESPASS}" | awk -F ' ' '{print $1}' | xargs -I{} curl -s -XPOST "http://localhost:9200/{}/_close?pretty" -u"${ESUSER}:${ESPASS}" > /dev/null
						done

						# open all indices
						curl -s -XGET "http://localhost:9200/_cat/indices?h=idx" -u"${ESUSER}:${ESPASS}" | awk -F ' ' '{print $1}' | xargs -I{} curl -s -XPOST "http://localhost:9200/{}/_open?pretty" -u"${ESUSER}:${ESPASS}" > /dev/null

						echo ""

						# indices list
						curl -XGET 'http://localhost:9200/_cat/indices' -u"${ESUSER}:${ESPASS}"

						sed -i "s/path\.repo.*/path\.repo: [\"\/disk\/data\/LOGBACKUP\"]/" /usr/geni/conf.base/elasticsearch/elasticsearch.yml

						systemctl stop elasticsearch.service
						systemctl start elasticsearch.service
						systemctl start procmon.service
						# procmond restart centerd

						echo -e "\nsnapshot restore end..."

curl -XDELETE "http://logserver:9200/_template/template_nac" -u"${ESUSER}:${ESPASS}"
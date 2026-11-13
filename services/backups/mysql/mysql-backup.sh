#!/bin/bash
DAYS_TO_KEEP=7
DATE=$(date '+%d.%m.%Y_%H-%M-%S')

cd $DOCKER_PATH
/usr/bin/docker-compose exec -T db mariabackup --backup --user=$DB_USER --password=$DB_PWD --databases=$DB_NAME --stream=xbstream | /usr/bin/gzip > $DOCKER_PATH/$BACKUP_PATH/backup_$DATE.gz
/usr/bin/find $DOCKER_PATH/$BACKUP_PATH/* -name "*.gz" -ctime +$DAYS_TO_KEEP -exec rm {} \;

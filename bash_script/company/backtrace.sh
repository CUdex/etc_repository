#!/bin/sh
if [ "x$1" = "x" ] ; then
	echo "Usage: backtrace.sh [processname]";
	exit
fi

PIDS=`ps -efT | grep $1 | awk '{print $3}'`

for PID in $PIDS; do
	gdb --pid=$PID --batch --ex=bt
done

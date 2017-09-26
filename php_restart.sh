#!/bin/bash


process_num=`ps -aux |grep "index_cli\.php" |awk '{print $2}'`
for i in $process_num;do
    kill -9 $i
done


/usr/bin/php  /var/www/yc120_api/index_cli.php /Kbox/Queue/readqueue &
/usr/bin/php  /var/www/yc120_api/index_cli.php /Kbox/Wxreport/index &
/usr/bin/php  /var/www/yc120_api/index_cli.php /Kbox/Queue/rangegl &
/usr/bin/php  /var/www/yc120_api/index_cli.php /Kbox/Queue/statistics &

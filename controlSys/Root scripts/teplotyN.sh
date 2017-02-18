#!/bin/bash

echo "28.589649080000=" > /var/www/html/Home-system/www/temp.txt &&
tail /mnt/1wire/28.589649080000/temperature | tee -a /var/www/html/Home-system/www/temp.txt &&
echo "|" >> /var/www/html/Home-system/www/temp.txt &&
echo "28.FF1E82631603=" >> /var/www/html/Home-system/www/temp.txt &&
tail /mnt/1wire/28.FF1E82631603/temperature | tee -a /var/www/html/Home-system/www/temp.txt &&
echo "|" >> /var/www/html/Home-system/www/temp.txt
echo "28.BFA248080000=" >> /var/www/html/Home-system/www/temp.txt &&
tail /mnt/1wire/28.BFA248080000/temperature | tee -a /var/www/html/Home-system/www/temp.txt &&
echo "|" >> /var/www/html/Home-system/www/temp.txt
echo "28.3B8948080000=" >> /var/www/html/Home-system/www/temp.txt &&
tail /mnt/1wire/28.3B8948080000/temperature | tee -a /var/www/html/Home-system/www/temp.txt &&
echo "|" >> /var/www/html/Home-system/www/temp.txt
echo "28.D4BC47080000=" >> /var/www/html/Home-system/www/temp.txt &&
tail /mnt/1wire/28.D4BC47080000/temperature | tee -a /var/www/html/Home-system/www/temp.txt

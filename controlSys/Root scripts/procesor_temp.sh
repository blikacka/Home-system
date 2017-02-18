#!/bin/sh
cpu_temp=$(perl -e 'm/(\d+)/; $x=$1; s/\d+//; printf ("%.1f", ( $x / 1000))' -p /sys/class/thermal/thermal_zone0/temp) && curl --request GET "http://10.10.10.148:1019/Home-system/www/temperature/save-temp?sensor=pitemp&temperature=$cpu_temp&cronHash=cb7ddd82ced9a4e1a9a4e1afd7abcf13cd8b862475bf55cc8feba0bf95d1fc03bdc536"

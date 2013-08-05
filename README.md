#Aushowmatic

Aushowmatic is a light torrent-based PVR. I'm currently using it on Raspbian but it should work on all distributions based on Debian

**Note:** It used to parse dailytvtorrents.org but it is offline at the moment, so now it parse eztv.it. (Not ezrss.it because their data seems not as good as EZTV)

**Note 2:** I know that EZTV parsing is slow and not optimal, but it mostly done by crontab so it is not really a problem :p

##Requirements

- PHP
- transmission-daemon
- transmission-remote

##Installation

###Step 1:
Download source and configure by editing config.php
(at least TRANSMISSION_CMD and TRANSMISSION_WEB)

###Step 2:
Add in your crontab something like this:


	0 */8 * * * php /var/www/aushowmatic/_cron.php

###Step 3:

Update permissions to allow write access on ./files/*
	
###Step 4(optionnal):
If you want to be able to execute systems commands (like start/stop XBMC, poweroff or reboot) by sudo, you have to add some permissions by editing /etc/sudoers:


	www-data ALL=NOPASSWD: /usr/lib/xbmc/xbmc.bin, /bin/kill, /sbin/poweroff, /sbin/reboot, /usr/bin/du


WARNING: THIS IS NOT RECOMMENDED IF YOUR DEVICE IS ACCESSIBLE FROM WAN

##TODO

- improve CSS for handheld devices

##License

Released under the WTFPL license - http://www.wtfpl.net/

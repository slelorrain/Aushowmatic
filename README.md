#Aushowmatic

Aushowmatic is a light torrent-based PVR (personal video recorder). I'm currently using it on Raspbian but it should work on all distributions based on Debian

**Note:** It's using showRSS (showrss.info) as source. Also, the EZTV parser wasn't updated because of the [hostile takeover](https://en.wikipedia.org/wiki/EZTV#Hostile_takeover)

![Screenshot](screenshot.png?raw=true)

##Requirements

- PHP
- php5-curl
- transmission-daemon
- transmission-remote

##Installation

###Step 1:
Download source. If you have already Git installed, you can clone with:


	git clone https://github.com/slelorrain/Aushowmatic.git
	

Otherwise, you can download archive with Wget:
	
	
	wget https://github.com/slelorrain/Aushowmatic/archive/master.zip
		

And configure by editing ./conf/user_config.json
(at least TRANSMISSION_CMD and TRANSMISSION_WEB)

###Step 2:
Edit your crontab with:
	
	
	crontab -e
	 
	
And add a rule looking like this one (check at least the path):


	0 */8 * * * php /var/www/Aushowmatic/_cron.php


(in this case, the script will be called every 8 hours. Modulate according to your needs)

###Step 3:
Check that your server application have write access on ./files/*. If not, you must update permissions.

###Step 4 (optional):
If you want to be able to execute systems commands (like start/stop Kodi, poweroff or reboot) by sudo, you have to add some permissions by editing /etc/sudoers:


	www-data ALL=NOPASSWD: /usr/lib/kodi/kodi.bin, /bin/kill, /sbin/poweroff, /sbin/reboot, /usr/bin/du


WARNING: THIS IS NOT RECOMMENDED IF YOUR DEVICE IS ACCESSIBLE FROM WAN

##License

Released under the WTFPL license - http://www.wtfpl.net/

[![forthebadge](http://forthebadge.com/images/badges/built-with-love.svg)](http://forthebadge.com)


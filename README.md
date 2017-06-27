# Aushowmatic

Aushowmatic is a light torrent-based PVR (personal video recorder). I'm currently using it on Raspbian but it should work on all distributions based on Debian.

**Note:** It's using showRSS ([showrss.info]) as source. Also, the EZTV parser wasn't updated because of the [hostile takeover](https://en.wikipedia.org/wiki/EZTV#Hostile_takeover)

![Screenshot](resources/screenshot.png?raw=true)

## Requirements

- PHP
- php5-curl
- transmission-daemon
- transmission-remote

## Installation

### Step 1:
Clone the repository with [Git]:
```
git clone https://github.com/slelorrain/Aushowmatic.git
```

And configure by editing `.env` (at least TRANSMISSION_CMD and TRANSMISSION_WEB)

### Step 2:
Install [Composer] (optional if you already have it):
```
curl -s http://getcomposer.org/installer | php
```

Install dependencies:
```
php composer.phar install
```

### Step 3:
Edit your crontab with:
```
crontab -e
```

And add a rule looking like this one (check at least the path):
```
0 */8 * * * php /var/www/Aushowmatic/_cron.php
```
(in this case, the script will be called every 8 hours. Modify according to your needs.)

### Step 4:
Check that your server application have write access on `./files/*`. If not, you must update permissions.

### Step 5 (optional):
If you want to be able to execute systems commands (like start/stop Kodi, poweroff or reboot) by sudo, you have to add some permissions by editing `/etc/sudoers`:
```
www-data ALL=NOPASSWD: /usr/lib/kodi/kodi.bin, /bin/kill, /sbin/poweroff, /sbin/reboot, /usr/bin/du
```

**WARNING: THIS IS NOT RECOMMENDED IF YOUR DEVICE IS ACCESSIBLE FROM WAN**

## License

Released under the [WTFPL license].

[![forthebadge](http://forthebadge.com/images/badges/built-with-love.svg)](http://forthebadge.com)

[Git]: https://git-scm.com
[Composer]: https://getcomposer.org
[showrss.info]: https://showrss.info
[WTFPL license]: http://www.wtfpl.net
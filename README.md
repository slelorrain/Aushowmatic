# Aushowmatic

Aushowmatic is a light torrent-based PVR (personal video recorder). I'm currently using it on Raspbian but it should work on all distributions based on Debian.

**Note:** It's using showRSS ([showrss.info]) and EZTV ([eztv.ag]) as source. Subtitles providers are OpenSubtitles ([opensubtitles.org]) and Addic7ed ([addic7ed.com]).

![Screenshot](resources/screenshot.png?raw=true)

## Contents

* [Requirements](#requirements)
* [Installation](#installation)
* [License](#license)
* [Thanks](#thanks)

## Requirements

- PHP >= 5.3 with cURL support
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
Check that your server application have write access on `./resources/feeds/*`. If not, you must update permissions.

### Step 5 (optional):
If you want to be able to execute systems commands (like start Kodi, poweroff or reboot) by sudo, you have to add some permissions by editing `/etc/sudoers`:
```
www-data ALL=NOPASSWD: /usr/bin/kodi, /sbin/poweroff, /sbin/reboot
```

**WARNING: THIS IS NOT RECOMMENDED IF YOUR DEVICE IS ACCESSIBLE FROM WAN**

## License

Released under the [WTFPL license].

## Thanks

[@PXgamer](https://github.com/PXgamer), [@zadkiel87](https://github.com/zadkiel87)

[![forthebadge](http://forthebadge.com/images/badges/built-with-love.svg)](http://forthebadge.com)

[Git]: https://git-scm.com
[Composer]: https://getcomposer.org
[showrss.info]: https://showrss.info
[eztv.ag]: https://eztv.ag
[opensubtitles.org]: https://www.opensubtitles.org
[addic7ed.com]: http://www.addic7ed.com
[WTFPL license]: http://www.wtfpl.net

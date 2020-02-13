# weDevs Demo

## Backup-restore Scripts

Find the scripts [here](https://bitbucket.org/wedevs/demo-reset/src/master/).

## Cron Setup

Go to `/etc/cron.daily` and create a file. For example: `reset-demo`

Make it executable with this content:

```
#!/usr/bin/env bash

/var/www/wpufdemo.wedevs.com/scripts/reset.sh
/var/www/pmdemo.wedevs.com/scripts/reset.sh
/var/www/dokandemo.wedevs.com/scripts/reset.sh
/var/www/weformsdemo.wedevs.com/scripts/reset.sh
```

Or, if using `crontab -e` command:

```
0 0 * * * /path/to/site/scripts/reset.sh
```

## `wp-config.php` constants

```
define( 'WP_DEBUG', false );
define( 'WP_DEBUG_LOG', false );
define( 'WP_DEBUG_DISPLAY', false );
define( 'WP_SITEURL', 'https://site.com' );
define( 'WP_HOME', 'https://site.com' );
define( 'WP_AUTO_UPDATE_CORE', true );
define( 'DISALLOW_FILE_EDIT', true );

define( 'DISABLE_THEME', true );
define( 'DISABLE_USERS', true );
define( 'DISABLE_SETTINGS', true );
define( 'DISABLE_TOOLS', true );
define( 'DISABLE_PLUGINS', true );
```

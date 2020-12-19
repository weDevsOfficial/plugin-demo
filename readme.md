# weDevs Demo

## Installation

Create mu-plugins folder

```bash
mkdir wp-content/mu-plugins
```

Download the plugin into mu-plugin

```bash
wget https://raw.githubusercontent.com/weDevsOfficial/plugin-demo/master/demo.php wp-content/mu-plugins/
```


## `wp-config.php` constants

```php
define( 'WP_DEBUG', false );
define( 'WP_DEBUG_LOG', false );
define( 'WP_DEBUG_DISPLAY', false );
define( 'WP_SITEURL', 'https://site.com' );
define( 'WP_HOME', 'https://site.com' );
define( 'WP_AUTO_UPDATE_CORE', true );
define( 'DISALLOW_FILE_EDIT', true );

// disable the menu and capability
define( 'DISABLE_THEME', true );
define( 'DISABLE_USERS', true );
define( 'DISABLE_SETTINGS', true );
define( 'DISABLE_TOOLS', true );
define( 'DISABLE_PLUGINS', true );
```

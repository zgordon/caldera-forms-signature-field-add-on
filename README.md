[![Build Status](https://travis-ci.org/kadimi/starter.svg?branch=master)](https://travis-ci.org/kadimi/starter)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/kadimi/starter/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/kadimi/starter/?branch=master)
[![Dependency Status](https://www.versioneye.com/user/projects/57d5e1948d1bad00444d350a/badge.svg?style=flat-square)](https://www.versioneye.com/user/projects/57d5e1948d1bad00444d350a)

# Caldera for Signature Field Plugin

This plugin lets you add a signature capture field to your Caldera Forms.  Collect a signature via a trackpad, mouse or device.

## Require other plugins

The plugin can require other plugins using the `require_plugin` method in the `init` method, the `require_plugin` method first parameter should be the plugin slug although in some circumstances the plugin name works too.

### Example:

```php
/**
 * Initializes plugin
 */
protected function init() {
	// ...
	// ...
	$this->require_plugin( 'titan-framework' );
	$this->require_plugin( 'Advanced Custom Fields' );
	// ...
	// ...
}
```

## Code Sniffing

The PHPCS ruleset included has the following specifications:

- It uses the `WordPress` standard
- It includes PHP files only
- It excludes files under any folder called `vendor`

The command you need is:

```bash
phpcs --standard=codesniffer.ruleset.xml
```

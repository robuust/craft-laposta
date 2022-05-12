LaPosta plugin for Craft
=================

Plugin that allows you to display LaPosta forms

## Requirements

This plugin requires Craft CMS 3.1.0 or later.

## Installation

To install the plugin, follow these instructions.

1. Open your terminal and go to your Craft project:

        cd /path/to/project

2. Then tell Composer to load the plugin:

        composer require robuust/craft-laposta

3. In the Control Panel, go to Settings → Plugins and click the “Install” button for Client Online.

## Config

Create a file called `laposta.php` in you Craft config folder with the following contents:

```php
<?php

return [
    // General
    'apiKey' => 9999, // YOUR API KEY
];

```

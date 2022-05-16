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
    'apiKey' => 'YOUR_API_KEY',
];

```

## Usage

Create a new "LaPosta" field and add it to the desired element's field layout.
Now when editing such element you can select a LaPosta list to use.

In your front-end templates you can render this LaPosta list as a form.

## Example

Here is an example that renders a LaPosta form. You can change and style this example any way you want.

```twig
  {% if errors is defined %}
    <p>{{ errors.message|t }}</p>
  {% endif %}
  <form method="post">
    {{ csrfInput() }}
    {{ actionInput('laposta/submit') }}
    {{ redirectInput('url_to_redirect_to') }}
    {% for field in entry.form %}
      <input id="{{ field.id }}" name="{{ field.name }}" type="{{ field.type }}" placeholder="{{ field.label }}"{% if field.required %} required{% endif %} value="{{ field.value }}" />
      <br />
    {% endfor %}
    <input type="submit" />
  </form>
```

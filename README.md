Laposta plugin for Craft
=================

Plugin that allows you to display Laposta forms

## Requirements

This plugin requires Craft CMS 3.1.0 or later.

## Installation

To install the plugin, follow these instructions.

1. Open your terminal and go to your Craft project:

        cd /path/to/project

2. Then tell Composer to load the plugin:

        composer require robuust/craft-laposta

3. In the Control Panel, go to Settings → Plugins and click the “Install” button for Laposta.

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

Create a new "Laposta" field and add it to the desired element's field layout.
Now when editing such element you can select a Laposta list to use.

In your front-end templates you can render this Laposta list as a form.

## Example

Here is an example that renders a Laposta form. You can change and style this example any way you want.

```twig
  {% if errors is defined %}
    <p>{{ errors.message|t }}</p>
  {% endif %}
  <form method="post">
    {{ csrfInput() }}
    {{ actionInput('laposta/submit') }}
    {{ redirectInput('url_to_redirect_to') }}
    {% for field in entry.form|filter(field => field.inform == "true") %}
      {% switch field.type %}
        {% case 'radio' or 'checkbox' %}
          {% for option in field.options %}
            <input id="{{ field.id }}-{{ loop.index }}" name="{{ field.name }}{% if field.type == 'checkbox' %}[]{% endif %}" type="{{ field.type }}" {% if field.required %} required{% endif %} value="{{ option }}" /> <label for="{{ field.id }}-{{ loop.index }}">{{ option }}</label>
          {% endfor %}
        {% default %}
          <input id="{{ field.id }}" name="{{ field.name }}" type="{{ field.type }}" placeholder="{{ field.label }}"{% if field.required %} required{% endif %} value="{{ field.value }}" />
      {% endswitch %}
      <br />
    {% endfor %}
    <input type="submit" />
  </form>
```

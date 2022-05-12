<?php

namespace robuust\laposta;

use craft\events\RegisterComponentTypesEvent;
use craft\services\Fields;
use robuust\laposta\fields\LaPosta;
use yii\base\Event;

/**
 * LaPosta plugin.
 */
class Plugin extends \craft\base\Plugin
{
    /**
     * Initializes the plugin.
     */
    public function init()
    {
        parent::init();

        Event::on(Fields::class, Fields::EVENT_REGISTER_FIELD_TYPES, function (RegisterComponentTypesEvent $event) {
            $event->types[] = LaPosta::class;
        });
    }
}

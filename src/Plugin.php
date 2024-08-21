<?php

namespace robuust\laposta;

use craft\events\RegisterComponentTypesEvent;
use craft\services\Fields;
use Laposta;
use robuust\laposta\fields\Laposta as LapostaField;
use robuust\laposta\models\Settings;
use yii\base\Event;

/**
 * Laposta plugin.
 */
class Plugin extends \craft\base\Plugin
{
    /**
     * Initializes the plugin.
     */
    public function init()
    {
        parent::init();

        // Set api key
        Laposta::setApiKey($this->settings->apiKey);

        // Register fieldtype
        Event::on(Fields::class, Fields::EVENT_REGISTER_FIELD_TYPES, function (RegisterComponentTypesEvent $event) {
            $event->types[] = LapostaField::class;
        });
    }

    /**
     * {@inheritdoc}
     */
    protected function createSettingsModel(): Settings
    {
        return new Settings();
    }
}

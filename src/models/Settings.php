<?php

namespace robuust\laposta\models;

use craft\base\Model;

/**
 * Settings model.
 */
class Settings extends Model
{
    /**
     * @var int
     */
    public $apiKey;

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['apiKey'], 'required'],
        ];
    }
}

<?php

namespace robuust\laposta\fields;

use Craft;
use craft\base\ElementInterface;
use craft\fields\Dropdown;
use Laposta_Field;
use Laposta_List;

/**
 * Laposta Field.
 *
 * @author    Bob Olde Hampsink <bob@robuust.digital>
 * @copyright Copyright (c) 2022, Robuust
 * @license   MIT
 *
 * @see       https://robuust.digital
 */
class Laposta extends Dropdown
{
    /**
     * {@inheritdoc}
     */
    public static function displayName(): string
    {
        return Craft::t('app', 'Laposta');
    }

    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        parent::init();

        // Get all lists
        $results = Craft::$app->getCache()->get('laposta');
        if ($results === false) {
            try {
                $list = new Laposta_List();
                $results = $list->all();

                Craft::$app->getCache()->set('laposta', $results);
            } catch (\Exception) {
                $results = ['data' => []];
            }
        }

        // Set as dropdown options
        foreach ($results['data'] as $result) {
            $this->options[] = [
                'value' => $result['list']['list_id'],
                'label' => $result['list']['name'],
            ];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function normalizeValue($value, ElementInterface $element = null): mixed
    {
        // Get list id
        $list = (string) parent::normalizeValue($value, $element);

        try {
            // Get all fields from list
            $field = new Laposta_Field($list);
            $results = $field->all();

            // Add hidden list id field
            $fields = [
                [
                    'id' => $list,
                    'name' => 'list_id',
                    'label' => Craft::t('site', 'List'),
                    'type' => 'hidden',
                    'required' => true,
                    'value' => $list,
                    'options' => [],
                ],
            ];

            // Add list fields
            foreach ($results['data'] as $result) {
                $fields[] = [
                    'id' => $result['field']['field_id'],
                    'name' => trim($result['field']['tag'], '{}'),
                    'label' => $result['field']['name'],
                    'type' => $this->getType($result['field']),
                    'required' => $result['field']['required'],
                    'value' => $result['field']['defaultvalue'],
                    'options' => $result['field']['options'] ?? [],
                ];
            }
        } catch (\Exception) {
            $fields = [];
        }

        return $fields;
    }

    /**
     * {@inheritdoc}
     */
    public function serializeValue($value, ElementInterface $element = null): mixed
    {
        if (is_array($value) && count($value)) {
            $value = $value[0]['value'];
        }

        return parent::serializeValue($value, $element);
    }

    /**
     * {@inheritdoc}
     */
    public function getElementValidationRules(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    protected function inputHtml($value, ElementInterface $element = null): string
    {
        /** @var SingleOptionFieldData $value */
        $options = $this->translatedOptions();

        return Craft::$app->getView()->renderTemplate('_includes/forms/select', [
            'id' => $this->getInputId(),
            'describedBy' => $this->describedBy,
            'name' => $this->handle,
            'value' => $value[0]['value'] ?? null,
            'options' => $options,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getSettings(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getSettingsHtml(): ?string
    {
        return null;
    }

    /**
     * Get type.
     *
     * @param array $field
     *
     * @return string
     */
    private function getType(array $field): string
    {
        if ($field['is_email']) {
            return 'email';
        }

        switch ($field['datatype']) {
            case 'numeric':
                return 'number';
            case 'select_single':
                return 'radio';
            case 'select_multiple':
                return 'checkbox';
            default:
                return $field['datatype'];
        }
    }
}

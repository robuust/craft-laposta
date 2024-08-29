<?php

namespace robuust\laposta\fields;

use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use craft\enums\AttributeStatus;
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
    public function getStatus(ElementInterface $element): ?array
    {
        // If the value is invalid and has a default value (which is going to be pulled in via inputHtml()),
        // preemptively mark the field as modified
        /** @var SingleOptionFieldData $value */
        $value = $element->getFieldValue($this->handle);

        if (!isset($value[0]) || !$value[0]['valid'] && $this->defaultValue() !== null) {
            return [
                AttributeStatus::Modified,
                Craft::t('app', 'This field has been modified.'),
            ];
        }

        return Field::getStatus($element);
    }

    /**
     * {@inheritdoc}
     */
    public function normalizeValue(mixed $value, ?ElementInterface $element = null): mixed
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
                    'valid' => true,
                    'options' => [],
                    'inform' => 'true',
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
                    'inform' => $result['field']['in_form'],
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
    public function serializeValue(mixed $value, ?ElementInterface $element = null): mixed
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
    protected function inputHtml(mixed $value, ?ElementInterface $element, bool $inline): string
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

<?php

namespace AlexSabur\OrchidEditorJSField;

use Orchid\Screen\Field;

/**
 *
 * @method EditorJS value($value = null)
 */
class EditorJS extends Field
{
    /**
     * @var string
     */
    protected $view = 'orchid-editorjs-field::field';

    /**
     * Default attributes value.
     *
     * @var array
     */
    protected $attributes = [
        'tools' => [],
        'value' => [],
        'title' => '',
        'hidden' => false,
        'readonly' => false,
        'placeholder' => null,
        'minHeight' => 300,
    ];

    /**
     * Attributes available for a particular tag.
     *
     * @var array
     */
    protected $inlineAttributes = [
        'value',
        'name',
    ];

    public function __construct()
    {
        $this->addBeforeRender(function () {
            $value = $this->get('value');

            if (!is_string($value)) {
                $this->set('value', json_encode($value, ENT_QUOTES));
            }
        });

        $this->addBeforeRender(function () {
            $this->set(
                'tools',
                collect($this->get('tools'))
                    ->mapWithKeys(function (Tools\Tool $tool) {
                        return [
                            $tool->getName() => $tool->toArray(),
                        ];
                    })
                    ->toJson(ENT_QUOTES)
            );
        });
    }

    /**
     * @param Tools\Tool[]|string $tools
     * @return $this
     */
    public function tools($tools)
    {
        if (is_array($tools)) {
            $this->set('tools', $tools);
        }

        return $this;
    }
}

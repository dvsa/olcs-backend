<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Auth\Service\OpenAm\Callback;

abstract class AbstractTextPromptCallback implements CallbackInterface
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $value;

    /**
     * @var string
     */
    private $label;

    /**
     * @var string
     */
    private $name;

    /**
     * @param string $label Label
     * @param string $name  Name
     * @param string $value Value
     *
     * @return void
     */
    public function __construct(string $label, string $name, string $value)
    {
        $this->label = $label;
        $this->name = $name;
        $this->value = $value;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'output' => [['name' => 'prompt', 'value' => $this->label]],
            'input' => [
                [
                    'name' => $this->name,
                    'value' => $this->getFilteredValue()
                ]
            ]
        ];
    }

    /**
     * Get filtered value
     *
     * @return string
     */
    protected function getFilteredValue()
    {
        return $this->value;
    }
}

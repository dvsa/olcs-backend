<?php

namespace Dvsa\Olcs\Api\Service\Toggle;

use Qandidate\Toggle\Context;
use Qandidate\Toggle\Toggle;
use Qandidate\Toggle\ToggleManager;

/**
 * Class ToggleService
 */
class ToggleService
{
    /**
     * @var ToggleManager
     */
    private $toggleManager;

    public function __construct(ToggleManager $toggleManager)
    {
        $this->toggleManager = $toggleManager;
    }

    /**
     * return whether the toggle is enabled
     *
     * @param string  $name
     * @param Context $context
     *
     * @return bool
     */
    public function isEnabled(string $name, Context $context = null): bool
    {
        if (!$context instanceof Context) {
            $context = new Context();
        }

        return $this->toggleManager->active($name, $context);
    }

    /**
     * override config to toggle a named feature on (for this single request only)
     *
     * @param string $name
     *
     * @return Toggle
     */
    public function enable(string $name): Toggle
    {
        $toggle = $this->fetchToggle($name);
        return $this->enableToggle($toggle);
    }

    /**
     * override config to toggle a named feature off (for this single request only)
     *
     * @param string $name
     *
     * @return Toggle
     */
    public function disable(string $name): Toggle
    {
        $toggle = $this->fetchToggle($name);
        return $this->disableToggle($toggle);
    }

    /**
     * make a toggle active
     *
     * @param Toggle $toggle
     *
     * @return Toggle
     */
    private function enableToggle(Toggle $toggle): Toggle
    {
        $toggle->activate(Toggle::ALWAYS_ACTIVE);
        $this->updateToggle($toggle);
        return $toggle;
    }

    /**
     * make a toggle inactive
     *
     * @param Toggle $toggle
     *
     * @return Toggle
     */
    private function disableToggle(Toggle $toggle): Toggle
    {
        $toggle->deactivate();
        $this->updateToggle($toggle);
        return $toggle;
    }

    /**
     * fetch a toggle object
     *
     * @param string $name
     *
     * @return Toggle
     */
    private function fetchToggle(string $name): Toggle
    {
        /** @var array|Toggle[] $toggleCollection */
        $toggleCollection = $this->toggleManager->all();
        return $toggleCollection[$name];
    }

    /**
     * updates the toggle inside the toggle manager
     *
     * @param Toggle $toggle
     *
     * @return void
     */
    private function updateToggle(Toggle $toggle): void
    {
        $this->toggleManager->update($toggle);
    }
}

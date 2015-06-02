<?php

/**
 * UpdateVariationCompletion
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Command\Application;

use Dvsa\Olcs\Transfer\Command\AbstractCommand;

/**
 * UpdateVariationCompletion
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class UpdateVariationCompletion extends AbstractCommand
{
    protected $id;

    protected $section;

    public function getId()
    {
        return $this->id;
    }

    public function getSection()
    {
        return $this->section;
    }
}

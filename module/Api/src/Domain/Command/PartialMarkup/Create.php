<?php
/**
 * Create partial markup record
 *
 * @author Andy Newton <andy@vitri.ltd>
 */

namespace Dvsa\Olcs\Api\Domain\Command\PartialMarkup;

use Dvsa\Olcs\Transfer\Command\AbstractCommand;
use Dvsa\Olcs\Transfer\FieldType\Traits\Markup;

final class Create extends AbstractCommand
{
    use Markup;

    /** @var int */
    protected $partial;

    /** @var int */
    protected $language;

    /**
     * @return int
     */
    public function getPartial()
    {
        return $this->partial;
    }

    /**
     * @return int
     */
    public function getLanguage()
    {
        return $this->language;
    }
}

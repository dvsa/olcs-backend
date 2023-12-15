<?php

/**
 * Create Correspondence Record
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Dvsa\Olcs\Api\Domain\Command\Email;

use Dvsa\Olcs\Transfer\Command\AbstractCommand;
use Dvsa\Olcs\Transfer\FieldType\Traits\Licence;

/**
 * Create Correspondence Record
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class CreateCorrespondenceRecord extends AbstractCommand
{
    use Licence;

    const TYPE_STANDARD = 'standard';
    const TYPE_CONTINUATION = 'continuation';

    protected $document;

    protected $type = self::TYPE_STANDARD;

    /**
     * @return mixed
     */
    public function getDocument()
    {
        return $this->document;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }
}

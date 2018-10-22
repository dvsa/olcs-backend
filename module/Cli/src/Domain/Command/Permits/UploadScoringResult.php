<?php

/**
 * Upload content of scoring result
 *
 */
namespace Dvsa\Olcs\Cli\Domain\Command\Permits;

use Dvsa\Olcs\Transfer\Command\AbstractCommand;

/**
 * Upload content of scoring result
 */
final class UploadScoringResult extends AbstractCommand
{
   /**
     * @var array
     */
    protected $csvContent;

    /**
     * Gets the value of csvContent
     *
     * @return array
     */
    public function getCsvContent()
    {
        return $this->csvContent;
    }
}

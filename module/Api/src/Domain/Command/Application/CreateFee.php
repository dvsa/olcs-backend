<?php

/**
 * Create Fee for an Application
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Command\Application;

use Dvsa\Olcs\Transfer\Command\AbstractCommand;

/**
 * Create Fee for an Application
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
final class CreateFee extends AbstractCommand
{
    /**
     * Application ID
     * @var int
     */
    protected $id;

    /**
     * FeeType.FeeType ref data id
     * @var string
     */
    protected $feeTypeFeeType;

    public function getId()
    {
        return $this->id;
    }

    public function getFeeTypeFeeType()
    {
        return $this->feeTypeFeeType;
    }
}

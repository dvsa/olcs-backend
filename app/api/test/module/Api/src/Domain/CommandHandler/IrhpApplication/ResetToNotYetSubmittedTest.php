<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\IrhpApplication;

use Dvsa\Olcs\Api\Domain\CommandHandler\IrhpApplication\ResetToNotYetSubmitted;
use Dvsa\Olcs\Api\Domain\Repository\IrhpApplication as IrhpApplicationRepo;
use Dvsa\Olcs\Api\Entity\IrhpInterface;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\AbstractUpdateDefinedValueTest;

class ResetToNotYetSubmittedTest extends AbstractUpdateDefinedValueTest
{
    protected $repoServiceName = 'IrhpApplication';
    protected $entityMethodName = 'resetToNotYetSubmitted';
    protected $definedValue = IrhpInterface::STATUS_NOT_YET_SUBMITTED;
    protected $entityClass = IrhpApplication::class;
    protected $repoClass = IrhpApplicationRepo::class;
    protected $sutClass = ResetToNotYetSubmitted::class;
}

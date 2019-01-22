<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\IrhpApplication;

use Dvsa\Olcs\Api\Entity\IrhpInterface;
use Dvsa\Olcs\Api\Domain\CommandHandler\IrhpApplication\Cancel as CancelIrhpApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\AbstractCancelApplicationHandlerTest;

class CancelTest extends AbstractCancelApplicationHandlerTest
{
    protected $repoServiceName = 'IrhpApplication';
    protected $entityClass = IrhpApplicationEntity::class;
    protected $sutClass = CancelIrhpApplication::class;
    protected $cancelStatus = IrhpInterface::STATUS_CANCELLED;
}

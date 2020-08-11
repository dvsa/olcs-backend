<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\IrhpApplication;

use Dvsa\Olcs\Api\Domain\CommandHandler\IrhpApplication\Expire;
use Dvsa\Olcs\Api\Domain\Repository\IrhpApplication as IrhpApplicationRepo;
use Dvsa\Olcs\Api\Entity\IrhpInterface;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\AbstractUpdateDefinedValueTest;

class ExpireTest extends AbstractUpdateDefinedValueTest
{
    protected $repoServiceName = 'IrhpApplication';
    protected $entityMethodName = 'expire';
    protected $definedValue = IrhpInterface::STATUS_EXPIRED;
    protected $entityClass = IrhpApplication::class;
    protected $repoClass = IrhpApplicationRepo::class;
    protected $sutClass = Expire::class;
}

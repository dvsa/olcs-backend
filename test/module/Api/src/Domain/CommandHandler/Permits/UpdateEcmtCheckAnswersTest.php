<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Permits;

use Dvsa\OlcsTest\Api\Domain\CommandHandler\AbstractUpdateDefinedValueTest;
use Dvsa\Olcs\Api\Domain\CommandHandler\Permits\UpdateEcmtCheckAnswers;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;

class UpdateEcmtCheckAnswersTest extends AbstractUpdateDefinedValueTest
{
    protected $repoServiceName = 'EcmtPermitApplication';
    protected $entityMethodName = 'setCheckedAnswers';
    protected $definedValue = true;
    protected $entityClass = EcmtPermitApplication::class;
    protected $sutClass = UpdateEcmtCheckAnswers::class;
}

<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\IrhpApplication;

use Dvsa\Olcs\Api\Domain\CommandHandler\IrhpApplication\UpdateDeclaration;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\AbstractCallEntityMethodTest;

class UpdateDeclarationTest extends AbstractCallEntityMethodTest
{
    protected $repoServiceName = 'IrhpApplication';
    protected $entityMethodName = 'makeDeclaration';
    protected $entityClass = 'IrhpApplication';
    protected $sutClass = UpdateDeclaration::class;
}

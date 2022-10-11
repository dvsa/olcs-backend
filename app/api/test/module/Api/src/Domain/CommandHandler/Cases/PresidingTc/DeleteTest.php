<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Cases\PresidingTc;

use Dvsa\Olcs\Api\Domain\CommandHandler\Cases\PresidingTc\Delete as DeleteHandler;
use Dvsa\Olcs\Api\Domain\Repository\PresidingTc as PresidingTcRepo;
use Dvsa\Olcs\Transfer\Command\Cases\PresidingTc\Delete as DeleteCmd;
use Dvsa\Olcs\Api\Entity\Pi\PresidingTc as PresidingTcEntity;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\AbstractDeleteCommandHandlerTest;

/**
 * Delete Presiding TC
 *
 * @author Andy Newton
 */
class DeleteTest extends AbstractDeleteCommandHandlerTest
{
    protected $cmdClass = DeleteCmd::class;
    protected $sutClass = DeleteHandler::class;
    protected $repoServiceName = 'PresidingTc';
    protected $repoClass = PresidingTcRepo::class;
    protected $entityClass = PresidingTcEntity::class;
}

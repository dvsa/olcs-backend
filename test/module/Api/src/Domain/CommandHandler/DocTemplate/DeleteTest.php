<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\DocTemplate;

use Dvsa\Olcs\Api\Domain\CommandHandler\DocTemplate\Delete as DeleteHandler;
use Dvsa\Olcs\Api\Domain\Repository\DocTemplate as DocTemplateRepo;
use Dvsa\Olcs\Transfer\Command\DocTemplate\Delete as DeleteCmd;
use Dvsa\Olcs\Api\Entity\Doc\DocTemplate as DocTemplateEntity;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\AbstractDeleteCommandHandlerTest;

/**
 * Delete DocTemplate test
 *
 * @author Andy Newton <andy@vitri.ltd>
 */
class DeleteTest extends AbstractDeleteCommandHandlerTest
{
    protected $cmdClass = DeleteCmd::class;
    protected $sutClass = DeleteHandler::class;
    protected $repoServiceName = 'DocTemplate';
    protected $repoClass = DocTemplateRepo::class;
    protected $entityClass = DocTemplateEntity::class;
}

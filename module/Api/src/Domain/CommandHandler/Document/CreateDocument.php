<?php

/**
 * Create Document
 * 
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Document;

use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\Document\CreateDocument as Cmd;

/**
 * Create Document
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
final class CreateDocument extends AbstractCommandHandler implements AuthAwareInterface
{
    use AuthAwareTrait;

    protected $repoServiceName = 'Document';

    public function handleCommand(CommandInterface $command)
    {
        /* @var $command Cmd */

        if ($command->getIsExternal() === null) {
            $isExternal = $this->isGranted(Permission::SELFSERVE_USER);
        } else {
            $isExternal = $command->getIsExternal();
        }

        $params = $command->getArrayCopy();
        $params['isExternal'] = $isExternal;

        return $this->handleSideEffect(
            \Dvsa\Olcs\Api\Domain\Command\Document\CreateDocumentSpecific::create($params)
        );
    }
}

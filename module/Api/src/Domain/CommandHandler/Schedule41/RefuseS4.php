<?php

/**
 * RefuseS4.php
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Schedule41;

use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\Schedule41 as Cmd;
use Dvsa\Olcs\Api\Domain\Command\Result;

use Dvsa\Olcs\Api\Entity\Application\S4;

/**
 * Class RefuseS4
 *
 * Refuse an S4 request.
 *
 * @package Dvsa\Olcs\Api\Domain\CommandHandler\Schedule41
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
final class RefuseS4 extends AbstractCommandHandler
{
    protected $repoServiceName = 'S4';

    public function handleCommand(CommandInterface $command)
    {
        /** @var S4 $s4 */
        $s4 = $this->getRepo()->getReference(S4::class, $command->getId());

        $s4->setAgreedDate(new \DateTime());
        $s4->setOutcome($this->getRepo()->getRefdataReference(S4::STATUS_REFUSED));

        $this->getRepo()->save($s4);

        $result = new Result();
        $result->addId('s4', $s4->getId());
        $result->addMessage('S4 Refused.');

        return $result;
    }
}

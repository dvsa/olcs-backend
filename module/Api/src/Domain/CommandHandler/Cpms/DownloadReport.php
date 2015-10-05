<?php

/**
 * Download Cpms Report
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Cpms;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CpmsAwareInterface;
use Dvsa\Olcs\Api\Domain\CpmsAwareTrait;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\Cpms\DownloadReport as Cmd;

/**
 * Download Cpms Report
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
final class DownloadReport extends AbstractCommandHandler implements CpmsAwareInterface
{
    use CpmsAwareTrait;

    /**
     * @param Cmd $command
     * @return \Dvsa\Olcs\Api\Domain\Command\Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        $reference = $command->getReference();
        $token = $command->getToken();
        $extension = $command->getExtension();

        $data = $this->getCpmsService()->downloadReport($reference, $token);

        // var_dump($data); exit;
        // @todo create document with downloaded data here
        $documentId = '666';

        $result->addMessage('Report downloaded');
        $result->addId('document', $documentId);

        return $result;
    }
}

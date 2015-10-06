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
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\Cpms\DownloadReport as Cmd;
use Dvsa\Olcs\Transfer\Command\Document\Upload as UploadCmd;

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
        $filename = sprintf(
            'Daily Balance Report%s',
            $command->getExtension() ? ('.'.$command->getExtension()) : ''
        );

        $data = $this->getCpmsService()->downloadReport($reference, $token);
        $result->addMessage('Report downloaded');

        $uploadData = [
            'content'     => base64_encode(trim($data)),
            'filename'    => $filename,
            'category'    => Category::CATEGORY_LICENSING,
            'subCategory' => Category::DOC_SUB_CATEGORY_FINANCIAL_REPORTS,
            'isExternal'  => false,
        ];

        $command = UploadCmd::create($uploadData);
        $result->merge($this->handleSideEffect($command));

        return $result;
    }
}

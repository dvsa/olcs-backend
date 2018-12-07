<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Api\Entity\System\SubCategory;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Transfer\Command\Document\Upload;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;

/**
 * StoreEcmtPermitApplicationSnapshot
 */
final class StoreEcmtPermitApplicationSnapshot extends AbstractCommandHandler implements
    TransactionedInterface
{
    protected $repoServiceName = 'EcmtPermitApplication';

    /**
     * Handle command
     *
     * @param CommandInterface $command DTO
     *
     * @return Result
     * @throws ValidationException
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    public function handleCommand(CommandInterface $command)
    {
        $ecmtPermitApplication = $this->getRepo()->fetchUsingId($command);

        $this->result->merge($this->generateDocument($command->getHtml(), $ecmtPermitApplication));
        $this->result->addId('EcmtPermitApplication', $ecmtPermitApplication->getId());
        $this->result->addMessage('ECMT Permit Application snapshot created');

        return $this->result;
    }

    /**
     * Generate the document for the snapshot and store it on the docstore
     *
     * @param string     $content    HTML snapshot content
     * @param EcmtPermitApplication $ecmtPermitApplication EcmtPermitApplication snapshot is for
     *
     * @return Result
     */
    protected function generateDocument($content, EcmtPermitApplication $ecmtPermitApplication)
    {
        $name = sprintf(
            'Permit Application %s Snapshot (app submitted)',
            $ecmtPermitApplication->getApplicationRef()
        );

        $data = [
            'content' => base64_encode(trim($content)),
            'category' => Category::CATEGORY_PERMITS,
            'subCategory' => SubCategory::DOC_SUB_CATEGORY_PERMIT_APPLICATION,
            'isExternal' => false,
            'isScan' => false,
            'filename' => $name .'.html',
            'description' => $name,
        ];

        return $this->handleSideEffect(Upload::create($data));
    }
}

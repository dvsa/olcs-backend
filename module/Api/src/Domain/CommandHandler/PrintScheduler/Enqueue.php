<?php

/**
 * Queue a print job
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\PrintScheduler;

use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Api\Entity\Licence\Licence;

/**
 * Queue a print job
 *
 */
final class Enqueue extends AbstractCommandHandler
{
    protected $repoServiceName = 'Document';

    public function handleCommand(CommandInterface $command)
    {
        /* @var $command \Dvsa\Olcs\Api\Domain\Command\PrintScheduler\Enqueue */

        $document = new \Dvsa\Olcs\Api\Entity\Doc\Document($command->getFileIdentifier());

        $document->setDescription($command->getJobName());
        $document->setFilename(str_replace(' ', '_', $command->getJobName()) . '.rtf');
        // hard coded simply so we can demo against *something*
        $document->setLicence($this->getRepo()->getReference(Licence::class, 7));
        $document->setCategory($this->getRepo()->getCategoryReference(Category::CATEGORY_LICENSING));
        $document->setSubCategory(
            $this->getRepo()->getSubCategoryReference(Category::DOC_SUB_CATEGORY_LICENCE_VEHICLE_LIST)
        );
        $document->setIsExternal(false);
        $document->setIsReadOnly('Y');
        $document->setIssuedDate(new \Datetime());

        $this->getRepo()->save($document);

        $result = new Result();
        $result->addMessage("File '{$command->getFileIdentifier()}', '{$command->getJobName()}' printed");
        return $result;
    }
}

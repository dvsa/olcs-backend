<?php

/**
 * Process Si Compliance Episode
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Cases\Si;

use Dvsa\Olcs\Api\Domain\Exception\Exception;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\System\Category as CategoryEntity;
use Dvsa\Olcs\Transfer\Command\Cases\Si\ComplianceEpisode as ComplianceEpisodeDocCmd;
use Dvsa\Olcs\Transfer\Command\Document\Upload as UploadCmd;
use Dvsa\Olcs\Api\Domain\Command\Cases\Si\ComplianceEpisode as ComplianceEpisodeCmd;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Save Si compliance document and then trigger processing
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
final class ComplianceEpisodeDocument extends AbstractCommandHandler
{
    /**
     * @var Result
     */
    protected $result;

    /**
     * Handle command to save the xml to the doc store, then trigger processing of the compliance episode
     *
     * @param CommandInterface $command
     * @return Result
     * @throws \Exception
     */
    public function handleCommand(CommandInterface $command)
    {
        /**
         * @var ComplianceEpisodeDocCmd $command
         * @var array $erruData
         */
        $result = $this->handleSideEffect($this->createDocumentCommand($command->getXml()));
        
        //we process the compliance episode in a separate command as it needs to run in a DB transaction,
        //whereas we save the incoming xml document regardless
        try {
            $result->merge(
                $this->handleSideEffect(
                    ComplianceEpisodeCmd::create(['id' => $result->getId('document')])
                )
            );
        } catch (\Exception $e) {
            //will result in a 400 response from XML controller, which is what we're looking for
            throw new Exception('some data was not correct');
        }

        return $result;
    }

    /**
     * Returns an upload command to add xml to the doc store
     *
     * @param string $content
     *
     * @return UploadCmd
     */
    private function createDocumentCommand($content)
    {
        $data = [
            'content' => base64_encode($content),
            'category' => CategoryEntity::CATEGORY_COMPLIANCE,
            'subCategory' => CategoryEntity::DOC_SUB_CATEGORY_NR,
            'filename' => 'compliance-episode.xml',
            'description' => 'ERRU incoming compliance episode'
        ];

        return UploadCmd::create($data);
    }
}

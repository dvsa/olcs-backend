<?php

namespace Dvsa\Olcs\Cli\Domain\CommandHandler;

use Dvsa\Olcs\Api\Domain\Command\Document\GenerateAndStoreWithMultipleAddresses;
use Dvsa\Olcs\Api\Domain\Repository\Licence;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\Document\PrintLetter;
use \Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
/**
 * Export data to csv files for data.gov.uk
 *
 * @author Dmitry Golubev <dmitrij.golubev@valtech.co.uk>
 */
final class LastTmLetter extends AbstractCommandHandler
{
    const GB_GV_TEMPLATE  = 'GV_Duplicate_vehicle_letter';
    const GB_PSV_TEMPLATE = 'GV_Duplicate_vehicle_letter';
    const NI_GV_TEMPLATE  = 'GV_Duplicate_vehicle_letter';

    /**
     * @var string
     */
    protected $repoServiceName = 'Licence';

    /**
     * Handle command
     *
     * @param \Dvsa\Olcs\Cli\Domain\Command\DataGovUkExport $command Command
     *
     * @return \Dvsa\Olcs\Api\Domain\Command\Result
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     * @throws \Exception
     */
    public function handleCommand(CommandInterface $command)
    {
        /** @var Licence $licenceRepo */
        $licenceRepo = $this->getRepo();
        $eligibleLicences = $licenceRepo->fetchForLastTmAutoLetter();

        /** @var LicenceEntity $licence */
        foreach ($eligibleLicences as $licence) {
            $documents = $this->generateDocuments($licence);
            $this->printAndEmailDocuments($documents);
        }

        return $this->result;
    }

    /**
     * @param LicenceEntity $licence
     * @return array|null
     */
    private function generateDocuments(LicenceEntity $licence)
    {
        $template = $this->selectTemplate($licence);

        $generateCommandData = [
            'template' => $template,
            'query' => [
                'licence' => $licence->getId(),
                'vehicle' => 1
            ],
            'description' => 'Last TM letter Licence '. $licence->getLicNo(),
            'licence'     => $licence->getId(),
            'category'    => Category::CATEGORY_LICENSING,
            'subCategory' => Category::DOC_SUB_CATEGORY_OTHER_DOCUMENTS,
            'isExternal'  => false
        ];

        $data = [
            'generateCommandData' => $generateCommandData,
            'addressBookmark' => 'op_address',
            'bookmarkBundle' => [
                'correspondenceCd' => ['address']
            ]
        ];

        $result = $this->handleSideEffect(GenerateAndStoreWithMultipleAddresses::create($data));
        $this->result->merge($result);

        return $result->getId('documents');
    }

    /**
     * @param array $documents (array of document ids)
     * @return void
     */
    protected function printAndEmailDocuments($documents)
    {
        foreach ($documents as $document) {
            $this->printAndEmailDocument($document);
        }
    }

    /**
     * @param int $document (id)
     * @return void
     */
    protected function printAndEmailDocument($document)
    {
        $result = $this->handleSideEffects(
            [
                PrintLetter::create([
                    'id' => $document,
                    'method' => PrintLetter::METHOD_PRINT_AND_POST
                ]),
                PrintLetter::create([
                    'id' => $document,
                    'method' => PrintLetter::METHOD_EMAIL
                ])
            ]
        );

        $this->result->merge($result);
    }

    /**
     * @param LicenceEntity $licence
     * @return string
     */
    private function selectTemplate(LicenceEntity $licence): string
    {
        $template = self::GB_GV_TEMPLATE;

        if ($licence->isNi()) {
            $template = self::NI_GV_TEMPLATE;
        } elseif ($licence->isPsv()) {
            $template = self::GB_PSV_TEMPLATE;
        }

        return $template;
    }
}

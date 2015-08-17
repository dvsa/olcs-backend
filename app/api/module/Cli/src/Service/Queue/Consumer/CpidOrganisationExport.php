<?php

/**
 * Class CpidOrganisationExport
 * @author Josh Curtis <josh@josh-curtis.co.uk>
 */
namespace Dvsa\Olcs\Cli\Service\Queue\Consumer;

use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Api\Entity\Queue\Queue as QueueEntity;
use Dvsa\Olcs\Api\Domain\Command\Document\CreateDocumentSpecific;

/**
 * Class CpidOrganisationExport
 * @package Dvsa\Olcs\Cli\Service\Queue\Consumer
 * @author Josh Curtis <josh@josh-curtis.co.uk>
 */
class CpidOrganisationExport implements MessageConsumerInterface, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    public function processMessage(QueueEntity $item)
    {
        $options = (array)json_decode($item->getOptions());

        $iterableResult = $this->getServiceLocator()
            ->get('RepositoryServiceManager')
            ->get('Organisation')
            ->fetchAllByStatusForCpidExport($options['status']);

        $filename = md5(microtime());
        $handle = fopen("/tmp/{$filename}", 'w');
        while (($row = $iterableResult->next()) !== false) {
            fputcsv($handle, $row[key($row)]);
        }

        $file = $this->uploadFile(file_get_contents("/tmp/{$filename}"));

        unlink("/tmp/{$filename}");

        $this->getServiceLocator()
            ->get('CommandHandlerManager')
            ->handleCommand(
                CreateDocumentSpecific::create(
                    [
                        'filename' => 'cpid-classification-' . $filename . '.csv',
                        'identifier' => $file->getIdentifier(),
                        'category' => Category::CATEGORY_LICENSING,
                        'subCategory' => Category::DOC_SUB_CATEGORY_CPID,
                        'description' => 'CPID Classifications',
                        'isExternal' => false,
                        'isScan' => false
                    ]
                )
            );
    }

    private function uploadFile($contents)
    {
        /** @var \Dvsa\Olcs\Api\Service\File\ContentStoreFileUploader $uploader */
        $uploader = $this->getServiceLocator()
            ->get('FileUploader');

        $uploader->setFile(
            [
                'content' => $contents
            ]
        );

        return $uploader->upload();
    }
}

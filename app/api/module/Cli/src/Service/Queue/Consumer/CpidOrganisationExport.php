<?php

namespace Dvsa\Olcs\Cli\Service\Queue\Consumer;

use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Api\Entity\Queue\Queue as QueueEntity;
use Dvsa\Olcs\Api\Domain\Command\Document\CreateDocumentSpecific;

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
                        'filename' => 'testing123',
                        'identifier' => 123, //$file->getIdentifier()
                        'category' => Category::CATEGORY_APPLICATION, // Change me
                        'subCategory' => Category::TASK_SUB_CATEGORY_APPLICATION_FORMS_ASSISTED_DIGITAL, // Change me
                        'description' => 'Test description.',
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
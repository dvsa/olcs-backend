<?php

/**
 * Class CpidOrganisationExport
 * @author Josh Curtis <josh@josh-curtis.co.uk>
 */
namespace Dvsa\Olcs\Cli\Service\Queue\Consumer;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\LockHandler;

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

        $path = $this->getServiceLocator()
                ->get('config')['file-system']['path'];

        $filename = $this->createTmpFile($path);

        $handle = fopen($filename, 'w');
        while (($row = $iterableResult->next()) !== false) {
            fputcsv($handle, $row[key($row)]);
        }
        fclose($handle);

        $file = $this->uploadFile(file_get_contents($filename));

        unlink($filename);

        $this->getServiceLocator()
            ->get('CommandHandlerManager')
            ->handleCommand(
                CreateDocumentSpecific::create(
                    [
                        'filename' => 'cpid-classification.csv',
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

    private function createTmpFile($path, $prefix = '')
    {
        $fileSystem = new Filesystem();

        $lock = new LockHandler(hash('sha256', $path));
        $lock->lock(true);

        do {
            $filename = $path . DIRECTORY_SEPARATOR . uniqid($prefix);
        } while ($fileSystem->exists($filename));

        $fileSystem->touch($filename);

        $lock->release();

        return $filename;
    }
}

<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Report;

use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Domain\QueueAwareTrait;
use Dvsa\Olcs\Api\Domain\UploaderAwareInterface;
use Dvsa\Olcs\Api\Domain\UploaderAwareTrait;
use Dvsa\Olcs\Api\Entity\Queue\Queue;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Entity\System\SubCategory;
use Dvsa\Olcs\Api\Service\Document\NamingServiceAwareInterface;
use Dvsa\Olcs\Api\Service\Document\NamingServiceAwareTrait;
use Dvsa\Olcs\Api\Service\File\MimeNotAllowedException;
use Dvsa\Olcs\DocumentShare\Data\Object\File as DsFile;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\Report\Upload as UploadCmd;
use Dvsa\Olcs\Utils\Helper\FileHelper;

/**
 * Upload
 */
final class Upload extends AbstractCommandHandler implements
    AuthAwareInterface,
    NamingServiceAwareInterface,
    TransactionedInterface,
    UploaderAwareInterface
{
    const ERR_MIME = 'ERR_MIME';

    use AuthAwareTrait,
        NamingServiceAwareTrait,
        QueueAwareTrait,
        UploaderAwareTrait;

    protected $repoServiceName = 'Document';

    /**
     * Execute command
     *
     * @param UploadCmd $command Command
     *
     * @return Result
     * @throws ValidationException
     * @throws \Exception
     */
    public function handleCommand(CommandInterface $command)
    {
        // upload the file
        $identifier = $this->determineIdentifier($command);
        $file = $this->uploadFile($command, $identifier);

        // process the uploaded report
        $this->process($command->getReportType(), $file);

        return $this->result;
    }

    /**
     * Define file name(path)
     *
     * @param UploadCmd $command Upload command
     *
     * @return string
     */
    protected function determineIdentifier(UploadCmd $command)
    {
        $description = 'uploaded_report';
        $subCategory = null;

        if ($command->getReportType() === RefData::REPORT_TYPE_COMM_LIC_BULK_REPRINT) {
            $description = 'community_licence_bulk_reprint';
            $subCategory = SubCategory::DOC_SUB_CATEGORY_COMMUNITY_LICENCE;
        }

        $categoryReference = $this->getRepo()->getCategoryReference(Category::CATEGORY_REPORT);
        $subCategoryReference
            = ($subCategory !== null) ? $this->getRepo()->getSubCategoryReference($subCategory) : null;

        $extension = FileHelper::getExtension($command->getFilename());

        return $this->getNamingService()
            ->generateName($description, $extension, $categoryReference, $subCategoryReference);
    }

    /**
     * Upload file to Document storage
     *
     * @param UploadCmd $command    Upload Command
     * @param string    $identifier File name (path)
     *
     * @return DsFile
     * @throws ValidationException
     * @throws \Exception
     */
    protected function uploadFile(UploadCmd $command, $identifier)
    {
        $content = $command->getContent();

        $dsFile = new DsFile();

        if (!empty($content['tmp_name'])) {
            $dsFile->setContentFromStream($content['tmp_name']);

            if ('application/octet-stream' === $dsFile->getMimeType()) {
                $dsFile->setMimeType($content['type']);
            }
        } else {
            $dsFile->setContent(base64_decode($content));
        }

        try {
            $file = $this->getUploader()->upload($identifier, $dsFile);

            $this->result->addMessage('File uploaded');
            $this->result->addId('identifier', $file->getIdentifier());

            return $file;
        } catch (MimeNotAllowedException $ex) {
            throw new ValidationException([self::ERR_MIME => self::ERR_MIME]);
        } catch (\Exception $e) {
            throw $e;
        } finally {
            unset($dsFile);
        }
    }

    /**
     * Process the uploaded report
     *
     * @param string $reportType Report type
     * @param DsFile $file       The uploaded file
     *
     * @return void
     */
    protected function process($reportType, DsFile $file)
    {
        if ($reportType === RefData::REPORT_TYPE_COMM_LIC_BULK_REPRINT) {
            $this->result->merge(
                $this->handleSideEffect(
                    $this->createQueue(
                        null,
                        Queue::TYPE_COMM_LIC_BULK_REPRINT,
                        [
                            'identifier' => $file->getIdentifier(),
                            'user' => $this->getCurrentUser()->getId(),
                        ]
                    )
                )
            );
        }
    }
}

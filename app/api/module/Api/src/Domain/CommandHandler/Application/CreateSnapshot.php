<?php

/**
 * Create Snapshot
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\Command\Document\CreateDocumentSpecific;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview\Generator;
use Dvsa\Olcs\Transfer\Command\Application\CreateSnapshot as Cmd;

/**
 * Create Snapshot
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class CreateSnapshot extends AbstractCommandHandler
{
    const CODE_GV_APP             = 'GV79';
    const CODE_GV_VAR_UPGRADE     = 'GV80A';
    const CODE_GV_VAR_NO_UPGRADE  = 'GV81';

    const CODE_PSV_APP = 'PSV421';
    const CODE_PSV_APP_SR = 'PSV356';
    const CODE_PSV_VAR_UPGRADE    = 'PSV431A';
    const CODE_PSV_VAR_NO_UPGRADE = 'PSV431';

    protected $repoServiceName = 'Application';

    protected $uploader;

    /**
     * @var Generator
     */
    protected $reviewSnapshotService;

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mainServiceLocator = $serviceLocator->getServiceLocator();

        $this->uploader = $mainServiceLocator->get('FileUploader');
        $this->reviewSnapshotService = $mainServiceLocator->get('ReviewSnapshot');

        return parent::createService($serviceLocator);
    }

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        /** @var ApplicationEntity $application */
        $application = $this->getRepo()->fetchUsingId($command);

        $markup = $this->reviewSnapshotService->generate($application);
        $result->addMessage('Snapshot generated');

        $file = $this->uploadFile($markup);
        $result->addMessage('Snapshot uploaded');

        $result->merge($this->createDocumentRecord($application, $command->getEvent(), $file));

        return $result;
    }

    protected function uploadFile($content)
    {
        $this->uploader->setFile(['content' => $content]);

        return $this->uploader->upload();
    }

    protected function createDocumentRecord(ApplicationEntity $application, $event, $file)
    {
        $licenceId = $application->getLicence()->getId();

        $code = $this->getDocumentCode($application);

        $defaults = [
            'identifier' => $file->getIdentifier(),
            'application' => $application->getId(),
            'licence' => $licenceId,
            'category' => Category::CATEGORY_APPLICATION,
            'subCategory' => Category::TASK_SUB_CATEGORY_APPLICATION_FORMS_ASSISTED_DIGITAL,
            'isExternal' => false,
            'isScan' => false
        ];

        // merge defaults with event specific values
        $data = array_merge($defaults, $this->getDocumentData($event, $code));

        return $this->handleSideEffect(CreateDocumentSpecific::create($data));
    }

    /**
     * Get Application code
     *
     * @param int $applicationId   Application ID
     * @param array $applicationData
     *
     * @return string Eg GV80A
     */
    protected function getDocumentCode(ApplicationEntity $application)
    {
        // All New application options
        if (!$application->isVariation()) {
            if ($application->isGoods()) {
                return self::CODE_GV_APP;
            }

            if ($application->isSpecialRestricted()) {
                return self::CODE_PSV_APP_SR;
            }

            return self::CODE_PSV_APP;
        }

        $isUpgrade = $application->isRealUpgrade();

        if ($application->isGoods()) {

            if ($isUpgrade) {
                return self::CODE_GV_VAR_UPGRADE;
            }

            return self::CODE_GV_VAR_NO_UPGRADE;
        } else {

            if ($isUpgrade) {
                return self::CODE_PSV_VAR_UPGRADE;
            }

            return self::CODE_PSV_VAR_NO_UPGRADE;
        }
    }

    /**
     * Get Document entity data
     *
     * @param int    $event One the of the self::ON_* constants
     * @param string $code  Application code eg GV79
     *
     * @return array Document entity data
     */
    protected function getDocumentData($event, $code)
    {
        $descriptionPrefix = $code . ' Application Snapshot ';

        switch ((string)$event) {
            case (string)Cmd::ON_GRANT:
                return [
                    'filename' => $descriptionPrefix . 'Grant.html',
                    'description' => $descriptionPrefix .'(at grant/valid)',
                ];
            case (string)Cmd::ON_SUBMIT:
                return [
                    'subCategory' => Category::TASK_SUB_CATEGORY_APPLICATION_FORMS_DIGITAL,
                    'filename' => $descriptionPrefix . 'Submit.html',
                    'description' => $descriptionPrefix .'(at submission)',
                    'isExternal' => true,
                ];
            case (string)Cmd::ON_REFUSE:
                return [
                    'filename' => $descriptionPrefix . 'Refuse.html',
                    'description' => $descriptionPrefix .'(at refuse)',
                ];
            case (string)Cmd::ON_WITHDRAW:
                return [
                    'filename' => $descriptionPrefix . 'Withdraw.html',
                    'description' => $descriptionPrefix .'(at withdraw)',
                ];
            case (string)Cmd::ON_NTU:
                return [
                    'filename' => $descriptionPrefix . 'NTU.html',
                    'description' => $descriptionPrefix .'(at NTU)',
                ];
            default:
                throw new ValidationException(['Unexpected event']);
        }
    }
}

<?php

/**
 * Create Snapshot
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Transfer\Command\Document\Upload;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview\Generator;
use Dvsa\Olcs\Transfer\Command\Application\CreateSnapshot as Cmd;

/**
 * Create Snapshot
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class CreateSnapshot extends AbstractCommandHandler implements AuthAwareInterface
{
    use AuthAwareTrait;

    const CODE_GV_APP             = 'GV79';
    const CODE_GV_VAR_UPGRADE     = 'GV80A';
    const CODE_GV_VAR_NO_UPGRADE  = 'GV81';

    const CODE_PSV_APP = 'PSV421';
    const CODE_PSV_APP_SR = 'PSV356';
    const CODE_PSV_VAR_UPGRADE    = 'PSV431A';
    const CODE_PSV_VAR_NO_UPGRADE = 'PSV431';

    protected $repoServiceName = 'Application';

    /**
     * @var Generator
     */
    protected $reviewSnapshotService;

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mainServiceLocator = $serviceLocator->getServiceLocator();

        $this->reviewSnapshotService = $mainServiceLocator->get('ReviewSnapshot');

        return parent::createService($serviceLocator);
    }

    public function handleCommand(CommandInterface $command)
    {
        /** @var ApplicationEntity $application */
        $application = $this->getRepo()->fetchUsingId($command);

        $markup = $this->reviewSnapshotService->generate($application, $this->isInternalUser());
        $this->result->addMessage('Snapshot generated');

        $this->result->merge($this->generateDocument($markup, $application, $command->getEvent()));

        return $this->result;
    }

    protected function generateDocument($content, ApplicationEntity $application, $event)
    {
        $licenceId = $application->getLicence()->getId();
        $code = $this->getDocumentCode($application);

        $data = [
            'content' => base64_encode(trim($content)),
            'application' => $application->getId(),
            'licence' => $licenceId,
            'category' => Category::CATEGORY_APPLICATION,
            'subCategory' => Category::TASK_SUB_CATEGORY_APPLICATION_FORMS_ASSISTED_DIGITAL,
            'isExternal' => false,
            'isScan' => false
        ];

        $data = array_merge($data, $this->getDocumentData($application, $event, $code));

        return $this->handleSideEffect(Upload::create($data));
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
     * @param ApplicationEntity $application The Application entity
     * @param int               $event       One the of the self::ON_* constants
     * @param string            $code        Application code eg GV79
     *
     * @return array Document entity data
     */
    protected function getDocumentData($application, $event, $code)
    {
        $descriptionPrefix = sprintf('%s Application %d Snapshot ', $code, $application->getId());

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

<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Application;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Domain\Command\Application\UpdateApplicationCompletion as UpdateApplicationCompletionCmd;

/**
 * Application
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Application extends AbstractQueryHandler
{
    const OPERATING_CENTRES_SECTION = 'operatingCentres';

    protected $repoServiceName = 'Application';
    protected $extraRepos = ['Note', 'SystemParameter'];

    /**
     * @var \Dvsa\Olcs\Api\Service\Lva\SectionAccessService
     */
    private $sectionAccessService;

    /**
     * @var \Dvsa\Olcs\Api\Service\FeesHelperService
     */
    private $feesHelper;

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mainServiceLocator = $serviceLocator->getServiceLocator();

        $this->sectionAccessService = $mainServiceLocator->get('SectionAccessService');
        $this->feesHelper = $mainServiceLocator->get('FeesHelperService');

        return parent::createService($serviceLocator);
    }

    public function handleQuery(QueryInterface $query)
    {
        /* @var $application ApplicationEntity */
        $application = $this->getRepo()->fetchUsingId($query);

        $this->auditRead($application);

        if ($query->getValidateAppCompletion() && $application->isVariation()) {
            $this->getCommandHandler()->handleCommand(
                UpdateApplicationCompletionCmd::create(
                    ['id' => $application->getId(), 'section' => self::OPERATING_CENTRES_SECTION]
                )
            );
        }

        $latestNote = $this->getRepo('Note')->fetchForOverview($application->getLicence()->getId());
        return $this->result(
            $application,
            [
                'licence' => [
                    'organisation' => [
                        'type',
                        'disqualifications',
                        'organisationPersons' => [
                            'person' => ['disqualifications']
                        ],
                    ],
                ],
                'applicationCompletion',
                's4s' => [
                    'outcome'
                ],
                'status',
                'goodsOrPsv'
            ],
            [
                'sections' => $this->sectionAccessService->getAccessibleSections($application),
                'outstandingFeeTotal' => $this->feesHelper->getTotalOutstandingFeeAmountForApplication(
                    $application->getId()
                ),
                'variationCompletion' => $application->getVariationCompletion(),
                'canCreateCase' => $application->canCreateCase(),
                'existingPublication' => !$application->getPublicationLinks()->isEmpty(),
                'isPublishable' => $application->isPublishable(),
                'latestNote' => $latestNote,
                'disableCardPayments' => $this->getRepo('SystemParameter')->getDisableSelfServeCardPayments(),
                'isMlh' => $application->getLicence()->getOrganisation()->isMlh(),
                'allowedOperatorLocation' =>
                    $application->getLicence()->getOrganisation()->getAllowedOperatorLocation(),
                'canHaveInspectionRequest' => !$application->isSpecialRestricted(),
            ]
        );
    }
}

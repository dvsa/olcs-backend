<?php

namespace Dvsa\Olcs\Api\Service\Nr\Mapping;

use Olcs\XmlTools\Filter\MapXmlFile;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Olcs\XmlTools\Xml\Specification\NodeAttribute;
use Olcs\XmlTools\Xml\Specification\Recursion;
use Olcs\XmlTools\Xml\Specification\RecursionAttribute;
use Olcs\XmlTools\Xml\Specification\RecursionValue;

/**
 * Class ComplianceEpisodeXmlFactory
 * @package Dvsa\Olcs\Api\Service\Nr\Mapping
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class ComplianceEpisodeXmlFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return MapXmlFile
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mapping = new Recursion($this->getSeriousInfringement());

        return $mapping;
    }

    /**
     * Gets information to create the serious infringement
     *
     * @return array
     */
    protected function getSeriousInfringement()
    {
        $seriousInfringement = [
            'Header' => [
                new NodeAttribute('workflowId', 'workflowId'),
                new NodeAttribute('memberStateCode', 'from')
            ],
            'Body' => [
                new NodeAttribute('notificationNumber', 'businessCaseId'),
                new NodeAttribute('originatingAuthority', 'originatingAuthority'),
                new Recursion(
                    'TransportUndertaking',
                    [
                        new NodeAttribute('communityLicenceNumber', 'communityLicenceNumber'),
                        new NodeAttribute('vrm', 'vehicleRegNumber'),
                        new NodeAttribute('transportUndertakingName', 'name'),
                        $this->getSi()
                    ]
                )
            ],
        ];

        return $seriousInfringement;
    }

    protected function getSi()
    {
        $spec = [
            new NodeAttribute(['infringementDate'], 'dateOfInfringement'),
            new NodeAttribute(['siCategoryType'], 'infringementType'),
            new NodeAttribute(['checkDate'], 'dateOfCheck'),
            $this->getPenaltiesImposed(),
            $this->getPenaltiesRequested()
        ];

        return new RecursionValue(
            'si',
            new RecursionAttribute('SeriousInfringement', $spec)
        );
    }

    /**
     * Gets imposed penalty data
     *
     * @return RecursionValue
     */
    protected function getPenaltiesImposed()
    {
        $spec = [
            new NodeAttribute('finalDecisionDate', 'finalDecisionDate'),
            new NodeAttribute('siPenaltyImposedType', 'penaltyTypeImposed'),
            new NodeAttribute('startDate', 'startDate'),
            new NodeAttribute('endDate', 'endDate'),
            new NodeAttribute('executed', 'isExecuted'),
        ];

        return new RecursionValue(
            'imposedErrus',
            new RecursionAttribute('PenaltyImposed', $spec)
        );
    }

    /**
     * Gets requested penalty data
     *
     * @return RecursionValue
     */
    protected function getPenaltiesRequested()
    {
        $spec = [
            new NodeAttribute('siPenaltyRequestedType', 'penaltyTypeRequested'),
            new NodeAttribute('duration', 'duration'),
        ];

        return new RecursionValue(
            'requestedErrus',
            new RecursionAttribute('PenaltyRequested', $spec)
        );
    }
}

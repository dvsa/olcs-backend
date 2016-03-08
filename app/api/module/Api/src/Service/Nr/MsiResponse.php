<?php

namespace Dvsa\Olcs\Api\Service\Nr;

use Dvsa\Olcs\Api\Entity\Cases\Cases as CasesEntity;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime as DateTimeExtended;
use Dvsa\Olcs\Api\Entity\Si\SeriousInfringement as SiEntity;
use Dvsa\Olcs\Api\Entity\Si\SiPenalty as SiPenaltyEntity;
use Dvsa\Olcs\Api\Entity\Si\ErruRequest as ErruRequestEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Olcs\XmlTools\Xml\XmlNodeBuilder;

/**
 * Class MsiResponse
 * @package Dvsa\Olcs\Api\Service\Nr
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class MsiResponse
{
    const AUTHORITY_TRU = 'Transport Regulation Unit';
    const AUTHORITY_TC = 'Traffic Commissioner';

    /**
     * @var string $responseDateTime
     */
    private $responseDateTime;

    /**
     * @var String
     */
    private $technicalId;

    /**
     * @var String
     */
    private $authority;

    /**
     * @var XmlNodeBuilder
     */
    private $xmlBuilder;

    public function __construct(XmlNodeBuilder $xmlBuilder)
    {
        $this->xmlBuilder = $xmlBuilder;
    }

    /**
     * @return XmlNodeBuilder
     */
    public function getXmlBuilder()
    {
        return $this->xmlBuilder;
    }

    /**
     * @return mixed
     */
    public function getResponseDateTime()
    {
        return $this->responseDateTime;
    }

    /**
     * @param mixed $responseDateTime
     */
    public function setResponseDateTime($responseDateTime)
    {
        $this->responseDateTime = $responseDateTime;
    }

    /**
     * @return String
     */
    public function getTechnicalId()
    {
        return $this->technicalId;
    }

    /**
     * @param String $technicalId
     */
    public function setTechnicalId($technicalId)
    {
        $this->technicalId = $technicalId;
    }

    /**
     * @return String
     */
    public function getAuthority()
    {
        return $this->authority;
    }

    /**
     * @param String $authority
     */
    public function setAuthority($authority)
    {
        $this->authority = $authority;
    }

    /**
     * Returns the result of filtering $value
     *
     * @param CasesEntity $case
     * @throws ForbiddenException
     *
     * @return array
     */
    public function create(CasesEntity $case)
    {
        if (!$case->canSendMsiResponse()) {
            throw new ForbiddenException('Unable to send Msi Response');
        }

        $this->setTechnicalId($this->generateGuid());
        $dateTime = new DateTimeExtended();
        $this->setResponseDateTime($dateTime->format(\DateTime::ISO8601));

        if ($case->getLicence() === null) {
            $this->setAuthority(self::AUTHORITY_TRU);
        } else {
            $this->setAuthority(self::AUTHORITY_TC);
        }

        $erruRequest = $case->getErruRequest();

        $xmlData = [
            'Header' => $this->getHeader($erruRequest),
            'Body' => $this->getBody($case, $erruRequest)
        ];

        $this->xmlBuilder->setData($xmlData);
        return $this->xmlBuilder->buildTemplate();
    }

    /**
     * @param ErruRequestEntity $erruRequest
     * @return array
     */
    private function getHeader(ErruRequestEntity $erruRequest)
    {
        return [
            'name' => 'Header',
            'attributes' => [
                'technicalId' => $this->getTechnicalId(),
                'workflowId' => $erruRequest->getWorkflowId(),
                'sentAt' => $this->getResponseDateTime(),
                'from' => 'UK'
            ],
            'nodes' => [
                0 => [
                    'name' => 'To',
                    'nodes' => [
                        0 => [
                            'name' => 'MemberState',
                            'attributes' => [
                                'code' => $erruRequest->getMemberStateCode()->getId()
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * @param CasesEntity $cases
     * @param ErruRequestEntity $erruRequest
     * @return array
     */
    private function getBody(CasesEntity $cases, ErruRequestEntity $erruRequest)
    {
        return [
            'name' => 'Body',
            'attributes' => [
                'businessCaseId' => $erruRequest->getNotificationNumber(),
                'originatingAuthority' => $erruRequest->getOriginatingAuthority(),
                'licensingAuthority' => $this->getAuthority(),
                'responseDateTime' => $this->getResponseDateTime()
            ],
            'nodes' => [
                0 => [
                    'name' => 'TransportUndertaking',
                    'attributes' => [
                        'name' => $erruRequest->getTransportUndertakingName()
                    ],
                    'nodes' => $this->formatPenalties($cases->getSeriousInfringements())
                ]
            ]
        ];
    }

    /**
     * Formats penalty information into something usable by xml node builder
     *
     * @param ArrayCollection $seriousInfringements
     * @return array
     */
    private function formatPenalties(ArrayCollection $seriousInfringements)
    {
        $formattedPenalties = [];

        /**
         * @var SiPenaltyEntity $penalty
         * @var SiEntity $si
         */
        foreach ($seriousInfringements as $si) {
            $penalties = $si->getAppliedPenalties();

            foreach ($penalties as $penalty) {
                $newPenalty = [];
                $newPenalty['authorityImposingPenalty'] = $this->getAuthority();
                $newPenalty['penaltyTypeImposed'] = $penalty->getSiPenaltyType()->getId();

                if ($penalty->getImposed() === 'N') {
                    $newPenalty['isImposed'] = 'No';
                    $newPenalty['reasonNotImposed'] = $penalty->getReasonNotImposed();
                } else {
                    $newPenalty['isImposed'] = 'Yes';
                }

                $startDate = $penalty->getStartDate();
                $endDate = $penalty->getEndDate();

                if ($startDate) {
                    $newPenalty['startDate'] = $startDate;
                }

                if ($endDate) {
                    $newPenalty['endDate'] = $endDate;
                }

                $formattedPenalties[] = [
                    'name' => 'PenaltyImposed',
                    'attributes' => $newPenalty
                ];
            }
        }

        return $formattedPenalties;
    }

    /**
     * Generate a GUID
     *
     * @return string
     */
    private function generateGuid()
    {
        // com_create_guid is unavailable on our environments
        return sprintf(
            '%04X%04X-%04X-%04X-%04X-%04X%04X%04X',
            mt_rand(0, 65535),
            mt_rand(0, 65535),
            mt_rand(0, 65535),
            mt_rand(16384, 20479),
            mt_rand(32768, 49151),
            mt_rand(0, 65535),
            mt_rand(0, 65535),
            mt_rand(0, 65535)
        );
    }
}

<?php

namespace Dvsa\Olcs\Snapshot\Service\Snapshots\Permits;

use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Snapshot\Service\Snapshots\AbstractGenerator;
use Dvsa\Olcs\Snapshot\Service\Snapshots\SnapshotGeneratorInterface;

/**
 * Class EcmtAnnualGenerator
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class EcmtAnnualGenerator extends AbstractGenerator implements SnapshotGeneratorInterface
{
    /**
     * @var array
     */
    private $data;

    /**
     * Generate the snapshot html
     *
     * @return string
     * @throws \Exception
     */
    public function generate(): string
    {
        /** @var EcmtPermitApplication $ecmtPermitApplication */
        $ecmtPermitApplication = $this->data['entity'];

        if (!$ecmtPermitApplication instanceof EcmtPermitApplication) {
            throw new \Exception('Snapshot generator expects ECMT permit application record');
        }

        return $this->generateReadonly(
            [
                'permitType' => $ecmtPermitApplication->getPermitType()->getDescription(),
                'operator' => $ecmtPermitApplication->getLicence()->getOrganisation()->getName(),
                'ref' => $ecmtPermitApplication->getApplicationRef(),
                'questionAnswerPartialName' => 'question-answer-section',
                'questionAnswerData' => $ecmtPermitApplication->getQuestionAnswerData(),
                'guidanceDeclaration' => [
                    'bullets' => 'markup-irhp-declaration-1',
                    'declaration' => 'permits.snapshot.declaration',
                ],
            ],
            'permit-application'
        );
    }

    /**
     * Set the data needed to generate the HTML
     *
     * @param array $data data required to generate the HTML
     *
     * @return void
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }
}

<?php

namespace Dvsa\Olcs\Snapshot\Service\Snapshots\Permits;

use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Snapshot\Service\Snapshots\AbstractGenerator;
use Dvsa\Olcs\Snapshot\Service\Snapshots\SnapshotGeneratorInterface;

/**
 * Class IrhpGenerator
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class IrhpGenerator extends AbstractGenerator implements SnapshotGeneratorInterface
{
    const SKIPPED_QUESTIONS = [
        'custom-check-answers',
        'custom-declaration',
    ];

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
        /** @var IrhpApplication $irhpApplication */
        $irhpApplication = $this->data['entity'];

        if (!$irhpApplication instanceof IrhpApplication) {
            throw new \Exception('Snapshot generator expects IRHP application record');
        }

        $questionAnswerData = $irhpApplication->getQuestionAnswerData();

        //we don't need check answers and declaration on a snapshot
        foreach (self::SKIPPED_QUESTIONS as $question) {
            unset($questionAnswerData[$question]);
        }

        return $this->generateReadonly(
            [
                'permitType' => $irhpApplication->getIrhpPermitType()->getName()->getDescription(),
                'operator' => $irhpApplication->getLicence()->getOrganisation()->getName(),
                'ref' => $irhpApplication->getApplicationRef(),
                'questionAnswerData' => $questionAnswerData,
                'guidanceDeclaration' => [
                    'bullets' => [
                        'permits.irhp.declaration.bullet.guidance.note',
                        'permits.irhp.declaration.bullet.conditions',
                        'permits.irhp.declaration.bullet.guidance.carry',
                        'permits.irhp.declaration.bullet.guidance.transport',
                    ],
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

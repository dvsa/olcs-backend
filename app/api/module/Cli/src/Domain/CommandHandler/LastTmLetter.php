<?php

namespace Dvsa\Olcs\Cli\Domain\CommandHandler;

use Dvsa\Olcs\Api\Domain\Command\Document\GenerateAndStoreWithMultipleAddresses;
use Dvsa\Olcs\Api\Domain\Repository\Licence;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\Doc\Document;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Api\Entity\System\SubCategory;
use Dvsa\Olcs\Api\Entity\User\User;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\Document\PrintLetter;
use \Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Transfer\Command\Task\CreateTask;
use Dvsa\Olcs\Api\Entity\Tm\TransportManagerLicence as TmlEntity;

final class LastTmLetter extends AbstractCommandHandler
{
    const GB_GV_TEMPLATE = [
        'identifier' => 'GV_letter_to_op_regarding_no_TM_specified',
        'id'         => 919
    ];
    const GB_PSV_TEMPLATE = [
        'identifier' => 'PSV_letter_to_op_regarding_no_TM_specified',
        'id'         => 920
    ];
    const NI_GV_TEMPLATE = [
        'identifier' => 'GV_letter_to_op_regarding_no_TM_specified_NI',
        'id'         => 918
    ];


    /**
     * @var string
     */
    protected $repoServiceName = 'Licence';

    protected $extraRepos = ['User','Document','DocTemplate'];

    /**
     * Handle command
     *
     * @param \Dvsa\Olcs\Cli\Domain\Command\DataGovUkExport $command Command
     *
     * @return \Dvsa\Olcs\Api\Domain\Command\Result
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     * @throws \Exception
     */
    public function handleCommand(CommandInterface $command)
    {
        /** @var Licence $licenceRepo */
        $licenceRepo = $this->getRepo();
        $eligibleLicences = $licenceRepo->fetchForLastTmAutoLetter();

        /** @var LicenceEntity $licence */
        foreach ($eligibleLicences as $licence) {
            $documents = $this->generateDocuments($licence);
            $this->printAndEmailDocuments($documents);
        }

        return $this->result;
    }

    /**
     * @param LicenceEntity $licence
     * @return array|null
     */
    private function generateDocuments(LicenceEntity $licence)
    {
        $template = $this->selectTemplate($licence);

        $caseworkerDetailsBundle = [
            'contactDetails' => [
                'address',
                'phoneContacts' => [
                    'phoneContactType'
                ],
                'person'
            ],
            'team' => [
                'trafficArea' => [
                    'contactDetails' => [
                        'address'
                    ]
                ]
            ]
        ];

        $caseworkerNameBundle = [
            'contactDetails' => [
                'person'
            ]
        ];

        $licenceBundle = [
            'trafficArea',
        ];

        $createTaskResult = $this->handleSideEffect($this->createTaskSideEffect($licence->getId()));
        $this->result->merge($createTaskResult);

        $userRepo = $this->getRepo('User');
        /** @var User $user */
        $user = $userRepo->fetchById($createTaskResult->getId('assignedToUser'));
        $contactDetails = $user->serialize($caseworkerDetailsBundle);
        $licenceDetails = $licence->serialize($licenceBundle);
        $caseworkerName = $user->serialize($caseworkerNameBundle);
        $caseworkerDetails = [
            $contactDetails,
            $licenceDetails
        ];

        $generateCommandData = [
            'template' => $template['identifier'],
            'query' => [
                'licence' => $licence->getId(),
            ],
            'description' => 'Last TM letter Licence ' . $licence->getLicNo(),
            'licence' => $licence->getId(),
            'category' => Category::CATEGORY_TRANSPORT_MANAGER,
            'subCategory' => Category::DOC_SUB_CATEGORY_TRANSPORT_MANAGER_CORRESPONDENCE,
            'isExternal' => false,
            'metadata' => json_encode([
                'details' => [
                    'category' => Category::CATEGORY_TRANSPORT_MANAGER,
                    'documentSubCategory' => Category::DOC_SUB_CATEGORY_TRANSPORT_MANAGER_CORRESPONDENCE,
                    'documentTemplate' => $template['id'],
                    'allowEmail' => $licence->getOrganisation()->getAllowEmail()
                ]
            ]),
            'knownValues' => [
                'caseworker_details' => $caseworkerDetails,
                'caseworker_name' => $caseworkerName
            ]
        ];

        $data = [
            'generateCommandData' => $generateCommandData,
            'addressBookmark' => 'licence_holder_address',
            'bookmarkBundle' => [
                'correspondenceCd' => ['address']
            ]
        ];

        $result = $this->handleSideEffect(GenerateAndStoreWithMultipleAddresses::create($data));
        $this->result->merge($result);

        return $result->getId('documents');
    }

    /**
     * @param array $documents (array of document ids)
     * @return void
     */
    private function printAndEmailDocuments($documents)
    {
        foreach ($documents as $document) {
            $this->printDocument($document);
            $this->maybeEmailDocument($document);
        }
    }

    /**
     * @param int $document (id)
     * @return void
     */
    private function printDocument($document)
    {
        $result = $this->handleSideEffect(
            PrintLetter::create([
                'id' => $document,
                'method' => PrintLetter::METHOD_PRINT_AND_POST
            ])
        );

        $this->result->merge($result);
    }

    /**
     * @param int $document (id)
     * @return void
     */
    private function maybeEmailDocument($document)
    {
        $documentRepo = $this->getRepo('Document');
        /** @var Document $docEntity */
        $docEntity = $documentRepo->fetchById($document);
        $metadata = json_decode($docEntity->getMetadata(), true);
        if ($this->shouldSendEmail($metadata)) {
            $result = $this->handleSideEffect(
                PrintLetter::create([
                    'id' => $document,
                    'method' => PrintLetter::METHOD_EMAIL
                ])
            );
            $this->result->merge($result);
        }
    }

    /**
     * @param LicenceEntity $licence
     * @return array
     */
    private function selectTemplate(LicenceEntity $licence)
    {
        $template = self::GB_GV_TEMPLATE;

        if ($licence->isNi()) {
            $template = self::NI_GV_TEMPLATE;
        } elseif ($licence->isPsv()) {
            $template = self::GB_PSV_TEMPLATE;
        }

        return $template;
    }

    /**
     * @param array $metadata
     * @return bool
     */
    private function shouldSendEmail($metadata)
    {
        return array_key_exists('details', $metadata) &&
            is_array($metadata['details']) &&
            array_key_exists('sendToAddress', $metadata['details']) &&
            $metadata['details']['sendToAddress'] === 'correspondenceAddress' &&
            array_key_exists('allowEmail', $metadata['details']) &&
            $metadata['details']['allowEmail'] === 'Y';
    }

    private function createTaskSideEffect($licenceId)
    {
        $params = [
            'category' => Category::CATEGORY_APPLICATION,
            'subCategory' => SubCategory::TM_SUB_CATEGORY_TM1_REMOVAL,
            'description' => TmlEntity::DESC_TM_REMOVED_LAST_RESPONSE,
            'actionDate' => (new DateTime())->add(new \DateInterval('P21D'))->format('Y-m-d'),
            'licence' => $licenceId,
            'urgent' => 'Y'
        ];

        return CreateTask::create($params);
    }
}

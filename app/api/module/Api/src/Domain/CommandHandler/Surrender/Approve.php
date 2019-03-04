<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Surrender;

use Dvsa\Olcs\Api\Domain\Command\Document\GenerateAndStore;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Exception\Exception;
use Dvsa\Olcs\Api\Domain\Exception\RuntimeException;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Surrender;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\Licence\SurrenderLicence;
use Dvsa\Olcs\Transfer\Command\Surrender\Update as UpdateSurrender;

class Approve extends AbstractSurrenderCommandHandler
{
    protected $repoServiceName = 'Licence';

    /**
     * @param CommandInterface $command
     *
     * @return Result
     * @throws RuntimeException
     * @throws \Exception
     */
    public function handleCommand(CommandInterface $command)
    {
        $updateSurrenderResult = $this->handleSideEffect(UpdateSurrender::create(
            [
                'id' => $command->getId(),
                'status' => Surrender::SURRENDER_STATUS_APPROVED
            ]
        ));
        $this->result->addMessage($updateSurrenderResult);

        $surrenderLicenceResult = $this->handleSideEffect(SurrenderLicence::create(
            [
                'id' => $command->getId(),
                'surrenderDate' => $command->getSurrenderDate(),
                'terminated' => false
            ]
        ));

        $this->result->addMessage($surrenderLicenceResult);

        $this->result->merge($this->generateDocumentAndSendNotificationEmail($command->getId()));

        return $this->result;
    }

    private function generateDocumentAndSendNotificationEmail(int $licId): Result
    {
        /** @var $licenceEntity \Dvsa\Olcs\Api\Entity\Licence\Licence */
        $licenceEntity = $this->getRepo()->fetchById($licId);

        [$template, $description] = $this->returnTemplateAndDescription($licenceEntity);

        $dtoData = [
            'template' => $template,
            'query' => [
                'licence' => $licId,
                'user' => $licenceEntity->getCreatedBy()->getId()
            ],
            'description' => $description,
            'licence' => $licId,
            'category' => Category::CATEGORY_LICENSING,
            'subCategory' => Category::TASK_SUB_CATEGORY_APPLICATION_SURRENDER,
            'isExternal' => true,
            'isScan' => false,
            'dispatch' => true
        ];

        return $this->handleSideEffect(GenerateAndStore::create($dtoData));
    }

    private function returnTemplateAndDescription(Licence $licenceEntity): array
    {
        $goodsOrPsv = $licenceEntity->getGoodsOrPsv()->getId();
        $licType = $licenceEntity->getLicenceType()->getId();
        $isNi = $licenceEntity->isNi();

        if ($goodsOrPsv === Licence::LICENCE_CATEGORY_PSV && $this->isValidLicenceType($licType) && !$isNi) {
            return ['GB/SURRENDER_LETTER_TO_OPERATOR_PSV', 'PSV - Surrender actioned letter'];
        }

        if ($goodsOrPsv === Licence::LICENCE_CATEGORY_GOODS_VEHICLE && $this->isValidLicenceType($licType) && !$isNi) {
            return ['GB/SURRENDER_LETTER_TO_OPERATOR_GV_GB', 'GV - Surrender actioned letter'];
        }

        if ($goodsOrPsv === Licence::LICENCE_CATEGORY_GOODS_VEHICLE && $this->isValidLicenceType($licType) && $isNi) {
            return ['NI/SURRENDER_LETTER_TO_OPERATOR_GV_NI', 'GV - Surrender actioned letter (NI)'];
        }

        throw new Exception('Licence type not surrenderable');
    }

    private function isValidLicenceType(string $licType): bool
    {
        $validTypes = [
            Licence::LICENCE_TYPE_STANDARD_NATIONAL,
            Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL,
            Licence::LICENCE_TYPE_RESTRICTED
        ];

        return in_array($licType, $validTypes);
    }
}

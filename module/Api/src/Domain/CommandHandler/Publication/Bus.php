<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Publication;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Domain\PublicationGeneratorAwareInterface;
use Dvsa\Olcs\Api\Domain\PublicationGeneratorAwareTrait;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Doctrine\ORM\Query;

use Dvsa\Olcs\Transfer\Command\Publication\PiHearing as PiHearingCmd;
use Dvsa\Olcs\Api\Entity\Pi\PiHearing as PiHearingEntity;
use Dvsa\Olcs\Api\Entity\Cases\Cases as CasesEntity;
use Dvsa\Olcs\Api\Entity\Bus\BusReg as BusRegEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Publication\Publication as PublicationEntity;
use Dvsa\Olcs\Api\Entity\Publication\PublicationSection as PublicationSectionEntity;
use Dvsa\Olcs\Api\Entity\Publication\PublicationLink as PublicationLinkEntity;
use Dvsa\Olcs\Api\Domain\Query\Bookmark\UnpublishedBusReg as UnpublishedBusRegQry;

/**
 * Bus command handler
 *
 * @package Dvsa\Olcs\Api\Domain\CommandHandler\Publication
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class Bus extends AbstractCommandHandler implements TransactionedInterface, PublicationGeneratorAwareInterface
{
    use PublicationGeneratorAwareTrait;
    use CreatePublicationTrait;

    protected $repoServiceName = 'PublicationLink';

    protected $extraRepos = ['Bus', 'Publication', 'TrafficArea'];

    /**
     * @param CommandInterface $command
     * @return Result
     * @throws \Exception
     */
    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        /**
         * @var PiHearingEntity $hearing
         * @var BusRegEntity $busReg
         * @var CasesEntity $case
         * @var LicenceEntity $licence
         * @var $command PiHearingCmd
         */
        $busReg = $this->getRepo('Bus')->fetchUsingId($command, Query::HYDRATE_OBJECT);
        $licence = $busReg->getLicence();
        $revertStatus = $busReg->getRevertStatus()->getId();
        $trafficAreas = $busReg->getTrafficAreas();
        $text1 = $busReg->getRegNo();

        $shortNotice = $busReg->getIsShortNotice();

        foreach ($trafficAreas as $ta) {
            switch ($revertStatus) {
                case BusRegEntity::STATUS_NEW:
                    $handler = 'BusGrantNew';
                    $pubSection = ($shortNotice == 'Y' ?
                        PublicationSectionEntity::BUS_NEW_SHORT_SECTION :
                        PublicationSectionEntity::BUS_NEW_SECTION
                    );
                    break;
                case BusRegEntity::STATUS_VAR:
                    $handler = 'BusGrantVariation';
                    $pubSection = ($shortNotice == 'Y' ?
                        PublicationSectionEntity::BUS_VAR_SHORT_SECTION :
                        PublicationSectionEntity::BUS_VAR_SECTION
                    );
                    break;
                case BusRegEntity::STATUS_CANCEL:
                    $handler = 'BusGrantCancel';
                    $pubSection = ($shortNotice == 'Y' ?
                        PublicationSectionEntity::BUS_CANCEL_SHORT_SECTION :
                        PublicationSectionEntity::BUS_CANCEL_SECTION
                    );
                    break;
                default:
                    throw new ForbiddenException('This status can\'t be published');
            }

            /**
             * @var UnpublishedBusRegQry $unpublishedQuery
             * @var PublicationEntity $publication
             * @var PublicationLinkEntity $publicationLink
             */
            $publication = $this->getPublication($ta->getId(), 'N&P');
            $publicationSection = $this->getPublicationSection($pubSection);
            $unpublishedQuery = $this->getUnpublishedBusRegQuery($publication->getId(), $busReg->getId(), $pubSection);
            $publicationLink = $this->getPublicationLink($unpublishedQuery);

            $publicationLink->updateBusReg($busReg, $licence, $publication, $publicationSection, $ta, $text1);

            $result->merge($this->createPublication($handler, $publicationLink, []));
        }

        return $result;
    }

    public function getUnpublishedBusRegQuery($publication, $busReg, $pubSection)
    {
        $data =  [
            'publication' => $publication,
            'busReg' => $busReg,
            'publicationSection' => $pubSection
        ];

        return UnpublishedBusRegQry::create($data);
    }
}

<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Publication;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Publication\PublicationSection as PublicationSectionEntity;
use Dvsa\Olcs\Api\Entity\Publication\PublicationLink as PublicationLinkEntity;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Create Publication Trait
 */
trait CreatePublicationTrait
{
    /**
     * @param int $pubSection
     * @return PublicationSectionEntity
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    public function getPublicationSection($pubSection)
    {
        return $this->getRepo()->getReference(PublicationSectionEntity::class, $pubSection);
    }

    /**
     * @param int $trafficAreaId
     * @param string $pubType
     * @return PublicationEntity
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    public function getPublication($trafficAreaId, $pubType)
    {
        return $this->getRepo('Publication')->fetchLatestForTrafficAreaAndType($trafficAreaId, $pubType);
    }

    /**
     * Check if we have an existing publication link, if not return a new one
     *
     * @param QueryInterface $query
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     * @return PublicationLinkEntity
     */
    public function getPublicationLink(QueryInterface $query)
    {
        $previousPublication = $this->getRepo()->fetchSingleUnpublished($query);

        if (!empty($previousPublication)) {
            return $previousPublication;
        }

        return new PublicationLinkEntity();
    }

    /**
     * @param $publicationConfig
     * @param PublicationLinkEntity $publicationLink
     * @param $existingContext
     * @return Result
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     * @throws \Exception
     */
    private function createPublication(
        $publicationConfig,
        PublicationLinkEntity $publicationLink,
        $existingContext
    ) {
        $publicationLink = $this->getPublicationGenerator()->createPublication(
            $publicationConfig,
            $publicationLink,
            $existingContext
        );

        $this->getRepo()->save($publicationLink);

        $result = new Result();
        $result->addId('publicationLink', $publicationLink->getId());

        return $result;
    }

    private function clearPoliceData(PublicationLinkEntity $publicationLink)
    {
        $policeData = $publicationLink->getPoliceDatas();
        if (!$policeData->isEmpty()) {
            foreach ($policeData as $police) {
                $police->setPublicationLink(null);
            }
        }

        $publicationLink->setPoliceDatas($policeData);
        return $publicationLink;
    }
}

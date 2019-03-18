<?php

/**
 * Generate Permit Documents
 */

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Command\IrhpPermit\GenerateCoverLetterDocument;
use Dvsa\Olcs\Api\Domain\Command\IrhpPermit\GeneratePermitDocument;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermit as IrhpPermitEntity;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Doctrine\ORM\Query;

/**
 * Generate a Permit in RTF form
 *
 * @author Henry White <henry.white@capgemini.com>
 */
final class GeneratePermitDocuments extends AbstractCommandHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_ECMT];
    protected $repoServiceName = 'IrhpPermit';

    /**
     * @var array
     */
    private $coverLetterLicenceIds = [];

    /**
     * Handle command
     *
     * @param CommandInterface $command Command
     *
     * @return Result
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    public function handleCommand(CommandInterface $command)
    {
        foreach ($command->getIds() as $id) {
            $irhpPermit = $this->getRepo()->fetchById($id, Query::HYDRATE_OBJECT);

            // generate cover letter
            $this->generateCoverLetter($irhpPermit);

            // generate permit
            $this->generatePermit($irhpPermit);
        }

        return $this->result;
    }

    /**
     * Generate cover letter document
     *
     * @param IrhpPermitEntity $irhpPermit IRHP Permit
     *
     * @return void
     */
    private function generateCoverLetter(IrhpPermitEntity $irhpPermit)
    {
        $irhpPermitApplication = $irhpPermit->getIrhpPermitApplication();

        $irhpPermitType = $irhpPermitApplication->getIrhpPermitWindow()->getIrhpPermitStock()->getIrhpPermitType();

        if ($irhpPermitType->isBilateral()) {
            $licenceId = $irhpPermitApplication->getRelatedApplication()->getLicence()->getId();

            if (isset($this->coverLetterLicenceIds[$licenceId])) {
                // only one cover letter per licence required
                return;
            }

            $this->coverLetterLicenceIds[$licenceId] = true;
        }

        $this->result->merge(
            $this->handleSideEffect(
                GenerateCoverLetterDocument::create(
                    [
                        'irhpPermit' => $irhpPermit->getId(),
                    ]
                )
            ),
            true
        );
    }

    /**
     * Generate permit document
     *
     * @param IrhpPermitEntity $irhpPermit IRHP Permit
     *
     * @return void
     */
    private function generatePermit(IrhpPermitEntity $irhpPermit)
    {
        $this->result->merge(
            $this->handleSideEffect(
                GeneratePermitDocument::create(
                    [
                        'irhpPermit' => $irhpPermit->getId(),
                    ]
                )
            ),
            true
        );
    }
}

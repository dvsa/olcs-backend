<?php

/**
 * Update IrfoPsvAuth
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Irfo;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Entity\Irfo\IrfoPsvAuth;
use Dvsa\Olcs\Api\Entity\Irfo\IrfoPsvAuthType;
use Dvsa\Olcs\Api\Entity\Irfo\IrfoPsvAuthNumber;
use Dvsa\Olcs\Api\Entity\ContactDetails\Country;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Doctrine\ORM\Query;

/**
 * Update IrfoPsvAuth
 */
final class UpdateIrfoPsvAuth extends AbstractCommandHandler
{
    protected $repoServiceName = 'IrfoPsvAuth';

    protected $extraRepos = ['IrfoPsvAuthNumber'];

    public function handleCommand(CommandInterface $command)
    {
        try {
            $this->getRepo()->beginTransaction();

            $irfoPsvAuth = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT, $command->getVersion());

            $irfoPsvAuth->setIrfoPsvAuthType(
                $this->getRepo()->getReference(IrfoPsvAuthType::class, $command->getIrfoPsvAuthType())
            );
            $irfoPsvAuth->setStatus($this->getRepo()->getRefdataReference($command->getStatus()));
            $irfoPsvAuth->setValidityPeriod($command->getValidityPeriod());
            $irfoPsvAuth->setServiceRouteFrom($command->getServiceRouteFrom());
            $irfoPsvAuth->setServiceRouteTo($command->getServiceRouteTo());
            $irfoPsvAuth->setJourneyFrequency($command->getJourneyFrequency());
            $irfoPsvAuth->setIsFeeExemptApplication($command->getIsFeeExemptApplication());
            $irfoPsvAuth->setIsFeeExemptAnnual($command->getIsFeeExemptAnnual());
            $irfoPsvAuth->setExemptionDetails($command->getExemptionDetails());
            $irfoPsvAuth->setCopiesRequired($command->getCopiesRequired());
            $irfoPsvAuth->setCopiesRequiredTotal($command->getCopiesRequiredTotal());

            if ($command->getJourneyFrequency() !== null) {
                $irfoPsvAuth->setJourneyFrequency(
                    $this->getRepo()->getRefdataReference($command->getJourneyFrequency())
                );
            }

            if ($command->getInForceDate() !== null) {
                $irfoPsvAuth->setInForceDate(new \DateTime($command->getInForceDate()));
            }

            if ($command->getExpiryDate() !== null) {
                $irfoPsvAuth->setExpiryDate(new \DateTime($command->getExpiryDate()));
            }

            if ($command->getApplicationSentDate() !== null) {
                $irfoPsvAuth->setApplicationSentDate(new \DateTime($command->getApplicationSentDate()));
            }

            if ($command->getCountrys() !== null) {
                $countries = [];

                foreach ($command->getCountrys() as $countryId) {
                    $countries[] = $this->getRepo()->getReference(Country::class, $countryId);
                }

                $irfoPsvAuth->setCountrys($countries);
            }

            $this->getRepo()->save($irfoPsvAuth);

            // deal with IrfoPsvAuthNumbers
            $this->processIrfoPsvAuthNumbers($irfoPsvAuth, $command->getIrfoPsvAuthNumbers());

            $result = new Result();
            $result->addId('irfoPsvAuth', $irfoPsvAuth->getId());
            $result->addMessage('IRFO PSV Auth updated successfully');

            $this->getRepo()->commit();

            return $result;
        } catch (\Exception $ex) {
            $this->getRepo()->rollback();

            throw $ex;
        }
    }

    /**
     * @param IrfoPsvAuth $irfoPsvAuth
     * @param array $irfoPsvAuthNumbers
     * @return array
     */
    private function processIrfoPsvAuthNumbers(IrfoPsvAuth $irfoPsvAuth, array $irfoPsvAuthNumbers)
    {
        $reduced = [];

        foreach ($irfoPsvAuthNumbers as $irfoPsvAuthNumber) {
            if (empty($irfoPsvAuthNumber['name'])) {
                // filter out empty values
                continue;
            }

            if (!empty($irfoPsvAuthNumber['id'])) {
                // update
                $irfoPsvAuthNumberEntity = $this->getRepo('IrfoPsvAuthNumber')->fetchById(
                    $irfoPsvAuthNumber['id'],
                    Query::HYDRATE_OBJECT,
                    $irfoPsvAuthNumber['version']
                );
                $irfoPsvAuthNumberEntity->setName($irfoPsvAuthNumber['name']);
            } else {
                // create
                $irfoPsvAuthNumberEntity = new IrfoPsvAuthNumber(
                    $irfoPsvAuth,
                    $irfoPsvAuthNumber['name']
                );
            }

            $this->getRepo('IrfoPsvAuthNumber')->save($irfoPsvAuthNumberEntity);
            $reduced[] = $irfoPsvAuthNumberEntity->getId();
        }

        // remove the rest records
        foreach ($irfoPsvAuth->getIrfoPsvAuthNumbers() as $irfoPsvAuthNumberEntity) {
            if (!in_array($irfoPsvAuthNumberEntity->getId(), $reduced)) {
                $this->getRepo('IrfoPsvAuthNumber')->delete($irfoPsvAuthNumberEntity);
            }
        }

        return $reduced;
    }
}

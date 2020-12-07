<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Email;

use Dvsa\Olcs\Api\Domain\CommandHandler\Traits\PermitEmailTrait;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Laminas\I18n\Translator\Translator;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Send confirmation of ECMT short term app being successful
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class SendEcmtShortTermSuccessful extends AbstractEcmtShortTermEmailHandler
{
    use PermitEmailTrait;

    protected $repoServiceName = 'IrhpApplication';
    protected $template = 'ecmt-short-term-app-successful';
    protected $subject = 'email.ecmt.short.term.response.subject';

    /** @var Translator */
    private $translator;

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator Service Manager
     *
     * @return $this
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mainServiceLocator = $serviceLocator->getServiceLocator();

        $this->translator = $mainServiceLocator->get('translator');

        return parent::createService($serviceLocator);
    }

    /**
     * Get template variables
     *
     * @param IrhpApplication $recordObject
     *
     * @return array
     */
    protected function getTemplateVariables($recordObject): array
    {
        $this->getRepo()->refresh($recordObject);

        $irhpPermitApplication = $recordObject->getFirstIrhpPermitApplication();
        $irhpPermitStock = $irhpPermitApplication->getIrhpPermitWindow()->getIrhpPermitStock();

        $euro5PermitsGranted = $irhpPermitApplication->countPermitsAwarded(RefData::EMISSIONS_CATEGORY_EURO5_REF);
        $euro6PermitsGranted = $irhpPermitApplication->countPermitsAwarded(RefData::EMISSIONS_CATEGORY_EURO6_REF);

        $issueFee = $recordObject->getLatestIssueFee();
        $invoicedDateTime = $issueFee->getInvoicedDateTime();
        $irhpApplicationId = $recordObject->getId();

        $previousLocale = $this->translator->getLocale();

        $locale = $this->getTranslateToWelsh($recordObject) == 'Y' ? 'cy_GB' : 'en_GB';
        $this->translator->setLocale($locale);

        $periodName = $this->translator->translate(
            $irhpPermitStock->getPeriodNameKey()
        );

        $this->translator->setLocale($previousLocale);

        return [
            'applicationRef' => $recordObject->getApplicationRef(),
            'euro5PermitsGranted' => $euro5PermitsGranted,
            'euro6PermitsGranted' => $euro6PermitsGranted,
            'issueFeeAmount' => $this->formatCurrency($issueFee->getFeeTypeAmount()),
            'issueFeeTotal' => $this->formatCurrency($issueFee->getOutstandingAmount()),
            'paymentDeadlineNumDays' => '10', // TODO - OLCS-21979
            'issueFeeDeadlineDate' => $this->calculateDueDate($invoicedDateTime),
            'paymentUrl' => 'http://selfserve/permits/application/' . $irhpApplicationId . '/awaiting-fee',
            'periodName' => $periodName
        ];
    }
}

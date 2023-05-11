<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Email;

use Dvsa\Olcs\Api\Domain\CommandHandler\Traits\PermitEmailTrait;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Service\Permits\Fees\DaysToPayIssueFeeProvider;
use Interop\Container\ContainerInterface;
use Laminas\I18n\Translator\Translator;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Send confirmation of ECMT short term apply/pay/score/get app being part successful
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class SendEcmtShortTermApsgPartSuccessful extends AbstractEcmtShortTermEmailHandler
{
    use PermitEmailTrait;

    protected $repoServiceName = 'IrhpApplication';
    protected $template = 'ecmt-short-term-app-part-successful';
    protected $subject = 'email.ecmt.short.term.response.subject';

    /** @var Translator */
    private $translator;

    /** @var DaysToPayIssueFeeProvider */
    private $daysToPayIssueFeeProvider;

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator Service Manager
     *
     * @return $this
     */
    public function createService(ServiceLocatorInterface $serviceLocator, $name = null, $requestedName = null)
    {
        return $this->__invoke($serviceLocator, SendEcmtShortTermApsgPartSuccessful::class);
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

        $euro5PermitsRequired = $irhpPermitApplication->getRequiredEuro5();
        $euro6PermitsRequired = $irhpPermitApplication->getRequiredEuro6();
        $euro5PermitsGranted = $irhpPermitApplication->countPermitsAwarded(RefData::EMISSIONS_CATEGORY_EURO5_REF);
        $euro6PermitsGranted = $irhpPermitApplication->countPermitsAwarded(RefData::EMISSIONS_CATEGORY_EURO6_REF);

        $issueFee = $recordObject->getLatestIssueFee();
        $invoicedDateTime = $issueFee->getInvoicedDateTime();
        $irhpApplicationId = $recordObject->getId();

        $previousLocale = $this->translator->getLocale();

        $locale = $this->getTranslateToWelsh($recordObject) == 'Y' ? 'cy_GB' : 'en_GB';
        $this->translator->setLocale($locale);

        $periodName = $this->translator->translate(
            $irhpPermitApplication->getIrhpPermitWindow()->getIrhpPermitStock()->getPeriodNameKey()
        );

        $this->translator->setLocale($previousLocale);

        $daysToPayIssueFee = $this->daysToPayIssueFeeProvider->getDays();

        return [
            'applicationRef' => $recordObject->getApplicationRef(),
            'euro5PermitsRequired' => $euro5PermitsRequired,
            'euro6PermitsRequired' => $euro6PermitsRequired,
            'euro5PermitsGranted' => $euro5PermitsGranted,
            'euro6PermitsGranted' => $euro6PermitsGranted,
            'issueFeeAmount' => $this->formatCurrency($issueFee->getFeeTypeAmount()),
            'issueFeeTotal' => $this->formatCurrency($issueFee->getOutstandingAmount()),
            'paymentDeadlineNumDays' => $daysToPayIssueFee,
            'issueFeeDeadlineDate' => $this->calculateDueDate($invoicedDateTime, $daysToPayIssueFee),
            'paymentUrl' => 'http://selfserve/permits/application/' . $irhpApplicationId . '/awaiting-fee',
            'periodName' => $periodName
        ];
    }
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $fullContainer = $container;
        
        if (method_exists($container, 'getServiceLocator') && $container->getServiceLocator()) {
            $container = $container->getServiceLocator();
        }

        $this->translator = $container->get('translator');
        $this->daysToPayIssueFeeProvider = $container->get('PermitsFeesDaysToPayIssueFeeProvider');
        return parent::__invoke($fullContainer, $requestedName, $options);
    }
}

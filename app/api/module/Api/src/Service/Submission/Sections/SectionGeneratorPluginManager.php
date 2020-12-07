<?php

namespace Dvsa\Olcs\Api\Service\Submission\Sections;

use Laminas\ServiceManager\AbstractPluginManager;
use Laminas\ServiceManager\Exception\InvalidServiceException;
use Laminas\ServiceManager\Exception\RuntimeException;

/**
 * Class PluginManager
 * @package Dvsa\Olcs\Api\Service\Submission\Sections
 */
class SectionGeneratorPluginManager extends AbstractPluginManager
{
    protected $instanceOf = SectionGeneratorInterface::class;

    protected $aliases = [
        'casesummary' => CaseSummary::class,
        'caseoutline' => CaseOutline::class,
        'mostseriousinfringement' => MostSeriousInfringement::class,
        'outstandingapplications' => OutstandingApplications::class,
        'people' => People::class,
        'operatingcentres' => OperatingCentres::class,
        'conditionsandundertakings' => ConditionsAndUndertakings::class,
        'intelligenceunitcheck' => NoDataCommentsOnly::class,
        'interim' => NoDataCommentsOnly::class,
        'advertisement' => NoDataCommentsOnly::class,
        'linkedlicencesappnumbers' => LinkedLicences::class,
        'leadtcarea' => LeadTcArea::class,
        'currentsubmissions' => NoDataCommentsOnly::class,
        'authrequestedappliedfor' => AuthRequestedAppliedFor::class,
        'transportmanagers' => TransportManagers::class,
        'continuouseffectivecontrol' => NoDataCommentsOnly::class,
        'fitnessandrepute' => NoDataCommentsOnly::class,
        'previoushistory' => NoDataCommentsOnly::class,
        'busregappdetails' => NoDataCommentsOnly::class,
        'transportauthoritycomments' => NoDataCommentsOnly::class,
        'totalbusregistrations' => NoDataCommentsOnly::class,
        'locallicencehistory' => NoDataCommentsOnly::class,
        'linkedmlhhistory' => NoDataCommentsOnly::class,
        'registrationdetails' => NoDataCommentsOnly::class,
        'maintenancetachographshours' => NoDataCommentsOnly::class,
        'prohibitionhistory' => ProhibitionHistory::class,
        'convictionfpnoffencehistory' => ConvictionFpnOffenceHistory::class,
        'annualtesthistory' => AnnualTestHistory::class,
        'penalties' => Penalties::class,
        'otherissues' => NoDataCommentsOnly::class,
        'tereports' => NoDataCommentsOnly::class,
        'siteplans' => NoDataCommentsOnly::class,
        'planningpermission' => NoDataCommentsOnly::class,
        'applicantscomments' => ApplicantsComments::class,
        'applicantsresponses' => ApplicantsResponses::class,
        'visibilityaccessegresssize' => NoDataCommentsOnly::class,
        'compliancecomplaints' => ComplianceComplaints::class,
        'environmentalcomplaints' => EnvironmentalComplaints::class,
        'oppositions' => Oppositions::class,
        'financialinformation' => NoDataCommentsOnly::class,
        'maps' => NoDataCommentsOnly::class,
        'waivefeelatefee' => NoDataCommentsOnly::class,
        'surrender' => NoDataCommentsOnly::class,
        'annex' => NoDataCommentsOnly::class,
        'statements' => Statements::class,
        'tmdetails' => TmDetails::class,
        'tmresponsibilities' => TmResponsibilities::class,
        'tmqualifications' => TmQualifications::class,
        'tmotheremployment' => TmOtherEmployment::class,
        'tmprevioushistory' => TmPreviousHistory::class,
    ];

    protected $factories = [
        // ZF2.5
        'dvsaolcsapiservicesubmissionsectionscasesummary' => AbstractFactory::class,
        'dvsaolcsapiservicesubmissionsectionscaseoutline' => AbstractFactory::class,
        'dvsaolcsapiservicesubmissionsectionsmostseriousinfringement' => AbstractFactory::class,
        'dvsaolcsapiservicesubmissionsectionsoutstandingapplications' => AbstractFactory::class,
        'dvsaolcsapiservicesubmissionsectionspeople' => AbstractFactory::class,
        'dvsaolcsapiservicesubmissionsectionsoperatingcentres' => AbstractFactory::class,
        'dvsaolcsapiservicesubmissionsectionsconditionsandundertakings' => AbstractFactory::class,
        'dvsaolcsapiservicesubmissionsectionsnodatacommentsonly' => AbstractFactory::class,
        'dvsaolcsapiservicesubmissionsectionslinkedlicences' => AbstractFactory::class,
        'dvsaolcsapiservicesubmissionsectionsleadtcarea' => AbstractFactory::class,
        'dvsaolcsapiservicesubmissionsectionsauthrequestedappliedfor' => AbstractFactory::class,
        'dvsaolcsapiservicesubmissionsectionstransportmanagers' => AbstractFactory::class,
        'dvsaolcsapiservicesubmissionsectionsprohibitionhistory' => AbstractFactory::class,
        'dvsaolcsapiservicesubmissionsectionsconvictionfpnoffencehistory' => AbstractFactory::class,
        'dvsaolcsapiservicesubmissionsectionsannualtesthistory' => AbstractFactory::class,
        'dvsaolcsapiservicesubmissionsectionspenalties' => AbstractFactory::class,
        'dvsaolcsapiservicesubmissionsectionsstatements' => AbstractFactory::class,
        'dvsaolcsapiservicesubmissionsectionsenvironmentalcomplaints' => AbstractFactory::class,
        'dvsaolcsapiservicesubmissionsectionscompliancecomplaints' => AbstractFactory::class,
        'dvsaolcsapiservicesubmissionsectionsoppositions' => AbstractFactory::class,
        'dvsaolcsapiservicesubmissionsectionstmdetails' => AbstractFactory::class,
        'dvsaolcsapiservicesubmissionsectionstmresponsibilities' => AbstractFactory::class,
        'dvsaolcsapiservicesubmissionsectionstmqualifications' => AbstractFactory::class,
        'dvsaolcsapiservicesubmissionsectionstmotheremployment' => AbstractFactory::class,
        'dvsaolcsapiservicesubmissionsectionstmprevioushistory' => AbstractFactory::class,
        'dvsaolcsapiservicesubmissionsectionsapplicantscomments' => AbstractFactory::class,
        'dvsaolcsapiservicesubmissionsectionsapplicantsresponses' => AbstractFactory::class,

        // ZF3.0
        CaseSummary::class => AbstractFactory::class,
        CaseOutline::class => AbstractFactory::class,
        MostSeriousInfringement::class => AbstractFactory::class,
        OutstandingApplications::class => AbstractFactory::class,
        People::class => AbstractFactory::class,
        OperatingCentres::class => AbstractFactory::class,
        ConditionsAndUndertakings::class => AbstractFactory::class,
        NoDataCommentsOnly::class => AbstractFactory::class,
        LinkedLicences::class => AbstractFactory::class,
        LeadTcArea::class => AbstractFactory::class,
        AuthRequestedAppliedFor::class => AbstractFactory::class,
        TransportManagers::class => AbstractFactory::class,
        ProhibitionHistory::class => AbstractFactory::class,
        ConvictionFpnOffenceHistory::class => AbstractFactory::class,
        AnnualTestHistory::class => AbstractFactory::class,
        Penalties::class => AbstractFactory::class,
        Statements::class => AbstractFactory::class,
        EnvironmentalComplaints::class => AbstractFactory::class,
        ComplianceComplaints::class => AbstractFactory::class,
        Oppositions::class => AbstractFactory::class,
        TmDetails::class => AbstractFactory::class,
        TmResponsibilities::class => AbstractFactory::class,
        TmQualifications::class => AbstractFactory::class,
        TmOtherEmployment::class => AbstractFactory::class,
        TmPreviousHistory::class => AbstractFactory::class,
        ApplicantsComments::class => AbstractFactory::class,
        ApplicantsResponses::class => AbstractFactory::class,
    ];

    public function validate($instance)
    {
        if (! $instance instanceof $this->instanceOf) {
            throw new InvalidServiceException(sprintf(
                'Invalid plugin "%s" created; not an instance of %s',
                get_class($instance),
                $this->instanceOf
            ));
        }
    }

    public function validatePlugin($instance)
    {
        try {
            $this->validate($instance);
        } catch (InvalidServiceException $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }
}

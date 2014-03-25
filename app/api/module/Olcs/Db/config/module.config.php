<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
return array(
    'router' => array(
        'routes' => array(
            'default' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/[:route]',
                    'constraints' => array(
                        'route' => '[a-zA-Z0-9\/\_\-]+'
                    ),
                    'defaults' => array(
                        'controller' => 'Error',
                        'action' => 'index'
                    )
                )
            ),
            'rest' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/[:controller][/:id]',
                    'constraints' => array(
                        'controller' => '[a-zA-Z\-]+',
                        'id' => '[0-9]+'
                    )
                )
            )
        )
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory'
        ),
        'factories' => array(
            'serviceFactory' => '\Olcs\Db\Service\Factory'
        )
    ),
    'controllers' => array(
        'invokables' => array(
            'address' => 'Olcs\Db\Controller\AddressController',
            'application' => 'Olcs\Db\Controller\ApplicationController',
            'cardpaymenttokenusage' => 'Olcs\Db\Controller\CardPaymentTokenUsageController',
            'casecategorylink' => 'Olcs\Db\Controller\CaseCategoryLinkController',
            'casedetailcomment' => 'Olcs\Db\Controller\CaseDetailCommentController',
            'casedetailtype' => 'Olcs\Db\Controller\CaseDetailTypeController',
            'casenote' => 'Olcs\Db\Controller\CaseNoteController',
            'contactdetails' => 'Olcs\Db\Controller\ContactDetailsController',
            'contactdetailsextrainfo' => 'Olcs\Db\Controller\ContactDetailsExtraInfoController',
            'contactdetailslicence' => 'Olcs\Db\Controller\ContactDetailsLicenceController',
            'contactdetailstype' => 'Olcs\Db\Controller\ContactDetailsTypeController',
            'convictioncaseUuage' => 'Olcs\Db\Controller\ConvictionCaseUsageController',
            'convictioncategory' => 'Olcs\Db\Controller\ConvictionCategoryController',
            'conviction' => 'Olcs\Db\Controller\ConvictionController',
            'fee' => 'Olcs\Db\Controller\FeeController',
            'kvpstore' => 'Olcs\Db\Controller\KvpstoreController',
            'licencecondition' => 'Olcs\Db\Controller\LicenceConditionController',
            'licence' => 'Olcs\Db\Controller\LicenceController',
            'licencevehicleusage' => 'Olcs\Db\Controller\LicenceVehicleUsageController',
            'note' => 'Olcs\Db\Controller\NoteController',
            'operator-search' => 'Olcs\Db\Controller\OperatorSearchController',
            'person-search' => 'Olcs\Db\Controller\PersonSearchController',
            'person-licence-search' => 'Olcs\Db\Controller\PersonLicenceSearchController',
            'operatingcentrecondition' => 'Olcs\Db\Controller\OperatingCentreConditionController',
            'operatingcentre' => 'Olcs\Db\Controller\OperatingCentreController',
            'organisation' => 'Olcs\Db\Controller\OrganisationController',
            'organisationowner' => 'Olcs\Db\Controller\OrganisationOwnerController',
            'organisationtype' => 'Olcs\Db\Controller\OrganisationTypeController',
            'payment' => 'Olcs\Db\Controller\PaymentController',
            'paymentfeelink' => 'Olcs\Db\Controller\PaymentFeeLinkController',
            'permission' => 'Olcs\Db\Controller\PermissionController',
            'person' => 'Olcs\Db\Controller\PersonController',
            'persondisqualification' => 'Olcs\Db\Controller\PersonDisqualificationController',
            'role' => 'Olcs\Db\Controller\RoleController',
            'submission' => 'Olcs\Db\Controller\SubmissionController',
            'submissiondecision' => 'Olcs\Db\Controller\SubmissionDecisionController',
            'submissiondecisionrecommendation' => 'Olcs\Db\Controller\SubmissionDecisionRecommendationController',
            'submissionrecommendation' => 'Olcs\Db\Controller\SubmissionRecommendationController',
            'subsidiarycompany' => 'Olcs\Db\Controller\SubsidiaryCompanyController',
            'tmlicenselink' => 'Olcs\Db\Controller\TmLicenseLinkController',
            'tmqualification' => 'Olcs\Db\Controller\TmQualificationController',
            'tradingname' => 'Olcs\Db\Controller\TradingNameController',
            'trafficarea' => 'Olcs\Db\Controller\TrafficAreaController',
            'trailer' => 'Olcs\Db\Controller\TrailerController',
            'user' => 'Olcs\Db\Controller\UserController',
            'vosacase' => 'Olcs\Db\Controller\VosaCaseController',
            'vehicle' => 'Olcs\Db\Controller\VehicleController',
            'Error' => 'Olcs\Db\Controller\ErrorController'
        )
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions' => false,
        'strategies' => array(
            'ViewJsonStrategy'
        )
    )
);

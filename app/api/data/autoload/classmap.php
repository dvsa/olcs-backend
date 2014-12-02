<?php

$rootPath = realpath(__DIR__ . '/../../');

return array(
    'DoctrineModule\Module' => $rootPath . '/vendor/doctrine/doctrine-module/src/DoctrineModule/Module.php',
    'DoctrineModule\ServiceFactory\AbstractDoctrineServiceFactory' => $rootPath . '/vendor/doctrine/doctrine-module/src'
        . '/DoctrineModule/ServiceFactory/AbstractDoctrineServiceFactory.php',
    'DoctrineORMModule\Module' => $rootPath . '/vendor/doctrine/doctrine-orm-module/src/DoctrineORMModule/Module.php',
    'Doctrine\Common\Annotations\AnnotationRegistry' => $rootPath . '/vendor/doctrine/annotations/lib/Doctrine/Common'
        . '/Annotations/AnnotationRegistry.php',
    'Doctrine\Common\Collections\ArrayCollection' => $rootPath . '/vendor/doctrine/collections/lib/Doctrine/Common'
        . '/Collections/ArrayCollection.php',
    'Doctrine\Common\Collections\Collection' => $rootPath . '/vendor/doctrine/collections/lib/Doctrine/Common'
        . '/Collections/Collection.php',
    'Doctrine\Common\Collections\Criteria' => $rootPath . '/vendor/doctrine/collections/lib/Doctrine/Common/Collections'
        . '/Criteria.php',
    'Doctrine\Common\Collections\Selectable' => $rootPath . '/vendor/doctrine/collections/lib/Doctrine/Common'
        . '/Collections/Selectable.php',
    'Doctrine\Common\EventManager' => $rootPath . '/vendor/doctrine/common/lib/Doctrine/Common/EventManager.php',
    'Doctrine\Common\EventSubscriber' => $rootPath . '/vendor/doctrine/common/lib/Doctrine/Common/EventSubscriber.php',
    'Doctrine\Common\Persistence\Mapping\ClassMetadata' => $rootPath . '/vendor/doctrine/common/lib/Doctrine/Common'
        . '/Persistence/Mapping/ClassMetadata.php',
    'Doctrine\Common\Persistence\ObjectManager' => $rootPath . '/vendor/doctrine/common/lib/Doctrine/Common/Persistence'
        . '/ObjectManager.php',
    'Doctrine\Common\Persistence\ObjectRepository' => $rootPath . '/vendor/doctrine/common/lib/Doctrine/Common'
        . '/Persistence/ObjectRepository.php',
    'Doctrine\DBAL\Configuration' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/Configuration.php',
    'Doctrine\DBAL\LockMode' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/LockMode.php',
    'Doctrine\DBAL\Platforms\AbstractPlatform' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/Platforms'
        . '/AbstractPlatform.php',
    'Doctrine\DBAL\Platforms\MySqlPlatform' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/Platforms'
        . '/MySqlPlatform.php',
    'Doctrine\DBAL\Schema\AbstractAsset' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/Schema'
        . '/AbstractAsset.php',
    'Doctrine\DBAL\Schema\Constraint' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/Schema/Constraint.php',
    'Doctrine\DBAL\Schema\ForeignKeyConstraint' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/Schema'
        . '/ForeignKeyConstraint.php',
    'Doctrine\DBAL\Schema\Index' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/Schema/Index.php',
    'Doctrine\DBAL\Schema\Sequence' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/Schema/Sequence.php',
    'Doctrine\DBAL\Schema\Table' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/Schema/Table.php',
    'Doctrine\DBAL\Schema\TableDiff' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/Schema/TableDiff.php',
    'Doctrine\DBAL\Types\Type' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/Types/Type.php',
    'Doctrine\Instantiator\Instantiator' => $rootPath . '/vendor/doctrine/instantiator/src/Doctrine/Instantiator'
        . '/Instantiator.php',
    'Doctrine\Instantiator\InstantiatorInterface' => $rootPath . '/vendor/doctrine/instantiator/src/Doctrine'
        . '/Instantiator/InstantiatorInterface.php',
    'Doctrine\ORM\AbstractQuery' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/AbstractQuery.php',
    'Doctrine\ORM\Configuration' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Configuration.php',
    'Doctrine\ORM\EntityManager' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/EntityManager.php',
    'Doctrine\ORM\EntityManagerInterface' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM'
        . '/EntityManagerInterface.php',
    'Doctrine\ORM\EntityRepository' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/EntityRepository.php',
    'Doctrine\ORM\Mapping\ClassMetadata' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Mapping'
        . '/ClassMetadata.php',
    'Doctrine\ORM\Mapping\ClassMetadataInfo' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Mapping'
        . '/ClassMetadataInfo.php',
    'Doctrine\ORM\Mapping\DefaultNamingStrategy' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Mapping'
        . '/DefaultNamingStrategy.php',
    'Doctrine\ORM\Mapping\NamingStrategy' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Mapping'
        . '/NamingStrategy.php',
    'Doctrine\ORM\Query' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Query.php',
    'Doctrine\ORM\QueryBuilder' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/QueryBuilder.php',
    'Doctrine\ORM\Query\Expr' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Query/Expr.php',
    'Doctrine\ORM\Query\Expr\Andx' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Query/Expr/Andx.php',
    'Doctrine\ORM\Query\Expr\Base' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Query/Expr/Base.php',
    'Doctrine\ORM\Query\Expr\Comparison' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Query/Expr'
        . '/Comparison.php',
    'Doctrine\ORM\Query\Expr\Composite' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Query/Expr/Composite.php',
    'Doctrine\ORM\Query\Expr\From' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Query/Expr/From.php',
    'Doctrine\ORM\Query\Expr\Func' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Query/Expr/Func.php',
    'Doctrine\ORM\Query\Expr\Join' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Query/Expr/Join.php',
    'Doctrine\ORM\Query\Expr\OrderBy' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Query/Expr/OrderBy.php',
    'Doctrine\ORM\Query\Expr\Orx' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Query/Expr/Orx.php',
    'Doctrine\ORM\Query\Expr\Select' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Query/Expr/Select.php',
    'Doctrine\ORM\Query\Parameter' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Query/Parameter.php',
    'Doctrine\ORM\Query\ParameterTypeInferer' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Query'
        . '/ParameterTypeInferer.php',
    'Doctrine\ORM\Query\ResultSetMapping' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Query'
        . '/ResultSetMapping.php',
    'Elastica\AbstractUpdateAction' => $rootPath . '/vendor/ruflin/elastica/lib/Elastica/AbstractUpdateAction.php',
    'Elastica\Client' => $rootPath . '/vendor/ruflin/elastica/lib/Elastica/Client.php',
    'Elastica\Connection' => $rootPath . '/vendor/ruflin/elastica/lib/Elastica/Connection.php',
    'Elastica\Connection\ConnectionPool' => $rootPath . '/vendor/ruflin/elastica/lib/Elastica/Connection'
        . '/ConnectionPool.php',
    'Elastica\Connection\Strategy\CallbackStrategy' => $rootPath . '/vendor/ruflin/elastica/lib/Elastica/Connection'
        . '/Strategy/CallbackStrategy.php',
    'Elastica\Connection\Strategy\Simple' => $rootPath . '/vendor/ruflin/elastica/lib/Elastica/Connection/Strategy'
        . '/Simple.php',
    'Elastica\Connection\Strategy\StrategyFactory' => $rootPath . '/vendor/ruflin/elastica/lib/Elastica/Connection'
        . '/Strategy/StrategyFactory.php',
    'Elastica\Connection\Strategy\StrategyInterface' => $rootPath . '/vendor/ruflin/elastica/lib/Elastica/Connection'
        . '/Strategy/StrategyInterface.php',
    'Elastica\Document' => $rootPath . '/vendor/ruflin/elastica/lib/Elastica/Document.php',
    'Elastica\Param' => $rootPath . '/vendor/ruflin/elastica/lib/Elastica/Param.php',
    'Elastica\Request' => $rootPath . '/vendor/ruflin/elastica/lib/Elastica/Request.php',
    'Elastica\Response' => $rootPath . '/vendor/ruflin/elastica/lib/Elastica/Response.php',
    'Gedmo\Mapping\MappedEventSubscriber' => $rootPath . '/vendor/gedmo/doctrine-extensions/lib/Gedmo/Mapping'
        . '/MappedEventSubscriber.php',
    'Gedmo\Translatable\TranslatableListener' => $rootPath . '/vendor/gedmo/doctrine-extensions/lib/Gedmo/Translatable'
        . '/TranslatableListener.php',
    'GenericMockService' => false,
    'MissingClassName' => false,
    'MockService' => false,
    'Mockery' => $rootPath . '/vendor/mockery/mockery/library/Mockery.php',
    'Mockery\CompositeExpectation' => $rootPath . '/vendor/mockery/mockery/library/Mockery/CompositeExpectation.php',
    'Mockery\Configuration' => $rootPath . '/vendor/mockery/mockery/library/Mockery/Configuration.php',
    'Mockery\Container' => $rootPath . '/vendor/mockery/mockery/library/Mockery/Container.php',
    'Mockery\CountValidator\CountValidatorAbstract' => $rootPath . '/vendor/mockery/mockery/library/Mockery'
        . '/CountValidator/CountValidatorAbstract.php',
    'Mockery\CountValidator\Exact' => $rootPath . '/vendor/mockery/mockery/library/Mockery/CountValidator/Exact.php',
    'Mockery\Expectation' => $rootPath . '/vendor/mockery/mockery/library/Mockery/Expectation.php',
    'Mockery\ExpectationDirector' => $rootPath . '/vendor/mockery/mockery/library/Mockery/ExpectationDirector.php',
    'Mockery\ExpectationInterface' => $rootPath . '/vendor/mockery/mockery/library/Mockery/ExpectationInterface.php',
    'Mockery\Generator\CachingGenerator' => $rootPath . '/vendor/mockery/mockery/library/Mockery/Generator'
        . '/CachingGenerator.php',
    'Mockery\Generator\DefinedTargetClass' => $rootPath . '/vendor/mockery/mockery/library/Mockery/Generator'
        . '/DefinedTargetClass.php',
    'Mockery\Generator\Generator' => $rootPath . '/vendor/mockery/mockery/library/Mockery/Generator/Generator.php',
    'Mockery\Generator\Method' => $rootPath . '/vendor/mockery/mockery/library/Mockery/Generator/Method.php',
    'Mockery\Generator\MockConfiguration' => $rootPath . '/vendor/mockery/mockery/library/Mockery/Generator'
        . '/MockConfiguration.php',
    'Mockery\Generator\MockConfigurationBuilder' => $rootPath . '/vendor/mockery/mockery/library/Mockery/Generator'
        . '/MockConfigurationBuilder.php',
    'Mockery\Generator\MockDefinition' => $rootPath . '/vendor/mockery/mockery/library/Mockery/Generator'
        . '/MockDefinition.php',
    'Mockery\Generator\Parameter' => $rootPath . '/vendor/mockery/mockery/library/Mockery/Generator/Parameter.php',
    'Mockery\Generator\StringManipulationGenerator' => $rootPath . '/vendor/mockery/mockery/library/Mockery/Generator'
        . '/StringManipulationGenerator.php',
    'Mockery\Generator\StringManipulation\Pass\CallTypeHintPass' => $rootPath . '/vendor/mockery/mockery/library'
        . '/Mockery/Generator/StringManipulation/Pass/CallTypeHintPass.php',
    'Mockery\Generator\StringManipulation\Pass\ClassNamePass' => $rootPath . '/vendor/mockery/mockery/library/Mockery'
        . '/Generator/StringManipulation/Pass/ClassNamePass.php',
    'Mockery\Generator\StringManipulation\Pass\ClassPass' => $rootPath . '/vendor/mockery/mockery/library/Mockery'
        . '/Generator/StringManipulation/Pass/ClassPass.php',
    'Mockery\Generator\StringManipulation\Pass\InstanceMockPass' => $rootPath . '/vendor/mockery/mockery/library'
        . '/Mockery/Generator/StringManipulation/Pass/InstanceMockPass.php',
    'Mockery\Generator\StringManipulation\Pass\InterfacePass' => $rootPath . '/vendor/mockery/mockery/library/Mockery'
        . '/Generator/StringManipulation/Pass/InterfacePass.php',
    'Mockery\Generator\StringManipulation\Pass\MethodDefinitionPass' => $rootPath . '/vendor/mockery/mockery/library'
        . '/Mockery/Generator/StringManipulation/Pass/MethodDefinitionPass.php',
    'Mockery\Generator\StringManipulation\Pass\Pass' => $rootPath . '/vendor/mockery/mockery/library/Mockery/Generator'
        . '/StringManipulation/Pass/Pass.php',
    'Mockery\Generator\StringManipulation\Pass\RemoveBuiltinMethodsThatAreFinalPass' => $rootPath . '/vendor/mockery'
        . '/mockery/library/Mockery/Generator/StringManipulation/Pass/RemoveBuiltinMethodsThatAreFinalPass.php',
    'Mockery\Generator\StringManipulation\Pass\RemoveUnserializeForInternalSerializableClassesPass' => $rootPath . ''
        . '/vendor/mockery/mockery/library/Mockery/Generator/StringManipulation/Pass'
        . '/RemoveUnserializeForInternalSerializableClassesPass.php',
    'Mockery\Loader\EvalLoader' => $rootPath . '/vendor/mockery/mockery/library/Mockery/Loader/EvalLoader.php',
    'Mockery\Loader\Loader' => $rootPath . '/vendor/mockery/mockery/library/Mockery/Loader/Loader.php',
    'Mockery\MethodCall' => $rootPath . '/vendor/mockery/mockery/library/Mockery/MethodCall.php',
    'Mockery\MockInterface' => $rootPath . '/vendor/mockery/mockery/library/Mockery/MockInterface.php',
    'Mockery\ReceivedMethodCalls' => $rootPath . '/vendor/mockery/mockery/library/Mockery/ReceivedMethodCalls.php',
    'OlcsTest\Db\Entity\Abstracts\EntityTester' => $rootPath . '/test/module/Olcs/Db/src//Entity/Abstracts'
        . '/EntityTester.php',
    'OlcsTest\Db\Service\Stub\EntityStub' => false,
    'OlcsTest\Db\Service\Stubs\EntityStub' => $rootPath . '/test/module/Olcs/Db/src//Service/Stubs/EntityStub.php',
    'Olcs\Db\Module' => false,
    'Olcs\Db\Service\MISSING' => false,
    'Olcs\Logging\Module' => $rootPath . '/vendor/olcs/olcs-logging/src/Module.php',
    'Psr\Log\LoggerInterface' => false,
    'Simple' => false,
    'Zend\Cache\Service\StorageCacheAbstractServiceFactory' => $rootPath . '/vendor/zendframework/zendframework/library'
        . '/Zend/Cache/Service/StorageCacheAbstractServiceFactory.php',
    'Zend\Config\Factory' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Config/Factory.php',
    'Zend\EventManager\Event' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/EventManager/Event.php',
    'Zend\EventManager\EventInterface' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/EventManager'
        . '/EventInterface.php',
    'Zend\EventManager\EventManager' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/EventManager'
        . '/EventManager.php',
    'Zend\EventManager\EventManagerAwareInterface' => $rootPath . '/vendor/zendframework/zendframework/library/Zend'
        . '/EventManager/EventManagerAwareInterface.php',
    'Zend\EventManager\EventManagerInterface' => $rootPath . '/vendor/zendframework/zendframework/library/Zend'
        . '/EventManager/EventManagerInterface.php',
    'Zend\EventManager\EventsCapableInterface' => $rootPath . '/vendor/zendframework/zendframework/library/Zend'
        . '/EventManager/EventsCapableInterface.php',
    'Zend\EventManager\ListenerAggregateInterface' => $rootPath . '/vendor/zendframework/zendframework/library/Zend'
        . '/EventManager/ListenerAggregateInterface.php',
    'Zend\EventManager\ResponseCollection' => $rootPath . '/vendor/zendframework/zendframework/library/Zend'
        . '/EventManager/ResponseCollection.php',
    'Zend\EventManager\SharedEventAggregateAwareInterface' => $rootPath . '/vendor/zendframework/zendframework/library'
        . '/Zend/EventManager/SharedEventAggregateAwareInterface.php',
    'Zend\EventManager\SharedEventManager' => $rootPath . '/vendor/zendframework/zendframework/library/Zend'
        . '/EventManager/SharedEventManager.php',
    'Zend\EventManager\SharedEventManagerAwareInterface' => $rootPath . '/vendor/zendframework/zendframework/library'
        . '/Zend/EventManager/SharedEventManagerAwareInterface.php',
    'Zend\EventManager\SharedEventManagerInterface' => $rootPath . '/vendor/zendframework/zendframework/library/Zend'
        . '/EventManager/SharedEventManagerInterface.php',
    'Zend\EventManager\StaticEventManager' => $rootPath . '/vendor/zendframework/zendframework/library/Zend'
        . '/EventManager/StaticEventManager.php',
    'Zend\Filter\AbstractFilter' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Filter'
        . '/AbstractFilter.php',
    'Zend\Filter\FilterInterface' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Filter'
        . '/FilterInterface.php',
    'Zend\Filter\FilterPluginManager' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Filter'
        . '/FilterPluginManager.php',
    'Zend\Filter\Word\AbstractSeparator' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Filter/Word'
        . '/AbstractSeparator.php',
    'Zend\Filter\Word\DashToCamelCase' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Filter/Word'
        . '/DashToCamelCase.php',
    'Zend\Filter\Word\SeparatorToCamelCase' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Filter'
        . '/Word/SeparatorToCamelCase.php',
    'Zend\Form\FormAbstractServiceFactory' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Form'
        . '/FormAbstractServiceFactory.php',
    'Zend\Form\FormElementManager' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Form'
        . '/FormElementManager.php',
    'Zend\Form\View\HelperConfig' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Form/View'
        . '/HelperConfig.php',
    'Zend\Http\AbstractMessage' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Http'
        . '/AbstractMessage.php',
    'Zend\Http\Headers' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Http/Headers.php',
    'Zend\Http\PhpEnvironment\Response' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Http'
        . '/PhpEnvironment/Response.php',
    'Zend\Http\Response' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Http/Response.php',
    'Zend\I18n\View\HelperConfig' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/I18n/View'
        . '/HelperConfig.php',
    'Zend\InputFilter\InputFilterPluginManager' => $rootPath . '/vendor/zendframework/zendframework/library/Zend'
        . '/InputFilter/InputFilterPluginManager.php',
    'Zend\Json\Json' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Json/Json.php',
    'Zend\Loader\AutoloaderFactory' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Loader'
        . '/AutoloaderFactory.php',
    'Zend\Loader\ModuleAutoloader' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Loader'
        . '/ModuleAutoloader.php',
    'Zend\Log\Logger' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Log/Logger.php',
    'Zend\Log\LoggerAbstractServiceFactory' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Log'
        . '/LoggerAbstractServiceFactory.php',
    'Zend\Log\LoggerAwareTrait' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Log'
        . '/LoggerAwareTrait.php',
    'Zend\Log\LoggerInterface' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Log'
        . '/LoggerInterface.php',
    'Zend\Log\ProcessorPluginManager' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Log'
        . '/ProcessorPluginManager.php',
    'Zend\Log\WriterPluginManager' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Log'
        . '/WriterPluginManager.php',
    'Zend\ModuleManager\Feature\BootstrapListenerInterface' => $rootPath . '/vendor/zendframework/zendframework/library'
        . '/Zend/ModuleManager/Feature/BootstrapListenerInterface.php',
    'Zend\ModuleManager\Feature\ConfigProviderInterface' => $rootPath . '/vendor/zendframework/zendframework/library'
        . '/Zend/ModuleManager/Feature/ConfigProviderInterface.php',
    'Zend\ModuleManager\Feature\ControllerProviderInterface' => $rootPath . '/vendor/zendframework/zendframework'
        . '/library/Zend/ModuleManager/Feature/ControllerProviderInterface.php',
    'Zend\ModuleManager\Feature\DependencyIndicatorInterface' => $rootPath . '/vendor/zendframework/zendframework'
        . '/library/Zend/ModuleManager/Feature/DependencyIndicatorInterface.php',
    'Zend\ModuleManager\Feature\InitProviderInterface' => $rootPath . '/vendor/zendframework/zendframework/library/Zend'
        . '/ModuleManager/Feature/InitProviderInterface.php',
    'Zend\ModuleManager\Listener\AbstractListener' => $rootPath . '/vendor/zendframework/zendframework/library/Zend'
        . '/ModuleManager/Listener/AbstractListener.php',
    'Zend\ModuleManager\Listener\AutoloaderListener' => $rootPath . '/vendor/zendframework/zendframework/library/Zend'
        . '/ModuleManager/Listener/AutoloaderListener.php',
    'Zend\ModuleManager\Listener\ConfigListener' => $rootPath . '/vendor/zendframework/zendframework/library/Zend'
        . '/ModuleManager/Listener/ConfigListener.php',
    'Zend\ModuleManager\Listener\ConfigMergerInterface' => $rootPath . '/vendor/zendframework/zendframework/library'
        . '/Zend/ModuleManager/Listener/ConfigMergerInterface.php',
    'Zend\ModuleManager\Listener\DefaultListenerAggregate' => $rootPath . '/vendor/zendframework/zendframework/library'
        . '/Zend/ModuleManager/Listener/DefaultListenerAggregate.php',
    'Zend\ModuleManager\Listener\InitTrigger' => $rootPath . '/vendor/zendframework/zendframework/library/Zend'
        . '/ModuleManager/Listener/InitTrigger.php',
    'Zend\ModuleManager\Listener\ListenerOptions' => $rootPath . '/vendor/zendframework/zendframework/library/Zend'
        . '/ModuleManager/Listener/ListenerOptions.php',
    'Zend\ModuleManager\Listener\LocatorRegistrationListener' => $rootPath . '/vendor/zendframework/zendframework'
        . '/library/Zend/ModuleManager/Listener/LocatorRegistrationListener.php',
    'Zend\ModuleManager\Listener\ModuleDependencyCheckerListener' => $rootPath . '/vendor/zendframework/zendframework'
        . '/library/Zend/ModuleManager/Listener/ModuleDependencyCheckerListener.php',
    'Zend\ModuleManager\Listener\ModuleLoaderListener' => $rootPath . '/vendor/zendframework/zendframework/library/Zend'
        . '/ModuleManager/Listener/ModuleLoaderListener.php',
    'Zend\ModuleManager\Listener\ModuleResolverListener' => $rootPath . '/vendor/zendframework/zendframework/library'
        . '/Zend/ModuleManager/Listener/ModuleResolverListener.php',
    'Zend\ModuleManager\Listener\OnBootstrapListener' => $rootPath . '/vendor/zendframework/zendframework/library/Zend'
        . '/ModuleManager/Listener/OnBootstrapListener.php',
    'Zend\ModuleManager\Listener\ServiceListener' => $rootPath . '/vendor/zendframework/zendframework/library/Zend'
        . '/ModuleManager/Listener/ServiceListener.php',
    'Zend\ModuleManager\Listener\ServiceListenerInterface' => $rootPath . '/vendor/zendframework/zendframework/library'
        . '/Zend/ModuleManager/Listener/ServiceListenerInterface.php',
    'Zend\ModuleManager\ModuleEvent' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/ModuleManager'
        . '/ModuleEvent.php',
    'Zend\ModuleManager\ModuleManager' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/ModuleManager'
        . '/ModuleManager.php',
    'Zend\ModuleManager\ModuleManagerInterface' => $rootPath . '/vendor/zendframework/zendframework/library/Zend'
        . '/ModuleManager/ModuleManagerInterface.php',
    'Zend\Mvc\ApplicationInterface' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc'
        . '/ApplicationInterface.php',
    'Zend\Mvc\Controller\AbstractController' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc'
        . '/Controller/AbstractController.php',
    'Zend\Mvc\Controller\AbstractRestfulController' => $rootPath . '/vendor/zendframework/zendframework/library/Zend'
        . '/Mvc/Controller/AbstractRestfulController.php',
    'Zend\Mvc\Controller\ControllerManager' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc'
        . '/Controller/ControllerManager.php',
    'Zend\Mvc\Controller\PluginManager' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc/Controller'
        . '/PluginManager.php',
    'Zend\Mvc\Controller\Plugin\AbstractPlugin' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc'
        . '/Controller/Plugin/AbstractPlugin.php',
    'Zend\Mvc\Controller\Plugin\Params' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc/Controller'
        . '/Plugin/Params.php',
    'Zend\Mvc\Controller\Plugin\PluginInterface' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc'
        . '/Controller/Plugin/PluginInterface.php',
    'Zend\Mvc\Exception\DomainException' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc/Exception'
        . '/DomainException.php',
    'Zend\Mvc\Exception\ExceptionInterface' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc'
        . '/Exception/ExceptionInterface.php',
    'Zend\Mvc\InjectApplicationEventInterface' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc'
        . '/InjectApplicationEventInterface.php',
    'Zend\Mvc\MvcEvent' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc/MvcEvent.php',
    'Zend\Mvc\Router\RouteInterface' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc/Router'
        . '/RouteInterface.php',
    'Zend\Mvc\Router\RouteMatch' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc/Router'
        . '/RouteMatch.php',
    'Zend\Mvc\Router\RoutePluginManager' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc/Router'
        . '/RoutePluginManager.php',
    'Zend\Mvc\Router\RouteStackInterface' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc/Router'
        . '/RouteStackInterface.php',
    'Zend\Mvc\Service\AbstractPluginManagerFactory' => $rootPath . '/vendor/zendframework/zendframework/library/Zend'
        . '/Mvc/Service/AbstractPluginManagerFactory.php',
    'Zend\Mvc\Service\ConfigFactory' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc/Service'
        . '/ConfigFactory.php',
    'Zend\Mvc\Service\ControllerLoaderFactory' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc'
        . '/Service/ControllerLoaderFactory.php',
    'Zend\Mvc\Service\ControllerPluginManagerFactory' => $rootPath . '/vendor/zendframework/zendframework/library/Zend'
        . '/Mvc/Service/ControllerPluginManagerFactory.php',
    'Zend\Mvc\Service\EventManagerFactory' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc/Service'
        . '/EventManagerFactory.php',
    'Zend\Mvc\Service\FilterManagerFactory' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc'
        . '/Service/FilterManagerFactory.php',
    'Zend\Mvc\Service\FormElementManagerFactory' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc'
        . '/Service/FormElementManagerFactory.php',
    'Zend\Mvc\Service\HydratorManagerFactory' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc'
        . '/Service/HydratorManagerFactory.php',
    'Zend\Mvc\Service\InputFilterManagerFactory' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc'
        . '/Service/InputFilterManagerFactory.php',
    'Zend\Mvc\Service\LogProcessorManagerFactory' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc'
        . '/Service/LogProcessorManagerFactory.php',
    'Zend\Mvc\Service\LogWriterManagerFactory' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc'
        . '/Service/LogWriterManagerFactory.php',
    'Zend\Mvc\Service\ModuleManagerFactory' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc'
        . '/Service/ModuleManagerFactory.php',
    'Zend\Mvc\Service\RoutePluginManagerFactory' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc'
        . '/Service/RoutePluginManagerFactory.php',
    'Zend\Mvc\Service\SerializerAdapterPluginManagerFactory' => $rootPath . '/vendor/zendframework/zendframework'
        . '/library/Zend/Mvc/Service/SerializerAdapterPluginManagerFactory.php',
    'Zend\Mvc\Service\ServiceListenerFactory' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc'
        . '/Service/ServiceListenerFactory.php',
    'Zend\Mvc\Service\ServiceManagerConfig' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc'
        . '/Service/ServiceManagerConfig.php',
    'Zend\Mvc\Service\ValidatorManagerFactory' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc'
        . '/Service/ValidatorManagerFactory.php',
    'Zend\Mvc\Service\ViewHelperManagerFactory' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc'
        . '/Service/ViewHelperManagerFactory.php',
    'Zend\Navigation\View\HelperConfig' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Navigation'
        . '/View/HelperConfig.php',
    'Zend\Serializer\AdapterPluginManager' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Serializer'
        . '/AdapterPluginManager.php',
    'Zend\ServiceManager\AbstractFactoryInterface' => $rootPath . '/vendor/zendframework/zendframework/library/Zend'
        . '/ServiceManager/AbstractFactoryInterface.php',
    'Zend\ServiceManager\AbstractPluginManager' => $rootPath . '/vendor/zendframework/zendframework/library/Zend'
        . '/ServiceManager/AbstractPluginManager.php',
    'Zend\ServiceManager\Config' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/ServiceManager'
        . '/Config.php',
    'Zend\ServiceManager\ConfigInterface' => $rootPath . '/vendor/zendframework/zendframework/library/Zend'
        . '/ServiceManager/ConfigInterface.php',
    'Zend\ServiceManager\Exception\ExceptionInterface' => $rootPath . '/vendor/zendframework/zendframework/library/Zend'
        . '/ServiceManager/Exception/ExceptionInterface.php',
    'Zend\ServiceManager\Exception\RuntimeException' => $rootPath . '/vendor/zendframework/zendframework/library/Zend'
        . '/ServiceManager/Exception/RuntimeException.php',
    'Zend\ServiceManager\FactoryInterface' => $rootPath . '/vendor/zendframework/zendframework/library/Zend'
        . '/ServiceManager/FactoryInterface.php',
    'Zend\ServiceManager\ServiceLocatorAwareInterface' => $rootPath . '/vendor/zendframework/zendframework/library/Zend'
        . '/ServiceManager/ServiceLocatorAwareInterface.php',
    'Zend\ServiceManager\ServiceLocatorAwareTrait' => $rootPath . '/vendor/zendframework/zendframework/library/Zend'
        . '/ServiceManager/ServiceLocatorAwareTrait.php',
    'Zend\ServiceManager\ServiceLocatorInterface' => $rootPath . '/vendor/zendframework/zendframework/library/Zend'
        . '/ServiceManager/ServiceLocatorInterface.php',
    'Zend\ServiceManager\ServiceManager' => $rootPath . '/vendor/zendframework/zendframework/library/Zend'
        . '/ServiceManager/ServiceManager.php',
    'Zend\Stdlib\AbstractOptions' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Stdlib'
        . '/AbstractOptions.php',
    'Zend\Stdlib\ArrayUtils' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Stdlib/ArrayUtils.php',
    'Zend\Stdlib\CallbackHandler' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Stdlib'
        . '/CallbackHandler.php',
    'Zend\Stdlib\DispatchableInterface' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Stdlib'
        . '/DispatchableInterface.php',
    'Zend\Stdlib\ErrorHandler' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Stdlib'
        . '/ErrorHandler.php',
    'Zend\Stdlib\Glob' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Stdlib/Glob.php',
    'Zend\Stdlib\Hydrator\HydratorPluginManager' => $rootPath . '/vendor/zendframework/zendframework/library/Zend'
        . '/Stdlib/Hydrator/HydratorPluginManager.php',
    'Zend\Stdlib\Message' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Stdlib/Message.php',
    'Zend\Stdlib\MessageInterface' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Stdlib'
        . '/MessageInterface.php',
    'Zend\Stdlib\ParameterObjectInterface' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Stdlib'
        . '/ParameterObjectInterface.php',
    'Zend\Stdlib\PriorityQueue' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Stdlib'
        . '/PriorityQueue.php',
    'Zend\Stdlib\RequestInterface' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Stdlib'
        . '/RequestInterface.php',
    'Zend\Stdlib\ResponseInterface' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Stdlib'
        . '/ResponseInterface.php',
    'Zend\Stdlib\SplPriorityQueue' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Stdlib'
        . '/SplPriorityQueue.php',
    'Zend\Validator\ValidatorPluginManager' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Validator'
        . '/ValidatorPluginManager.php',
    'Zend\View\HelperPluginManager' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/View'
        . '/HelperPluginManager.php',
    'Zend\View\Model\ModelInterface' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/View/Model'
        . '/ModelInterface.php',
);

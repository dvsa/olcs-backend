<?php

$rootPath = realpath(__DIR__ . '/../../');

return array(
    'DoctrineModule\Module' => $rootPath . '/vendor/doctrine/doctrine-module/src/DoctrineModule/Module.php',
    'DoctrineModule\Mvc\Router\Console\SymfonyCli' => $rootPath . '/vendor/doctrine/doctrine-module/src/DoctrineModule'
        . '/Mvc/Router/Console/SymfonyCli.php',
    'DoctrineModule\Options\Cache' => $rootPath . '/vendor/doctrine/doctrine-module/src/DoctrineModule/Options'
        . '/Cache.php',
    'DoctrineModule\Options\Driver' => $rootPath . '/vendor/doctrine/doctrine-module/src/DoctrineModule/Options'
        . '/Driver.php',
    'DoctrineModule\Options\EventManager' => $rootPath . '/vendor/doctrine/doctrine-module/src/DoctrineModule/Options'
        . '/EventManager.php',
    'DoctrineModule\ServiceFactory\AbstractDoctrineServiceFactory' => $rootPath . '/vendor/doctrine/doctrine-module/src'
        . '/DoctrineModule/ServiceFactory/AbstractDoctrineServiceFactory.php',
    'DoctrineModule\Service\AbstractFactory' => $rootPath . '/vendor/doctrine/doctrine-module/src/DoctrineModule'
        . '/Service/AbstractFactory.php',
    'DoctrineModule\Service\CacheFactory' => $rootPath . '/vendor/doctrine/doctrine-module/src/DoctrineModule/Service'
        . '/CacheFactory.php',
    'DoctrineModule\Service\CliFactory' => $rootPath . '/vendor/doctrine/doctrine-module/src/DoctrineModule/Service'
        . '/CliFactory.php',
    'DoctrineModule\Service\DriverFactory' => $rootPath . '/vendor/doctrine/doctrine-module/src/DoctrineModule/Service'
        . '/DriverFactory.php',
    'DoctrineModule\Service\EventManagerFactory' => $rootPath . '/vendor/doctrine/doctrine-module/src/DoctrineModule'
        . '/Service/EventManagerFactory.php',
    'DoctrineModule\Service\SymfonyCliRouteFactory' => $rootPath . '/vendor/doctrine/doctrine-module/src/DoctrineModule'
        . '/Service/SymfonyCliRouteFactory.php',
    'DoctrineModule\Stdlib\Hydrator\DoctrineObject' => $rootPath . '/vendor/doctrine/doctrine-module/src/DoctrineModule'
        . '/Stdlib/Hydrator/DoctrineObject.php',
    'DoctrineModule\Stdlib\Hydrator\Strategy\AbstractCollectionStrategy' => $rootPath . '/vendor/doctrine'
        . '/doctrine-module/src/DoctrineModule/Stdlib/Hydrator/Strategy/AbstractCollectionStrategy.php',
    'DoctrineModule\Stdlib\Hydrator\Strategy\AllowRemoveByValue' => $rootPath . '/vendor/doctrine/doctrine-module/src'
        . '/DoctrineModule/Stdlib/Hydrator/Strategy/AllowRemoveByValue.php',
    'DoctrineModule\Version' => $rootPath . '/vendor/doctrine/doctrine-module/src/DoctrineModule/Version.php',
    'DoctrineORMModule\Module' => $rootPath . '/vendor/doctrine/doctrine-orm-module/src/DoctrineORMModule/Module.php',
    'DoctrineORMModule\Options\Configuration' => $rootPath . '/vendor/doctrine/doctrine-orm-module/src'
        . '/DoctrineORMModule/Options/Configuration.php',
    'DoctrineORMModule\Options\DBALConfiguration' => $rootPath . '/vendor/doctrine/doctrine-orm-module/src'
        . '/DoctrineORMModule/Options/DBALConfiguration.php',
    'DoctrineORMModule\Options\DBALConnection' => $rootPath . '/vendor/doctrine/doctrine-orm-module/src'
        . '/DoctrineORMModule/Options/DBALConnection.php',
    'DoctrineORMModule\Options\EntityManager' => $rootPath . '/vendor/doctrine/doctrine-orm-module/src'
        . '/DoctrineORMModule/Options/EntityManager.php',
    'DoctrineORMModule\Options\EntityResolver' => $rootPath . '/vendor/doctrine/doctrine-orm-module/src'
        . '/DoctrineORMModule/Options/EntityResolver.php',
    'DoctrineORMModule\Service\ConfigurationFactory' => $rootPath . '/vendor/doctrine/doctrine-orm-module/src'
        . '/DoctrineORMModule/Service/ConfigurationFactory.php',
    'DoctrineORMModule\Service\DBALConfigurationFactory' => $rootPath . '/vendor/doctrine/doctrine-orm-module/src'
        . '/DoctrineORMModule/Service/DBALConfigurationFactory.php',
    'DoctrineORMModule\Service\DBALConnectionFactory' => $rootPath . '/vendor/doctrine/doctrine-orm-module/src'
        . '/DoctrineORMModule/Service/DBALConnectionFactory.php',
    'DoctrineORMModule\Service\DoctrineObjectHydratorFactory' => $rootPath . '/vendor/doctrine/doctrine-orm-module/src'
        . '/DoctrineORMModule/Service/DoctrineObjectHydratorFactory.php',
    'DoctrineORMModule\Service\EntityManagerFactory' => $rootPath . '/vendor/doctrine/doctrine-orm-module/src'
        . '/DoctrineORMModule/Service/EntityManagerFactory.php',
    'DoctrineORMModule\Service\EntityResolverFactory' => $rootPath . '/vendor/doctrine/doctrine-orm-module/src'
        . '/DoctrineORMModule/Service/EntityResolverFactory.php',
    'Doctrine\Common\Annotations\Annotation' => $rootPath . '/vendor/doctrine/annotations/lib/Doctrine/Common'
        . '/Annotations/Annotation.php',
    'Doctrine\Common\Annotations\AnnotationReader' => $rootPath . '/vendor/doctrine/annotations/lib/Doctrine/Common'
        . '/Annotations/AnnotationReader.php',
    'Doctrine\Common\Annotations\AnnotationRegistry' => $rootPath . '/vendor/doctrine/annotations/lib/Doctrine/Common'
        . '/Annotations/AnnotationRegistry.php',
    'Doctrine\Common\Annotations\Annotation\Target' => $rootPath . '/vendor/doctrine/annotations/lib/Doctrine/Common'
        . '/Annotations/Annotation/Target.php',
    'Doctrine\Common\Annotations\CachedReader' => $rootPath . '/vendor/doctrine/annotations/lib/Doctrine/Common'
        . '/Annotations/CachedReader.php',
    'Doctrine\Common\Annotations\DocLexer' => $rootPath . '/vendor/doctrine/annotations/lib/Doctrine/Common/Annotations'
        . '/DocLexer.php',
    'Doctrine\Common\Annotations\DocParser' => $rootPath . '/vendor/doctrine/annotations/lib/Doctrine/Common'
        . '/Annotations/DocParser.php',
    'Doctrine\Common\Annotations\IndexedReader' => $rootPath . '/vendor/doctrine/annotations/lib/Doctrine/Common'
        . '/Annotations/IndexedReader.php',
    'Doctrine\Common\Annotations\PhpParser' => $rootPath . '/vendor/doctrine/annotations/lib/Doctrine/Common'
        . '/Annotations/PhpParser.php',
    'Doctrine\Common\Annotations\Reader' => $rootPath . '/vendor/doctrine/annotations/lib/Doctrine/Common/Annotations'
        . '/Reader.php',
    'Doctrine\Common\Annotations\TokenParser' => $rootPath . '/vendor/doctrine/annotations/lib/Doctrine/Common'
        . '/Annotations/TokenParser.php',
    'Doctrine\Common\Cache\ApcCache' => $rootPath . '/vendor/doctrine/cache/lib/Doctrine/Common/Cache/ApcCache.php',
    'Doctrine\Common\Cache\ArrayCache' => $rootPath . '/vendor/doctrine/cache/lib/Doctrine/Common/Cache/ArrayCache.php',
    'Doctrine\Common\Cache\Cache' => $rootPath . '/vendor/doctrine/cache/lib/Doctrine/Common/Cache/Cache.php',
    'Doctrine\Common\Cache\CacheProvider' => $rootPath . '/vendor/doctrine/cache/lib/Doctrine/Common/Cache'
        . '/CacheProvider.php',
    'Doctrine\Common\ClassLoader' => $rootPath . '/vendor/doctrine/common/lib/Doctrine/Common/ClassLoader.php',
    'Doctrine\Common\Collections\ArrayCollection' => $rootPath . '/vendor/doctrine/collections/lib/Doctrine/Common'
        . '/Collections/ArrayCollection.php',
    'Doctrine\Common\Collections\Collection' => $rootPath . '/vendor/doctrine/collections/lib/Doctrine/Common'
        . '/Collections/Collection.php',
    'Doctrine\Common\Collections\Criteria' => $rootPath . '/vendor/doctrine/collections/lib/Doctrine/Common/Collections'
        . '/Criteria.php',
    'Doctrine\Common\Collections\Expr\Comparison' => $rootPath . '/vendor/doctrine/collections/lib/Doctrine/Common'
        . '/Collections/Expr/Comparison.php',
    'Doctrine\Common\Collections\Expr\Expression' => $rootPath . '/vendor/doctrine/collections/lib/Doctrine/Common'
        . '/Collections/Expr/Expression.php',
    'Doctrine\Common\Collections\Selectable' => $rootPath . '/vendor/doctrine/collections/lib/Doctrine/Common'
        . '/Collections/Selectable.php',
    'Doctrine\Common\EventArgs' => $rootPath . '/vendor/doctrine/common/lib/Doctrine/Common/EventArgs.php',
    'Doctrine\Common\EventManager' => $rootPath . '/vendor/doctrine/common/lib/Doctrine/Common/EventManager.php',
    'Doctrine\Common\EventSubscriber' => $rootPath . '/vendor/doctrine/common/lib/Doctrine/Common/EventSubscriber.php',
    'Doctrine\Common\Inflector\Inflector' => $rootPath . '/vendor/doctrine/inflector/lib/Doctrine/Common/Inflector'
        . '/Inflector.php',
    'Doctrine\Common\Lexer' => $rootPath . '/vendor/doctrine/common/lib/Doctrine/Common/Lexer.php',
    'Doctrine\Common\Lexer\AbstractLexer' => $rootPath . '/vendor/doctrine/lexer/lib/Doctrine/Common/Lexer'
        . '/AbstractLexer.php',
    'Doctrine\Common\Persistence\Event\LifecycleEventArgs' => $rootPath . '/vendor/doctrine/common/lib/Doctrine/Common'
        . '/Persistence/Event/LifecycleEventArgs.php',
    'Doctrine\Common\Persistence\Event\LoadClassMetadataEventArgs' => $rootPath . '/vendor/doctrine/common/lib/Doctrine'
        . '/Common/Persistence/Event/LoadClassMetadataEventArgs.php',
    'Doctrine\Common\Persistence\Mapping\AbstractClassMetadataFactory' => $rootPath . '/vendor/doctrine/common/lib'
        . '/Doctrine/Common/Persistence/Mapping/AbstractClassMetadataFactory.php',
    'Doctrine\Common\Persistence\Mapping\ClassMetadata' => $rootPath . '/vendor/doctrine/common/lib/Doctrine/Common'
        . '/Persistence/Mapping/ClassMetadata.php',
    'Doctrine\Common\Persistence\Mapping\ClassMetadataFactory' => $rootPath . '/vendor/doctrine/common/lib/Doctrine'
        . '/Common/Persistence/Mapping/ClassMetadataFactory.php',
    'Doctrine\Common\Persistence\Mapping\Driver\AnnotationDriver' => $rootPath . '/vendor/doctrine/common/lib/Doctrine'
        . '/Common/Persistence/Mapping/Driver/AnnotationDriver.php',
    'Doctrine\Common\Persistence\Mapping\Driver\MappingDriver' => $rootPath . '/vendor/doctrine/common/lib/Doctrine'
        . '/Common/Persistence/Mapping/Driver/MappingDriver.php',
    'Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain' => $rootPath . '/vendor/doctrine/common/lib'
        . '/Doctrine/Common/Persistence/Mapping/Driver/MappingDriverChain.php',
    'Doctrine\Common\Persistence\Mapping\MappingException' => $rootPath . '/vendor/doctrine/common/lib/Doctrine/Common'
        . '/Persistence/Mapping/MappingException.php',
    'Doctrine\Common\Persistence\Mapping\ReflectionService' => $rootPath . '/vendor/doctrine/common/lib/Doctrine/Common'
        . '/Persistence/Mapping/ReflectionService.php',
    'Doctrine\Common\Persistence\Mapping\RuntimeReflectionService' => $rootPath . '/vendor/doctrine/common/lib/Doctrine'
        . '/Common/Persistence/Mapping/RuntimeReflectionService.php',
    'Doctrine\Common\Persistence\Mapping\StaticReflectionService' => $rootPath . '/vendor/doctrine/common/lib/Doctrine'
        . '/Common/Persistence/Mapping/StaticReflectionService.php',
    'Doctrine\Common\Persistence\ObjectManager' => $rootPath . '/vendor/doctrine/common/lib/Doctrine/Common/Persistence'
        . '/ObjectManager.php',
    'Doctrine\Common\Persistence\ObjectRepository' => $rootPath . '/vendor/doctrine/common/lib/Doctrine/Common'
        . '/Persistence/ObjectRepository.php',
    'Doctrine\Common\Persistence\Proxy' => $rootPath . '/vendor/doctrine/common/lib/Doctrine/Common/Persistence'
        . '/Proxy.php',
    'Doctrine\Common\PropertyChangedListener' => $rootPath . '/vendor/doctrine/common/lib/Doctrine/Common'
        . '/PropertyChangedListener.php',
    'Doctrine\Common\Proxy\AbstractProxyFactory' => $rootPath . '/vendor/doctrine/common/lib/Doctrine/Common/Proxy'
        . '/AbstractProxyFactory.php',
    'Doctrine\Common\Proxy\Proxy' => $rootPath . '/vendor/doctrine/common/lib/Doctrine/Common/Proxy/Proxy.php',
    'Doctrine\Common\Proxy\ProxyDefinition' => $rootPath . '/vendor/doctrine/common/lib/Doctrine/Common/Proxy'
        . '/ProxyDefinition.php',
    'Doctrine\Common\Proxy\ProxyGenerator' => $rootPath . '/vendor/doctrine/common/lib/Doctrine/Common/Proxy'
        . '/ProxyGenerator.php',
    'Doctrine\Common\Util\ClassUtils' => $rootPath . '/vendor/doctrine/common/lib/Doctrine/Common/Util/ClassUtils.php',
    'Doctrine\Common\Util\Inflector' => $rootPath . '/vendor/doctrine/common/lib/Doctrine/Common/Util/Inflector.php',
    'Doctrine\Common\Version' => $rootPath . '/vendor/doctrine/common/lib/Doctrine/Common/Version.php',
    'Doctrine\DBAL\Configuration' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/Configuration.php',
    'Doctrine\DBAL\Connection' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/Connection.php',
    'Doctrine\DBAL\DBALException' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/DBALException.php',
    'Doctrine\DBAL\Driver' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/Driver.php',
    'Doctrine\DBAL\DriverManager' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/DriverManager.php',
    'Doctrine\DBAL\Driver\AbstractMySQLDriver' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/Driver'
        . '/AbstractMySQLDriver.php',
    'Doctrine\DBAL\Driver\Connection' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/Driver/Connection.php',
    'Doctrine\DBAL\Driver\DriverException' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/Driver'
        . '/DriverException.php',
    'Doctrine\DBAL\Driver\ExceptionConverterDriver' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/Driver'
        . '/ExceptionConverterDriver.php',
    'Doctrine\DBAL\Driver\PDOConnection' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/Driver'
        . '/PDOConnection.php',
    'Doctrine\DBAL\Driver\PDOException' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/Driver'
        . '/PDOException.php',
    'Doctrine\DBAL\Driver\PDOMySql\Driver' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/Driver/PDOMySql'
        . '/Driver.php',
    'Doctrine\DBAL\Driver\PDOStatement' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/Driver'
        . '/PDOStatement.php',
    'Doctrine\DBAL\Driver\ResultStatement' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/Driver'
        . '/ResultStatement.php',
    'Doctrine\DBAL\Driver\ServerInfoAwareConnection' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/Driver'
        . '/ServerInfoAwareConnection.php',
    'Doctrine\DBAL\Driver\Statement' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/Driver/Statement.php',
    'Doctrine\DBAL\Events' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/Events.php',
    'Doctrine\DBAL\Exception\ConstraintViolationException' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL'
        . '/Exception/ConstraintViolationException.php',
    'Doctrine\DBAL\Exception\DatabaseObjectNotFoundException' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL'
        . '/Exception/DatabaseObjectNotFoundException.php',
    'Doctrine\DBAL\Exception\DriverException' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/Exception'
        . '/DriverException.php',
    'Doctrine\DBAL\Exception\NotNullConstraintViolationException' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine'
        . '/DBAL/Exception/NotNullConstraintViolationException.php',
    'Doctrine\DBAL\Exception\ServerException' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/Exception'
        . '/ServerException.php',
    'Doctrine\DBAL\Exception\TableNotFoundException' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/Exception'
        . '/TableNotFoundException.php',
    'Doctrine\DBAL\LockMode' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/LockMode.php',
    'Doctrine\DBAL\Migrations\Version' => false,
    'Doctrine\DBAL\Platforms\AbstractPlatform' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/Platforms'
        . '/AbstractPlatform.php',
    'Doctrine\DBAL\Platforms\Keywords\KeywordList' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/Platforms'
        . '/Keywords/KeywordList.php',
    'Doctrine\DBAL\Platforms\Keywords\MySQLKeywords' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/Platforms'
        . '/Keywords/MySQLKeywords.php',
    'Doctrine\DBAL\Platforms\MySqlPlatform' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/Platforms'
        . '/MySqlPlatform.php',
    'Doctrine\DBAL\Query\Expression\ExpressionBuilder' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/Query'
        . '/Expression/ExpressionBuilder.php',
    'Doctrine\DBAL\SQLParserUtils' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/SQLParserUtils.php',
    'Doctrine\DBAL\Schema\AbstractAsset' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/Schema'
        . '/AbstractAsset.php',
    'Doctrine\DBAL\Schema\AbstractSchemaManager' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/Schema'
        . '/AbstractSchemaManager.php',
    'Doctrine\DBAL\Schema\Column' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/Schema/Column.php',
    'Doctrine\DBAL\Schema\Comparator' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/Schema/Comparator.php',
    'Doctrine\DBAL\Schema\Constraint' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/Schema/Constraint.php',
    'Doctrine\DBAL\Schema\ForeignKeyConstraint' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/Schema'
        . '/ForeignKeyConstraint.php',
    'Doctrine\DBAL\Schema\Identifier' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/Schema/Identifier.php',
    'Doctrine\DBAL\Schema\Index' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/Schema/Index.php',
    'Doctrine\DBAL\Schema\MySqlSchemaManager' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/Schema'
        . '/MySqlSchemaManager.php',
    'Doctrine\DBAL\Schema\Schema' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/Schema/Schema.php',
    'Doctrine\DBAL\Schema\SchemaConfig' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/Schema'
        . '/SchemaConfig.php',
    'Doctrine\DBAL\Schema\SchemaDiff' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/Schema/SchemaDiff.php',
    'Doctrine\DBAL\Schema\Sequence' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/Schema/Sequence.php',
    'Doctrine\DBAL\Schema\Table' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/Schema/Table.php',
    'Doctrine\DBAL\Schema\TableDiff' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/Schema/TableDiff.php',
    'Doctrine\DBAL\Schema\Visitor\AbstractVisitor' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/Schema'
        . '/Visitor/AbstractVisitor.php',
    'Doctrine\DBAL\Schema\Visitor\NamespaceVisitor' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/Schema'
        . '/Visitor/NamespaceVisitor.php',
    'Doctrine\DBAL\Schema\Visitor\RemoveNamespacedAssets' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL'
        . '/Schema/Visitor/RemoveNamespacedAssets.php',
    'Doctrine\DBAL\Schema\Visitor\Visitor' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/Schema/Visitor'
        . '/Visitor.php',
    'Doctrine\DBAL\Statement' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/Statement.php',
    'Doctrine\DBAL\Tools\Console\Command\ImportCommand' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/Tools'
        . '/Console/Command/ImportCommand.php',
    'Doctrine\DBAL\Tools\Console\Command\RunSqlCommand' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/Tools'
        . '/Console/Command/RunSqlCommand.php',
    'Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/Tools'
        . '/Console/Helper/ConnectionHelper.php',
    'Doctrine\DBAL\Types\ArrayType' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/Types/ArrayType.php',
    'Doctrine\DBAL\Types\BigIntType' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/Types/BigIntType.php',
    'Doctrine\DBAL\Types\BinaryType' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/Types/BinaryType.php',
    'Doctrine\DBAL\Types\BlobType' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/Types/BlobType.php',
    'Doctrine\DBAL\Types\BooleanType' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/Types/BooleanType.php',
    'Doctrine\DBAL\Types\DateTimeType' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/Types/DateTimeType.php',
    'Doctrine\DBAL\Types\DateTimeTzType' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/Types'
        . '/DateTimeTzType.php',
    'Doctrine\DBAL\Types\DateType' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/Types/DateType.php',
    'Doctrine\DBAL\Types\DecimalType' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/Types/DecimalType.php',
    'Doctrine\DBAL\Types\FloatType' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/Types/FloatType.php',
    'Doctrine\DBAL\Types\GuidType' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/Types/GuidType.php',
    'Doctrine\DBAL\Types\IntegerType' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/Types/IntegerType.php',
    'Doctrine\DBAL\Types\JsonArrayType' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/Types'
        . '/JsonArrayType.php',
    'Doctrine\DBAL\Types\ObjectType' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/Types/ObjectType.php',
    'Doctrine\DBAL\Types\SimpleArrayType' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/Types'
        . '/SimpleArrayType.php',
    'Doctrine\DBAL\Types\SmallIntType' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/Types/SmallIntType.php',
    'Doctrine\DBAL\Types\StringType' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/Types/StringType.php',
    'Doctrine\DBAL\Types\TextType' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/Types/TextType.php',
    'Doctrine\DBAL\Types\TimeType' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/Types/TimeType.php',
    'Doctrine\DBAL\Types\Type' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL/Types/Type.php',
    'Doctrine\DBAL\VersionAwarePlatformDriver' => $rootPath . '/vendor/doctrine/dbal/lib/Doctrine/DBAL'
        . '/VersionAwarePlatformDriver.php',
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
    'Doctrine\ORM\Event\LifecycleEventArgs' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Event'
        . '/LifecycleEventArgs.php',
    'Doctrine\ORM\Event\ListenersInvoker' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Event'
        . '/ListenersInvoker.php',
    'Doctrine\ORM\Event\LoadClassMetadataEventArgs' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Event'
        . '/LoadClassMetadataEventArgs.php',
    'Doctrine\ORM\Event\OnFlushEventArgs' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Event'
        . '/OnFlushEventArgs.php',
    'Doctrine\ORM\Event\PreFlushEventArgs' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Event'
        . '/PreFlushEventArgs.php',
    'Doctrine\ORM\Event\PreUpdateEventArgs' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Event'
        . '/PreUpdateEventArgs.php',
    'Doctrine\ORM\Events' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Events.php',
    'Doctrine\ORM\Id\AbstractIdGenerator' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Id'
        . '/AbstractIdGenerator.php',
    'Doctrine\ORM\Id\AssignedGenerator' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Id/AssignedGenerator.php',
    'Doctrine\ORM\Id\IdentityGenerator' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Id/IdentityGenerator.php',
    'Doctrine\ORM\Internal\CommitOrderCalculator' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Internal'
        . '/CommitOrderCalculator.php',
    'Doctrine\ORM\Internal\Hydration\AbstractHydrator' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Internal'
        . '/Hydration/AbstractHydrator.php',
    'Doctrine\ORM\Internal\Hydration\ArrayHydrator' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Internal'
        . '/Hydration/ArrayHydrator.php',
    'Doctrine\ORM\Internal\Hydration\ObjectHydrator' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Internal'
        . '/Hydration/ObjectHydrator.php',
    'Doctrine\ORM\Internal\Hydration\ScalarHydrator' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Internal'
        . '/Hydration/ScalarHydrator.php',
    'Doctrine\ORM\Internal\Hydration\SimpleObjectHydrator' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM'
        . '/Internal/Hydration/SimpleObjectHydrator.php',
    'Doctrine\ORM\Mapping\Annotation' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Mapping/Annotation.php',
    'Doctrine\ORM\Mapping\ClassMetadata' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Mapping'
        . '/ClassMetadata.php',
    'Doctrine\ORM\Mapping\ClassMetadataFactory' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Mapping'
        . '/ClassMetadataFactory.php',
    'Doctrine\ORM\Mapping\ClassMetadataInfo' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Mapping'
        . '/ClassMetadataInfo.php',
    'Doctrine\ORM\Mapping\Column' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Mapping/Column.php',
    'Doctrine\ORM\Mapping\DefaultEntityListenerResolver' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Mapping'
        . '/DefaultEntityListenerResolver.php',
    'Doctrine\ORM\Mapping\DefaultNamingStrategy' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Mapping'
        . '/DefaultNamingStrategy.php',
    'Doctrine\ORM\Mapping\DefaultQuoteStrategy' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Mapping'
        . '/DefaultQuoteStrategy.php',
    'Doctrine\ORM\Mapping\Driver\AnnotationDriver' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Mapping/Driver'
        . '/AnnotationDriver.php',
    'Doctrine\ORM\Mapping\Driver\DatabaseDriver' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Mapping/Driver'
        . '/DatabaseDriver.php',
    'Doctrine\ORM\Mapping\Driver\DriverChain' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Mapping/Driver'
        . '/DriverChain.php',
    'Doctrine\ORM\Mapping\Entity' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Mapping/Entity.php',
    'Doctrine\ORM\Mapping\EntityListenerResolver' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Mapping'
        . '/EntityListenerResolver.php',
    'Doctrine\ORM\Mapping\GeneratedValue' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Mapping'
        . '/GeneratedValue.php',
    'Doctrine\ORM\Mapping\HasLifecycleCallbacks' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Mapping'
        . '/HasLifecycleCallbacks.php',
    'Doctrine\ORM\Mapping\Id' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Mapping/Id.php',
    'Doctrine\ORM\Mapping\Index' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Mapping/Index.php',
    'Doctrine\ORM\Mapping\JoinColumn' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Mapping/JoinColumn.php',
    'Doctrine\ORM\Mapping\JoinTable' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Mapping/JoinTable.php',
    'Doctrine\ORM\Mapping\ManyToMany' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Mapping/ManyToMany.php',
    'Doctrine\ORM\Mapping\ManyToOne' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Mapping/ManyToOne.php',
    'Doctrine\ORM\Mapping\MappedSuperclass' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Mapping'
        . '/MappedSuperclass.php',
    'Doctrine\ORM\Mapping\NamingStrategy' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Mapping'
        . '/NamingStrategy.php',
    'Doctrine\ORM\Mapping\OneToMany' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Mapping/OneToMany.php',
    'Doctrine\ORM\Mapping\OrderBy' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Mapping/OrderBy.php',
    'Doctrine\ORM\Mapping\PrePersist' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Mapping/PrePersist.php',
    'Doctrine\ORM\Mapping\PreUpdate' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Mapping/PreUpdate.php',
    'Doctrine\ORM\Mapping\QuoteStrategy' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Mapping'
        . '/QuoteStrategy.php',
    'Doctrine\ORM\Mapping\Table' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Mapping/Table.php',
    'Doctrine\ORM\Mapping\UniqueConstraint' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Mapping'
        . '/UniqueConstraint.php',
    'Doctrine\ORM\Mapping\Version' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Mapping/Version.php',
    'Doctrine\ORM\ORMException' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/ORMException.php',
    'Doctrine\ORM\OptimisticLockException' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM'
        . '/OptimisticLockException.php',
    'Doctrine\ORM\PersistentCollection' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/PersistentCollection.php',
    'Doctrine\ORM\Persisters\AbstractCollectionPersister' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM'
        . '/Persisters/AbstractCollectionPersister.php',
    'Doctrine\ORM\Persisters\BasicEntityPersister' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Persisters'
        . '/BasicEntityPersister.php',
    'Doctrine\ORM\Persisters\EntityPersister' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Persisters'
        . '/EntityPersister.php',
    'Doctrine\ORM\Persisters\ManyToManyPersister' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Persisters'
        . '/ManyToManyPersister.php',
    'Doctrine\ORM\Proxy\Proxy' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Proxy/Proxy.php',
    'Doctrine\ORM\Proxy\ProxyFactory' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Proxy/ProxyFactory.php',
    'Doctrine\ORM\Query' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Query.php',
    'Doctrine\ORM\QueryBuilder' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/QueryBuilder.php',
    'Doctrine\ORM\Query\AST\ArithmeticExpression' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Query/AST'
        . '/ArithmeticExpression.php',
    'Doctrine\ORM\Query\AST\ComparisonExpression' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Query/AST'
        . '/ComparisonExpression.php',
    'Doctrine\ORM\Query\AST\ConditionalExpression' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Query/AST'
        . '/ConditionalExpression.php',
    'Doctrine\ORM\Query\AST\ConditionalPrimary' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Query/AST'
        . '/ConditionalPrimary.php',
    'Doctrine\ORM\Query\AST\ConditionalTerm' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Query/AST'
        . '/ConditionalTerm.php',
    'Doctrine\ORM\Query\AST\FromClause' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Query/AST/FromClause.php',
    'Doctrine\ORM\Query\AST\IdentificationVariableDeclaration' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM'
        . '/Query/AST/IdentificationVariableDeclaration.php',
    'Doctrine\ORM\Query\AST\InExpression' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Query/AST'
        . '/InExpression.php',
    'Doctrine\ORM\Query\AST\InputParameter' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Query/AST'
        . '/InputParameter.php',
    'Doctrine\ORM\Query\AST\Join' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Query/AST/Join.php',
    'Doctrine\ORM\Query\AST\JoinAssociationDeclaration' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Query/AST'
        . '/JoinAssociationDeclaration.php',
    'Doctrine\ORM\Query\AST\JoinAssociationPathExpression' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Query'
        . '/AST/JoinAssociationPathExpression.php',
    'Doctrine\ORM\Query\AST\Literal' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Query/AST/Literal.php',
    'Doctrine\ORM\Query\AST\Node' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Query/AST/Node.php',
    'Doctrine\ORM\Query\AST\NullComparisonExpression' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Query/AST'
        . '/NullComparisonExpression.php',
    'Doctrine\ORM\Query\AST\OrderByClause' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Query/AST'
        . '/OrderByClause.php',
    'Doctrine\ORM\Query\AST\OrderByItem' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Query/AST'
        . '/OrderByItem.php',
    'Doctrine\ORM\Query\AST\PathExpression' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Query/AST'
        . '/PathExpression.php',
    'Doctrine\ORM\Query\AST\RangeVariableDeclaration' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Query/AST'
        . '/RangeVariableDeclaration.php',
    'Doctrine\ORM\Query\AST\SelectClause' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Query/AST'
        . '/SelectClause.php',
    'Doctrine\ORM\Query\AST\SelectExpression' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Query/AST'
        . '/SelectExpression.php',
    'Doctrine\ORM\Query\AST\SelectStatement' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Query/AST'
        . '/SelectStatement.php',
    'Doctrine\ORM\Query\AST\SimpleArithmeticExpression' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Query/AST'
        . '/SimpleArithmeticExpression.php',
    'Doctrine\ORM\Query\AST\WhereClause' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Query/AST'
        . '/WhereClause.php',
    'Doctrine\ORM\Query\Exec\AbstractSqlExecutor' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Query/Exec'
        . '/AbstractSqlExecutor.php',
    'Doctrine\ORM\Query\Exec\SingleSelectExecutor' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Query/Exec'
        . '/SingleSelectExecutor.php',
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
    'Doctrine\ORM\Query\FilterCollection' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Query'
        . '/FilterCollection.php',
    'Doctrine\ORM\Query\Filter\SQLFilter' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Query/Filter'
        . '/SQLFilter.php',
    'Doctrine\ORM\Query\Lexer' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Query/Lexer.php',
    'Doctrine\ORM\Query\Parameter' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Query/Parameter.php',
    'Doctrine\ORM\Query\ParameterTypeInferer' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Query'
        . '/ParameterTypeInferer.php',
    'Doctrine\ORM\Query\Parser' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Query/Parser.php',
    'Doctrine\ORM\Query\ParserResult' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Query/ParserResult.php',
    'Doctrine\ORM\Query\QueryException' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Query/QueryException.php',
    'Doctrine\ORM\Query\ResultSetMapping' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Query'
        . '/ResultSetMapping.php',
    'Doctrine\ORM\Query\SqlWalker' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Query/SqlWalker.php',
    'Doctrine\ORM\Query\TreeWalker' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Query/TreeWalker.php',
    'Doctrine\ORM\Query\TreeWalkerAdapter' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Query'
        . '/TreeWalkerAdapter.php',
    'Doctrine\ORM\Query\TreeWalkerChain' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Query'
        . '/TreeWalkerChain.php',
    'Doctrine\ORM\Query\TreeWalkerChainIterator' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Query'
        . '/TreeWalkerChainIterator.php',
    'Doctrine\ORM\Repository\DefaultRepositoryFactory' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Repository'
        . '/DefaultRepositoryFactory.php',
    'Doctrine\ORM\Repository\RepositoryFactory' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Repository'
        . '/RepositoryFactory.php',
    'Doctrine\ORM\Tools\Console\Command\ClearCache\MetadataCommand' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine'
        . '/ORM/Tools/Console/Command/ClearCache/MetadataCommand.php',
    'Doctrine\ORM\Tools\Console\Command\ClearCache\QueryCommand' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM'
        . '/Tools/Console/Command/ClearCache/QueryCommand.php',
    'Doctrine\ORM\Tools\Console\Command\ClearCache\ResultCommand' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM'
        . '/Tools/Console/Command/ClearCache/ResultCommand.php',
    'Doctrine\ORM\Tools\Console\Command\ConvertDoctrine1SchemaCommand' => $rootPath . '/vendor/doctrine/orm/lib'
        . '/Doctrine/ORM/Tools/Console/Command/ConvertDoctrine1SchemaCommand.php',
    'Doctrine\ORM\Tools\Console\Command\ConvertMappingCommand' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM'
        . '/Tools/Console/Command/ConvertMappingCommand.php',
    'Doctrine\ORM\Tools\Console\Command\EnsureProductionSettingsCommand' => $rootPath . '/vendor/doctrine/orm/lib'
        . '/Doctrine/ORM/Tools/Console/Command/EnsureProductionSettingsCommand.php',
    'Doctrine\ORM\Tools\Console\Command\GenerateEntitiesCommand' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM'
        . '/Tools/Console/Command/GenerateEntitiesCommand.php',
    'Doctrine\ORM\Tools\Console\Command\GenerateProxiesCommand' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM'
        . '/Tools/Console/Command/GenerateProxiesCommand.php',
    'Doctrine\ORM\Tools\Console\Command\GenerateRepositoriesCommand' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine'
        . '/ORM/Tools/Console/Command/GenerateRepositoriesCommand.php',
    'Doctrine\ORM\Tools\Console\Command\InfoCommand' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Tools'
        . '/Console/Command/InfoCommand.php',
    'Doctrine\ORM\Tools\Console\Command\RunDqlCommand' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Tools'
        . '/Console/Command/RunDqlCommand.php',
    'Doctrine\ORM\Tools\Console\Command\SchemaTool\AbstractCommand' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine'
        . '/ORM/Tools/Console/Command/SchemaTool/AbstractCommand.php',
    'Doctrine\ORM\Tools\Console\Command\SchemaTool\CreateCommand' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM'
        . '/Tools/Console/Command/SchemaTool/CreateCommand.php',
    'Doctrine\ORM\Tools\Console\Command\SchemaTool\DropCommand' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM'
        . '/Tools/Console/Command/SchemaTool/DropCommand.php',
    'Doctrine\ORM\Tools\Console\Command\SchemaTool\UpdateCommand' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM'
        . '/Tools/Console/Command/SchemaTool/UpdateCommand.php',
    'Doctrine\ORM\Tools\Console\Command\ValidateSchemaCommand' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM'
        . '/Tools/Console/Command/ValidateSchemaCommand.php',
    'Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Tools'
        . '/Console/Helper/EntityManagerHelper.php',
    'Doctrine\ORM\Tools\Console\MetadataFilter' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Tools/Console'
        . '/MetadataFilter.php',
    'Doctrine\ORM\Tools\DisconnectedClassMetadataFactory' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Tools'
        . '/DisconnectedClassMetadataFactory.php',
    'Doctrine\ORM\Tools\Export\ClassMetadataExporter' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Tools'
        . '/Export/ClassMetadataExporter.php',
    'Doctrine\ORM\Tools\Export\Driver\AbstractExporter' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Tools'
        . '/Export/Driver/AbstractExporter.php',
    'Doctrine\ORM\Tools\Export\Driver\XmlExporter' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Tools/Export'
        . '/Driver/XmlExporter.php',
    'Doctrine\ORM\Tools\Pagination\CountOutputWalker' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Tools'
        . '/Pagination/CountOutputWalker.php',
    'Doctrine\ORM\Tools\Pagination\CountWalker' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Tools/Pagination'
        . '/CountWalker.php',
    'Doctrine\ORM\Tools\Pagination\LimitSubqueryOutputWalker' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM'
        . '/Tools/Pagination/LimitSubqueryOutputWalker.php',
    'Doctrine\ORM\Tools\Pagination\Paginator' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Tools/Pagination'
        . '/Paginator.php',
    'Doctrine\ORM\Tools\Pagination\WhereInWalker' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Tools'
        . '/Pagination/WhereInWalker.php',
    'Doctrine\ORM\Tools\ResolveTargetEntityListener' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Tools'
        . '/ResolveTargetEntityListener.php',
    'Doctrine\ORM\Tools\SchemaTool' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Tools/SchemaTool.php',
    'Doctrine\ORM\Tools\ToolEvents' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Tools/ToolEvents.php',
    'Doctrine\ORM\UnitOfWork' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/UnitOfWork.php',
    'Doctrine\ORM\Utility\IdentifierFlattener' => $rootPath . '/vendor/doctrine/orm/lib/Doctrine/ORM/Utility'
        . '/IdentifierFlattener.php',
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
    'Entity' => false,
    'Gedmo\Mapping\Annotation\SoftDeleteable' => $rootPath . '/vendor/gedmo/doctrine-extensions/lib/Gedmo/Mapping'
        . '/Annotation/SoftDeleteable.php',
    'Gedmo\Mapping\Annotation\Translatable' => $rootPath . '/vendor/gedmo/doctrine-extensions/lib/Gedmo/Mapping'
        . '/Annotation/Translatable.php',
    'Gedmo\Mapping\Driver' => $rootPath . '/vendor/gedmo/doctrine-extensions/lib/Gedmo/Mapping/Driver.php',
    'Gedmo\Mapping\Driver\AbstractAnnotationDriver' => $rootPath . '/vendor/gedmo/doctrine-extensions/lib/Gedmo/Mapping'
        . '/Driver/AbstractAnnotationDriver.php',
    'Gedmo\Mapping\Driver\AnnotationDriverInterface' => $rootPath . '/vendor/gedmo/doctrine-extensions/lib/Gedmo'
        . '/Mapping/Driver/AnnotationDriverInterface.php',
    'Gedmo\Mapping\Driver\Chain' => $rootPath . '/vendor/gedmo/doctrine-extensions/lib/Gedmo/Mapping/Driver/Chain.php',
    'Gedmo\Mapping\Event\AdapterInterface' => $rootPath . '/vendor/gedmo/doctrine-extensions/lib/Gedmo/Mapping/Event'
        . '/AdapterInterface.php',
    'Gedmo\Mapping\Event\Adapter\ORM' => $rootPath . '/vendor/gedmo/doctrine-extensions/lib/Gedmo/Mapping/Event/Adapter'
        . '/ORM.php',
    'Gedmo\Mapping\ExtensionMetadataFactory' => $rootPath . '/vendor/gedmo/doctrine-extensions/lib/Gedmo/Mapping'
        . '/ExtensionMetadataFactory.php',
    'Gedmo\Mapping\MappedEventSubscriber' => $rootPath . '/vendor/gedmo/doctrine-extensions/lib/Gedmo/Mapping'
        . '/MappedEventSubscriber.php',
    'Gedmo\SoftDeleteable' => false,
    'Gedmo\SoftDeleteable\Filter\SoftDeleteableFilter' => $rootPath . '/vendor/gedmo/doctrine-extensions/lib/Gedmo'
        . '/SoftDeleteable/Filter/SoftDeleteableFilter.php',
    'Gedmo\SoftDeleteable\Mapping\Driver\Annotation' => $rootPath . '/vendor/gedmo/doctrine-extensions/lib/Gedmo'
        . '/SoftDeleteable/Mapping/Driver/Annotation.php',
    'Gedmo\SoftDeleteable\Mapping\Driver\Database' => false,
    'Gedmo\SoftDeleteable\Mapping\Event\Adapter\ORM' => $rootPath . '/vendor/gedmo/doctrine-extensions/lib/Gedmo'
        . '/SoftDeleteable/Mapping/Event/Adapter/ORM.php',
    'Gedmo\SoftDeleteable\Mapping\Event\SoftDeleteableAdapter' => $rootPath . '/vendor/gedmo/doctrine-extensions/lib'
        . '/Gedmo/SoftDeleteable/Mapping/Event/SoftDeleteableAdapter.php',
    'Gedmo\SoftDeleteable\Mapping\Validator' => $rootPath . '/vendor/gedmo/doctrine-extensions/lib/Gedmo/SoftDeleteable'
        . '/Mapping/Validator.php',
    'Gedmo\SoftDeleteable\SoftDeleteableListener' => $rootPath . '/vendor/gedmo/doctrine-extensions/lib/Gedmo'
        . '/SoftDeleteable/SoftDeleteableListener.php',
    'Gedmo\Translatable\Entity\MappedSuperclass\AbstractPersonalTranslation' => $rootPath . '/vendor/gedmo'
        . '/doctrine-extensions/lib/Gedmo/Translatable/Entity/MappedSuperclass/AbstractPersonalTranslation.php',
    'Gedmo\Translatable\Entity\MappedSuperclass\AbstractTranslation' => $rootPath . '/vendor/gedmo/doctrine-extensions'
        . '/lib/Gedmo/Translatable/Entity/MappedSuperclass/AbstractTranslation.php',
    'Gedmo\Translatable\Entity\Translation' => $rootPath . '/vendor/gedmo/doctrine-extensions/lib/Gedmo/Translatable'
        . '/Entity/Translation.php',
    'Gedmo\Translatable\Mapping\Driver\Annotation' => $rootPath . '/vendor/gedmo/doctrine-extensions/lib/Gedmo'
        . '/Translatable/Mapping/Driver/Annotation.php',
    'Gedmo\Translatable\Mapping\Driver\Database' => false,
    'Gedmo\Translatable\Mapping\Event\Adapter\ORM' => $rootPath . '/vendor/gedmo/doctrine-extensions/lib/Gedmo'
        . '/Translatable/Mapping/Event/Adapter/ORM.php',
    'Gedmo\Translatable\Mapping\Event\TranslatableAdapter' => $rootPath . '/vendor/gedmo/doctrine-extensions/lib/Gedmo'
        . '/Translatable/Mapping/Event/TranslatableAdapter.php',
    'Gedmo\Translatable\Query\TreeWalker\TranslationWalker' => $rootPath . '/vendor/gedmo/doctrine-extensions/lib/Gedmo'
        . '/Translatable/Query/TreeWalker/TranslationWalker.php',
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
    'ORM\Entity' => false,
    'ORM\HasLifecycleCallbacks' => false,
    'ORM\Index' => false,
    'ORM\MappedSuperclass' => false,
    'ORM\Table' => false,
    'ORM\UniqueConstraint' => false,
    'OlcsTest\Db\Entity\Abstracts\EntityTester' => $rootPath . '/test/module/Olcs/Db/src//Entity/Abstracts'
        . '/EntityTester.php',
    'OlcsTest\Db\Service\Stub\EntityStub' => false,
    'OlcsTest\Db\Service\Stubs\EntityStub' => $rootPath . '/test/module/Olcs/Db/src//Service/Stubs/EntityStub.php',
    'Olcs\Db\Entity\HintQuestion' => $rootPath . '/module/Olcs/Db/src/Entity/HintQuestion.php',
    'Olcs\Db\Entity\Traits\Action1Field' => $rootPath . '/module/Olcs/Db/src/Entity/Traits/Action1Field.php',
    'Olcs\Db\Entity\Traits\AdPlacedDateField' => $rootPath . '/module/Olcs/Db/src/Entity/Traits/AdPlacedDateField.php',
    'Olcs\Db\Entity\Traits\AdPlacedField' => $rootPath . '/module/Olcs/Db/src/Entity/Traits/AdPlacedField.php',
    'Olcs\Db\Entity\Traits\AdPlacedIn70Field' => $rootPath . '/module/Olcs/Db/src/Entity/Traits/AdPlacedIn70Field.php',
    'Olcs\Db\Entity\Traits\CategoryText1024Field' => $rootPath . '/module/Olcs/Db/src/Entity/Traits'
        . '/CategoryText1024Field.php',
    'Olcs\Db\Entity\Traits\CloseDateField' => $rootPath . '/module/Olcs/Db/src/Entity/Traits/CloseDateField.php',
    'Olcs\Db\Entity\Traits\FamilyName35Field' => $rootPath . '/module/Olcs/Db/src/Entity/Traits/FamilyName35Field.php',
    'Olcs\Db\Entity\Traits\Forename35Field' => $rootPath . '/module/Olcs/Db/src/Entity/Traits/Forename35Field.php',
    'Olcs\Db\Entity\Traits\GrantedDateField' => $rootPath . '/module/Olcs/Db/src/Entity/Traits/GrantedDateField.php',
    'Olcs\Db\Entity\Traits\IsCancelledField' => $rootPath . '/module/Olcs/Db/src/Entity/Traits/IsCancelledField.php',
    'Olcs\Db\Entity\Traits\IsInterimField' => $rootPath . '/module/Olcs/Db/src/Entity/Traits/IsInterimField.php',
    'Olcs\Db\Entity\Traits\IsMaintenanceSuitableField' => $rootPath . '/module/Olcs/Db/src/Entity/Traits'
        . '/IsMaintenanceSuitableField.php',
    'Olcs\Db\Entity\Traits\IsNiField' => $rootPath . '/module/Olcs/Db/src/Entity/Traits/IsNiField.php',
    'Olcs\Db\Entity\Traits\IsNiFieldAlt1' => $rootPath . '/module/Olcs/Db/src/Entity/Traits/IsNiFieldAlt1.php',
    'Olcs\Db\Entity\Traits\LicNo18Field' => $rootPath . '/module/Olcs/Db/src/Entity/Traits/LicNo18Field.php',
    'Olcs\Db\Entity\Traits\NiFlagField' => $rootPath . '/module/Olcs/Db/src/Entity/Traits/NiFlagField.php',
    'Olcs\Db\Entity\Traits\NoOfTrailersRequiredField' => $rootPath . '/module/Olcs/Db/src/Entity/Traits'
        . '/NoOfTrailersRequiredField.php',
    'Olcs\Db\Entity\Traits\NoOfVehiclesRequiredField' => $rootPath . '/module/Olcs/Db/src/Entity/Traits'
        . '/NoOfVehiclesRequiredField.php',
    'Olcs\Db\Entity\Traits\PermissionField' => $rootPath . '/module/Olcs/Db/src/Entity/Traits/PermissionField.php',
    'Olcs\Db\Entity\Traits\RoleManyToOne' => $rootPath . '/module/Olcs/Db/src/Entity/Traits/RoleManyToOne.php',
    'Olcs\Db\Entity\Traits\ServiceNo70Field' => $rootPath . '/module/Olcs/Db/src/Entity/Traits/ServiceNo70Field.php',
    'Olcs\Db\Entity\Traits\TotAuthLargeVehiclesField' => $rootPath . '/module/Olcs/Db/src/Entity/Traits'
        . '/TotAuthLargeVehiclesField.php',
    'Olcs\Db\Entity\Traits\TotAuthMediumVehiclesField' => $rootPath . '/module/Olcs/Db/src/Entity/Traits'
        . '/TotAuthMediumVehiclesField.php',
    'Olcs\Db\Entity\Traits\TotAuthSmallVehiclesField' => $rootPath . '/module/Olcs/Db/src/Entity/Traits'
        . '/TotAuthSmallVehiclesField.php',
    'Olcs\Db\Entity\Traits\TotAuthTrailersField' => $rootPath . '/module/Olcs/Db/src/Entity/Traits'
        . '/TotAuthTrailersField.php',
    'Olcs\Db\Entity\Traits\TotAuthVehiclesField' => $rootPath . '/module/Olcs/Db/src/Entity/Traits'
        . '/TotAuthVehiclesField.php',
    'Olcs\Db\Entity\Traits\TotCommunityLicencesField' => $rootPath . '/module/Olcs/Db/src/Entity/Traits'
        . '/TotCommunityLicencesField.php',
    'Olcs\Db\Entity\View\Address' => false,
    'Olcs\Db\Entity\View\Application' => false,
    'Olcs\Db\Entity\View\ApplicationCompletion' => false,
    'Olcs\Db\Entity\View\ApplicationOperatingCentre' => false,
    'Olcs\Db\Entity\View\Cases' => false,
    'Olcs\Db\Entity\View\Category' => false,
    'Olcs\Db\Entity\View\CompanySubsidiary' => false,
    'Olcs\Db\Entity\View\Complaint' => false,
    'Olcs\Db\Entity\View\ConditionUndertaking' => false,
    'Olcs\Db\Entity\View\ContactDetails' => false,
    'Olcs\Db\Entity\View\Country' => false,
    'Olcs\Db\Entity\View\DiscSequence' => false,
    'Olcs\Db\Entity\View\Document' => false,
    'Olcs\Db\Entity\View\Decision' => false,
    'Olcs\Db\Entity\View\DocumentSubCategory' => false,
    'Olcs\Db\Entity\View\Fee' => false,
    'Olcs\Db\Entity\View\FeeType' => false,
    'Olcs\Db\Entity\View\GoodsDisc' => false,
    'Olcs\Db\Entity\View\LicenceNoGen' => false,
    'Olcs\Db\Entity\View\LicenceOperatingCentre' => false,
    'Olcs\Db\Entity\View\LicenceVehicle' => false,
    'Olcs\Db\Entity\View\Note' => false,
    'Olcs\Db\Entity\View\OperatingCentre' => false,
    'Olcs\Db\Entity\View\Organisation' => false,
    'Olcs\Db\Entity\View\OrganisationNatureOfBusiness' => false,
    'Olcs\Db\Entity\View\OrganisationPerson' => false,
    'Olcs\Db\Entity\View\OrganisationUser' => false,
    'Olcs\Db\Entity\View\PhoneContact' => false,
    'Olcs\Db\Entity\View\Pi' => false,
    'Olcs\Db\Entity\View\PiDefinition' => false,
    'Olcs\Db\Entity\View\PiHearing' => false,
    'Olcs\Db\Entity\View\PiVenue' => false,
    'Olcs\Db\Entity\View\PresidingTc' => false,
    'Olcs\Db\Entity\View\PreviousConviction' => false,
    'Olcs\Db\Entity\View\PreviousLicence' => false,
    'Olcs\Db\Entity\View\Prohibition' => false,
    'Olcs\Db\Entity\View\ProposeToRevoke' => false,
    'Olcs\Db\Entity\View\PsvDisc' => false,
    'Olcs\Db\Entity\View\RefData' => false,
    'Olcs\Db\Entity\View\PublicHoliday' => false,
    'Olcs\Db\Entity\View\Publication' => false,
    'Olcs\Db\Entity\View\PublicationLink' => false,
    'Olcs\Db\Entity\View\Reason' => false,
    'Olcs\Db\Entity\View\Sla' => false,
    'Olcs\Db\Entity\View\Submission' => false,
    'Olcs\Db\Entity\View\SubmissionSectionComment' => false,
    'Olcs\Db\Entity\View\Task' => false,
    'Olcs\Db\Entity\View\TaskSubCategory' => false,
    'Olcs\Db\Entity\View\Team' => false,
    'Olcs\Db\Entity\View\TradingName' => false,
    'Olcs\Db\Entity\View\TrafficArea' => false,
    'Olcs\Db\Entity\View\TransportManager' => false,
    'Olcs\Db\Entity\View\User' => false,
    'Olcs\Db\Entity\View\Vehicle' => false,
    'Olcs\Db\Entity\View\Workshop' => false,
    'Olcs\Db\Module' => false,
    'Olcs\Db\Service\Address' => false,
    'Olcs\Db\Service\Application' => false,
    'Olcs\Db\Service\ApplicationCompletion' => false,
    'Olcs\Db\Service\ApplicationOperatingCentre' => false,
    'Olcs\Db\Service\Cases' => false,
    'Olcs\Db\Service\Category' => false,
    'Olcs\Db\Service\CompanySubsidiary' => false,
    'Olcs\Db\Service\Complaint' => false,
    'Olcs\Db\Service\ConditionUndertaking' => false,
    'Olcs\Db\Service\ContactDetails' => false,
    'Olcs\Db\Service\Country' => false,
    'Olcs\Db\Service\DiscSequence' => false,
    'Olcs\Db\Service\Document' => false,
    'Olcs\Db\Service\DocumentSearchView' => false,
    'Olcs\Db\Service\Decision' => false,
    'Olcs\Db\Service\DocumentSubCategory' => false,
    'Olcs\Db\Service\Fee' => false,
    'Olcs\Db\Service\FeeType' => false,
    'Olcs\Db\Service\GoodsDisc' => false,
    'Olcs\Db\Service\LicenceNoGen' => false,
    'Olcs\Db\Service\LicenceOperatingCentre' => false,
    'Olcs\Db\Service\LicenceVehicle' => false,
    'Olcs\Db\Service\MISSING' => false,
    'Olcs\Db\Service\Note' => false,
    'Olcs\Db\Service\OperatingCentre' => false,
    'Olcs\Db\Service\Organisation' => false,
    'Olcs\Db\Service\OrganisationNatureOfBusiness' => false,
    'Olcs\Db\Service\OrganisationPerson' => false,
    'Olcs\Db\Service\OrganisationUser' => false,
    'Olcs\Db\Service\PhoneContact' => false,
    'Olcs\Db\Service\Pi' => false,
    'Olcs\Db\Service\PiDefinition' => false,
    'Olcs\Db\Service\PiHearing' => false,
    'Olcs\Db\Service\PiVenue' => false,
    'Olcs\Db\Service\PresidingTc' => false,
    'Olcs\Db\Service\PreviousConviction' => false,
    'Olcs\Db\Service\PreviousLicence' => false,
    'Olcs\Db\Service\Prohibition' => false,
    'Olcs\Db\Service\ProposeToRevoke' => false,
    'Olcs\Db\Service\PsvDisc' => false,
    'Olcs\Db\Service\RefData' => false,
    'Olcs\Db\Service\PublicHoliday' => false,
    'Olcs\Db\Service\Publication' => false,
    'Olcs\Db\Service\PublicationLink' => false,
    'Olcs\Db\Service\Reason' => false,
    'Olcs\Db\Service\Sla' => false,
    'Olcs\Db\Service\Submission' => false,
    'Olcs\Db\Service\SubmissionSectionComment' => false,
    'Olcs\Db\Service\Task' => false,
    'Olcs\Db\Service\TaskSearchView' => false,
    'Olcs\Db\Service\TaskSubCategory' => false,
    'Olcs\Db\Service\Team' => false,
    'Olcs\Db\Service\TradingName' => false,
    'Olcs\Db\Service\TrafficArea' => false,
    'Olcs\Db\Service\TransportManager' => false,
    'Olcs\Db\Service\User' => false,
    'Olcs\Db\Service\Vehicle' => false,
    'Olcs\Db\Service\Workshop' => false,
    'Olcs\Logging\Helper\LogError' => $rootPath . '/vendor/olcs/olcs-logging/src/Helper/LogError.php',
    'Olcs\Logging\Helper\LogException' => $rootPath . '/vendor/olcs/olcs-logging/src/Helper/LogException.php',
    'Olcs\Logging\Listener\LogError' => $rootPath . '/vendor/olcs/olcs-logging/src/Listener/LogError.php',
    'Olcs\Logging\Listener\LogRequest' => $rootPath . '/vendor/olcs/olcs-logging/src/Listener/LogRequest.php',
    'Olcs\Logging\Log\Formatter\Exception' => $rootPath . '/vendor/olcs/olcs-logging/src/Log/Formatter/Exception.php',
    'Olcs\Logging\Log\Formatter\Standard' => $rootPath . '/vendor/olcs/olcs-logging/src/Log/Formatter/Standard.php',
    'Olcs\Logging\Log\Processor\Microtime' => $rootPath . '/vendor/olcs/olcs-logging/src/Log/Processor/Microtime.php',
    'Olcs\Logging\Log\Processor\RemoteIp' => $rootPath . '/vendor/olcs/olcs-logging/src/Log/Processor/RemoteIp.php',
    'Olcs\Logging\Log\Processor\SessionId' => $rootPath . '/vendor/olcs/olcs-logging/src/Log/Processor/SessionId.php',
    'Olcs\Logging\Log\Processor\UserId' => $rootPath . '/vendor/olcs/olcs-logging/src/Log/Processor/UserId.php',
    'Olcs\Logging\Module' => $rootPath . '/vendor/olcs/olcs-logging/src/Module.php',
    'PHPUnit_Extensions_Database_TestCase' => false,
    'PHPUnit_Extensions_SeleniumTestCase' => false,
    'PHPUnit_Extensions_Story_TestCase' => false,
    'PHP_Invoker' => false,
    'Psr\Log\LoggerInterface' => false,
    'Simple' => false,
    'Symfony\Component\Console\Application' => $rootPath . '/vendor/symfony/console/Symfony/Component/Console'
        . '/Application.php',
    'Symfony\Component\Console\Command\Command' => $rootPath . '/vendor/symfony/console/Symfony/Component/Console'
        . '/Command/Command.php',
    'Symfony\Component\Console\Command\HelpCommand' => $rootPath . '/vendor/symfony/console/Symfony/Component/Console'
        . '/Command/HelpCommand.php',
    'Symfony\Component\Console\Command\ListCommand' => $rootPath . '/vendor/symfony/console/Symfony/Component/Console'
        . '/Command/ListCommand.php',
    'Symfony\Component\Console\Formatter\OutputFormatter' => $rootPath . '/vendor/symfony/console/Symfony/Component'
        . '/Console/Formatter/OutputFormatter.php',
    'Symfony\Component\Console\Formatter\OutputFormatterInterface' => $rootPath . '/vendor/symfony/console/Symfony'
        . '/Component/Console/Formatter/OutputFormatterInterface.php',
    'Symfony\Component\Console\Formatter\OutputFormatterStyle' => $rootPath . '/vendor/symfony/console/Symfony'
        . '/Component/Console/Formatter/OutputFormatterStyle.php',
    'Symfony\Component\Console\Formatter\OutputFormatterStyleInterface' => $rootPath . '/vendor/symfony/console/Symfony'
        . '/Component/Console/Formatter/OutputFormatterStyleInterface.php',
    'Symfony\Component\Console\Formatter\OutputFormatterStyleStack' => $rootPath . '/vendor/symfony/console/Symfony'
        . '/Component/Console/Formatter/OutputFormatterStyleStack.php',
    'Symfony\Component\Console\Helper\DebugFormatterHelper' => $rootPath . '/vendor/symfony/console/Symfony/Component'
        . '/Console/Helper/DebugFormatterHelper.php',
    'Symfony\Component\Console\Helper\DialogHelper' => $rootPath . '/vendor/symfony/console/Symfony/Component/Console'
        . '/Helper/DialogHelper.php',
    'Symfony\Component\Console\Helper\FormatterHelper' => $rootPath . '/vendor/symfony/console/Symfony/Component'
        . '/Console/Helper/FormatterHelper.php',
    'Symfony\Component\Console\Helper\Helper' => $rootPath . '/vendor/symfony/console/Symfony/Component/Console/Helper'
        . '/Helper.php',
    'Symfony\Component\Console\Helper\HelperInterface' => $rootPath . '/vendor/symfony/console/Symfony/Component'
        . '/Console/Helper/HelperInterface.php',
    'Symfony\Component\Console\Helper\HelperSet' => $rootPath . '/vendor/symfony/console/Symfony/Component/Console'
        . '/Helper/HelperSet.php',
    'Symfony\Component\Console\Helper\InputAwareHelper' => $rootPath . '/vendor/symfony/console/Symfony/Component'
        . '/Console/Helper/InputAwareHelper.php',
    'Symfony\Component\Console\Helper\ProcessHelper' => $rootPath . '/vendor/symfony/console/Symfony/Component/Console'
        . '/Helper/ProcessHelper.php',
    'Symfony\Component\Console\Helper\ProgressHelper' => $rootPath . '/vendor/symfony/console/Symfony/Component/Console'
        . '/Helper/ProgressHelper.php',
    'Symfony\Component\Console\Helper\QuestionHelper' => $rootPath . '/vendor/symfony/console/Symfony/Component/Console'
        . '/Helper/QuestionHelper.php',
    'Symfony\Component\Console\Helper\Table' => $rootPath . '/vendor/symfony/console/Symfony/Component/Console/Helper'
        . '/Table.php',
    'Symfony\Component\Console\Helper\TableHelper' => $rootPath . '/vendor/symfony/console/Symfony/Component/Console'
        . '/Helper/TableHelper.php',
    'Symfony\Component\Console\Helper\TableStyle' => $rootPath . '/vendor/symfony/console/Symfony/Component/Console'
        . '/Helper/TableStyle.php',
    'Symfony\Component\Console\Input\ArgvInput' => $rootPath . '/vendor/symfony/console/Symfony/Component/Console/Input'
        . '/ArgvInput.php',
    'Symfony\Component\Console\Input\Input' => $rootPath . '/vendor/symfony/console/Symfony/Component/Console/Input'
        . '/Input.php',
    'Symfony\Component\Console\Input\InputArgument' => $rootPath . '/vendor/symfony/console/Symfony/Component/Console'
        . '/Input/InputArgument.php',
    'Symfony\Component\Console\Input\InputAwareInterface' => $rootPath . '/vendor/symfony/console/Symfony/Component'
        . '/Console/Input/InputAwareInterface.php',
    'Symfony\Component\Console\Input\InputDefinition' => $rootPath . '/vendor/symfony/console/Symfony/Component/Console'
        . '/Input/InputDefinition.php',
    'Symfony\Component\Console\Input\InputInterface' => $rootPath . '/vendor/symfony/console/Symfony/Component/Console'
        . '/Input/InputInterface.php',
    'Symfony\Component\Console\Input\InputOption' => $rootPath . '/vendor/symfony/console/Symfony/Component/Console'
        . '/Input/InputOption.php',
    'Symfony\Component\Console\Output\ConsoleOutput' => $rootPath . '/vendor/symfony/console/Symfony/Component/Console'
        . '/Output/ConsoleOutput.php',
    'Symfony\Component\Console\Output\ConsoleOutputInterface' => $rootPath . '/vendor/symfony/console/Symfony/Component'
        . '/Console/Output/ConsoleOutputInterface.php',
    'Symfony\Component\Console\Output\NullOutput' => $rootPath . '/vendor/symfony/console/Symfony/Component/Console'
        . '/Output/NullOutput.php',
    'Symfony\Component\Console\Output\Output' => $rootPath . '/vendor/symfony/console/Symfony/Component/Console/Output'
        . '/Output.php',
    'Symfony\Component\Console\Output\OutputInterface' => $rootPath . '/vendor/symfony/console/Symfony/Component'
        . '/Console/Output/OutputInterface.php',
    'Symfony\Component\Console\Output\StreamOutput' => $rootPath . '/vendor/symfony/console/Symfony/Component/Console'
        . '/Output/StreamOutput.php',
    'Symfony\Component\Yaml\Yaml' => $rootPath . '/vendor/symfony/yaml/Symfony/Component/Yaml/Yaml.php',
    'Table' => false,
    'Zend\Cache\Service\StorageCacheAbstractServiceFactory' => $rootPath . '/vendor/zendframework/zendframework/library'
        . '/Zend/Cache/Service/StorageCacheAbstractServiceFactory.php',
    'Zend\Config\Factory' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Config/Factory.php',
    'Zend\Console\Console' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Console/Console.php',
    'Zend\Console\Request' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Console/Request.php',
    'Zend\Console\Response' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Console/Response.php',
    'Zend\Escaper\Escaper' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Escaper/Escaper.php',
    'Zend\EventManager\AbstractListenerAggregate' => $rootPath . '/vendor/zendframework/zendframework/library/Zend'
        . '/EventManager/AbstractListenerAggregate.php',
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
    'Zend\EventManager\ListenerAggregateTrait' => $rootPath . '/vendor/zendframework/zendframework/library/Zend'
        . '/EventManager/ListenerAggregateTrait.php',
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
    'Zend\Filter\FilterChain' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Filter/FilterChain.php',
    'Zend\Filter\FilterInterface' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Filter'
        . '/FilterInterface.php',
    'Zend\Filter\FilterPluginManager' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Filter'
        . '/FilterPluginManager.php',
    'Zend\Filter\Word\AbstractSeparator' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Filter/Word'
        . '/AbstractSeparator.php',
    'Zend\Filter\Word\CamelCaseToSeparator' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Filter'
        . '/Word/CamelCaseToSeparator.php',
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
    'Zend\Http\Client' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Http/Client.php',
    'Zend\Http\Client\Adapter\AdapterInterface' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Http'
        . '/Client/Adapter/AdapterInterface.php',
    'Zend\Http\Client\Adapter\Exception\ExceptionInterface' => $rootPath . '/vendor/zendframework/zendframework/library'
        . '/Zend/Http/Client/Adapter/Exception/ExceptionInterface.php',
    'Zend\Http\Client\Adapter\Exception\RuntimeException' => $rootPath . '/vendor/zendframework/zendframework/library'
        . '/Zend/Http/Client/Adapter/Exception/RuntimeException.php',
    'Zend\Http\Client\Adapter\Socket' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Http/Client'
        . '/Adapter/Socket.php',
    'Zend\Http\Client\Adapter\StreamInterface' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Http'
        . '/Client/Adapter/StreamInterface.php',
    'Zend\Http\Client\Exception\ExceptionInterface' => $rootPath . '/vendor/zendframework/zendframework/library/Zend'
        . '/Http/Client/Exception/ExceptionInterface.php',
    'Zend\Http\Client\Exception\RuntimeException' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Http'
        . '/Client/Exception/RuntimeException.php',
    'Zend\Http\Exception\ExceptionInterface' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Http'
        . '/Exception/ExceptionInterface.php',
    'Zend\Http\Exception\RuntimeException' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Http'
        . '/Exception/RuntimeException.php',
    'Zend\Http\HeaderLoader' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Http/HeaderLoader.php',
    'Zend\Http\Header\Connection' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Http/Header'
        . '/Connection.php',
    'Zend\Http\Header\ContentType' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Http/Header'
        . '/ContentType.php',
    'Zend\Http\Header\Cookie' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Http/Header/Cookie.php',
    'Zend\Http\Header\GenericHeader' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Http/Header'
        . '/GenericHeader.php',
    'Zend\Http\Header\HeaderInterface' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Http/Header'
        . '/HeaderInterface.php',
    'Zend\Http\Header\Host' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Http/Header/Host.php',
    'Zend\Http\Headers' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Http/Headers.php',
    'Zend\Http\PhpEnvironment\RemoteAddress' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Http'
        . '/PhpEnvironment/RemoteAddress.php',
    'Zend\Http\PhpEnvironment\Request' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Http'
        . '/PhpEnvironment/Request.php',
    'Zend\Http\PhpEnvironment\Response' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Http'
        . '/PhpEnvironment/Response.php',
    'Zend\Http\Request' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Http/Request.php',
    'Zend\Http\Response' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Http/Response.php',
    'Zend\I18n\Translator\Translator' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/I18n/Translator'
        . '/Translator.php',
    'Zend\I18n\Translator\TranslatorAwareInterface' => $rootPath . '/vendor/zendframework/zendframework/library/Zend'
        . '/I18n/Translator/TranslatorAwareInterface.php',
    'Zend\I18n\Translator\TranslatorInterface' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/I18n'
        . '/Translator/TranslatorInterface.php',
    'Zend\I18n\View\HelperConfig' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/I18n/View'
        . '/HelperConfig.php',
    'Zend\I18n\View\Helper\AbstractTranslatorHelper' => $rootPath . '/vendor/zendframework/zendframework/library/Zend'
        . '/I18n/View/Helper/AbstractTranslatorHelper.php',
    'Zend\I18n\View\Helper\Translate' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/I18n/View/Helper'
        . '/Translate.php',
    'Zend\InputFilter\InputFilterPluginManager' => $rootPath . '/vendor/zendframework/zendframework/library/Zend'
        . '/InputFilter/InputFilterPluginManager.php',
    'Zend\Json\Json' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Json/Json.php',
    'Zend\Loader\AutoloaderFactory' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Loader'
        . '/AutoloaderFactory.php',
    'Zend\Loader\ModuleAutoloader' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Loader'
        . '/ModuleAutoloader.php',
    'Zend\Loader\PluginClassLoader' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Loader'
        . '/PluginClassLoader.php',
    'Zend\Loader\PluginClassLocator' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Loader'
        . '/PluginClassLocator.php',
    'Zend\Loader\ShortNameLocator' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Loader'
        . '/ShortNameLocator.php',
    'Zend\Log\Formatter\Base' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Log/Formatter/Base.php',
    'Zend\Log\Formatter\FormatterInterface' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Log'
        . '/Formatter/FormatterInterface.php',
    'Zend\Log\Logger' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Log/Logger.php',
    'Zend\Log\LoggerAbstractServiceFactory' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Log'
        . '/LoggerAbstractServiceFactory.php',
    'Zend\Log\LoggerAwareTrait' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Log'
        . '/LoggerAwareTrait.php',
    'Zend\Log\LoggerInterface' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Log'
        . '/LoggerInterface.php',
    'Zend\Log\ProcessorPluginManager' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Log'
        . '/ProcessorPluginManager.php',
    'Zend\Log\Processor\ProcessorInterface' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Log'
        . '/Processor/ProcessorInterface.php',
    'Zend\Log\Processor\RequestId' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Log/Processor'
        . '/RequestId.php',
    'Zend\Log\WriterPluginManager' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Log'
        . '/WriterPluginManager.php',
    'Zend\Log\Writer\AbstractWriter' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Log/Writer'
        . '/AbstractWriter.php',
    'Zend\Log\Writer\FormatterPluginManager' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Log'
        . '/Writer/FormatterPluginManager.php',
    'Zend\Log\Writer\Stream' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Log/Writer/Stream.php',
    'Zend\Log\Writer\WriterInterface' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Log/Writer'
        . '/WriterInterface.php',
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
    'Zend\Mvc\Application' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc/Application.php',
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
    'Zend\Mvc\DispatchListener' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc'
        . '/DispatchListener.php',
    'Zend\Mvc\Exception\DomainException' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc/Exception'
        . '/DomainException.php',
    'Zend\Mvc\Exception\ExceptionInterface' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc'
        . '/Exception/ExceptionInterface.php',
    'Zend\Mvc\I18n\Translator' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc/I18n'
        . '/Translator.php',
    'Zend\Mvc\InjectApplicationEventInterface' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc'
        . '/InjectApplicationEventInterface.php',
    'Zend\Mvc\ModuleRouteListener' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc'
        . '/ModuleRouteListener.php',
    'Zend\Mvc\MvcEvent' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc/MvcEvent.php',
    'Zend\Mvc\ResponseSender\AbstractResponseSender' => $rootPath . '/vendor/zendframework/zendframework/library/Zend'
        . '/Mvc/ResponseSender/AbstractResponseSender.php',
    'Zend\Mvc\ResponseSender\ConsoleResponseSender' => $rootPath . '/vendor/zendframework/zendframework/library/Zend'
        . '/Mvc/ResponseSender/ConsoleResponseSender.php',
    'Zend\Mvc\ResponseSender\HttpResponseSender' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc'
        . '/ResponseSender/HttpResponseSender.php',
    'Zend\Mvc\ResponseSender\PhpEnvironmentResponseSender' => $rootPath . '/vendor/zendframework/zendframework/library'
        . '/Zend/Mvc/ResponseSender/PhpEnvironmentResponseSender.php',
    'Zend\Mvc\ResponseSender\ResponseSenderInterface' => $rootPath . '/vendor/zendframework/zendframework/library/Zend'
        . '/Mvc/ResponseSender/ResponseSenderInterface.php',
    'Zend\Mvc\ResponseSender\SendResponseEvent' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc'
        . '/ResponseSender/SendResponseEvent.php',
    'Zend\Mvc\ResponseSender\SimpleStreamResponseSender' => $rootPath . '/vendor/zendframework/zendframework/library'
        . '/Zend/Mvc/ResponseSender/SimpleStreamResponseSender.php',
    'Zend\Mvc\RouteListener' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc/RouteListener.php',
    'Zend\Mvc\Router\Console\RouteInterface' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc'
        . '/Router/Console/RouteInterface.php',
    'Zend\Mvc\Router\Console\SimpleRouteStack' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc'
        . '/Router/Console/SimpleRouteStack.php',
    'Zend\Mvc\Router\Http\Literal' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc/Router/Http'
        . '/Literal.php',
    'Zend\Mvc\Router\Http\RouteInterface' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc/Router'
        . '/Http/RouteInterface.php',
    'Zend\Mvc\Router\Http\RouteMatch' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc/Router/Http'
        . '/RouteMatch.php',
    'Zend\Mvc\Router\Http\Segment' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc/Router/Http'
        . '/Segment.php',
    'Zend\Mvc\Router\Http\TreeRouteStack' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc/Router'
        . '/Http/TreeRouteStack.php',
    'Zend\Mvc\Router\PriorityList' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc/Router'
        . '/PriorityList.php',
    'Zend\Mvc\Router\RouteInterface' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc/Router'
        . '/RouteInterface.php',
    'Zend\Mvc\Router\RouteMatch' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc/Router'
        . '/RouteMatch.php',
    'Zend\Mvc\Router\RoutePluginManager' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc/Router'
        . '/RoutePluginManager.php',
    'Zend\Mvc\Router\RouteStackInterface' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc/Router'
        . '/RouteStackInterface.php',
    'Zend\Mvc\Router\SimpleRouteStack' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc/Router'
        . '/SimpleRouteStack.php',
    'Zend\Mvc\SendResponseListener' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc'
        . '/SendResponseListener.php',
    'Zend\Mvc\Service\AbstractPluginManagerFactory' => $rootPath . '/vendor/zendframework/zendframework/library/Zend'
        . '/Mvc/Service/AbstractPluginManagerFactory.php',
    'Zend\Mvc\Service\ApplicationFactory' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc/Service'
        . '/ApplicationFactory.php',
    'Zend\Mvc\Service\ConfigFactory' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc/Service'
        . '/ConfigFactory.php',
    'Zend\Mvc\Service\ConsoleViewManagerFactory' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc'
        . '/Service/ConsoleViewManagerFactory.php',
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
    'Zend\Mvc\Service\HttpViewManagerFactory' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc'
        . '/Service/HttpViewManagerFactory.php',
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
    'Zend\Mvc\Service\RequestFactory' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc/Service'
        . '/RequestFactory.php',
    'Zend\Mvc\Service\ResponseFactory' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc/Service'
        . '/ResponseFactory.php',
    'Zend\Mvc\Service\RoutePluginManagerFactory' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc'
        . '/Service/RoutePluginManagerFactory.php',
    'Zend\Mvc\Service\RouterFactory' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc/Service'
        . '/RouterFactory.php',
    'Zend\Mvc\Service\SerializerAdapterPluginManagerFactory' => $rootPath . '/vendor/zendframework/zendframework'
        . '/library/Zend/Mvc/Service/SerializerAdapterPluginManagerFactory.php',
    'Zend\Mvc\Service\ServiceListenerFactory' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc'
        . '/Service/ServiceListenerFactory.php',
    'Zend\Mvc\Service\ServiceManagerConfig' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc'
        . '/Service/ServiceManagerConfig.php',
    'Zend\Mvc\Service\TranslatorServiceFactory' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc'
        . '/Service/TranslatorServiceFactory.php',
    'Zend\Mvc\Service\ValidatorManagerFactory' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc'
        . '/Service/ValidatorManagerFactory.php',
    'Zend\Mvc\Service\ViewHelperManagerFactory' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc'
        . '/Service/ViewHelperManagerFactory.php',
    'Zend\Mvc\Service\ViewJsonRendererFactory' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc'
        . '/Service/ViewJsonRendererFactory.php',
    'Zend\Mvc\Service\ViewJsonStrategyFactory' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc'
        . '/Service/ViewJsonStrategyFactory.php',
    'Zend\Mvc\Service\ViewManagerFactory' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc/Service'
        . '/ViewManagerFactory.php',
    'Zend\Mvc\Service\ViewResolverFactory' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc/Service'
        . '/ViewResolverFactory.php',
    'Zend\Mvc\Service\ViewTemplateMapResolverFactory' => $rootPath . '/vendor/zendframework/zendframework/library/Zend'
        . '/Mvc/Service/ViewTemplateMapResolverFactory.php',
    'Zend\Mvc\Service\ViewTemplatePathStackFactory' => $rootPath . '/vendor/zendframework/zendframework/library/Zend'
        . '/Mvc/Service/ViewTemplatePathStackFactory.php',
    'Zend\Mvc\View\Console\CreateViewModelListener' => $rootPath . '/vendor/zendframework/zendframework/library/Zend'
        . '/Mvc/View/Console/CreateViewModelListener.php',
    'Zend\Mvc\View\Console\DefaultRenderingStrategy' => $rootPath . '/vendor/zendframework/zendframework/library/Zend'
        . '/Mvc/View/Console/DefaultRenderingStrategy.php',
    'Zend\Mvc\View\Console\ExceptionStrategy' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc/View'
        . '/Console/ExceptionStrategy.php',
    'Zend\Mvc\View\Console\InjectNamedConsoleParamsListener' => $rootPath . '/vendor/zendframework/zendframework'
        . '/library/Zend/Mvc/View/Console/InjectNamedConsoleParamsListener.php',
    'Zend\Mvc\View\Console\InjectViewModelListener' => $rootPath . '/vendor/zendframework/zendframework/library/Zend'
        . '/Mvc/View/Console/InjectViewModelListener.php',
    'Zend\Mvc\View\Console\RouteNotFoundStrategy' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc'
        . '/View/Console/RouteNotFoundStrategy.php',
    'Zend\Mvc\View\Console\ViewManager' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc/View'
        . '/Console/ViewManager.php',
    'Zend\Mvc\View\Http\CreateViewModelListener' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc'
        . '/View/Http/CreateViewModelListener.php',
    'Zend\Mvc\View\Http\DefaultRenderingStrategy' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc'
        . '/View/Http/DefaultRenderingStrategy.php',
    'Zend\Mvc\View\Http\ExceptionStrategy' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc/View'
        . '/Http/ExceptionStrategy.php',
    'Zend\Mvc\View\Http\InjectTemplateListener' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc'
        . '/View/Http/InjectTemplateListener.php',
    'Zend\Mvc\View\Http\InjectViewModelListener' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc'
        . '/View/Http/InjectViewModelListener.php',
    'Zend\Mvc\View\Http\RouteNotFoundStrategy' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc'
        . '/View/Http/RouteNotFoundStrategy.php',
    'Zend\Mvc\View\Http\ViewManager' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Mvc/View/Http'
        . '/ViewManager.php',
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
    'Zend\Session\AbstractContainer' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Session'
        . '/AbstractContainer.php',
    'Zend\Session\AbstractManager' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Session'
        . '/AbstractManager.php',
    'Zend\Session\Config\ConfigInterface' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Session'
        . '/Config/ConfigInterface.php',
    'Zend\Session\Config\SessionConfig' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Session/Config'
        . '/SessionConfig.php',
    'Zend\Session\Config\StandardConfig' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Session'
        . '/Config/StandardConfig.php',
    'Zend\Session\Container' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Session/Container.php',
    'Zend\Session\ManagerInterface' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Session'
        . '/ManagerInterface.php',
    'Zend\Session\SessionManager' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Session'
        . '/SessionManager.php',
    'Zend\Session\Storage\AbstractSessionArrayStorage' => $rootPath . '/vendor/zendframework/zendframework/library/Zend'
        . '/Session/Storage/AbstractSessionArrayStorage.php',
    'Zend\Session\Storage\SessionArrayStorage' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Session'
        . '/Storage/SessionArrayStorage.php',
    'Zend\Session\Storage\StorageInitializationInterface' => $rootPath . '/vendor/zendframework/zendframework/library'
        . '/Zend/Session/Storage/StorageInitializationInterface.php',
    'Zend\Session\Storage\StorageInterface' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Session'
        . '/Storage/StorageInterface.php',
    'Zend\Session\ValidatorChain' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Session'
        . '/ValidatorChain.php',
    'Zend\Stdlib\AbstractOptions' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Stdlib'
        . '/AbstractOptions.php',
    'Zend\Stdlib\ArrayObject' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Stdlib/ArrayObject.php',
    'Zend\Stdlib\ArrayUtils' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Stdlib/ArrayUtils.php',
    'Zend\Stdlib\CallbackHandler' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Stdlib'
        . '/CallbackHandler.php',
    'Zend\Stdlib\DispatchableInterface' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Stdlib'
        . '/DispatchableInterface.php',
    'Zend\Stdlib\ErrorHandler' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Stdlib'
        . '/ErrorHandler.php',
    'Zend\Stdlib\Extractor\ExtractionInterface' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Stdlib'
        . '/Extractor/ExtractionInterface.php',
    'Zend\Stdlib\Glob' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Stdlib/Glob.php',
    'Zend\Stdlib\Hydrator\AbstractHydrator' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Stdlib'
        . '/Hydrator/AbstractHydrator.php',
    'Zend\Stdlib\Hydrator\FilterEnabledInterface' => $rootPath . '/vendor/zendframework/zendframework/library/Zend'
        . '/Stdlib/Hydrator/FilterEnabledInterface.php',
    'Zend\Stdlib\Hydrator\Filter\FilterComposite' => $rootPath . '/vendor/zendframework/zendframework/library/Zend'
        . '/Stdlib/Hydrator/Filter/FilterComposite.php',
    'Zend\Stdlib\Hydrator\Filter\FilterInterface' => $rootPath . '/vendor/zendframework/zendframework/library/Zend'
        . '/Stdlib/Hydrator/Filter/FilterInterface.php',
    'Zend\Stdlib\Hydrator\Filter\FilterProviderInterface' => $rootPath . '/vendor/zendframework/zendframework/library'
        . '/Zend/Stdlib/Hydrator/Filter/FilterProviderInterface.php',
    'Zend\Stdlib\Hydrator\HydrationInterface' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Stdlib'
        . '/Hydrator/HydrationInterface.php',
    'Zend\Stdlib\Hydrator\HydratorInterface' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Stdlib'
        . '/Hydrator/HydratorInterface.php',
    'Zend\Stdlib\Hydrator\HydratorPluginManager' => $rootPath . '/vendor/zendframework/zendframework/library/Zend'
        . '/Stdlib/Hydrator/HydratorPluginManager.php',
    'Zend\Stdlib\Hydrator\NamingStrategyEnabledInterface' => $rootPath . '/vendor/zendframework/zendframework/library'
        . '/Zend/Stdlib/Hydrator/NamingStrategyEnabledInterface.php',
    'Zend\Stdlib\Hydrator\StrategyEnabledInterface' => $rootPath . '/vendor/zendframework/zendframework/library/Zend'
        . '/Stdlib/Hydrator/StrategyEnabledInterface.php',
    'Zend\Stdlib\Hydrator\Strategy\StrategyInterface' => $rootPath . '/vendor/zendframework/zendframework/library/Zend'
        . '/Stdlib/Hydrator/Strategy/StrategyInterface.php',
    'Zend\Stdlib\Message' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Stdlib/Message.php',
    'Zend\Stdlib\MessageInterface' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Stdlib'
        . '/MessageInterface.php',
    'Zend\Stdlib\ParameterObjectInterface' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Stdlib'
        . '/ParameterObjectInterface.php',
    'Zend\Stdlib\Parameters' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Stdlib/Parameters.php',
    'Zend\Stdlib\ParametersInterface' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Stdlib'
        . '/ParametersInterface.php',
    'Zend\Stdlib\PriorityList' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Stdlib'
        . '/PriorityList.php',
    'Zend\Stdlib\PriorityQueue' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Stdlib'
        . '/PriorityQueue.php',
    'Zend\Stdlib\RequestInterface' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Stdlib'
        . '/RequestInterface.php',
    'Zend\Stdlib\ResponseInterface' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Stdlib'
        . '/ResponseInterface.php',
    'Zend\Stdlib\SplPriorityQueue' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Stdlib'
        . '/SplPriorityQueue.php',
    'Zend\Stdlib\SplStack' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Stdlib/SplStack.php',
    'Zend\Stdlib\StringUtils' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Stdlib/StringUtils.php',
    'Zend\Stdlib\StringWrapper\AbstractStringWrapper' => $rootPath . '/vendor/zendframework/zendframework/library/Zend'
        . '/Stdlib/StringWrapper/AbstractStringWrapper.php',
    'Zend\Stdlib\StringWrapper\Intl' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Stdlib'
        . '/StringWrapper/Intl.php',
    'Zend\Stdlib\StringWrapper\StringWrapperInterface' => $rootPath . '/vendor/zendframework/zendframework/library/Zend'
        . '/Stdlib/StringWrapper/StringWrapperInterface.php',
    'Zend\Uri\Http' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Uri/Http.php',
    'Zend\Uri\Uri' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Uri/Uri.php',
    'Zend\Uri\UriInterface' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Uri/UriInterface.php',
    'Zend\Validator\AbstractValidator' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Validator'
        . '/AbstractValidator.php',
    'Zend\Validator\Hostname' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Validator/Hostname.php',
    'Zend\Validator\Ip' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Validator/Ip.php',
    'Zend\Validator\Translator\TranslatorAwareInterface' => $rootPath . '/vendor/zendframework/zendframework/library'
        . '/Zend/Validator/Translator/TranslatorAwareInterface.php',
    'Zend\Validator\Translator\TranslatorInterface' => $rootPath . '/vendor/zendframework/zendframework/library/Zend'
        . '/Validator/Translator/TranslatorInterface.php',
    'Zend\Validator\ValidatorInterface' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Validator'
        . '/ValidatorInterface.php',
    'Zend\Validator\ValidatorPluginManager' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/Validator'
        . '/ValidatorPluginManager.php',
    'Zend\View\HelperPluginManager' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/View'
        . '/HelperPluginManager.php',
    'Zend\View\Helper\AbstractHelper' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/View/Helper'
        . '/AbstractHelper.php',
    'Zend\View\Helper\HelperInterface' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/View/Helper'
        . '/HelperInterface.php',
    'Zend\View\Helper\Placeholder' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/View/Helper'
        . '/Placeholder.php',
    'Zend\View\Helper\ViewModel' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/View/Helper'
        . '/ViewModel.php',
    'Zend\View\Model\ClearableModelInterface' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/View'
        . '/Model/ClearableModelInterface.php',
    'Zend\View\Model\ModelInterface' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/View/Model'
        . '/ModelInterface.php',
    'Zend\View\Model\RetrievableChildrenInterface' => $rootPath . '/vendor/zendframework/zendframework/library/Zend'
        . '/View/Model/RetrievableChildrenInterface.php',
    'Zend\View\Model\ViewModel' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/View/Model'
        . '/ViewModel.php',
    'Zend\View\Renderer\JsonRenderer' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/View/Renderer'
        . '/JsonRenderer.php',
    'Zend\View\Renderer\PhpRenderer' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/View/Renderer'
        . '/PhpRenderer.php',
    'Zend\View\Renderer\RendererInterface' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/View'
        . '/Renderer/RendererInterface.php',
    'Zend\View\Renderer\TreeRendererInterface' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/View'
        . '/Renderer/TreeRendererInterface.php',
    'Zend\View\Resolver\AggregateResolver' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/View'
        . '/Resolver/AggregateResolver.php',
    'Zend\View\Resolver\ResolverInterface' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/View'
        . '/Resolver/ResolverInterface.php',
    'Zend\View\Resolver\TemplateMapResolver' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/View'
        . '/Resolver/TemplateMapResolver.php',
    'Zend\View\Resolver\TemplatePathStack' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/View'
        . '/Resolver/TemplatePathStack.php',
    'Zend\View\Strategy\JsonStrategy' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/View/Strategy'
        . '/JsonStrategy.php',
    'Zend\View\Strategy\PhpRendererStrategy' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/View'
        . '/Strategy/PhpRendererStrategy.php',
    'Zend\View\Variables' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/View/Variables.php',
    'Zend\View\View' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/View/View.php',
    'Zend\View\ViewEvent' => $rootPath . '/vendor/zendframework/zendframework/library/Zend/View/ViewEvent.php',
    'author' => false,
    'license' => false,
    'package' => false,
);

<?php

/**
 * ConvertMappingCommand
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Cli;

use Doctrine\ORM\Tools\Console\Command\ConvertMappingCommand as DoctrineConvertMappingCommand;
use Doctrine\ORM\Tools\Console\MetadataFilter;
use Doctrine\ORM\Tools\EntityGenerator;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Doctrine\ORM\Tools\DisconnectedClassMetadataFactory;

/**
 * ConvertMappingCommand
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ConvertMappingCommand extends DoctrineConvertMappingCommand
{
    protected $mappingConfig = [];

    public function __construct($mappingConfig)
    {
        parent::__construct();

        $this->mappingConfig = $mappingConfig;
    }

    /**
     * {@inheritdoc}
     *
     * @NOTE Unfortunately had to copy and paste all of this from the parent
     * as there is no place to hook in a step to ammend the metadata, I have added the required hook
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getHelper('em')->getEntityManager();

        if ($input->getOption('from-database') === true) {
            $databaseDriver = new DatabaseDriver(
                $em->getConnection()->getSchemaManager(), $this->mappingConfig
            );

            $em->getConfiguration()->setMetadataDriverImpl(
                $databaseDriver
            );

            if (($namespace = $input->getOption('namespace')) !== null) {
                $databaseDriver->setNamespace($namespace);
            }
        }

        $cmf = new DisconnectedClassMetadataFactory();
        $cmf->setEntityManager($em);

        $metadata = $cmf->getAllMetadata();
        $metadata = MetadataFilter::filter($metadata, $input->getOption('filter'));

        $metadata = $this->configureMetadata($metadata);

        // Process destination directory
        if ( ! is_dir($destPath = $input->getArgument('dest-path'))) {
            mkdir($destPath, 0777, true);
        }
        $destPath = realpath($destPath);

        if ( ! file_exists($destPath)) {
            throw new \InvalidArgumentException(
                sprintf("Mapping destination directory '<info>%s</info>' does not exist.", $input->getArgument('dest-path'))
            );
        }

        if ( ! is_writable($destPath)) {
            throw new \InvalidArgumentException(
                sprintf("Mapping destination directory '<info>%s</info>' does not have write permissions.", $destPath)
            );
        }

        $toType = strtolower($input->getArgument('to-type'));

        $exporter = $this->getExporter($toType, $destPath);
        $exporter->setOverwriteExistingFiles($input->getOption('force'));

        if ($toType == 'annotation') {
            $entityGenerator = new EntityGenerator();
            $exporter->setEntityGenerator($entityGenerator);

            $entityGenerator->setNumSpaces($input->getOption('num-spaces'));

            if (($extend = $input->getOption('extend')) !== null) {
                $entityGenerator->setClassToExtend($extend);
            }
        }

        if (count($metadata)) {
            foreach ($metadata as $class) {
                $output->writeln(sprintf('Processing entity "<info>%s</info>"', $class->name));
            }

            $exporter->setMetadata($metadata);
            $exporter->export();

            $output->writeln(PHP_EOL . sprintf(
                'Exporting "<info>%s</info>" mapping information to "<info>%s</info>"', $toType, $destPath
            ));
        } else {
            $output->writeln('No Metadata Classes to process.');
        }
    }

    protected function configureMetadata($metadata)
    {
        foreach ($metadata as $object) {

            $name = $object->getName();

            if (isset($this->mappingConfig[$name]['fields'])) {
                foreach ($this->mappingConfig[$name]['fields'] as $old => $new) {
                    $object->associationMappings[$old]['fieldName'] = $new;
                }
            }
        }

        return $metadata;
    }
}

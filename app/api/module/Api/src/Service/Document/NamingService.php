<?php

/**
 * Document Naming Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Service\Document;

use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Api\Entity\System\SubCategory;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Document Naming Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class NamingService implements FactoryInterface
{
    /**
     * e.g. documents/{Category}/{SubCategory}/{Date:Y}/{Date:m}/{Date:YmdHis}_{Context}_{Description}.{Extension}
     *
     * @var string
     */
    protected $pattern;

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');

        if (!isset($config['document_share']['path'])) {
            throw new \RuntimeException('document_share/path has not been defined in config');
        }

        $this->pattern = $config['document_share']['path'];

        return $this;
    }

    public function generateName(
        $description,
        $extension,
        Category $category = null,
        SubCategory $subCategory = null,
        ContextProviderInterface $entity = null
    ) {
        $model = new NamingModel(new DateTime(), $description, $extension, $category, $subCategory, $entity);

        $name = preg_replace_callback(
            '/{([a-zA-Z]+)(?:\:([^:}]+))?}/',
            function ($matches) use ($model) {
                if (isset($matches[2])) {
                    return $this->valueOrAlt($model->{'get' . $matches[1]}($matches[2]), 'Unknown');
                }

                return $this->valueOrAlt($model->{'get' . $matches[1]}(), 'Unknown');
            },
            $this->pattern
        );

        return str_replace(' ', '_', $name);
    }

    protected function valueOrAlt($value = null, $alt = 'Unknown')
    {
        return $value !== null ? $value : $alt;
    }
}

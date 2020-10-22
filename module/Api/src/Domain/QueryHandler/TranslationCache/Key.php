<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\TranslationCache;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\TranslationLoaderAwareInterface;
use Dvsa\Olcs\Api\Domain\TranslationLoaderAwareTrait;
use Dvsa\Olcs\Api\Service\Translator\TranslationLoader;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Translation key by locale
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class Key extends AbstractQueryHandler implements TranslationLoaderAwareInterface
{
    use TranslationLoaderAwareTrait;

    protected $repoServiceName = 'TranslationKeyText';

    public function handleQuery(QueryInterface $query)
    {
        return $this->translationLoader->getMessages($query->getId(), TranslationLoader::DEFAULT_TEXT_DOMAIN);
    }
}

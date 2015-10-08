<?php

/**
 * Response
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Mvc\Controller\Plugin;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\QueryHandler\Result as QueryResult;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Http\Response as HttpResponse;
use Zend\View\Model\JsonModel;

/**
 * Response
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Response extends AbstractPlugin
{
    public function notFound()
    {
        $response = $this->getController()->getResponse();
        $response->setStatusCode(HttpResponse::STATUS_CODE_404);

        return $response;
    }

    /**
     * @param string $retryAfter number of seconds
     */
    public function notReady($retryAfter = null)
    {
        $response = $this->getController()->getResponse();
        $response->setStatusCode(HttpResponse::STATUS_CODE_503);
        if ($retryAfter) {
            $response->getHeaders()->addHeaders(['Retry-After' => $retryAfter]);
        }

        return $response;
    }

    public function error($code, array $messages = [])
    {
        $response = $this->getController()->getResponse();
        $response->setStatusCode($code);

        if (empty($messages)) {
            return $response;
        }

        return new JsonModel(['messages' => $messages]);
    }

    public function singleResult($result)
    {
        $response = $this->getController()->getResponse();
        $response->setStatusCode(HttpResponse::STATUS_CODE_200);

        if ($result instanceof QueryResult) {
            $result = $result->serialize();
        }

        if (!is_array($result)) {
            $result = $result->jsonSerialize();
        }

        return new JsonModel($result);
    }

    public function multipleResults($count, $results, array $extra = [])
    {
        $response = $this->getController()->getResponse();
        $response->setStatusCode(HttpResponse::STATUS_CODE_200);

        return new JsonModel(['count' => $count, 'results' => $results, 'extra' => $extra]);
    }

    public function successfulUpdate(Result $result)
    {
        $response = $this->getController()->getResponse();
        $response->setStatusCode(HttpResponse::STATUS_CODE_200);

        return new JsonModel($result->toArray());
    }

    public function successfulCreate(Result $result)
    {
        $response = $this->getController()->getResponse();
        $response->setStatusCode(HttpResponse::STATUS_CODE_201);

        return new JsonModel($result->toArray());
    }
}

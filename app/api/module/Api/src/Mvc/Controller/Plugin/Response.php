<?php

/**
 * Response
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Mvc\Controller\Plugin;

use Dvsa\Olcs\Api\Domain\Command\Result;
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

        return new JsonModel($result);
    }

    public function multipleResults($count, $results)
    {
        $response = $this->getController()->getResponse();
        $response->setStatusCode(HttpResponse::STATUS_CODE_200);

        return new JsonModel(['count' => $count, 'results' => $results]);
    }

    public function successfulUpdate(Result $result)
    {
        $response = $this->getController()->getResponse();
        $response->setStatusCode(HttpResponse::STATUS_CODE_200);

        return new JsonModel($result->toArray());
    }
}

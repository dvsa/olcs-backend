<?php

namespace Olcs\Db\Controller;

use Zend\Http\Response;

/**
 * Class RefDataController
 * @package Olcs\Db\Controller
 */
class RefDataController extends AbstractController
{
    public function get($id)
    {
        $lang = $this->params()->fromRoute('lang');
        /** @var \Doctrine\Orm\EntityManager $em */
        $em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $metadata = $em->getClassMetadata('OlcsEntities\Entity\RefData');
        $repository = new \Olcs\Db\Entity\Repository\RefData($em, $metadata);

        $data = $repository->findAllByCategoryAndLanguage($id, $lang);

        return $this->respond(Response::STATUS_CODE_200, 'OK', $data);
    }
}

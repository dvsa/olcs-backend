<?php

namespace Olcs\Db\Controller;

use Zend\Http\Response;

/**
 * Class RefDataController
 * @package Olcs\Db\Controller
 */
class RefDataController extends AbstractController
{
    protected function getLanguage()
    {
        return $this->getEvent()->getRequest()->getHeaders('accept-language')->getFieldValue();
    }

    public function get($id)
    {
        $lang = $this->getLanguage();

        /** @var \Doctrine\Orm\EntityManager $em */
        $em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $repository = $em->getRepository('Dvsa\Olcs\Api\Entity\System\RefData');

        $data = $repository->findByIdentifierAndLanguage($id, $lang);
        return $this->respond(Response::STATUS_CODE_200, 'OK', $data);
    }

    public function getList()
    {
        $category = $this->getEvent()->getRouteMatch()->getParam('category');

        $lang = $this->getLanguage();
        /** @var \Doctrine\Orm\EntityManager $em */
        $em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $repository = $em->getRepository('Dvsa\Olcs\Api\Entity\System\RefData');

        $data = $repository->findAllByCategoryAndLanguage($category, $lang);

        return $this->respond(Response::STATUS_CODE_200, 'OK', $data);
    }
}

<?php

namespace AppBundle\Admin\Controller;

use AppBundle\Admin\Form\Type\PostType;
use AppBundle\Entity\Post;
use AppBundle\Helper\PathHelper;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AdminController;
use EasyCorp\Bundle\EasyAdminBundle\Event\EasyAdminEvents;
use Symfony\Component\HttpFoundation\Request;

class UserController extends AdminController
{
    protected function findAll($entityClass, $page = 1, $maxPerPage = 15, $sortField = null, $sortDirection = null, $dqlFilter = null)
    {
        if (empty($sortDirection) || !in_array(strtoupper($sortDirection), array('ASC', 'DESC'))) {
            $sortDirection = 'DESC';
        }

        $queryBuilder = $this->executeDynamicMethod('create<EntityName>ListQueryBuilder', array($entityClass, $sortDirection, $sortField, $dqlFilter));

        $queryBuilder->andWhere( 'entity.roles NOT LIKE :role1');
        $queryBuilder->setParameter('role1', '%ROLE_SUPER_ADMIN%');

        $this->dispatch(EasyAdminEvents::POST_LIST_QUERY_BUILDER, array(
            'query_builder' => $queryBuilder,
            'sort_field' => $sortField,
            'sort_direction' => $sortDirection,
        ));

        return $this->get('easyadmin.paginator')->createOrmPaginator($queryBuilder, $page, $maxPerPage);
    }
//    
//    
//    protected function listAction()
//    {
//        $this->dispatch(EasyAdminEvents::PRE_LIST);
//
//        $fields = $this->entity['list']['fields'];
//        $paginator = $this->findAll($this->entity['class'], $this->request->query->get('page', 1), $this->entity['list']['max_results'], $this->request->query->get('sortField'), $this->request->query->get('sortDirection'), $this->entity['list']['dql_filter']);
//
//        $this->dispatch(EasyAdminEvents::POST_LIST, array('paginator' => $paginator));
//
//        $parameters = array(
//            'paginator' => $paginator,
//            'fields' => $fields,
//            'delete_form_template' => $this->createDeleteForm($this->entity['name'], '__id__')->createView(),
//        );
//
//        return $this->executeDynamicMethod('render<EntityName>Template', array('list', $this->entity['templates']['list'], $parameters));
//    }

}
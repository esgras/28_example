<?php

namespace AppBundle\Admin\Controller;

use AppBundle\Admin\Form\Type\AboutChallengeType;
use AppBundle\Admin\Form\Type\PostType;
use AppBundle\Entity\CompanyPage;
use AppBundle\Entity\Day;
use AppBundle\Entity\Post;
use AppBundle\Entity\Product;
use AppBundle\Helper\PathHelper;
use AppBundle\Helper\YoutubeHelper;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AdminController;
use EasyCorp\Bundle\EasyAdminBundle\Event\EasyAdminEvents;
use EasyCorp\Bundle\EasyAdminBundle\Exception\UndefinedEntityException;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\Translator;

class DayController extends AdminController
{

    protected function findBy($entityClass, $searchQuery, array $searchableFields, $page = 1, $maxPerPage = 15, $sortField = null, $sortDirection = null, $dqlFilter = null)
    {
        if (empty($sortDirection) || !in_array(strtoupper($sortDirection), array('ASC', 'DESC'))) {
            $sortDirection = 'DESC';
        }
        
        $dqlFilter = $this->getDqlForProduct($dqlFilter);

        $queryBuilder = $this->executeDynamicMethod('create<EntityName>SearchQueryBuilder', array($entityClass, $searchQuery, $searchableFields, $sortField, $sortDirection, $dqlFilter));

        $this->dispatch(EasyAdminEvents::POST_SEARCH_QUERY_BUILDER, array(
            'query_builder' => $queryBuilder,
            'search_query' => $searchQuery,
            'searchable_fields' => $searchableFields,
        ));

        return $this->get('easyadmin.paginator')->createOrmPaginator($queryBuilder, $page, $maxPerPage);
    }
    
    protected function findAll($entityClass, $page = 1, $maxPerPage = 15, $sortField = null, $sortDirection = null, $dqlFilter = null)
    {
        if (empty($sortDirection) || !in_array(strtoupper($sortDirection), array('ASC', 'DESC'))) {
            $sortDirection = 'DESC';
        }

        $dqlFilter = $this->getDqlForProduct($dqlFilter);

        $queryBuilder = $this->executeDynamicMethod('create<EntityName>ListQueryBuilder', array($entityClass, $sortDirection, $sortField, $dqlFilter));

        $this->dispatch(EasyAdminEvents::POST_LIST_QUERY_BUILDER, array(
            'query_builder' => $queryBuilder,
            'sort_field' => $sortField,
            'sort_direction' => $sortDirection,
        ));

        return $this->get('easyadmin.paginator')->createOrmPaginator($queryBuilder, $page, $maxPerPage);
    }

    protected function getDqlForProduct($dqlFilter)
    {
        $productId = $this->request->query->get('product', 1);
        if ($this->request->query->get('entity') != 'Day') {
            return $dqlFilter;
        }

        if (empty($productId)) {
            $products = $this->getDoctrine()->getManager()->getRepository(Product::class)->findAll();
            $product = array_shift($products);
            if(!empty($product)) {
                $productId = $product->getId();
            }
        }

        $dqlFilter =  $dqlFilter . (!empty($dqlFilter) ? ' AND ' : '' ) . ' entity.product = ' . intval($productId);


        return $dqlFilter;
    }

    protected function searchAction()
    {
        $this->dispatch(EasyAdminEvents::PRE_SEARCH);

        $query = trim($this->request->query->get('query'));
        // if the search query is empty, redirect to the 'list' action
        if ('' === $query) {
            $queryParameters = array_replace($this->request->query->all(), array('action' => 'list'));
            unset($queryParameters['query']);

            return $this->redirect($this->get('router')->generate('easyadmin', $queryParameters));
        }
        

        $searchableFields = $this->entity['search']['fields'];
//        dump($searchableFields); die;

        $defaultSortField = isset($this->entity['search']['sort']['field']) ? $this->entity['search']['sort']['field'] : null;
        $defaultSortDirection = isset($this->entity['search']['sort']['direction']) ? $this->entity['search']['sort']['direction'] : null;
        $paginator = $this->findBy(
            $this->entity['class'],
            $query,
            $searchableFields,
            $this->request->query->get('page', 1),
            $this->entity['list']['max_results'],
            $this->request->query->get('sortField', $defaultSortField),
            $this->request->query->get('sortDirection', $defaultSortDirection),
            $this->entity['search']['dql_filter']
        );
        $fields = $this->entity['list']['fields'];

        $this->dispatch(EasyAdminEvents::POST_SEARCH, array(
            'fields' => $fields,
            'paginator' => $paginator,
        ));

        $parameters = array(
            'paginator' => $paginator,
            'fields' => $fields,
            'delete_form_template' => $this->createDeleteForm($this->entity['name'], '__id__')->createView(),
            'product' => $this->getProduct()
        );

        return $this->executeDynamicMethod('render<EntityName>Template', array('search', $this->entity['templates']['list'], $parameters));
    }

    protected function listAction()
    {
        $this->dispatch(EasyAdminEvents::PRE_LIST);

        $product = $this->getProduct();
        $fields = $this->entity['list']['fields'];

        $sortField = $this->request->query->get('sortField');
        if ($this->request->query->get('entity') == 'Day' && $sortField == 'id') {
            $sortField = 'number';
        }

        $paginator = $this->findAll($this->entity['class'], $this->request->query->get('page', 1), $this->entity['list']['max_results'], $sortField, $this->request->query->get('sortDirection'), $this->entity['list']['dql_filter']);

        $this->dispatch(EasyAdminEvents::POST_LIST, array('paginator' => $paginator));

        $parameters = array(
            'paginator' => $paginator,
            'fields' => $fields,
            'delete_form_template' => $this->createDeleteForm($this->entity['name'], '__id__')->createView(),
            'product' => $product
        );

        return $this->executeDynamicMethod('render<EntityName>Template', array('list', $this->entity['templates']['list'], $parameters));
    }

    public function productsListAction($page=1, Request $request)
    {
        $request->query->set('entity', 'Product');
        $request->query->set('action', 'list');
        $this->initialize($request);

        $this->dispatch(EasyAdminEvents::PRE_LIST);

        $fields = $this->entity['list']['fields'];
        $fields = array_map(function($field) {if (isset($field['sortable'])) $field['sortable'] = false; return $field; }, $fields);
        $paginator = $this->findAll($this->entity['class'], $this->request->query->get('page', 1), $this->entity['list']['max_results'], $this->request->query->get('sortField'), $this->request->query->get('sortDirection'), $this->entity['list']['dql_filter']);

        $this->dispatch(EasyAdminEvents::POST_LIST, array('paginator' => $paginator));

        $parameters = array(
            'paginator' => $paginator,
            'fields' => $fields,
            'delete_form_template' => $this->createDeleteForm($this->entity['name'], '__id__')->createView(),
        );

        return $this->render('@EasyAdmin/day/product_list.html.twig', $parameters);
    }

    protected function getProduct()
    {
        $productId = $this->request->get('product', 1);
        $product = $this->em->getRepository(Product::class)->find($productId);

        if (!$product) {
            throw $this->createNotFoundException('Product not found');
        }

        return $product;
    }

    protected function newAction()
    {
        $this->dispatch(EasyAdminEvents::PRE_NEW);
        $product = $this->getProduct();

        $entity = $this->executeDynamicMethod('createNew<EntityName>Entity');

        $easyadmin = $this->request->attributes->get('easyadmin');
        $easyadmin['item'] = $entity;
        $this->request->attributes->set('easyadmin', $easyadmin);

        $fields = $this->entity['new']['fields'];

        $newForm = $this->executeDynamicMethod('create<EntityName>NewForm', array($entity, $fields));
        
        $newForm->handleRequest($this->request);
        if ($newForm->isSubmitted() && $newForm->isValid()
            && $this->checkProductDays($product, $newForm)
        ) {
            $this->dispatch(EasyAdminEvents::PRE_PERSIST, array('entity' => $entity));
            $product->addProductDay($entity);

            $this->executeDynamicMethod('prePersist<EntityName>Entity', array($entity, true));
            $this->executeDynamicMethod('persist<EntityName>Entity', array($entity, $newForm));

            $this->dispatch(EasyAdminEvents::POST_PERSIST, array('entity' => $entity));

            return $this->redirectToReferrer();
        }

        $this->dispatch(EasyAdminEvents::POST_NEW, array(
            'entity_fields' => $fields,
            'form' => $newForm,
            'entity' => $entity,
        ));

        $parameters = array(
            'form' => $newForm->createView(),
            'entity_fields' => $fields,
            'entity' => $entity,
        );

        return $this->executeDynamicMethod('render<EntityName>Template', array('new', $this->entity['templates']['new'], $parameters));
    }

    protected function checkProductDays(Product $product, Form $form)
    {
        if ($form->getData()->getNumber() > $product->getDays()) {
            $form->get('number')->addError(new FormError("Значение должно быть {$product->getDays()} или меньше"));
            return false;
        }
        
        $day = $this->em->getRepository(Day::class)->findOneBy(['product' => $product, 
            'number' => $form->getData()->getNumber()]);

        if ($day) {
            $form->get('number')->addError(new FormError("День #{$form->getData()->getNumber()} уже существует"));
            return false;
        }
        
        return true;
    }


    public function deleteVideoAction(Day $day)
    {
        $this->get('app.video_helper')->removeVideo($day, $day->getId());
        $day->setVideoFile(NULL);
        $this->getDoctrine()->getManager()->flush();

       return new Response(1);
    }

    protected function editAction()
    {
        $this->dispatch(EasyAdminEvents::PRE_EDIT);

        $id = $this->request->query->get('id');
        $easyadmin = $this->request->attributes->get('easyadmin');
        $entity = $easyadmin['item'];

        if ($this->request->isXmlHttpRequest() && $property = $this->request->query->get('property')) {
            $newValue = 'true' === mb_strtolower($this->request->query->get('newValue'));
            $fieldsMetadata = $this->entity['list']['fields'];

            if (!isset($fieldsMetadata[$property]) || 'toggle' !== $fieldsMetadata[$property]['dataType']) {
                throw new \RuntimeException(sprintf('The type of the "%s" property is not "toggle".', $property));
            }

            $this->updateEntityProperty($entity, $property, $newValue);

            // cast to integer instead of string to avoid sending empty responses for 'false'
            return new Response((int) $newValue);
        }

        $fields = $this->entity['edit']['fields'];

        $editForm = $this->executeDynamicMethod('create<EntityName>EditForm', array($entity, $fields));
        $deleteForm = $this->createDeleteForm($this->entity['name'], $id);
        
        $editForm->handleRequest($this->request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->dispatch(EasyAdminEvents::PRE_UPDATE, array('entity' => $entity));
            $this->executeDynamicMethod('preUpdate<EntityName>Entity', array($entity, true));
            $entity->updateMark() ;
            $this->executeDynamicMethod('update<EntityName>Entity', array($entity, $editForm));

            $this->dispatch(EasyAdminEvents::POST_UPDATE, array('entity' => $entity));

            return $this->redirectToReferrer();
        }

        $this->dispatch(EasyAdminEvents::POST_EDIT);

        $parameters = array(
            'form' => $editForm->createView(),
            'entity_fields' => $fields,
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        );

        $template = '@EasyAdmin/day/edit.html.twig';


        return $this->executeDynamicMethod('render<EntityName>Template', array('edit',
            $template,
            $parameters));
    }


    public function aboutChallengeAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository(CompanyPage::class)->findOneBy(['slug' => 'about-challenge']);
//        dump($page); die;

        $request->query->set('entity', 'CompanyPageSecond');
        $request->query->set('action', 'edit');
        $request->query->set('id', $entity->getId());
        $this->initialize($request);

        $fields = $this->entity['edit']['fields'];
        $form = $this->executeDynamicMethod('create<EntityName>EditForm', array($entity, $fields));
//        dump($form); die;
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid() && $this->checkYoutubeLike($form)) {
            $entity->updateMark();
            $this->em->flush();

            return $this->redirectToRoute('about_challenge_edit');
        }


        $deleteForm = $this->createDeleteForm($this->entity['name'], $entity->getId());

        $parameters = array(
            'form' => $form->createView(),
            'entity_fields' => $fields,
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        );

        $template = '@EasyAdmin/day/about_challenge.html.twig';

        return $this->executeDynamicMethod('render<EntityName>Template', array('edit',
            $template,
            $parameters));
    }

    protected function checkYoutubeLike(Form $form)
    {
        $data = $form->getData();
        $youtubeHelper = new YoutubeHelper();
        if ($data->getYoutubeLink() && !$youtubeHelper->videoExists($data->getYoutubeLink())) {
            $form->get('youtubeLink')->addError(new FormError('Видео не существует'));
            return false;
        }

        $data->setYoutubeLink($youtubeHelper->getEmbedLink($data->getYoutubeLink()));

        return true;
    }

}
<?php

namespace AppBundle\Admin\Controller;

use AppBundle\Admin\Form\Type\AboutChallengeType;
use AppBundle\Admin\Form\Type\PostType;
use AppBundle\Entity\CompanyPage;
use AppBundle\Entity\Day;
use AppBundle\Entity\Landing\Block;
use AppBundle\Entity\Landing\LandingPage;
use AppBundle\Entity\Post;
use AppBundle\Entity\Product;
use AppBundle\Helper\PathHelper;
use AppBundle\Helper\YoutubeHelper;
use function Couchbase\defaultDecoder;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AdminController;
use EasyCorp\Bundle\EasyAdminBundle\Event\EasyAdminEvents;
use EasyCorp\Bundle\EasyAdminBundle\Exception\UndefinedEntityException;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\Translator;

class LandingPageController extends AdminController
{

    /**
     * The method that is executed when the user performs a 'edit' action on an entity.
     *
     * @return Response|RedirectResponse
     */
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
            $this->executeDynamicMethod('update<EntityName>Entity', array($entity, $editForm));
            
                      
            if ($entity->getId() == 1) {
                $this->updateBlocks($entity, $this->request);
                if ($entity->getCssFile() instanceof UploadedFile) {                    
                    $path = $this->moveFile($entity->getCssFile(), $this->getUploadDir($entity), $this->getUploadPath($entity));
                    $entity->setAdditionalCss($path);
                }
                $this->em->flush();
                
                return $this->redirectToRoute('easyadmin', ['entity' => $this->request->get('entity'), 'action' => 'edit', 'id' =>$entity->getId()]);
            }

            $this->dispatch(EasyAdminEvents::POST_UPDATE, array('entity' => $entity));

            return $this->redirectToReferrer();
        }

        $this->dispatch(EasyAdminEvents::POST_EDIT);

        $parameters = array(
            'form' => $editForm->createView(),
            'entity_fields' => $fields,
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
            'blocks' => $entity->getBlocks()
        );

        $template = $this->entity['templates']['edit'];
        if ($entity->getId() == 1) {
            $template = '@EasyAdmin/landing_page/edit.html.twig';
        }

        return $this->executeDynamicMethod('render<EntityName>Template', array('edit', $template , $parameters));
    }

    protected function updateBlocks(LandingPage $landingPage, Request $request)
    {
        $this->updateBlock1($landingPage, $request);
        $this->updateBlock2($landingPage, $request);
        $this->updateBlock3($landingPage, $request);
        $this->updateBlock4($landingPage, $request);
        $this->updateBlock5($landingPage, $request);
        $this->updateBlock6($landingPage, $request);
        $this->updateBlock7($landingPage, $request);
        $this->updateBlock8($landingPage, $request);
        $this->updateBlock9($landingPage, $request);
        $this->updateBlock10($landingPage, $request);
        $this->updateBlock11($landingPage, $request);
        $this->updateBlock12($landingPage, $request);
        $this->updateBlock13($landingPage, $request);
        $this->updateBlock14($landingPage, $request);
        $this->updateBlock15($landingPage, $request);
        $this->updateBlock16($landingPage, $request);
        $this->updateBlock17($landingPage, $request);
        $this->updateBlock18($landingPage, $request);
        $this->updateBlock19($landingPage, $request);
        $this->updateBlock20($landingPage, $request);
    }

    protected function updateBlock1($landingPage, $request)
    {
        list($data, $status) = $this->getDataAndStatusFromBlock($request, 'block1');
        $block = $landingPage->getBlockByPosition(1);
        $block->setData($data)
            ->setStatus($status);
    }

    protected function updateBlock2($landingPage, $request)
    {
        $blockName = 'block2';
        list($data, $status) = $this->getDataAndStatusFromBlock($request, $blockName);
        $files = $this->handleBlockFiles($request, $blockName, $landingPage);
        $data = array_merge($data, $files);
        
        $block = $landingPage->getBlockByPosition(2);
        $block->setData($data)
            ->setStatus($status);
    }

    protected function updateBlock3($landingPage, $request)
    {
        $blockName = 'block3';
        list($data, $status) = $this->getDataAndStatusFromBlock($request, $blockName);
        $files = $this->handleBlockFiles($request, $blockName, $landingPage);
        $data = array_merge($data, $files);

        $block = $landingPage->getBlockByPosition(3);
        $block->setData($data)
            ->setStatus($status);
    }

    protected function updateBlock4($landingPage, $request)
    {
        $blockName = 'block4';
        list($data, $status) = $this->getDataAndStatusFromBlock($request, $blockName);
        $files = $this->handleBlockFiles($request, $blockName, $landingPage);
        $data = array_merge($data, $files);

        $block = $landingPage->getBlockByPosition(4);
        $block->setData($data)
            ->setStatus($status);
    }

    protected function updateBlock6($landingPage, $request)
    {
        $blockName = 'block6';
        list($data, $status) = $this->getDataAndStatusFromBlock($request, $blockName);
        $files = $this->handleBlockFiles($request, $blockName, $landingPage);
        $data = array_merge($data, $files);

        $block = $landingPage->getBlockByPosition(6);
        $block->setData($data)
            ->setStatus($status);
    }

    protected function updateBlock7($landingPage, $request)
    {
        $blockName = 'block7';
        list($data, $status) = $this->getDataAndStatusFromBlock($request, $blockName);
        $files = $this->handleBlockFiles($request, $blockName, $landingPage);
        $data = array_merge($data, $files);

        $block = $landingPage->getBlockByPosition(7);
        $block->setData($data)
            ->setStatus($status);
    }

    protected function updateBlock11($landingPage, $request)
    {
        $blockName = 'block11';
        list($data, $status) = $this->getDataAndStatusFromBlock($request, $blockName);
        $files = $this->handleBlockFiles($request, $blockName, $landingPage);
        $data = array_merge($data, $files);

        $block = $landingPage->getBlockByPosition(11);
        $block->setData($data)
            ->setStatus($status);
    }

    protected function updateBlock12($landingPage, $request)
    {
        $blockName = 'block12';
        list($data, $status) = $this->getDataAndStatusFromBlock($request, $blockName);
        $files = $this->handleBlockFiles($request, $blockName, $landingPage);
        $data = array_merge($data, $files);

        $block = $landingPage->getBlockByPosition(12);
        $block->setData($data)
            ->setStatus($status);
    }

    protected function updateBlock13($landingPage, $request)
    {
        $blockName = 'block13';
        list($data, $status) = $this->getDataAndStatusFromBlock($request, $blockName);
        $files = $this->handleBlockFiles($request, $blockName, $landingPage);
        $data = array_merge($data, $files);

        $block = $landingPage->getBlockByPosition(13);
        $block->setData($data)
            ->setStatus($status);
    }

    protected function updateBlock14($landingPage, $request)
    {
        $blockName = 'block14';
        list($data, $status) = $this->getDataAndStatusFromBlock($request, $blockName);
        $files = $this->handleBlockFiles($request, $blockName, $landingPage);
        $data = array_replace_recursive($data, $files);
        
        $block = $landingPage->getBlockByPosition(14);
        $oldData = $block->getData();
        foreach ($oldData as $key => $item) {
            if (!in_array($key, array_keys($data))) {
                $data[$key] = is_array($item) ? [] : '';
            }
        }

        $block->setData($data)
            ->setStatus($status);
    }

    protected function updateBlock15($landingPage, $request)
    {
        $blockName = 'block15';
        list($data, $status) = $this->getDataAndStatusFromBlock($request, $blockName);
        $files = $this->handleBlockFiles($request, $blockName, $landingPage);
        $data = array_replace_recursive($data, $files);

        $block = $landingPage->getBlockByPosition(15);
        $oldData = $block->getData();
        foreach ($oldData as $key => $item) {
            if (!in_array($key, array_keys($data))) {
                $data[$key] = is_array($item) ? [] : '';
            }
        }

        $block->setData($data)
            ->setStatus($status);
    }

    protected function updateBlock16($landingPage, $request)
    {
        $blockName = 'block16';
        list($data, $status) = $this->getDataAndStatusFromBlock($request, $blockName);
        $files = $this->handleBlockFiles($request, $blockName, $landingPage);
        $data = array_replace_recursive($data, $files);

        $block = $landingPage->getBlockByPosition(16);
        $oldData = $block->getData();
//        dump($data);
//        dump($oldData);
        foreach ($oldData as $key => $item) {
            if (!in_array($key, array_keys($data))) {
                $data[$key] = is_array($item) ? [] : '';
            }
        }

//        dump($data); die;
        
        $block->setData($data)
            ->setStatus($status);
    }

    protected function updateBlock17($landingPage, $request)
    {
        $blockName = 'block17';
        list($data, $status) = $this->getDataAndStatusFromBlock($request, $blockName);
        $files = $this->handleBlockFiles($request, $blockName, $landingPage);
        $data = array_replace_recursive($data, $files);

        $block = $landingPage->getBlockByPosition(17);
        $oldData = $block->getData();
        foreach ($oldData as $key => $item) {
            if (!in_array($key, array_keys($data))) {
                $data[$key] = is_array($item) ? [] : '';
            }
        }

        $block->setData($data)
            ->setStatus($status);
    }

    protected function updateBlock20($landingPage, $request)
    {
        $blockName = 'block20';
        list($data, $status) = $this->getDataAndStatusFromBlock($request, $blockName);
        $files = $this->handleBlockFiles($request, $blockName, $landingPage);
        $data = array_replace_recursive($data, $files);

        $block = $landingPage->getBlockByPosition(20);
        $oldData = $block->getData();
        
//        dump($data);
//        dump($oldData);

        foreach ($oldData as $key => $item) {
            if (!in_array($key, array_keys($data))) {
                $data[$key] = is_array($item) ? [] : '';
            }
        }

//        dump($data); die;
        $block->setData($data)
            ->setStatus($status);
    }

    protected function getUploadDir($entity)
    {
        return rtrim($this->getParameter('app.landing_upload_directory'), '/') .  '/'. $entity->getId() . '/';
    }

    protected function getUploadPath($entity)
    {
        return rtrim($this->getParameter('app.landing_upload_path'), '/') . '/' . $entity->getId() . '/';
    }

    protected function handleBlockFiles($request, $block, $landingPage)
    {
        $data = $request->files->get($block);
        if (!$data) return [];

        $uploadDir = $this->getUploadDir($landingPage);
        $uploadPath = $this->getUploadPath($landingPage);

        return $this->moveFiles($data, $uploadDir, $uploadPath);
    }

    protected function moveFiles($data, $uploadDir, $uploadPath)
    {
        $res = [];

        foreach ($data as $key => $el) {
            if ($el instanceof UploadedFile) {
                $res[$key] = $this->moveFile($el, $uploadDir, $uploadPath);
            } elseif (is_array($el)) {
                $result = $this->moveFiles($el, $uploadDir, $uploadPath);
                if (!empty($result)) {
                    $res[$key] = $result;
                }
            }
        }

        return $res;
    }

    protected function moveFile($file, $uploadDir, $uploadPath)
    {
        $name = uniqid() . mt_rand(0, 1000) . '_' .$file->getClientOriginalName();
        $file->move($uploadDir, $name);
        return $uploadPath . $name;
    }

    protected function updateBlock5($landingPage, $request)
    {
        list($data, $status) = $this->getDataAndStatusFromBlock($request, 'block5');
        $block = $landingPage->getBlockByPosition(5);
        $block->setData($data)
            ->setStatus($status);
    }

    protected function updateBlock8($landingPage, $request)
    {
        list($data, $status) = $this->getDataAndStatusFromBlock($request, 'block8');
        $block = $landingPage->getBlockByPosition(8);
        $block->setData($data)
            ->setStatus($status);
    }

    protected function updateBlock9($landingPage, $request)
    {
        list($data, $status) = $this->getDataAndStatusFromBlock($request, 'block9');
        $block = $landingPage->getBlockByPosition(9);
        $block->setData($data)
            ->setStatus($status);
    }

    protected function updateBlock10($landingPage, $request)
    {
        list($data, $status) = $this->getDataAndStatusFromBlock($request, 'block10');
        $block = $landingPage->getBlockByPosition(10);
        $block->setData($data)
            ->setStatus($status);
    }

    protected function updateBlock18($landingPage, $request)
    {
        list($data, $status) = $this->getDataAndStatusFromBlock($request, 'block18');
        $block = $landingPage->getBlockByPosition(18);
        $block->setData($data)
            ->setStatus($status);
    }

    protected function updateBlock19($landingPage, $request)
    {
        list($data, $status) = $this->getDataAndStatusFromBlock($request, 'block19');
        $block = $landingPage->getBlockByPosition(19);
        $block->setData($data)
            ->setStatus($status);
    }

    protected function getDataAndStatusFromBlock($request, $name)
    {
        $data = $request->request->get($name);
        if (isset($data['status'])) {
            $status =  Block::STATUS_VISIBLE;
            unset($data['status']);
        } else {
            $status = Block::STATUS_HIDDEN;
        }

        return [$data, $status];
    }

    public function previewAction(LandingPage $landingPage)
    {
        $page = $this->getDoctrine()->getManager()->getRepository(LandingPage::class)->find(1);

        return $this->render('widgets/landing/view.html.twig', [
            'page' => $page,
            'blocks' => $page->getBlocks(),
            'products' => $this->getDoctrine()->getManager()->getRepository(Product::class)->findAll(),
        ]);
    }
//
//    /**
//     * @Route("/view-landing")
//     */
//    public function viewLandingAction()
//    {
//
//    }



}
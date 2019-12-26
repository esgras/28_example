<?php

namespace AppBundle\Admin\Controller;

use AppBundle\Admin\Form\Type\PostType;
use AppBundle\Entity\Feedback;
use AppBundle\Entity\Post;
use AppBundle\Helper\PathHelper;
use AppBundle\Helper\YoutubeHelper;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AdminController;
use EasyCorp\Bundle\EasyAdminBundle\Event\EasyAdminEvents;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class FeedbackController extends AdminController
{
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

        $template = '@EasyAdmin/feedback/edit.html.twig';


        return $this->executeDynamicMethod('render<EntityName>Template', array('edit',
            $template,
            $parameters));
    }

    public function deleteImageAction(Feedback $feedback)
    {
        $imageHelper = $this->get('app.image_helper');
        $imageHelper->removeCollection($feedback);


        $feedback->setImageFile(NULL);
        $url = $this->generateUrl('easyadmin', ['entity' => 'Feedback', 'id' => $feedback->getId(),
            'action' => 'edit']);

        $feedback->setLinkPreview((new YoutubeHelper())->getImagePathForLink($feedback->getLink()));

        $this->getDoctrine()->getManager()->flush();

        return new RedirectResponse($url);
    }
}
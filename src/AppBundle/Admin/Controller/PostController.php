<?php

namespace AppBundle\Admin\Controller;

use AppBundle\Admin\Form\Type\PostType;
use AppBundle\Entity\Post;
use AppBundle\Helper\PathHelper;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AdminController;
use EasyCorp\Bundle\EasyAdminBundle\Event\EasyAdminEvents;
use Symfony\Component\HttpFoundation\Request;

class PostController extends AdminController
{
//    protected function newAction()
//    {
//        $post = new Post();
//        $request = Request::createFromGlobals();
//        $form = $this->createForm(PostType::class);
//
////        $postForm = $this->createForm(PostCreateType::class, null, [
////            'countries' => $countries,
////            'danceStyles' => $danceStyles,
////            'paymentMethods' => $paymentMethods
////        ]);
////        $postForm->handleRequest($this->request);
////
////        $imageExistsAndIsValid = $this->checkImageSize($user, $userForm);
////
////        if ($userForm->isSubmitted() && $userForm->isValid()
//////            && $imageExistsAndIsValid
////        ) {
////            $data = $userForm->getData();
////
////            $location = $this->getAddressLocation($data);
////            $existUser = $this->em->getRepository(User::class)->findOneBy(['email' => $data['email']]);
////
////            // Address is full and not found...
////            if (empty($location)) {
////                $key = $data['address'] ? 'address' : 'city';
////                $userForm->get($key)->addError(new FormError('Address not found. Enter real address.'));
////            } elseif ($existUser instanceof User) {
////                $userForm->get('email')->addError(new FormError('This Email is already in use.'));
////            } else {
////                list($user, $password) = $this->setNewUserData($user, $data, $request);
////                $this->updateNewUserStyles($user, $data);
////                $this->setNewUserAddress($user, $location, $data);
////
////                $data['setPrices'] = false;
////                $this->updateProfileData($user, $data, $userForm);
////                $this->em->persist($user);
////
////                $hash = $this->get('app.string_helper')->generateHash();
////                $user->setHash($hash);
////                $this->get('event_dispatcher')->dispatch('app.register_event', new RegisterEvent($user, User::REGISTERED_FROM_DASHBOARD));
////                $this->em->flush();
////
////                $this->sendCreateFromDashboardEmailMessage($user, $password, $hash);
////                $this->em->flush();
////
////                $userId = $user->getId();
////                if ($user->isTeacher()) {
////                    return $this->redirectToRoute('dashboard_teacher_offers', ['id' => $userId]);
////                } else {
////                    return $this->redirectToRoute('easyadmin', ['action' => 'edit', 'entity' => 'User', 'id' => $userId]);
////                }
////            }
////        }
//
//        $view = '@EasyAdmin/post/new.html.twig';
//        $this->dispatch(EasyAdminEvents::POST_EDIT);
////        $requestData = $request->request->all();
////        $achievements = !empty($requestData['achivements']) ? $requestData['achivements'] : [];
////        $education = !empty($requestData['education']) ? $requestData['education'] : [];
//
//
//        return $this->render($view, array(
//            'form' => $form->createView(),
//            'entity_fields' => $this->entity['edit']['fields'],
////            'achievements' => $achievements,
////            'education' => $education,
//            'entity' => $post,
////            'youtube' => $youtube
//        ));
//    }

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

        $template = '@EasyAdmin/post/edit.html.twig';


        return $this->executeDynamicMethod('render<EntityName>Template', array('edit',
            $template,
            $parameters));
    }
}
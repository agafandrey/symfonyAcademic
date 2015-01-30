<?php

namespace Academic\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Academic\UserBundle\Entity\User;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class UserController extends Controller
{
    /**
     * @Route("/list", name="user_list")
     * @Template("AcademicUserBundle:User:userlist.html.twig")
     */
    public function userListAction(Request $request)
    {
        $user = $this->get('security.context')->getToken()->getUser();

        if ($user->getRole()->getRole() !== 'ROLE_ADMIN') {
            $request->getSession()->getFlashBag()->add(
                'notice',
                'Unauthorised access!'
            );
            return $this->redirect($this->generateUrl('index'));
        }

        $repo = $this->getDoctrine()->getRepository('AcademicUserBundle:User');
        $users = $repo->findAll();

        return array('users' => $users);
    }

    /**
     * @Route("/create", name="user_create")
     * @Template("AcademicUserBundle:User:update.html.twig")
     */
    public function userCreateAction(Request $request)
    {
        $user = new User();

        if (false === $this->get('security.context')->isGranted('create', $user)) {
            $request->getSession()->getFlashBag()->add(
                'notice',
                'Unauthorised access!'
            );
            return $this->redirect($this->generateUrl('index'));
        }

        $form = $this->createFormBuilder($user)
            ->add('username', 'text', array('label' => 'User Name'))
            ->add('password', 'password', array('label' => 'Password'))
            ->add('fullname', 'text', array('label' => 'Full Name'))
            ->add('file', 'file', array('label' => 'Avatar', 'required' => false))
            ->add('email', 'email', array('label' => 'Email'))
            ->add('role', 'entity', array(
                'class' => 'AcademicUserBundle:Role',
                'property' => 'name',
            ))
            ->add('timezone', 'timezone')
            ->add('save', 'submit', array('label' => 'Save User'))
            ->getForm();

        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        if ($form->isValid()) {
            $checkUsername = $user->getUsername();
            $checkUser = $this->getDoctrine()->getRepository('AcademicUserBundle:User')
                ->loadUserByUsername($checkUsername);

            if ($checkUser->getId()) {
                $request->getSession()->getFlashBag()->add(
                    'notice',
                    'The user already exists'
                );
                return $this->redirect($this->generateUrl('user_list'));
            }

            $password = $user->getPassword();

            $encoder = $this->container
                ->get('security.encoder_factory')
                ->getEncoder($user);

            $user->setPassword($encoder->encodePassword($password, $user->getSalt()));
            $user->upload();

            $em->persist($user);
            $em->flush();
            $request->getSession()->getFlashBag()->add(
                'notice',
                'The user was saved!'
            );

            return $this->redirect($this->generateUrl('user_profile', array('user' => $user->getId())));
        }

        return array('form' => $form->createView());
    }

    /**
     * @Route("/profile/{user}", name="user_profile", defaults={"user"=0})
     * @Template("AcademicUserBundle:User:userprofile.html.twig")
     */
    public function userProfileAction(Request $request)
    {
        $id = $request->query->get('user') ? $request->query->get('user') : $request->get('user');
        if ($id) {
            $repo = $this->getDoctrine()->getRepository('AcademicUserBundle:User');
            $user = $repo->findOneById($id);
            if (!$user) {
                $request->getSession()->getFlashBag()->add(
                    'error',
                    'The user was not found!'
                );
                return $this->redirect($this->generateUrl('index'));
            }
        } else {
            $user = $this->get('security.context')->getToken()->getUser();
        }

        $isEditGranted = false;
        if (false !== $this->get('security.context')->isGranted('edit', $user)) {
            $isEditGranted = true;
        }
        return array('user' => $user, 'is_edit_granted' => $isEditGranted);
    }

    /**
     * @Route("/edit/{user}", name="user_edit")
     * @Template("AcademicUserBundle:User:update.html.twig")
     */
    public function userEditAction(Request $request)
    {
        $currentUser = $this->get('security.context')->getToken()->getUser();

        if (false === $this->get('security.context')->isGranted('edit', $currentUser)) {
            $request->getSession()->getFlashBag()->add(
                'notice',
                'Unauthorised access!'
            );
            return $this->redirect($this->generateUrl('index'));
        }

        $userId = $request->query->get('user') ? $request->query->get('user') : $request->get('user');

        if ($userId) {
            $repo = $this->getDoctrine()->getRepository('AcademicUserBundle:User');
            $user = $repo->findOneById($userId);
            if (!$user) {
                $request->getSession()->getFlashBag()->add(
                    'notice',
                    'The user was not found!'
                );
                return $this->redirect($this->generateUrl('index'));
            }
        } else {
            return $this->redirect($this->generateUrl('index'));
        }

        $formBuilder = $this->createFormBuilder($user)
            ->add('username', 'text', array('label' => 'User Name'))
            ->add('fullname', 'text', array('label' => 'Full Name'))
            ->add('file', 'file', array('label' => 'Avatar', 'required' => false))
            ->add('email', 'email', array('label' => 'Email'))
            ->add('timezone', 'timezone');

        if ($currentUser->getRole()->getRole() === 'ROLE_ADMIN') {
            $formBuilder->add('role', 'entity', array(
                'class' => 'AcademicUserBundle:Role',
                'property' => 'name',
            ));
        }

        $formBuilder->add('save', 'submit', array('label' => 'Save User'));
        $form = $formBuilder->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $checkUsername = $user->getUsername();
            $checkUser = $this->getDoctrine()->getRepository('AcademicUserBundle:User')
                ->loadUserByUsername($checkUsername);
            if ($checkUser->getId() && $checkUser->getId() != $user->getId()) {
                $request->getSession()->getFlashBag()->add(
                    'notice',
                    'The user already exists'
                );
                return $this->redirect($this->generateUrl('user_list'));
            }
            $user->upload();
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $request->getSession()->getFlashBag()->add(
                'notice',
                'The user was saved!'
            );

            return $this->redirect($this->generateUrl('user_profile', array('user' => $user->getId())));
        }

        return array('form' => $form->createView());
    }
}

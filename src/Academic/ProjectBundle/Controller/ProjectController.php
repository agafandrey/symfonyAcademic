<?php

namespace Academic\ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Academic\ProjectBundle\Entity\Project;
use Academic\UserBundle\Entity\User;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;


class ProjectController extends Controller
{

    /**
     * @Route("/projectlist", name="project_list")
     * @Template
     */
    public function projectlistAction(Request $request)
    {
        $project = new Project();

        $isCreateGranted = $this->get('security.context')->isGranted('create', $project);

        $repo = $this->getDoctrine()->getRepository('AcademicProjectBundle:Project');
        $projects = $repo->findAll();

        return array('projects' => $projects, 'is_create_granted' => $isCreateGranted);
    }

    /**
     * @Route("/projectcreate", name="project_create")
     * @Template ("AcademicProjectBundle:Project:update.html.twig")
     */
    public function projectcreateAction(Request $request)
    {
        $project = new Project();

        if (false === $this->get('security.context')->isGranted('create', $project)) {
            $request->getSession()->getFlashBag()->add(
                'notice',
                'Unauthorised access!'
            );
            return $this->redirect($this->generateUrl('project_list'));
        }

        $form = $this->createFormBuilder($project)
            ->add('name', 'text', array('label' => 'Project Label'))
            ->add('summary', 'textarea',  array('label' => 'Summary'))
            ->add('save', 'submit', array('label' => 'Save'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($project);
            $em->flush();

            $request->getSession()->getFlashBag()->add(
                'notice',
                'The project was saved!'
            );

            return $this->redirect($this->generateUrl('project_profile', array('project' => $project->getId())));
        }

        return array('form' => $form->createView());
    }

    /**
     * @Route("/projectedit", name="project_edit")
     * @Template("AcademicProjectBundle:Project:update.html.twig")
     */
    public function projecteditAction(Request $request)
    {
        $project = $this->getProjectToEdit($request);
        if(!$project->getId()){
            return $this->redirect($this->generateUrl('project_list'));
        }

        $form = $this->createFormBuilder($project)
            ->add('name', 'text', array('label' => 'Project Label'))
            ->add('summary', 'textarea',  array('label' => 'Summary'))
            ->add('save', 'submit', array('label' => 'Save'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($project);
            $em->flush();

            $request->getSession()->getFlashBag()->add(
                'notice',
                'The project was saved!'
            );

            return $this->redirect($this->generateUrl('project_profile', array('project' => $project->getId())));
        }

        return array('form' => $form->createView());
    }

    /**
     * @Route("/projectprofile", name="project_profile")
     * @Template
     */
    public function projectprofileAction(Request $request)
    {
        $id = $request->query->get('project');
        if ($id){
            $repo = $this->getDoctrine()->getRepository('AcademicProjectBundle:Project');
            $project = $repo->findOneById($id);
            if(!$project) {
                $request->getSession()->getFlashBag()->add(
                    'error',
                    'The user was not found!'
                );
                return $this->redirect($this->generateUrl('project_list'));
            }
        } else {
            return $this->redirect($this->generateUrl('project_list'));
        }

        if (false === $this->get('security.context')->isGranted('view', $project)) {
            $request->getSession()->getFlashBag()->add(
                'notice',
                'Unauthorized access!'
            );
            return $this->redirect($this->generateUrl('project_list'));
        }

        $isEditGranted = $this->get('security.context')->isGranted('create', $project);
        return array('project' => $project, 'is_edit_granted' => $isEditGranted);
    }

    /**
     * @Route("/projectparticipants", name="project_participant")
     * @Template("AcademicProjectBundle:Project:project_participant.html.twig")
     */
    public function projectparticipantsAction(Request $request)
    {
        $project = $this->getProjectToEdit($request);
        if(!$project->getId()){
            return $this->redirect($this->generateUrl('project_list'));
        }

        $participants = $project->getParticipant();

        $isEditGranted = false;
        if (false !== $this->get('security.context')->isGranted('edit', $project)) {
            $isEditGranted = true;
        }
        return array('participants' => $participants, 'is_edit_granted' => $isEditGranted, 'project' => $project);

    }

    /**
     * @Route("/removeparticipant", name="remove_participant")
     */
    public function removeparticipantAction(Request $request)
    {
        $participantId = $request->query->get('participant');
        $project = $this->getProjectToEdit($request);
        if(!$project->getId()){
            return $this->redirect($this->generateUrl('project_list'));
        }

        if ($participantId){
            $userRepo = $this->getDoctrine()->getRepository('AcademicUserBundle:User');
            $user = $userRepo->findOneById($participantId);
            if(!$user) {
                $request->getSession()->getFlashBag()->add(
                    'notice',
                    'The user is not found'
                );
                return $this->redirect($this->generateUrl('project_list'));
            }
        } else {
            return $this->redirect($this->generateUrl('project_list'));
        }


        $project->removeParticipant($user);
        $request->getSession()->getFlashBag()->add(
            'notice',
            'The user '.$user->getFullname().' is removed from project.'
        );

        $em = $this->getDoctrine()->getManager();
        $em->persist($project);
        $em->flush();
        return $this->redirect($this->generateUrl('project_participant', array('project' => $project->getId())));
    }

    /**
     * @Route("/chooseparticipant", name="choose_participant")
     * @Template("AcademicProjectBundle:Project:choose_participant.html.twig")
     */
    public function chooseparticipantAction(Request $request)
    {
        $project = $this->getProjectToEdit($request);
        if(!$project){
            return $this->redirect($this->generateUrl('project_list'));
        }

        $nonParticipants = $this->getDoctrine()->getRepository('AcademicProjectBundle:Project')->getNonParticipants($project->getId());

        return array('project' => $project, 'nonParticipants' => $nonParticipants);
    }

    /**
     * @Route("/assignparticipant", name="assign_participant")
     * @Template("AcademicProjectBundle:Project:assign_participant.html.twig")
     *
     */
    public function assignparticipantAction(Request $request)
    {
        $project = $this->getProjectToEdit($request);
        $participants = $request->get('participant');
        if (!$project->getId() || !$participants){
            return $this->redirect($this->generateUrl('project_list'));
        }

        $repo = $this->getDoctrine()->getRepository('AcademicUserBundle:User');
        $userCount = 0;
        foreach ($participants as $participantId){
            $user = $repo->findOneById($participantId);
            if(!$user) {
                $request->getSession()->getFlashBag()->add(
                    'notice ',
                    'The user was not found!'
                );
                return $this->redirect($this->generateUrl('project_list'));
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($project);
            $project->addParticipant($user);
            $userCount++;
        }

        $em->flush();

        $request->getSession()->getFlashBag()->add(
            'notice',
            $userCount . ' users have been added to ' . $project->getName()
        );

        return $this->redirect($this->generateUrl('project_participant', array('project' => $project->getId())));
    }

    private function getProjectToEdit(Request $request)
    {
        $projectId = $request->query->get('project') ? $request->query->get('project') : $request->get('project');

        $project = new Project();
        if ($projectId){
            $projectRepo = $this->getDoctrine()->getRepository('AcademicProjectBundle:Project');
            $result = $projectRepo->findOneById($projectId);
            if($result) {
                if (false === $this->get('security.context')->isGranted('edit', $result)) {
                    $request->getSession()->getFlashBag()->add(
                        'notice',
                        'Unauthorised access!'
                    );

                } else {
                    $project = $result;
                }
            } else {
                $request->getSession()->getFlashBag()->add(
                    'notice',
                    'The project is not found'
                );
            }
        }

        return $project;
    }
}

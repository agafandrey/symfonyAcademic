<?php

namespace Academic\ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Academic\ProjectBundle\Entity\Project;
use Academic\ProjectBundle\Entity\Issue;
use Academic\ProjectBundle\Entity\Issue\Comment;
use Symfony\Component\HttpFoundation\JsonResponse;
use Academic\ProjectBundle\Entity\IssueStatus;
use Academic\ProjectBundle\Entity\IssuePriority;
use Academic\ProjectBundle\Entity\IssueResolution;
use Academic\UserBundle\Entity\User;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;


class IssueController extends Controller
{

    /**
     * @Route("/issuelist", name="issue_list")
     * @Template
     */
    public function issuelistAction(Request $request)
    {
        $project = $this->getProject($request);

        $repo = $this->getDoctrine()->getRepository('AcademicProjectBundle:Issue');
        $issues = $repo->findByProject($project->getId());

        return array('issues' => $issues, 'project' => $project);

    }

    /**
     * @Route("/issuecreate", name="issue_create")
     * @Template ("AcademicProjectBundle:Issue:update.html.twig")
     */
    public function issuecreateAction(Request $request)
    {
        $project = $this->getProject($request);

        $issue = new Issue();

        $parrentIssueId = $request->query->get('parentissue');

        if ($parrentIssueId){
            $repo = $this->getDoctrine()->getRepository('AcademicProjectBundle:Issue');
            /** @var Issue $parentIssue */
            $parentIssue = $repo->findOneById($parrentIssueId);
            if(!$parentIssue || $parentIssue->getType() !== 'STORY') {
                $request->getSession()->getFlashBag()->add(
                    'notice',
                    'It\'s impossible to create sub task'
                );
                return $this->redirect($this->generateUrl('project_list'));
            }
            $project = $parentIssue->getProject();
            $parentIssue->addChildIssue($issue);
            //$issue->setParentIssue($parentIssue);
            $typeOptions = $this->prepareTypeOptions($issue->getAvailableTypesSubtask());
        } else {
            $typeOptions = $this->prepareTypeOptions($issue->getAvailableTypes());
        }

        $form = $this->createFormBuilder($issue)
            ->add('type', 'choice', array(
                'choices' => $typeOptions
                )
            )
            ->add('summary', 'text',  array('label' => 'Summary'))
            ->add('priority', 'entity', array(
                'class' => 'AcademicProjectBundle:IssuePriority',
                'property' => 'label',
            ))
            ->add('assignee', 'entity', array(
                'class' => 'AcademicUserBundle:User',
                'choices' => $project->getParticipant(),
                'property' => 'fullname',
                'label' => 'Assign To'
            ))
            ->add('description', 'textarea',  array('label' => 'Description'))
            ->add('save', 'submit', array('label' => 'Save'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $words = preg_split("/\s+/", $project->getName());
            $issueNumber = $project->getIssues()->count();
            $code = "";
            foreach ($words as $w) {
                $code .= strtoupper($w[0]);
            }
            $code .= $issueNumber + 1;
            $reporter = $this->get('security.context')->getToken()->getUser();
            $openedStatus = $this->getDoctrine()
                ->getRepository('AcademicProjectBundle:Issue')
                ->getOpenStatus();
            $notResolvedResolution = $this->getDoctrine()
                ->getRepository('AcademicProjectBundle:Issue')
                ->getResolutionUnResolved();

            $issue->setProject($project);
            $issue->setStatus($openedStatus);
            $issue->setResolution($notResolvedResolution);
            $issue->setReporter($reporter);
            $issue->setCode($code);
            $em = $this->getDoctrine()->getManager();
            $em->persist($issue);
            $em->flush();

            $request->getSession()->getFlashBag()->add(
                'notice',
                'The Issue was saved!'
            );

            return $this->redirect($this->generateUrl('issue_profile', array('issue' => $issue->getId())));
        }

        return array('form' => $form->createView());

    }

    /**
     * @Route("/issueedit", name="issue_edit")
     * @Template("AcademicProjectBundle:Issue:update.html.twig")
     */
    public function issueeditAction(Request $request)
    {
        $issue = $this->getIssue($request);
        $project = $issue->getProject();

        if ($issue->getType() === 'STORY'){
            $typeOptions = $this->prepareTypeOptions($issue->getAvailableTypesSubtask());
        } else {
            $typeOptions = $this->prepareTypeOptions($issue->getAvailableTypes());
        }

        $form = $this->createFormBuilder($issue)
            ->add('type', 'choice', array(
                    'choices' => $typeOptions
                )
            )
            ->add('summary', 'text',  array('label' => 'Summary'))
            ->add('priority', 'entity', array(
                'class' => 'AcademicProjectBundle:IssuePriority',
                'property' => 'label',
            ))
            ->add('assignee', 'entity', array(
                'class' => 'AcademicUserBundle:User',
                'choices' => $project->getParticipant(),
                'property' => 'fullname',
                'label' => 'Assign To'
            ))
            ->add('description', 'textarea',  array('label' => 'Description'))
            ->add('save', 'submit', array('label' => 'Save'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $openedStatus = $this->getDoctrine()
                ->getRepository('AcademicProjectBundle:Issue')
                ->getOpenStatus();
            $notResolvedResolution = $this->getDoctrine()
                ->getRepository('AcademicProjectBundle:Issue')
                ->getResolutionUnResolved();
            $issue->setStatus($openedStatus);
            $issue->setResolution($notResolvedResolution);
            $em = $this->getDoctrine()->getManager();
            $em->persist($issue);
            $em->flush();

            $request->getSession()->getFlashBag()->add(
                'notice',
                'The Issue was saved!'
            );

            return $this->redirect($this->generateUrl('issue_profile', array('issue' => $issue->getId())));
        }

        return array('form' => $form->createView());
    }

    /**
     * @Route("/issueprofile", name="issue_profile")
     * @Template
     */
    public function issueprofileAction(Request $request)
    {
        $issue = $this->getIssue($request);

        $project = $issue->getProject();

        if (false === $this->get('security.context')->isGranted('view', $project)) {
            $request->getSession()->getFlashBag()->add(
                'notice',
                'Unauthorized access!'
            );
            return $this->redirect($this->generateUrl('project_list'));
        }

        $isStory = false;
        if ($issue->getType() === 'STORY'){
            $isStory = true;
        }

        return array('issue' => $issue, 'project' => $project, 'isStory' => $isStory);
    }

    private function getProject(Request $request)
    {
        $projectId = $request->query->get('project') ? $request->query->get('project') : $request->get('project');

        $project = new Project();
        if ($projectId) {
            $projectRepo = $this->getDoctrine()->getRepository('AcademicProjectBundle:Project');
            $project = $projectRepo->findOneById($projectId);
            if (!$project) {
                $request->getSession()->getFlashBag()->add(
                    'notice',
                    'The project is not found'
                );
            }
        }

        //validate project permission
        if (false === $this->get('security.context')->isGranted('view', $project)) {
            $request->getSession()->getFlashBag()->add(
                'notice',
                'Unauthorised access!'
            );
            return $this->redirect($this->generateUrl('project_list'));
        }

        return $project;
    }

    private function prepareTypeOptions(array $types)
    {
        $options = array();
        foreach($types as $type){
            $options[$type['code']] = $type['label'];
        }

        return $options;
    }

    private  function getIssue(Request $request)
    {
        $issueId = $request->query->get('issue') ? $request->query->get('issue') : $request->get('issue');
        if ($issueId){
            $repo = $this->getDoctrine()->getRepository('AcademicProjectBundle:Issue');
            $issue = $repo->findOneById($issueId);
            if(!$issue ) {
                $request->getSession()->getFlashBag()->add(
                    'notice',
                    'The issue was not found!'
                );
                return $this->redirect($this->generateUrl('project_list'));
            }
        } else {
            return $this->redirect($this->generateUrl('project_list'));
        }

        return $issue;
    }

    /**
     * @Route("/processcomment", name="process_comment")
     */
    public function processcommentAction(Request $request)
    {
        $error = false;
        $issueId = $request->get('issue');
        $commentId = $request->get('comment');
        $commentBody = $request->get('comment_body');
        $response = array();
        if ($issueId){
            $repo = $this->getDoctrine()->getRepository('AcademicProjectBundle:Issue');
            $issue = $repo->findOneById($issueId);
            $comment = new Comment();
            if(!$issue ) {
                $error = true;
            }
            $response['new'] = true;
        } elseif ($commentId) {
            $repo = $this->getDoctrine()->getRepository('AcademicProjectBundle:Issue\Comment');
            $comment = $repo->findOneById($commentId);
            $issue = $comment->getIssue();
            if(!$comment ) {
                $error = true;
            }
        }

        if (!$error) {
            $user = $this->get('security.context')->getToken()->getUser();
            $comment->setIssue($issue);
            $comment->setUser($user);
            $comment->setBody($commentBody);
            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();
            $response['success'] = true;
            $response['comment_id'] = $comment->getId();
            $response['comment_html'] = $this->renderView('AcademicProjectBundle:Issue/Comment:comment.html.twig', array('comment' => $comment));
        } else {
            $response['success'] = false;
        }

        return new JsonResponse($response);
    }
}
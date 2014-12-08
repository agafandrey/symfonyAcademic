<?php
// src/Academic\ProjectBundle\Controller\UserBundle\IndexController.php
namespace Academic\ProjectBundle\Controller\UserBundle;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Academic\UserBundle\Controller\UserController as BaseUserController;

class UserController extends BaseUserController
{
    /**
     * @Route("/userprofile", name="user_profile")
     * @Template("AcademicProjectBundle:UserBundle\User:userprofile.html.twig")
     */
    public function userprofileAction(Request $request)
    {

        $returnArray = parent::userprofileAction($request);
        $user = $returnArray['user'];

        $activityRepo = $this->getDoctrine()->getRepository('AcademicProjectBundle:Issue\Activity');
        $userActivities =  $activityRepo->getUserActivities($user->getId());

        $issueRepo = $this->getDoctrine()->getRepository('AcademicProjectBundle:Issue');
        $userIssues =  $issueRepo->getAssigneeIssues($user->getId());

        $returnArray['userActivities'] = $userActivities;
        $returnArray['userIssues'] = $userIssues;

        return $returnArray;

    }
}
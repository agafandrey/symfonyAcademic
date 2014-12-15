<?php
// src/Academic\ProjectBundle\Controller\UserBundle\IndexController.php
namespace Academic\ProjectBundle\Controller\UserBundle;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Academic\UserBundle\Controller\IndexController as BaseController;

class IndexController extends BaseController
{
    /**
     * @Route("/", name="index")
     * @Template("AcademicProjectBundle:UserBundle\Index:index.html.twig")
     */
    public function indexAction(Request $request)
    {

        parent::indexAction($request);
        $currentUser = $this->get('security.context')->getToken()->getUser();

        $projectRepo = $this->getDoctrine()->getRepository('AcademicProjectBundle:Project');
        $userProjects =  $projectRepo->getUserProjects($currentUser->getId());

        $issueRepo = $this->getDoctrine()->getRepository('AcademicProjectBundle:Issue');
        $userIssues =  $issueRepo->getCollaboratorIssues($currentUser->getId());
        return array(
            'userProjects' => $userProjects,
            'userIssues' => $userIssues,
        );

    }
}

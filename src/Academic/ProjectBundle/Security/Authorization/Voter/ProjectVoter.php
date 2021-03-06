<?php
// src/Academic/ProjectBundle/Security/Authorization/Voter/ProjectVoter.php
namespace Academic\ProjectBundle\Security\Authorization\Voter;

use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class ProjectVoter implements VoterInterface
{
    const VIEW = 'view';
    const EDIT = 'edit';
    const CREATE = 'create';

    public function supportsAttribute($attribute)
    {
        return in_array($attribute, array(
            self::VIEW,
            self::EDIT,
            self::CREATE,
        ));
    }

    public function supportsClass($class)
    {
        $supportedClass = 'Academic\ProjectBundle\Entity\Project';

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    /**
     * @var $token
     * @var \Academic\ProjectBundle\Entity\Project $project
     * @var array $attributes
     * @return  bool
     */
    public function vote(TokenInterface $token, $project, array $attributes)
    {
        // check if class of this object is supported by this voter
        if (!$this->supportsClass(get_class($project))) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        // set the attribute to check against
        $attribute = $attributes[0];

        // check if the given attribute is covered by this voter
        if (!$this->supportsAttribute($attribute)) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        // get current logged in user
        $currentUser = $token->getUser();
        $userRole = $currentUser->getRole()->getRole();

        // make sure there is a user object (i.e. that the user is logged in)
        if (!$currentUser instanceof UserInterface) {
            return VoterInterface::ACCESS_DENIED;
        }

        switch($attribute) {
            case self::VIEW:
                if ($userRole === 'ROLE_ADMIN'
                    || $userRole === 'ROLE_MANAGER'
                    || $project->isParticipant($currentUser->getId())) {
                    return VoterInterface::ACCESS_GRANTED;
                }
                break;

            case self::EDIT:
                if ($userRole === 'ROLE_ADMIN' || $userRole === 'ROLE_MANAGER') {
                    return VoterInterface::ACCESS_GRANTED;
                }
                break;

            case self::CREATE:
                if ($userRole === 'ROLE_ADMIN' || $userRole === 'ROLE_MANAGER') {
                    return VoterInterface::ACCESS_GRANTED;
                }
                break;
        }

        return VoterInterface::ACCESS_DENIED;
    }
}

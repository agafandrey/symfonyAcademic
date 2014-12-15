<?php
// src/Academic/UserBundle/Entity/User.php
namespace Academic\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
* Academic\UserBundle\Entity\User
*
* @ORM\Table(name="users")
* @ORM\Entity(repositoryClass="Academic\UserBundle\Entity\UserRepository")
*/
class User implements UserInterface, \Serializable
{
    public function __construct()
    {
        $this->isActive = true;
        $this->salt = md5(uniqid(null, true));
    }

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=25, unique=true)
     * @Assert\NotBlank()
     */
    private $username;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=64)
     */
    private $password;

    /**
     * @Assert\Email(
     *  message = "The email '{{ value }}' is not a valid email.",
     *  checkMX = true
     * )
     * @ORM\Column(type="string", length=60, unique=true)
     */
    private $email;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=60)
     */
    private $fullname;

    /**
     * @ORM\Column(type="string", length=60, nullable=true)
     */
    private $avatar;

    /**
     * @ORM\ManyToOne(targetEntity="Role")
     *
     */
    private $role;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    private $salt;

    /**
     * @Assert\File(maxSize="6000000")
     */
    private $file;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=64)
     */
    private $timezone;

    /**
     * @inheritDoc
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @inheritDoc
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @inheritDoc
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {
    }

    /**
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            $this->role,
            $this->salt,
        ));
    }

    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            $this->role,
            $this->salt,
            ) = unserialize($serialized);
    }

    /**
     * @inheritDoc
     */
    public function getRoles()
    {
        return array($this->role->getRole());
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {

        $this->password = $password;

        return $this;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set fullname
     *
     * @param string $fullname
     * @return User
     */
    public function setFullname($fullname)
    {
        $this->fullname = $fullname;

        return $this;
    }

    /**
     * Get fullname
     *
     * @return string
     */
    public function getFullname()
    {
        return $this->fullname;
    }

    /**
     * Set avatar
     *
     * @param string $avatar
     * @return User
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * Get avatar
     *
     * @return string
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * Set role
     *
     * @param string $role
     * @return User
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return User
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    public function isEnabled()
    {
        return $this->isActive;
    }

    public function getAbsolutePath()
    {
        return null === $this->avatar
            ? null
            : $this->getUploadRootDir().'/'.$this->avatar;
    }

    public function getAvatarWebPath()
    {
        return null === $this->avatar
            ? null
            : $this->getUploadDir().'/'.$this->avatar;
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/avatars';
    }

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    public function upload()
    {
        if (null === $this->getFile()) {
            return;
        }

        $avatarName = 'avatar_'.$this->getUsername().'.'.$this->getFile()->getClientOriginalExtension();
        $this->getFile()->move(
            $this->getUploadRootDir(),
            $avatarName
        );

        $this->avatar = $avatarName;
        $this->file = null;
        return $this;
    }

    /**
     * Set timezone
     *
     * @param string $timezone
     * @return User
     */
    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;

        return $this;
    }

    /**
     * Get timezone
     *
     * @return string
     */
    public function getTimezone()
    {
        return $this->timezone;
    }
}

<?php
// src/Academic/UserBundle/Tests/Entity/UserTest.php
namespace src\Academic\UserBundle\Tests\Unit\Entity;

use Academic\UserBundle\Entity\User;
use Academic\UserBundle\Entity\Role;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UserTest extends \PHPUnit_Framework_TestCase
{
    public function testUsername()
    {
        $user = new User;
        $username = 'testusername';

        $this->assertNull($user->getUsername());

        $user->setUsername($username);

        $this->assertSame($username, $user->getUsername());
    }

    public function testPassword()
    {
        $user = new User;
        $password = 'testpassword';

        $this->assertNull($user->getPassword());

        $user->setPassword($password);

        $this->assertSame($password, $user->getPassword());
    }

    public function testEmail()
    {
        $user = new User;
        $email = 'email@mail.com';

        $this->assertNull($user->getEmail());

        $user->setEmail($email);

        $this->assertSame($email, $user->getEmail());
    }

    public function testFullname()
    {
        $user = new User;
        $fullname = 'Test User';

        $this->assertNull($user->getFullname());

        $user->setFullname($fullname);

        $this->assertSame($fullname, $user->getFullname());
    }

    public function testAvatar()
    {
        $user = new User;
        $avatar = 'test/avatar/path/avatar.jpg';

        $this->assertNull($user->getAvatar());

        $user->setAvatar($avatar);

        $this->assertSame($avatar, $user->getAvatar());
    }

    public function testRole()
    {
        $user = new User;
        $role = new Role;

        $this->assertNull($user->getRole());

        $user->setRole($role);

        $this->assertSame($role, $user->getRole());
    }

    public function testSalt()
    {
        $user = new User;
        $salt = md5(uniqid(null, true));

        $user->setSalt($salt);

        $this->assertEquals($salt, $user->getSalt());
    }

    public function testIsActive()
    {
        $user = new User;
        $isActive = true;

        $user->setIsActive($isActive);

        $this->assertSame($isActive, $user->getIsActive());
    }

    public function testTimeZone()
    {
        $user = new User;
        $timezone = 'America/Chicago';

        $user->setTimezone($timezone);

        $this->assertSame($timezone, $user->getTimezone());
    }

    public function testFile()
    {
        $user = new User;
        $file = null;

        $user->setFile($file);

        $this->assertSame($file, $user->getFile());
    }

    public function testSerializing()
    {
        $user = new User;
        $role = new Role;
        $userTwin = clone $user;
        $data  = $user->serialize();

        $this->assertNotEmpty($data);

        $user->setPassword('test_password')
            ->setUsername('username')
            ->setRole($role);

        $user->unserialize($data);

        $this->assertEquals($userTwin, $user);
    }

    public function testUpload()
    {
        $user = new User();

        $user->setUsername('werter');

        $fileUploaded = $this->getMockBuilder('Symfony\Component\HttpFoundation\File\UploadedFile')
            ->disableOriginalConstructor()
            ->getMock();

        $fileUploaded->expects($this->once())
            ->method('getClientOriginalExtension')
            ->will($this->returnValue('jpg'));

        $fileUploaded->expects($this->once())
            ->method('move')
            ->will($this->returnValue(true));

        $user->setFile($fileUploaded);

        $user->upload();

        $this->assertEquals('avatar_werter.jpg', $user->getAvatar());
        $this->assertEquals(null, $user->getFile());
    }
}
<?php

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Asserter;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation\Link;

/**
 * Programmer
 * @Serializer\ExclusionPolicy("all")
 * @ORM\Table(name="battle_programmer")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProgrammerRepository")
 * @Link(
 *     name ="self",
 *     route = "api_programmers_show",
 *     params={ "nickname": "object.getNickname()"}
 * )
 */
class Programmer
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     *@var string
     * @Assert\NotBlank(message="Please enter a clever nickname")
     * @Serializer\Expose()
     *@ORM\Column(name="nickname", type="string", length=100, unique=true)
     */
    private $nickname;

    /**
     *
     * @Serializer\Expose()
     * @var integer
     * @ORM\Column(name="avatarNumber", type="integer")
     */
    private $avatarNumber;

    /**
     * @Serializer\Expose()
     * @var string
     * @ORM\Column(name="tagLine", type="string", length=255, nullable=true)
     */
    private $tagLine;

    /**
     * @Serializer\Expose()
     * @var integer
     * @ORM\Column(name="powerLevel", type="integer")
     */
    private $powerLevel = 0;

    /**
     * @Serializer\Expose()
     * @Serializer\Groups({"deep"})
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function __construct($nickname = null, $avatarNumber = null)
    {
        $this->nickname = $nickname;
        $this->avatarNumber = $avatarNumber;
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
     * Set nickname
     *
     * @param string $nickname
     * @return Programmer
     */
    public function setNickname($nickname)
    {
        $this->nickname = $nickname;

        return $this;
    }

    /**
     * Get nickname
     *
     * @return string 
     */
    public function getNickname()
    {
        return $this->nickname;
    }

    /**
     * Set avatarNumber
     *
     * @param integer $avatarNumber
     * @return Programmer
     */
    public function setAvatarNumber($avatarNumber)
    {
        $this->avatarNumber = $avatarNumber;

        return $this;
    }

    /**
     * Get avatarNumber
     *
     * @return integer 
     */
    public function getAvatarNumber()
    {
        return $this->avatarNumber;
    }

    /**
     * Set tagLine
     *
     * @param string $tagLine
     * @return Programmer
     */
    public function setTagLine($tagLine)
    {
        $this->tagLine = $tagLine;

        return $this;
    }

    /**
     * Get tagLine
     *
     * @return string 
     */
    public function getTagLine()
    {
        return $this->tagLine;
    }

    /**
     * Set powerLevel
     *
     * @param integer $powerLevel
     * @return Programmer
     */
    public function setPowerLevel($powerLevel)
    {
        $this->powerLevel = $powerLevel;

        return $this;
    }

    /**
     * Get powerLevel
     *
     * @return integer 
     */
    public function getPowerLevel()
    {
        return $this->powerLevel;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param USer $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }
}

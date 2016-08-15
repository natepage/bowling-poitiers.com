<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use UserBundle\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints as AppAssert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Competition
 *
 * @ORM\Table(name="competition")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CompetitionRepository")
 * @UniqueEntity(
 *     fields={"title", "bowling", "author"},
 *     errorPath="root",
 *     message="Désolé, vous avez déjà ajouter une compétition portant ce nom, dans ce même bowling."
 * )
 */
class Competition
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     * @Assert\DateTime()
     */
    private $created;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start", type="datetime")
     * @Assert\DateTime()
     */
    private $start;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end", type="datetime")
     * @Assert\DateTime()
     * @AppAssert\After()
     */
    private $end;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255)
     */
    private $slug;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="competitions")
     */
    private $author;

    /**
     * @var string
     *
     * @ORM\Column(name="bowling", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $bowling;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     * @Assert\NotBlank(message="Merci de bien vouloir donner une description à votre compétition")
     */
    private $description;

    /**
     * @var integer
     *
     * @ORM\Column(name="partners", type="integer", options={"default": 0})
     * @Assert\NotBlank()
     */
    private $partners;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\CompetitionMessage", mappedBy="competition", cascade={"remove"})
     */
    private $messages;

    public function __construct()
    {
        $this->created = new \DateTime();
        $this->messages = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->title ?: '';
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Competition
     */
    public function setCreated(\DateTime $created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set start
     *
     * @param \DateTime $start
     *
     * @return Competition
     */
    public function setStart($start)
    {
        $this->start = $start;

        return $this;
    }

    /**
     * Get start
     *
     * @return \DateTime
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Set end
     *
     * @param \DateTime $end
     *
     * @return Competition
     */
    public function setEnd($end)
    {
        $this->end = $end;

        return $this;
    }

    /**
     * Get end
     *
     * @return \DateTime
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Competition
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return Competition
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set bowling
     *
     * @param string $bowling
     *
     * @return Competition
     */
    public function setBowling($bowling)
    {
        $this->bowling = $bowling;

        return $this;
    }

    /**
     * Get bowling
     *
     * @return string
     */
    public function getBowling()
    {
        return $this->bowling;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Competition
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set author
     *
     * @param \UserBundle\Entity\User $author
     *
     * @return Competition
     */
    public function setAuthor(\UserBundle\Entity\User $author = null)
    {
        $this->author = $author;

        if(null !== $author){
            $author->addCompetition($this);
        }

        return $this;
    }

    /**
     * Get author
     *
     * @return \UserBundle\Entity\User
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set partners
     *
     * @param integer $partners
     *
     * @return Competition
     */
    public function setPartners($partners)
    {
        $this->partners = $partners;

        return $this;
    }

    /**
     * Get partners
     *
     * @return integer
     */
    public function getPartners()
    {
        return $this->partners;
    }

    /**
     * Add message
     *
     * @param \AppBundle\Entity\CompetitionMessage $message
     *
     * @return Competition
     */
    public function addMessage(\AppBundle\Entity\CompetitionMessage $message)
    {
        $this->messages[] = $message;

        if(null !== $message){
            $message->setCompetition($this);
        }

        return $this;
    }

    /**
     * Remove message
     *
     * @param \AppBundle\Entity\CompetitionMessage $message
     */
    public function removeMessage(\AppBundle\Entity\CompetitionMessage $message)
    {
        $this->messages->removeElement($message);
    }

    /**
     * Get messages
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMessages()
    {
        return $this->messages;
    }
}

<?php

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as ModelUser;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * User
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="UserBundle\Repository\UserRepository")
 */
class User extends ModelUser
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Post", mappedBy="author")
     */
    protected $posts;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Page", mappedBy="author")
     */
    protected $pages;

    /**
     * @var string
     *
     * @ORM\Column(name="licence", type="string", length=255, nullable=true)
     * @Assert\Length(
     *      min = "10",
     *      max = "10",
     *      exactMessage = "Votre numéro de licence doit faire {{ limit }} caractères. En cas de problème persistent, laissez le champs vide vous pourrez le renseigner plus tard à partir de votre profil."
     * )
     */
    protected $licence;

    /**
     * @var boolean
     *
     * @ORM\Column(name="newsletter", type="boolean")
     */
    protected $newsletter;

    /**
     * @var string
     *
     * @ORM\Column(name="facebook_id", type="string", nullable=true)
     */
    protected $facebookId;

    /**
     * @var string
     *
     * @ORM\Column(name="google_id", type="string", nullable=true)
     */
    protected $googleId;

    /**
     * @var string
     *
     * @ORM\Column(name="twitter_id", type="string", nullable=true)
     */
    protected $twitterId;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Competition", mappedBy="author")
     */
    protected $competitions;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\CompetitionMessage", mappedBy="author")
     */
    protected $competitionMessages;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Competition", mappedBy="followers")
     */
    protected $competitionsFollowed;

    /**
     * @var boolean
     *
     * @ORM\Column(name="email_on_competition_created", type="boolean", nullable=true)
     */
    protected $emailOnCompetitionCreated;

    /**
     * @var boolean
     *
     * @ORM\Column(name="email_on_competition_message", type="boolean", nullable=true)
     */
    protected $emailOnCompetitionMessage;

    public function __construct()
    {
        parent::__construct();

        $this->posts = new ArrayCollection();
        $this->enabled = true;
        $this->locked = false;
        $this->newsletter = true;
        $this->competitions = new ArrayCollection();
        $this->competitionMessages = new ArrayCollection();
        $this->emailOnCompetitionCreated = false;
        $this->emailOnCompetitionMessage = true;
    }

    /**
     * Add posts
     *
     * @param \AppBundle\Entity\Post $posts
     * @return User
     */
    public function addPost(\AppBundle\Entity\Post $posts)
    {
        $this->posts[] = $posts;

        return $this;
    }

    /**
     * Remove posts
     *
     * @param \AppBundle\Entity\Post $posts
     */
    public function removePost(\AppBundle\Entity\Post $posts)
    {
        $this->posts->removeElement($posts);
    }

    /**
     * Get posts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPosts()
    {
        return $this->posts;
    }

    /**
     * Add pages
     *
     * @param \AppBundle\Entity\Page $pages
     * @return User
     */
    public function addPage(\AppBundle\Entity\Page $pages)
    {
        $this->pages[] = $pages;

        return $this;
    }

    /**
     * Remove pages
     *
     * @param \AppBundle\Entity\Page $pages
     */
    public function removePage(\AppBundle\Entity\Page $pages)
    {
        $this->pages->removeElement($pages);
    }

    /**
     * Get pages
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPages()
    {
        return $this->pages;
    }

    /**
     * Get licence
     *
     * @param null $licence
     * @return User
     */
    public function setLicence($licence = null)
    {
        $this->licence = $licence;

        return $this;
    }

    /**
     * Get licence
     *
     * @return string
     */
    public function getLicence()
    {
        return $this->licence;
    }

    /**
     * Set newsletter
     *
     * @param $newsletter
     * @return User
     */
    public function setNewsletter($newsletter)
    {
        $this->newsletter = $newsletter;

        return $this;
    }

    /**
     * Get newsletter
     *
     * @return bool
     */
    public function getNewsletter()
    {
        return $this->newsletter;
    }

    /**
     * Set facebookId
     *
     * @param string $facebookId
     *
     * @return User
     */
    public function setFacebookId($facebookId)
    {
        $this->facebookId = $facebookId;

        return $this;
    }

    /**
     * Get facebookId
     *
     * @return string
     */
    public function getFacebookId()
    {
        return $this->facebookId;
    }

    /**
     * Set googleId
     *
     * @param string $googleId
     *
     * @return User
     */
    public function setGoogleId($googleId)
    {
        $this->googleId = $googleId;

        return $this;
    }

    /**
     * Get googleId
     *
     * @return string
     */
    public function getGoogleId()
    {
        return $this->googleId;
    }

    /**
     * Set twitterId
     *
     * @param string $twitterId
     *
     * @return User
     */
    public function setTwitterId($twitterId)
    {
        $this->twitterId = $twitterId;

        return $this;
    }

    /**
     * Get twitterId
     *
     * @return string
     */
    public function getTwitterId()
    {
        return $this->twitterId;
    }

    /**
     * Add competition
     *
     * @param \AppBundle\Entity\Competition $competition
     *
     * @return User
     */
    public function addCompetition(\AppBundle\Entity\Competition $competition)
    {
        $this->competitions[] = $competition;

        return $this;
    }

    /**
     * Remove competition
     *
     * @param \AppBundle\Entity\Competition $competition
     */
    public function removeCompetition(\AppBundle\Entity\Competition $competition)
    {
        $this->competitions->removeElement($competition);
    }

    /**
     * Get competitions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCompetitions()
    {
        return $this->competitions;
    }

    /**
     * Add competitionMessage
     *
     * @param \AppBundle\Entity\CompetitionMessage $competitionMessage
     *
     * @return User
     */
    public function addCompetitionMessage(\AppBundle\Entity\CompetitionMessage $competitionMessage)
    {
        $this->competitionMessages[] = $competitionMessage;

        if(null !== $competitionMessage){
            $competitionMessage->setAuthor($this);
        }

        return $this;
    }

    /**
     * Remove competitionMessage
     *
     * @param \AppBundle\Entity\CompetitionMessage $competitionMessage
     */
    public function removeCompetitionMessage(\AppBundle\Entity\CompetitionMessage $competitionMessage)
    {
        $this->competitionMessages->removeElement($competitionMessage);
    }

    /**
     * Get competitionMessages
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCompetitionMessages()
    {
        return $this->competitionMessages;
    }

    /**
     * Add competitionsFollowed
     *
     * @param \AppBundle\Entity\Competition $competitionsFollowed
     *
     * @return User
     */
    public function addCompetitionsFollowed(\AppBundle\Entity\Competition $competitionsFollowed)
    {
        $this->competitionsFollowed[] = $competitionsFollowed;

        return $this;
    }

    /**
     * Remove competitionsFollowed
     *
     * @param \AppBundle\Entity\Competition $competitionsFollowed
     */
    public function removeCompetitionsFollowed(\AppBundle\Entity\Competition $competitionsFollowed)
    {
        $this->competitionsFollowed->removeElement($competitionsFollowed);
    }

    /**
     * Get competitionsFollowed
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCompetitionsFollowed()
    {
        return $this->competitionsFollowed;
    }

    /**
     * Set emailOnCompetitionCreated
     *
     * @param boolean $emailOnCompetitionCreated
     *
     * @return User
     */
    public function setEmailOnCompetitionCreated($emailOnCompetitionCreated)
    {
        $this->emailOnCompetitionCreated = $emailOnCompetitionCreated;

        return $this;
    }

    /**
     * Get emailOnCompetitionCreated
     *
     * @return boolean
     */
    public function getEmailOnCompetitionCreated()
    {
        return $this->emailOnCompetitionCreated;
    }

    /**
     * Set emailOnCompetitionMessage
     *
     * @param boolean $emailOnCompetitionMessage
     *
     * @return User
     */
    public function setEmailOnCompetitionMessage($emailOnCompetitionMessage)
    {
        $this->emailOnCompetitionMessage = $emailOnCompetitionMessage;

        return $this;
    }

    /**
     * Get emailOnCompetitionMessage
     *
     * @return boolean
     */
    public function getEmailOnCompetitionMessage()
    {
        return $this->emailOnCompetitionMessage;
    }
}

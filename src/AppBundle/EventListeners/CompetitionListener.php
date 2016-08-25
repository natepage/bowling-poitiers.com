<?php

namespace AppBundle\EventListeners;

use AppBundle\AppEvents;
use AppBundle\Event\CompetitionEvent;
use AppBundle\Event\CompetitionMessageEvent;
use AppBundle\Utils\Newsletter\EmailSenderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Templating\EngineInterface;

class CompetitionListener implements EventSubscriberInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var EmailSenderInterface
     */
    private $emailSender;

    /**
     * @var EngineInterface
     */
    private $templating;

    private $tokenStorage;

    /**
     * @var string
     */
    private $from;

    public function __construct(
        EntityManagerInterface $em,
        EmailSenderInterface $emailSender,
        EngineInterface $templating,
        TokenStorageInterface $tokenStorage,
        $from
    )
    {
        $this->em = $em;
        $this->emailSender = $emailSender;
        $this->templating = $templating;
        $this->tokenStorage = $tokenStorage;
        $this->from = $from;
    }

    public static function getSubscribedEvents()
    {
        return array(
            AppEvents::COMPETITION_CREATE_EVENT => 'competitionCreated',
            AppEvents::COMPETITION_UPDATE_EVENT => 'competitionUpdated',
            AppEvents::COMPETITION_REMOVE_EVENT => 'competitionRemoved',
            AppEvents::COMPETITION_MESSAGE_CREATE_EVENT => 'messageCreated',
            //AppEvents::COMPETITION_MESSAGE_REMOVE_EVENT => 'message'
        );
    }

    public function competitionCreated(CompetitionEvent $event)
    {
        $competition = $event->getCompetition();
        $currentUser = $this->tokenStorage->getToken()->getUser();
        $users = $this->em->getRepository('UserBundle:User')->findUserWhenCompetitionCreated($currentUser->getId());

        $subject = '[BCP][Compétitions] Une nouvelle compétition a été ajoutée sur le site !';
        $body = $this->templating->render('@App/competition/emails/competition_created.html.twig', array(
            'competition' => $competition
        ));

        foreach($users as $user){
            $to = array($user->getEmail());

            $this->emailSender->send($this->from, $to, $subject, $body);
        }
    }

    public function competitionUpdated(CompetitionEvent $event)
    {
        $competition = $event->getCompetition();

        $subject = sprintf('[BCP][Compétitions] La compétition "%s" a été modifiée !', $competition->getTitle());
        $body = $this->templating->render('@App/competition/emails/competition_updated.html.twig', array(
            'competition' => $competition
        ));

        foreach($competition->getFollowers() as $follower){
            $to = array($follower->getEmail());

            $this->emailSender->send($this->from, $to, $subject, $body);
        }
    }

    public function competitionRemoved(CompetitionEvent $event)
    {
        $competition = $event->getCompetition();
        $author = $competition->getAuthor();
        $currentUser = $this->tokenStorage->getToken()->getUser();

        if($author->getId() !== $currentUser->getId()){
            $subject = sprintf('[BCP][Compétitions] Votre compétition "%s" a été supprimée !', $competition->getTitle());
            $body = $this->templating->render('@App/competition/emails/competition_removed.html.twig', array(
                'competition' => $competition
            ));
            $to = array($author->getEmail());

            $this->emailSender->send($this->from, $to, $subject, $body);
        }
    }

    public function messageCreated(CompetitionMessageEvent $event)
    {
        $message = $event->getMessage();
        $author = $message->getAuthor();
        $competition = $message->getCompetition();
        $followers = $competition->getFollowers();
        $competitionAuthor = $competition->getAuthor();

        if($competitionAuthor->getEmailOnCompetitionMessage()){
            $followers->add($competitionAuthor);
        }

        foreach($followers as $follower){
            if($author->getId() !== $follower->getId()){
                $subject = sprintf('[BCP][Compétitions][Messages] Un nouveau message sur la compétition "%s"', $competition->getTitle());
                $body = $this->templating->render('@App/competition/emails/message_created.html.twig', array(
                    'competition' => $competition,
                    'message' => $message
                ));
                $to = array($follower->getEmail());

                $this->emailSender->send($this->from, $to, $subject, $body);
            }
        }
    }
}
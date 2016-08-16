<?php

namespace AppBundle\EventListeners;

use AppBundle\AppEvents;
use AppBundle\Event\CompetitionEvent;
use AppBundle\Event\CompetitionMessageEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CompetitionListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            AppEvents::COMPETITION_CREATE_EVENT => 'competition',
            AppEvents::COMPETITION_UPDATE_EVENT => 'competition',
            AppEvents::COMPETITION_REMOVE_EVENT => 'competition',
            AppEvents::COMPETITION_MESSAGE_CREATE_EVENT => 'message',
            AppEvents::COMPETITION_MESSAGE_REMOVE_EVENT => 'message'
        );
    }

    public function competition(CompetitionEvent $event)
    {

    }

    public function message(CompetitionMessageEvent $event)
    {

    }
}
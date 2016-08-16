<?php

namespace AppBundle\Event;

use AppBundle\Entity\CompetitionMessage;
use Symfony\Component\EventDispatcher\Event;

class CompetitionMessageEvent extends Event
{
    /**
     * @var CompetitionMessage
     */
    private $message;

    public function __construct(CompetitionMessage $message)
    {
        $this->message = $message;
    }

    /**
     * Get message
     *
     * @return CompetitionMessage
     */
    public function getMessage()
    {
        return $this->message;
    }
}
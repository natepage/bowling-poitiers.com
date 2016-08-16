<?php

namespace AppBundle\Event;

use AppBundle\Entity\Competition;
use Symfony\Component\EventDispatcher\Event;

class CompetitionEvent extends Event
{
    /**
     * @var Competition
     */
    private $competition;

    public function __construct(Competition $competition)
    {
        $this->competition = $competition;
    }

    /**
     * Get competition
     *
     * @return Competition
     */
    public function getCompetition()
    {
        return $this->competition;
    }
}
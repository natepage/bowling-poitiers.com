<?php

namespace AppBundle;

final class AppEvents
{
    /**
     * @Event("AppBundle\Event\CompetitionEvent")
     */
    const COMPETITION_CREATE_EVENT = 'app.competition.create';

    /**
     * @Event("AppBundle\Event\CompetitionEvent")
     */
    const COMPETITION_UPDATE_EVENT = 'app.competition.update';

    /**
     * @Event("AppBundle\Event\CompetitionEvent")
     */
    const COMPETITION_REMOVE_EVENT = 'app.competition.remove';

    /**
     * @Event("AppBundle\Event\CompetitionMessageEvent")
     */
    const COMPETITION_MESSAGE_CREATE_EVENT = 'app.competition.message.create';

    /**
     * @Event("AppBundle\Event\CompetitionMessageEvent")
     */
    const COMPETITION_MESSAGE_REMOVE_EVENT = 'app.competition.message.remove';
}
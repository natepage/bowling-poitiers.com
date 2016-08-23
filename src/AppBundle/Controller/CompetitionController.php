<?php

namespace AppBundle\Controller;

use AppBundle\AppEvents;
use AppBundle\Entity\Competition;
use AppBundle\Entity\CompetitionMessage;
use AppBundle\Event\CompetitionEvent;
use AppBundle\Event\CompetitionMessageEvent;
use AppBundle\Form\CompetitionMessageType;
use AppBundle\Form\CompetitionType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/competitions")
 */
class CompetitionController extends Controller
{
    const COMPETITIONS_PER_PAGE = 10;
    const MESSAGES_PER_PAGE = 5;

    /**
     * @var array
     */
    private $switchDisplay = array(
        'list' => array(
            'target' => 'calendar',
            'icon' => 'calendar',
            'text' => 'Affichage calendrier'
        ),
        'calendar' => array(
            'target' => 'list',
            'icon' => 'list',
            'text' => 'Affichage liste'
        )
    );

    /**
     * @var array
     */
    private $calendarClasses = array(
        'event-important',
        'event-info',
        'event-warning',
        'event-inverse',
        'event-success',
        'event-special'
    );

    /**
     * @Route(
     *     "/{display}/{page}",
     *     name="competitions_index",
     *     requirements={"display": "list|calendar", "page": "\d+"},
     *     defaults={"display": "list", "page": 1}
     * )
     * @Method("GET")
     */
    public function indexAction($display, $page)
    {
        $template = '@App/competition/index_%s.html.twig';
        $query = $this->getDoctrine()->getRepository('AppBundle:Competition')->getIndexQuery();
        $paginator = $this->get('knp_paginator');
        $limit = self::COMPETITIONS_PER_PAGE;
        $competitions = $paginator->paginate($query, $page, $limit);

        switch($display){
            case 'calendar':
                return $this->render(sprintf($template, $display), array(
                    'page' => $page,
                    'display' => $display,
                    'switchDisplay' => $this->switchDisplay[$display],
                    'competitionsCount' => $competitions->count()
                ));
                break;
            case 'list':
                return $this->render(sprintf($template, $display), array(
                    'competitions' => $competitions,
                    'page' => $page,
                    'display' => $display,
                    'switchDisplay' => $this->switchDisplay[$display],
                    'limit' => $limit
                ));
                break;
            default:
                throw $this->createNotFoundException();
        }
    }

    /**
     * @Route(
     *     "/calendar/ajax/index",
     *     name="competitions_ajax_index"
     * )
     * @Method("GET")
     */
    public function ajaxCompetitionsAction()
    {
        $competitions = $this->getDoctrine()->getManager()
                                            ->getRepository('AppBundle:Competition')
                                            ->findAll();

        $response = array();
        $i = 0;
        foreach($competitions as $competition){
            $competitionArray = $competition->toArray();

            $competitionArray['url'] = $this->generateUrl('competitions_view', array(
                'display' => 'calendar',
                'page' => 1,
                'id' => $competition->getId(),
                'slug' => $competition->getSlug()
            ));

            $competitionArray['class'] = $this->calendarClasses[$i];
            $i++;

            if($i == 6){
                $i = 0;
            }

            $response[] = $competitionArray;
        }

        return $this->json(array('success' => 1, 'result' => $response));
    }

    /**
     * @Route(
     *     "/{display}/view/{id}/{slug}/{page}",
     *     name="competitions_view",
     *     requirements={"display": "list|calendar", "id": "\d+", "page": "\d+"},
     *     defaults={"display": "list", "page": 1}
     * )
     * @ParamConverter(
     *     "competition",
     *     options={"mapping": {"id": "id", "slug": "slug"}}
     * )
     * @Method("GET")
     * @Template("@App/competition/view.html.twig")
     */
    public function viewAction(Competition $competition, $display, $page){}

    /**
     * @Route(
     *     "/{display}/create",
     *     name="competitions_create",
     *     requirements={"display": "list|calendar"},
     *     defaults={"display": "list"}
     * )
     * @Method({"GET", "POST"})
     * @Template("@App/competition/form_view.html.twig")
     */
    public function createAction(Request $request, $display)
    {
        $competition = new Competition();
        $competition->setAuthor($this->getUser());

        $form = $this->createForm(CompetitionType::class, $competition);

        if($form->handleRequest($request)->isValid()){
            $em = $this->getDoctrine()->getManager();

            $this->manageSlug($competition);
            $competition->setAuthor($this->getUser());

            $em->persist($competition);
            $em->flush();

            $event = new CompetitionEvent($competition);
            $this->get('event_dispatcher')->dispatch(AppEvents::COMPETITION_CREATE_EVENT, $event);

            $msg = sprintf('Votre compétition "%s" a été créée avec succès !', $competition->getTitle());
            $this->addFlash('success', $msg);

            return $this->redirect($this->generateUrl('competitions_index', array('display' => $display)));
        }

        return array('form' => $form->createView(), 'display' => $display, 'action' => 'create');
    }

    /**
     * @Route(
     *     "/{display}/update/{id}/{slug}",
     *     name="competitions_update",
     *     requirements={"display": "list|calendar", "id": "\d+"},
     *     defaults={"display": "list"}
     * )
     * @ParamConverter(
     *     "competition",
     *     options={"mapping": {"id": "id", "slug": "slug"}}
     * )
     * @Method({"GET", "POST"})
     * @Template("@App/competition/form_view.html.twig")
     */
    public function updateAction(Request $request, Competition $competition, $display)
    {
        $authorId = $competition->getAuthor()->getId();
        $currentUserId = $this->getUser()->getId();

        if($authorId !== $currentUserId){
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(CompetitionType::class, $competition);

        if($form->handleRequest($request)->isValid()){
            $em = $this->getDoctrine()->getManager();

            $this->manageSlug($competition);

            $em->flush();

            $event = new CompetitionEvent($competition);
            $this->get('event_dispatcher')->dispatch(AppEvents::COMPETITION_UPDATE_EVENT, $event);

            $msg = sprintf('Votre compétition "%s" a été modifiée avec succès !', $competition->getTitle());
            $this->addFlash('success', $msg);

            return $this->redirect($this->generateUrl('competitions_view', array(
                'display' => $display,
                'id' => $competition->getId(),
                'slug' => $competition->getSlug()
            )));
        }

        return array(
            'form' => $form->createView(),
            'competition' => $competition,
            'display' => $display,
            'action' => 'update'
        );
    }

    /**
     * @Route(
     *     "/{display}/remove/{id}/{slug}/{page}",
     *     name="competitions_remove",
     *     requirements={"display": "list|calendar", "id": "\d+", "page": "\d+"},
     *     defaults={"display": "list", "page": 1}
     * )
     * @ParamConverter(
     *     "competition",
     *     options={"mapping": {"id": "id", "slug": "slug"}}
     * )
     * @Method({"GET", "POST"})
     * @Template("@App/competition/remove.html.twig")
     */
    public function removeAction(Request $request, Competition $competition, $display, $page)
    {
        $authorId = $competition->getAuthor()->getId();
        $currentUserId = $this->getUser()->getId();

        if($authorId !== $currentUserId){
            throw $this->createNotFoundException();
        }

        $form = $this->createFormBuilder()->getForm();

        if($form->handleRequest($request)->isValid()){
            $em = $this->getDoctrine()->getManager();

            $em->remove($competition);
            $em->flush();

            $event = new CompetitionEvent($competition);
            $this->get('event_dispatcher')->dispatch(AppEvents::COMPETITION_REMOVE_EVENT, $event);

            $msg = sprintf('Votre compétition "%s" a été supprimée avec succès !', $competition->getTitle());
            $this->addFlash('success', $msg);

            return $this->redirect($this->generateUrl('competitions_index', array(
                'display' => $display,
                'page' => $page
            )));
        }

        return array(
            'form' => $form->createView(),
            'competition' => $competition,
            'display' => $display,
            'page' => $page
        );
    }

    /**
     * @Route(
     *     "/{display}/feed/{id}/{slug}/{page}",
     *     name="competitions_feed",
     *     requirements={"display": "list|calendar", "id": "\d+", "page": "\d+"},
     *     defaults={"display": "list", "page": 1}
     * )
     * @ParamConverter(
     *     "competition",
     *     options={"mapping": {"id": "id", "slug": "slug"}}
     * )
     * @Method("GET")
     */
    public function feedAction(Competition $competition, $display, $page)
    {
        if(!$this->isGranted('IS_AUTHENTICATED_FULLY')){
            throw $this->createNotFoundException();
        }

        $em = $this->getDoctrine()->getManager();

        $competition->addFollower($this->getUser());

        $em->flush();

        $msg = sprintf('Vous êtes maintenant abonné(e) à la compétition "%s"', $competition->getTitle());
        $this->addFlash('success', $msg);

        return $this->redirect($this->generateUrl('competitions_view', array(
            'display' => $display,
            'page' => $page,
            'id' => $competition->getId(),
            'slug' => $competition->getSlug()
        )));
    }

    /**
     * @Route(
     *     "/{display}/unfeed/{id}/{slug}/{page}",
     *     name="competitions_unfeed",
     *     requirements={"display": "list|calendar", "id": "\d+", "page": "\d+"},
     *     defaults={"display": "list", "page": 1}
     * )
     * @ParamConverter(
     *     "competition",
     *     options={"mapping": {"id": "id", "slug": "slug"}}
     * )
     * @Method("GET")
     */
    public function unfeedAction(Competition $competition, $display, $page)
    {
        if(!$this->isGranted('IS_AUTHENTICATED_FULLY')){
            throw $this->createNotFoundException();
        }

        $em = $this->getDoctrine()->getManager();

        $competition->removeFollower($this->getUser());

        $em->flush();

        $msg = sprintf('Vous n\'êtes plus abonné(e) à la compétition "%s"', $competition->getTitle());
        $this->addFlash('success', $msg);

        return $this->redirect($this->generateUrl('competitions_view', array(
            'display' => $display,
            'page' => $page,
            'id' => $competition->getId(),
            'slug' => $competition->getSlug()
        )));
    }

    /**
     * @Route(
     *     "/{display}/{id}/{slug}/messages/view/{page}/{messagesPage}",
     *     name="competitions_messages_view",
     *     requirements={
     *          "display": "list|calendar",
     *          "id": "\d+",
     *          "page": "\d+",
     *          "messagesPage": "\d+"
     *      },
     *     defaults={
     *          "display": "list",
     *          "page": 1,
     *          "messagesPage": 1
     *     }
     * )
     * @ParamConverter(
     *     "competition",
     *     options={"mapping": {"id": "id", "slug": "slug"}}
     * )
     * @Method("GET")
     * @Template("@App/competition/message/view.html.twig")
     */
    public function viewMessageAction(Competition $competition, $display, $page, $messagesPage)
    {
        $form = $this->createForm(CompetitionMessageType::class);

        $query = $this->getDoctrine()->getRepository('AppBundle:CompetitionMessage')
                                     ->getViewQuery($competition->getId());
        $paginator = $this->get('knp_paginator');
        $limit = self::MESSAGES_PER_PAGE;
        $messages = $paginator->paginate($query, $messagesPage, $limit, array(
            'pageParameterName' => 'messagesPage'
        ));

        return array(
            'competition' => $competition,
            'messages' => $messages,
            'display' => $display,
            'page' => $page,
            'messagesPage' => $messagesPage,
            'form' => $form->createView(),
            'limit' => $limit
        );
    }

    /**
     * @Route(
     *     "/{display}/{id}/{slug}/message/create/{page}/{messagesPage}",
     *     name="competitions_messages_create",
     *     requirements={
     *          "display": "list|calendar",
     *          "id": "\d+",
     *          "page": "\d+",
     *          "messagesPage": "\d+"
     *      },
     *     defaults={
     *          "display": "list",
     *          "page": 1,
     *          "messagesPage": 1
     *     }
     * )
     * @ParamConverter(
     *     "competition",
     *     options={"mapping": {"id": "id", "slug": "slug"}}
     * )
     * @Method({"GET", "POST"})
     * @Template("@App/competition/message/view.html.twig")
     */
    public function createMessageAction(Request $request, Competition $competition, $display, $page, $messagesPage)
    {
        $message = new CompetitionMessage();
        $message->setCompetition($competition)
                ->setAuthor($this->getUser());

        $form = $this->createForm(CompetitionMessageType::class, $message);

        if($form->handleRequest($request)->isValid()){
            $em = $this->getDoctrine()->getManager();

            $em->persist($message);
            $em->flush();

            $event = new CompetitionMessageEvent($message);
            $this->get('event_dispatcher')->dispatch(AppEvents::COMPETITION_MESSAGE_CREATE_EVENT, $event);

            $this->addFlash('success', 'Votre message a été correctement publié !');

            return $this->redirect($this->generateUrl('competitions_messages_view', array(
                'display' => $display,
                'page' => $page,
                'id' => $competition->getId(),
                'slug' => $competition->getSlug()
            )));
        }

        $query = $this->getDoctrine()->getRepository('AppBundle:CompetitionMessage')
                                     ->getViewQuery($competition->getId());
        $paginator = $this->get('knp_paginator');
        $limit = self::MESSAGES_PER_PAGE;
        $messages = $paginator->paginate($query, $messagesPage, $limit, array(
            'pageParameterName' => 'messagesPage'
        ));

        return array(
            'competition' => $competition,
            'messages' => $messages,
            'display' => $display,
            'page' => $page,
            'messagesPage' => $messagesPage,
            'form' => $form->createView(),
            'limit' => $limit
        );
    }

    /**
     * @Route(
     *     "/{display}/message/remove/{id}/{page}/{messagesPage}",
     *     name="competitions_messages_remove",
     *     requirements={
     *          "display": "list|calendar",
     *          "id": "\d+",
     *          "page": "\d+",
     *          "messagesPage": "\d+"
     *      },
     *     defaults={
     *          "display": "list",
     *          "page": 1,
     *          "messagesPage": 1
     *     }
     * )
     * @ParamConverter("message", options={"id": "id"})
     * @Method({"GET", "POST"})
     * @Template("@App/competition/message/remove.html.twig")
     */
    public function removeMessageAction(Request $request, CompetitionMessage $message, $display, $page, $messagesPage)
    {
        $authorId = $message->getAuthor()->getId();
        $currentUserId = $this->getUser()->getId();

        if($authorId !== $currentUserId){
            throw $this->createNotFoundException();
        }

        $form = $this->createFormBuilder()->getForm();

        if($form->handleRequest($request)->isValid()){
            $em = $this->getDoctrine()->getManager();

            $em->remove($message);
            $em->flush();

            $event = new CompetitionMessageEvent($message);
            $this->get('event_dispatcher')->dispatch(AppEvents::COMPETITION_MESSAGE_REMOVE_EVENT, $event);

            $this->addFlash('success', 'Votre message a été correctement supprimé !');

            return $this->redirect($this->generateUrl('competitions_messages_view', array(
                'display' => $display,
                'page' => $page,
                'messagesPage' => $messagesPage,
                'id' => $message->getCompetition()->getId(),
                'slug' => $message->getCompetition()->getSlug()
            )));
        }

        return array(
            'form' => $form->createView(),
            'message' => $message,
            'display' => $display,
            'page' => $page,
            'messagesPage' => $messagesPage
        );
    }

    /**
     * Set the competition's slug
     *
     * @param Competition $competition
     */
    private function manageSlug(Competition $competition)
    {
        $slugify = $this->get('sonata.core.slugify.cocur');
        $title = $competition->getTitle();

        $competition->setSlug($slugify->slugify($title));
    }
}
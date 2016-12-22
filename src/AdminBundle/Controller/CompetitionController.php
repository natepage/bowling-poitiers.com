<?php

namespace AdminBundle\Controller;

use AppBundle\AppEvents;
use AppBundle\Event\CompetitionEvent;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Exception\ModelManagerException;
use Symfony\Component\HttpFoundation\Request;

class CompetitionController extends CRUDController
{
    public function preCreate(Request $request, $competition)
    {
        $competition->setAuthor($this->getUser());
    }

    public function deleteAction($id)
    {
        $competition = $this->admin->getObject($id);

        if(!$competition){
            throw $this->createNotFoundException('Unable to find the object.');
        }

        $this->deleteCompetitions(array($competition));

        return $this->redirect($this->admin->generateUrl('list', $this->admin->getFilterParameters()));
    }

    public function batchActionDelete(ProxyQueryInterface $query)
    {
        $this->admin->checkAccess('batchDelete');

        $selectedCompetitions = $query->execute();
        $this->deleteCompetitions($selectedCompetitions);

        return $this->redirect($this->admin->generateUrl('list', $this->admin->getFilterParameters()));
    }

    private function deleteCompetitions($competitions)
    {
        $modelManager = $this->admin->getModelManager();
        $eventDispatcher = $this->get('event_dispatcher');

        try {
            foreach($competitions as $competition){
                $modelManager->delete($competition);

                $event = new CompetitionEvent($competition);
                $eventDispatcher->dispatch(AppEvents::COMPETITION_REMOVE_EVENT, $event);
            }

            $this->addFlash('sonata_flash_success', 'flash_batch_delete_success');
        } catch (ModelManagerException $e) {
            $this->handleModelManagerException($e);
            $this->addFlash('sonata_flash_error', 'flash_batch_delete_error');
        }
    }
}
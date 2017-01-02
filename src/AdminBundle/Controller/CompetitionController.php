<?php

namespace AdminBundle\Controller;

use AppBundle\AppEvents;
use AppBundle\Event\CompetitionEvent;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Exception\ModelManagerException;
use Symfony\Component\HttpFoundation\Request;

class CompetitionController extends CRUDController
{
    protected function preCreate(Request $request, $competition)
    {
        $competition->setAuthor($this->getUser());
    }

    public function batchActionDelete(ProxyQueryInterface $query)
    {
        $this->admin->checkAccess('batchDelete');

        $modelManager = $this->admin->getModelManager();
        $selectedCompetitions = $query->execute();
        $eventDispatcher = $this->get('event_dispatcher');

        try {
            foreach($selectedCompetitions as $competition){
                $modelManager->delete($competition);

                $event = new CompetitionEvent($competition);
                $eventDispatcher->dispatch(AppEvents::COMPETITION_REMOVE_EVENT, $event);
            }

            $this->addFlash('sonata_flash_success', 'flash_batch_delete_success');
        } catch (ModelManagerException $e) {
            $this->handleModelManagerException($e);
            $this->addFlash('sonata_flash_error', 'flash_batch_delete_error');
        }

        return $this->redirect($this->admin->generateUrl('list', $this->admin->getFilterParameters()));
    }
}

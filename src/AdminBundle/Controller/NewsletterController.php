<?php
declare(strict_types=1);

namespace AdminBundle\Controller;

use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;

class NewsletterController extends CRUDController
{
    public function batchActionActivate(ProxyQueryInterface $query)
    {
        if(!$this->isGranted('ROLE_NEWSLETTER_ADMIN')){
            throw $this->createAccessDeniedException();
        }

        $this->sendActivate($query->execute());

        $this->addFlash('sonata_flash_success', $this->admin->trans('flash_batch_activate_success'), $this->admin->flashIcon);

        return $this->redirect($this->admin->generateUrl('list', $this->admin->getFilterParameters()));
    }

    private function sendActivate($newsletters)
    {
        $newsletterManager = $this->get('bcp.newsletter');

        if($this->isGranted('ROLE_SUPER_ADMIN')){
            $newsletterManager->setIsSuperAdmin(true);
        }

        /**
         * @var \AppBundle\Entity\Newsletter $newsletter
         */
        foreach ($newsletters as $newsletter) {
            if ($newsletter->isActivated()) {
                continue;
            }

            $newsletterManager->alertActivate($newsletter);
        }
    }
}

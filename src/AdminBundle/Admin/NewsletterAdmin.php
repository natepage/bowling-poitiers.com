<?php

namespace AdminBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;

class NewsletterAdmin extends AbstractAdmin
{
    /**
     * @var string
     */
    protected $translationDomain = 'NewsletterAdmin';

    /**
     * @var string
     */
    public $flashIcon = '<i class="fa fa-3x fa-send-o"></i>';

    /**
     * @var int
     */
    protected $maxPerPage = 15;

    /**
     * @var array
     */
    protected $datagridValues = array(
        '_page' => 1,
        '_sort_order' => 'DESC',
        '_sort_by' => 'mail'
    );

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('mail')
            ->add('activated')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('mail')
            ->add('token')
            ->add('activated')
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('mail')
            ->add('activated')
        ;
    }

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('mail')
            ->add('token')
            ->add('activated')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBatchActions()
    {
        $actions = parent::getBatchActions();

        if($this->hasRole('ROLE_NEWSLETTER_ADMIN')) {
            $actions['activate'] = array(
                'label' => 'batch_action_activate',
                'translation_domain' => $this->translationDomain,
                'ask_confirmation' => true
            );
        }

        return $actions;
    }

    public function hasRole($role)
    {
        $tokenStorage = $this->getConfigurationPool()->getContainer()->get('security.token_storage');
        $authorizationChecker = $this->getConfigurationPool()->getContainer()->get('security.authorization_checker');

        return null !== $tokenStorage->getToken() && $authorizationChecker->isGranted($role);
    }
}

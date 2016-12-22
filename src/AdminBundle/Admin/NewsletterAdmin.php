<?php

namespace AdminBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Route\RouteCollection;
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
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('mail')
            ->add('token')
            ->add('_action', null, array(
                'actions' => array(
                    'edit' => array(),
                    'delete' => array(
                        'template' => '@Admin/CRUD/List/action_delete.html.twig',
                        'confirmation' => array(
                            'title' => $this->trans('list.delete_confirmation_title'),
                            'message' => $this->trans('list.delete_confirmation_message')
                        )
                    )
                )
            ))
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('mail')
        ;
    }

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('mail')
            ->add('token')
        ;
    }

    public function preValidate($newsletter)
    {
        $tokenGenerator = $this->getConfigurationPool()->getContainer()->get('fos_user.util.token_generator');
        $newsletter->setToken($tokenGenerator->generateToken());
    }
}
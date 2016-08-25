<?php

namespace AdminBundle\Admin;

use AppBundle\AppEvents;
use AppBundle\Entity\Competition;
use AppBundle\Event\CompetitionEvent;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class CompetitionAdmin extends AbstractAdmin
{
    /**
     * @var string
     */
    protected $translationDomain = 'CompetitionAdmin';

    /**
     * @var string
     */
    public $flashIcon = '<i class="fa fa-3x fa-calendar"></i>';

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
        '_sort_by' => 'created'
    );

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('title', 'text')
            ->add('bowling', 'text')
            ->add('partners', 'number')
            ->add('start', 'sonata_type_datetime_picker')
            ->add('end', 'sonata_type_datetime_picker')
            ->add('description', 'textarea', array(
                'attr' => array(
                    'class' => 'ckeditor'
                ),
                'required' => true
            ))
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('created', 'datetime', array('format' => 'd/m/Y, H:i'))
            ->addIdentifier('title')
            ->add('bowling')
            ->add('partners')
            ->add('start', 'datetime', array('format' => 'd/m/Y, H:i'))
            ->add('end', 'datetime', array('format' => 'd/m/Y, H:i'))
            ->add('author')
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('created')
            ->add('title')
            ->add('bowling')
            ->add('partners')
            ->add('start')
            ->add('end')
            ->add('author')
        ;
    }

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->with('tab_competition', array(
                'class' => 'col-md-8'
            ))
            ->add('title')
            ->add('bowling')
            ->add('partners')
            ->add('start', 'datetime', array('format' => 'd/m/Y, H:i'))
            ->add('end', 'datetime', array('format' => 'd/m/Y, H:i'))
            ->add('description', null, array('safe' => true))
            ->end()
            ->with('tab_publish', array(
                'class' => 'col-md-4'
            ))
            ->add('created', 'datetime', array('format' => 'd/m/Y, H:i'))
            ->add('author')
            ->end()
        ;
    }

    public function preValidate($competition)
    {
        $this->setSlug($competition);
    }

    public function preUpdate($competition)
    {
        $this->setSlug($competition);
    }

    public function postRemove($competition)
    {
        $eventDispatcher = $this->getConfigurationPool()->getContainer()->get('event_dispatcher');
        $event = new CompetitionEvent($competition);

        $eventDispatcher->dispatch(AppEvents::COMPETITION_REMOVE_EVENT, $event);
    }

    private function setSlug(Competition $competition)
    {
        $title = $competition->getTitle();
        $slugify = $this->getConfigurationPool()->getContainer()->get('sonata.core.slugify.cocur');

        $competition->setSlug($slugify->slugify($title));
    }
}
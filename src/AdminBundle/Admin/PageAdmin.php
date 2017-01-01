<?php

namespace AdminBundle\Admin;

use AppBundle\Entity\Page;
use Doctrine\Common\Collections\ArrayCollection;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;

class PageAdmin extends AbstractAdmin
{
    /**
     * @var string
     */
    protected $translationDomain = 'PageAdmin';

    /**
     * @var string
     */
    public $flashIcon = '<i class="fa fa-3x fa-file-text-o"></i>';

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

    /**
     * @var ArrayCollection
     */
    protected $oldPdfs;

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('tab_page', array(
                'class' => 'col-md-10'
            ))
                ->add('title', 'text')
                ->add('description', 'text')
                ->add('content', 'textarea', array(
                    'attr' => array(
                        'class' => 'ckeditor'
                    ),
                    'required' => false
                ))
            ->end()
            ->with('tab_publish', array(
                'class' => 'col-md-2'
            ))
                ->add('priority', 'number')
                ->add('published', 'checkbox', array(
                    'required' => false
                ))
            ->end()
            ->with('tab_pdfs', array(
                'class' => 'col-md-6'
            ))
            ->add('pdfs', 'sonata_type_collection', array(
                'label' => false,
                'by_reference' => false,
                //'cascade_validation' => true,
            ), array(
                'edit' => 'inline',
                'inline' => 'table'
            ))
            ->end()
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('created', 'datetime', array('format' => 'd/m/Y, H:i'))
            ->addIdentifier('title')
            ->add('description')
            ->add('authorName')
            ->add('published')
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('created')
            ->add('title')
            ->add('authorName')
        ;
    }

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->with('tab_page', array(
                'class' => 'col-md-8'
            ))
                ->add('title')
                ->add('description')
                ->add('content', null, array('safe' => true))
            ->end()
            ->with('tab_publish', array(
                'class' => 'col-md-4'
            ))
                ->add('priority')
                ->add('published')
            ->end()
            ->with('tab_pdfs', array(
                'class' => 'col-md-6'
            ))
                ->add('pdfs', null, array(
                    'display_label' => false,
                    'template' => 'AdminBundle:CRUD/Show:field_pdfs.html.twig'
                ))
            ->end()
        ;
    }

    /**
     * This function is called before the page's creating.
     *
     * @param $page
     */
    public function preValidate($page)
    {
        $this->setSlug($page);
        $this->handleEmptyElementsGiven($page, array('pdfs'));
    }

    /**
     * This function is called before the page's updating.
     *
     * @param mixed $page
     */
    public function preUpdate($page)
    {
        $this->setSlug($page);
        $this->handleEmptyElementsGiven($page, array('pdfs'));
    }

    /**
     * This function is called just after the page was updated.
     *
     * @param Page $page
     */
    public function postUpdate($page)
    {
        $this->handleRemovedElements($page, array(
            'pdfs'   => null
        ));
    }

    /**
     * Set oldPdfs
     *
     * @param ArrayCollection $oldPdfs
     */
    public function setOldPdfs(ArrayCollection $oldPdfs)
    {
        $this->oldPdfs = $oldPdfs;
    }

    /**
     * Get oldPdfs
     *
     * @return ArrayCollection
     */
    public function getOldPdfs()
    {
        return $this->oldPdfs;
    }

    /**
     * Handle old elements.
     *
     * @param Page $page
     * @param array $fields
     */
    public function handleOldElements(Page $page, array $fields)
    {
        foreach($fields as $field){
            $this->setOldElements($page, $field);
        }
    }

    /**
     * Keep elements in memory for use it after the page's updating.
     *
     * @param Page $page
     */
    public function setOldElements(Page $page, $field)
    {
        $setter = 'setOld' . ucfirst($field);
        $getter = 'get' . ucfirst($field);

        if(method_exists($this, $setter) && method_exists($page, $getter)){
            $elements = $page->$getter();

            if(is_array($elements) || ($elements instanceof \ArrayAccess)){
                $oldElements = new ArrayCollection();

                foreach($page->$getter() as $element){
                    $oldElements->add($element);
                }

                $this->$setter($oldElements);
            } else {
                $this->$setter($elements);
            }
        }
    }

    /**
     * Handle removed elements.
     *
     * @param Page $page
     * @param array $fields
     */
    private function handleRemovedElements(Page $page, array $fields)
    {
        foreach($fields as $field => $callback){
            $this->deleteRemovedElements($page, $field, $callback);
        }
    }

    /**
     * Delete elements which are removed in form.
     *
     * @param Page $page
     * @param string $field
     * @param null|string $callback
     */
    private function deleteRemovedElements(Page $page, $field, $callback = null)
    {
        $oldGetter = 'getOld' . ucfirst($field);
        $currentGetter = 'get' . ucfirst($field);

        $removedElements = array();

        $oldElements = $this->$oldGetter();
        $currentElements = $page->$currentGetter();

        $modelManager = $this->getModelManager();

        foreach($oldElements as $element){
            if(false === $currentElements->contains($element)){
                if($callback !== null){
                    $removedElements[] = $element;
                } else {
                    $modelManager->delete($element);
                }
            }
        }

        if($callback !== null){
            $this->$callback($removedElements);
        }
    }

    /**
     * Handle empty elements given when the user add empty element form.
     *
     * @param Page $page
     * @param array $fields
     */
    private function handleEmptyElementsGiven(Page $page, array $fields)
    {
        foreach($fields as $field){
            $this->removeEmptyElementsGiven($page, $field);
        }
    }

    /**
     * Remove empty elements given when the user add empty element form.
     *
     * @param Page $page
     * @param string $field
     */
    private function removeEmptyElementsGiven(Page $page, $field)
    {
        $getter = 'get' . ucfirst($field);

        $elements = $page->$getter();
        foreach($elements as $element){
            if(null === $element->getId() && null === $element->getFile()){
                $elements->removeElement($element);
            }
        }
    }

    /**
     * Slugify the page's title.
     *
     * @param Page $page
     */
    private function setSlug(Page $page)
    {
        $title = $page->getTitle();
        $slugify = $this->getConfigurationPool()->getContainer()->get('sonata.core.slugify.cocur');

        $page->setSlug($slugify->slugify($title));
    }
}

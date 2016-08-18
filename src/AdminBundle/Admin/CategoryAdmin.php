<?php

namespace AdminBundle\Admin;

use AppBundle\Entity\Category;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;

class CategoryAdmin extends AbstractAdmin
{
    /**
     * @var string
     */
    protected $translationDomain = 'CategoryAdmin';

    /**
     * @var string
     */
    public $flashIcon = '<i class="fa fa-3x fa-folder-o"></i>';

    /**
     * @var int
     */
    protected $maxPerPage = 15;

    /**
     * @var array
     */
    protected $datagridValues = array(
        '_page' => 1,
        '_sort_order' => 'ASC',
        '_sort_by' => 'title'
    );

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('form.tab_category', array(
                'class' => 'col-md-10'
            ))
                ->add('title', 'text')
            ->end()
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('title')
            ->add('postsCount')
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title')
        ;
    }

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->with('tab_category', array(
                'class' => 'col-md-12'
            ))
                ->add('title')
            ->end()
            ->with('tab_posts', array(
                'class' => 'col-md-12'
            ))
                ->add('posts')
            ->end()
        ;
    }

    /**
     * This function is called before the category's creating.
     *
     * @param $category
     */
    public function preValidate($category)
    {
        $this->setSlug($category);
    }

    /**
     * This function is called before the category's updating.
     *
     * @param mixed $category
     */
    public function preUpdate($category)
    {
        $this->setSlug($category);
    }

    /**
     * Slugify the category's title.
     *
     * @param Category $category
     */
    private function setSlug(Category $category)
    {
        $title = $category->getTitle();
        $slugify = $this->getConfigurationPool()->getContainer()->get('sonata.core.slugify.cocur');

        $category->setSlug($slugify->slugify($title));
    }
}
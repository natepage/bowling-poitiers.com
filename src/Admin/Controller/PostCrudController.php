<?php
declare(strict_types=1);

namespace App\Admin\Controller;

use App\Admin\Field\StatusField;
use App\Entity\Post;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

final class PostCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Post::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions = parent::configureActions($actions);
        $actions->add(Crud::PAGE_INDEX, Action::DETAIL);

        return $actions;
    }

    public function configureCrud(Crud $crud): Crud
    {
        $crud = parent::configureCrud($crud);
        $crud->setDefaultSort(['updatedAt' => 'DESC']);

        return $crud;
    }

    public function configureFields(string $pageName): iterable
    {
        $statusField = StatusField::new('status')
            ->mapping([
                Post::STATUS_DRAFT => ['entity.post.status.draft', 'warning'],
                Post::STATUS_PUBLISHED => ['entity.post.status.published', 'success'],
            ]);

        yield TextField::new('id')
            ->hideOnForm()
            ->hideOnIndex();

        if ($pageName === Crud::PAGE_DETAIL) {
            yield $statusField;
        }

        yield TextField::new('title');
        yield TextField::new('slug')->hideOnForm();
        yield TextField::new('description');
        yield TextEditorField::new('content')
            ->hideOnIndex();

        if (\in_array($pageName, [Crud::PAGE_INDEX, Crud::PAGE_NEW, Crud::PAGE_EDIT], true)) {
            yield $statusField;
        }

        yield DateTimeField::new('createdAt')->onlyOnDetail();
        yield DateTimeField::new('updatedAt')->hideOnForm();
    }
}

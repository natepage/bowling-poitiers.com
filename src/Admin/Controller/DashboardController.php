<?php
declare(strict_types=1);

namespace App\Admin\Controller;

use App\Entity\Post;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class DashboardController extends AbstractDashboardController
{
    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToCrud('Posts', 'fas fa-list', Post::class);
    }

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('@EasyAdmin/layout.html.twig');
    }
}

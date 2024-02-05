<?php

declare(strict_types=1);

namespace App\Home;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'show_home')]
    public function index(): Response
    {
        return $this->render('@app.src_dir/Home/output.twig');
    }
}

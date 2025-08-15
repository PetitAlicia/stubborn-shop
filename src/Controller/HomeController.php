<?php

namespace App\Controller;

use App\Entity\Sweatshirt;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $featuredSweatshirts = $entityManager->getRepository(Sweatshirt::class)
            ->findBy(['featured' => true]);

        return $this->render('home/index.html.twig', [
            'featuredSweatshirts' => $featuredSweatshirts,
        ]);
    }
}

<?php

namespace App\Controller;

use App\Repository\SweatshirtRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    #[Route('/products', name: 'app_products')]
    public function list(SweatshirtRepository $repository, Request $request): Response
    {
        $priceFilter = $request->query->get('price_range');

        $queryBuilder = $repository->createQueryBuilder('s');

        if ($priceFilter) {
            switch ($priceFilter) {
                case '10-29':
                    $queryBuilder->andWhere('s.price >= 10 AND s.price <= 29');
                    break;
                case '29-35':
                    $queryBuilder->andWhere('s.price > 29 AND s.price <= 35');
                    break;
                case '35-50':
                    $queryBuilder->andWhere('s.price > 35 AND s.price <= 50');
                    break;
            }
        }

        $sweatshirts = $queryBuilder->getQuery()->getResult();

        return $this->render('product/products.html.twig', [
            'sweatshirts' => $sweatshirts,
            'selectedFilter' => $priceFilter,
        ]);
    }

    #[Route('/products/{id}', name: 'app_product_show', requirements: ['id' => '\d+'])]
    public function show(int $id, SweatshirtRepository $repository): Response
    {
        $sweatshirt = $repository->find($id);

        if (!$sweatshirt) {
            throw $this->createNotFoundException('Sweatshirt non trouvé.');
        }

        return $this->render('product/show_product.html.twig', [
            'sweatshirt' => $sweatshirt,
        ]);
    }
}

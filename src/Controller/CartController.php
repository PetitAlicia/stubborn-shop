<?php

namespace App\Controller;

use App\Entity\Sweatshirt;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    #[Route('/cart/add/{id}', name: 'app_cart_add', methods: ['POST'])]
    public function addToCart(int $id, Request $request, EntityManagerInterface $em): RedirectResponse
    {
        $size = $request->request->get('size');

        $sweatshirt = $em->getRepository(Sweatshirt::class)->find($id);
        if (!$sweatshirt) {
            $this->addFlash('error', 'Sweat-shirt non trouvé.');
            return $this->redirectToRoute('app_products');
        }

        if ($sweatshirt->getStockBySize($size) <= 0) {
        $this->addFlash('error', 'Taille indisponible.');
        return $this->redirectToRoute('app_product_show', ['id' => $id]);
        }

        $session = $request->getSession();
        $cart = $session->get('cart', []);
        $cart[] = ['id' => $id, 'size' => $size];
        $session->set('cart', $cart);

        $this->addFlash('success', 'Sweat-shirt ajouté au panier.');

        return $this->redirectToRoute('app_products');
    }

    #[Route('/cart', name: 'app_cart')]
    public function showCart(Request $request, EntityManagerInterface $em): Response
    {
        $session = $request->getSession();
        $cart = $session->get('cart', []);

        $items = [];
        foreach ($cart as $item) {
            $key = $item['id'] . '_' . $item['size'];
            if (!isset($items[$key])) {
                $sweatshirt = $em->getRepository(Sweatshirt::class)->find($item['id']);
                if (!$sweatshirt) {
                    continue;
                }
                $items[$key] = [
                    'sweatshirt' => $sweatshirt,
                    'size' => $item['size'],
                    'quantity' => 1,
                ];
            } else {
                $items[$key]['quantity']++;
            }
        }

        return $this->render('cart/cart.html.twig', [
            'items' => $items,
        ]);
    }

    #[Route('/cart/remove/{id}/{size}', name: 'app_cart_remove', methods: ['POST'])]
    public function removeFromCart(int $id, string $size, Request $request): Response
    {
        $session = $request->getSession();
        $cart = $session->get('cart', []);

        foreach ($cart as $key => $item) {
            if ((int)$item['id'] === $id && $item['size'] === $size) {
                unset($cart[$key]);
                break;
            }
        }

        $cart = array_values($cart);
        $session->set('cart', $cart);

        $this->addFlash('success', 'Article supprimé du panier.');

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/finalize', name: 'app_cart_finalize')]
    public function finalizeCart(Request $request): Response
    {
        $session = $request->getSession();
        $session->remove('cart');

        $this->addFlash('success', 'Achat validé. Merci pour votre commande.');

        return $this->redirectToRoute('app_cart');
    }
}

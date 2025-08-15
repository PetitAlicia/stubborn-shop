<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CartControllerTest extends WebTestCase
{
    public function testAddToCart(): void
    {
        $client = static::createClient();

        $client->request('POST', '/cart/add/1', ['size' => 'M']);

        $this->assertTrue($client->getResponse()->isRedirect());

        $client->followRedirect();

        $this->assertSelectorTextContains('.flash-success', 'Sweat-shirt ajouté au panier.');
    }

    public function testFinalizeCart(): void
    {
        $client = static::createClient();

        $client->request('GET', '/cart/finalize');

        $this->assertTrue($client->getResponse()->isRedirect('/cart'));

        $crawler = $client->followRedirect();

        $this->assertSelectorTextContains('.flash-success', 'Achat validé. Merci pour votre commande.');
    }
}

<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MeControllerTest extends WebTestCase
{
    public function testHomePage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Välkommen till min me-sida!');
        $this->assertSelectorExists('.profile-image');
        $this->assertSelectorTextContains('p', 'Jag heter Tuan Anh Pham');
    }

    public function testAboutPage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/about');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Om MVC-kursen');
        $this->assertSelectorExists('img[alt="MVC-bild"]');
        $this->assertSelectorExists('a[href*="github.com/dbwebb-se/mvc"]');
        $this->assertSelectorExists('a[href*="github.com/tuananhpham95"]');
    }

    public function testReportPage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/report');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Redovisning');
        $this->assertSelectorExists('a[href="#kmom01"]');
        $this->assertSelectorExists('section#kmom01');
        $this->assertSelectorExists('section#kmom06');
    }

    public function testLuckyPage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/lucky');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Din lyckosiffra är:');

        $responseContent = $client->getResponse()->getContent();
        $this->assertNotFalse($responseContent);

        preg_match(
            '/Din lyckosiffra är: (\d+)/',
            $responseContent,
            $matches
        );

        $luckyNumber = (int) ($matches[1] ?? 0);

        $this->assertGreaterThanOrEqual(1, $luckyNumber);
        $this->assertLessThanOrEqual(100, $luckyNumber);
    }
}

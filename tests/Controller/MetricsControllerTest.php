<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MetricsControllerTest extends WebTestCase
{
    public function testMetricsIndex(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/metrics');

        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains('main h1', 'Metrics Analysis for Code Quality');

        $h2s = $crawler->filter('h2');
        $this->assertGreaterThanOrEqual(5, $h2s->count());

        $this->assertStringContainsString('Introduktion', $h2s->eq(0)->text());
        $this->assertStringContainsString('Phpmetrics', $h2s->eq(1)->text());
        $this->assertStringContainsString('Scrutinizer', $h2s->eq(2)->text());
        $this->assertStringContainsString('Förbättringar', $h2s->eq(3)->text());
        $this->assertStringContainsString('Diskussion', $h2s->eq(4)->text());

        $this->assertGreaterThanOrEqual(3, $crawler->filter('img[src*="scrutinizer-ci.com"]')->count());
        $this->assertGreaterThanOrEqual(1, $crawler->filter('img[src*="docs/metrics"]')->count());
        $this->assertGreaterThanOrEqual(1, $crawler->filter('img[src*="docs/scrutinizer"]')->count());
    }
}

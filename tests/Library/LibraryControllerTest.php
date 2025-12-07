<?php

namespace App\Tests\Library;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LibraryControllerTest extends WebTestCase
{
    public function testIndexPageLoads(): void
    {
        $client = static::createClient();
        $client->request('GET', '/library/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('h1');
    }

    public function testShowAllBooksPageLoads(): void
    {
        $client = static::createClient();
        $client->request('GET', '/library/show');

        $this->assertResponseIsSuccessful();
    }

    public function testCreateFormDisplays(): void
    {
        $client = static::createClient();
        $client->request('GET', '/library/create');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');
    }

    public function testCreateBook(): void
    {
        $client = static::createClient();

        // Open form
        $crawler = $client->request('GET', '/library/create');

        // Fill and submit
        $form = $crawler->selectButton('Create Book')->form([
            'form[title]' => 'Test Book',
            'form[isbn]' => '1234567890',
            'form[author]' => 'Tester',
            'form[image]' => null,
        ]);

        $client->submit($form);

        // A successful create redirects to show-all
        $this->assertResponseRedirects('/library/show');
    }

    public function testReset(): void
    {
        $client = static::createClient();
        $client->request('GET', '/library/reset');

        // Should redirect to show-all after seeding database
        $this->assertResponseRedirects('/library/show');
    }
}

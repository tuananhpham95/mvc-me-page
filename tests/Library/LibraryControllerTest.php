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
    }

    public function testShowAllBooksPageLoads(): void
    {
        $client = static::createClient();
        $client->request('GET', '/library/show');
        $this->assertResponseIsSuccessful();
    }

    public function testCreateFormPageLoads(): void
    {
        $client = static::createClient();
        $client->request('GET', '/library/create');
        $this->assertResponseIsSuccessful();
    }
}
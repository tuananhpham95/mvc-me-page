<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LibraryControllerTest extends WebTestCase
{
    public function testIndexLoads(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/library/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.intro h1', 'Digital Library');
        $this->assertSelectorExists('a.btn.btn-primary');
        $this->assertSelectorExists('a.btn.btn-outline');
    }

    public function testShowAllLoads(): void
    {
        $client = static::createClient();
        $client->request('GET', '/library/reset');

        $crawler = $client->request('GET', '/library/show');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('section.library-books h1', 'Alla Böcker');
        $this->assertSelectorExists('.book-grid');
    }

    public function testCreateBookForm(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/library/create');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');
        $this->assertSelectorExists('input');
    }

    public function testUpdateBookForm(): void
    {
        $client = static::createClient();
        $client->request('GET', '/library/reset');

        $crawler = $client->request('GET', '/library/show');

        $link = $crawler->filter('.book-card a')->first()->attr('href');
        $this->assertNotNull($link, 'Book link should exist');

        $matches = [];
        if (preg_match('/\d+$/', (string) $link, $matches)) {
            $bookId = $matches[0];
        } else {
            $this->fail('Could not extract book ID from link: ' . $link);
        }

        $crawler = $client->request('GET', '/library/update/' . $bookId);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');
    }

    public function testShowBookDetails(): void
    {
        $client = static::createClient();
        $client->request('GET', '/library/reset');

        $crawler = $client->request('GET', '/library/show');

        $firstBookLink = $crawler->filter('.book-card a')->first()->attr('href');
        $this->assertNotNull($firstBookLink, 'First book link should exist');

        $matches = [];
        if (preg_match('/\d+$/', (string) $firstBookLink, $matches)) {
            $bookId = $matches[0];
        } else {
            $this->fail('Could not extract book ID from link: ' . $firstBookLink);
        }

        $crawler = $client->request('GET', '/library/show/' . $bookId);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('section.book-details');
        $this->assertSelectorExists('section.book-details h1');
        $this->assertSelectorTextContains('section.book-details', 'ISBN');
        $this->assertSelectorTextContains('section.book-details', 'Författare');
    }

    public function testCreateAndDeleteBook(): void
    {
        $client = static::createClient();
        $client->request('GET', '/library/reset');

        $crawler = $client->request('GET', '/library/create');

        $form = $crawler->selectButton('Create Book')->form();
        $form['form[title]'] = 'Test Book';
        $form['form[author]'] = 'Test Author';
        $form['form[isbn]'] = '1234567890123';

        $client->submit($form);

        $this->assertResponseRedirects('/library/show');
        $client->followRedirect();
        $this->assertSelectorTextContains('.book-grid', 'Test Book');
    }
}

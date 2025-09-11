<?php

   namespace App\Tests\Controller;

   use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

   class LibraryControllerTest extends WebTestCase
   {
       public function testIndex(): void
       {
           $client = static::createClient([], ['HTTP_HOST' => 'localhost']);
           $crawler = $client->request('GET', '/library');

           $this->assertResponseIsSuccessful();
           $this->assertSelectorTextContains('h1', 'Välkommen till Vårt Digitala Bibliotek');
       }

       public function testCreate(): void
       {
           $client = static::createClient([], ['HTTP_HOST' => 'localhost']);
           $crawler = $client->request('GET', '/library/create');

           $this->assertResponseIsSuccessful();
           $this->assertSelectorTextContains('h1', 'Lägg till en ny bok');
       }

       public function testDeleteSuccess(): void
       {
           $client = static::createClient([], ['HTTP_HOST' => 'localhost']);
           $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

           // Skapa en testentitet
           $book = new Book();
           $book->setTitle('Test Book');
           $book->setIsbn('1234567890');
           $book->setAuthor('Test Author');
           $entityManager->persist($book);
           $entityManager->flush();

           // Hämta CSRF-token
           $crawler = $client->request('GET', '/library/delete/' . $book->getId());
           $token = $client->getContainer()->get('security.csrf.token_manager')->getToken('delete' . $book->getId())->getValue();

           $client->request('POST', '/library/delete/' . $book->getId(), [
               '_token' => $token,
           ]);

           $this->assertResponseRedirects('/library/show');
       }
   }

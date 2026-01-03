<?php

namespace App\Tests\Command;

use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class SeedBooksCommandTest extends KernelTestCase
{
    public function testExecuteSeedsThreeBooks(): void
    {
        // Boot kernel och hämta entity manager
        $kernel = self::bootKernel();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $kernel->getContainer()->get('doctrine.orm.entity_manager');

        // Rensa databasen
        $entityManager->createQuery('DELETE FROM App\Entity\Book')->execute();

        // Skapa application med bootat kernel
        $application = new Application($kernel);

        $command = $application->find('app:seed-books');
        $commandTester = new CommandTester($command);

        $commandTester->execute([]);

        // Kontrollera output
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Successfully seeded 3 books.', $output);
        $this->assertEquals(0, $commandTester->getStatusCode());

        // Kontrollera att 3 böcker lades till
        $books = $entityManager->getRepository(Book::class)->findAll();
        $this->assertCount(3, $books);

        // Kontrollera data
        $titles = array_map(fn(Book $book) => $book->getTitle(), $books);
        $this->assertContains('To Kill a Mockingbird', $titles);
        $this->assertContains('1984', $titles);
        $this->assertContains('Pride and Prejudice', $titles);

        $isbns = array_map(fn(Book $book) => $book->getIsbn(), $books);
        $this->assertContains('9780446310789', $isbns);
        $this->assertContains('9780451524935', $isbns);
        $this->assertContains('9780141439518', $isbns);

        $authors = array_map(fn(Book $book) => $book->getAuthor(), $books);
        $this->assertContains('Harper Lee', $authors);
        $this->assertContains('George Orwell', $authors);
        $this->assertContains('Jane Austen', $authors);

        $images = array_map(fn(Book $book) => $book->getImage(), $books);
        $this->assertContains('to-kill-a-mockingbird.jpg', $images);
        $this->assertContains('1984.jpeg', $images);
        $this->assertContains('pride-and-prejudice.jpg', $images);
    }
}
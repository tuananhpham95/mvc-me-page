<?php

namespace App\Command;

use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:seed-books')]
class SeedBooksCommand extends Command
{
    private EntityManagerInterface $entityManager;

    /**
     * Constructs the command with an EntityManager.
     *
     * @param EntityManagerInterface $entityManager the Doctrine entity manager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    /**
     * Configures the command description.
     */
    protected function configure(): void
    {
        $this->setDescription('Seeds the database with initial book data.');
    }

    /**
     * Executes the command to seed the database with three books.
     *
     * @param InputInterface  $input  the console input
     * @param OutputInterface $output the console output
     *
     * @return int the command status code
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $books = [
            [
                'title' => 'To Kill a Mockingbird',
                'isbn' => '9780446310789',
                'author' => 'Harper Lee',
                'image' => 'to-kill-a-mockingbird.jpg',
            ],
            [
                'title' => '1984',
                'isbn' => '9780451524935',
                'author' => 'George Orwell',
                'image' => '1984.jpeg',
            ],
            [
                'title' => 'Pride and Prejudice',
                'isbn' => '9780141439518',
                'author' => 'Jane Austen',
                'image' => 'pride-and-prejudice.jpg',
            ],
        ];

        foreach ($books as $bookData) {
            $book = new Book();
            $book->setTitle($bookData['title']);
            $book->setIsbn($bookData['isbn']);
            $book->setAuthor($bookData['author']);
            $book->setImage($bookData['image']);
            $this->entityManager->persist($book);
        }

        $this->entityManager->flush();
        $output->writeln('Successfully seeded 3 books.');

        return Command::SUCCESS;
    }
}

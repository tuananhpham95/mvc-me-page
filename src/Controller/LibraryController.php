<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/library')]
class LibraryController extends AbstractController
{
    /**
     * Displays the library index page.
     *
     * @return Response The rendered library index page
     */
    #[Route('/', name: 'library_index')]
    public function index(): Response
    {
        return $this->render('library/index.html.twig');
    }

    /**
     * Handles the creation of a new book.
     *
     * @param Request                $request       The HTTP request object
     * @param EntityManagerInterface $entityManager The Doctrine entity manager
     *
     * @return Response The rendered create form or a redirect to the show all page
     */
    #[Route('/create', name: 'library_create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $book = new Book();

        $form = $this->createFormBuilder($book)
            ->add('title', TextType::class, ['label' => 'Title'])
            ->add('isbn', TextType::class, ['label' => 'ISBN'])
            ->add('author', TextType::class, ['label' => 'Author'])
            ->add('image', TextType::class, ['label' => 'Image URL', 'required' => false])
            ->add('save', SubmitType::class, ['label' => 'Create Book'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($book);
            $entityManager->flush();
            $this->addFlash('success', 'Book created successfully.');

            return $this->redirectToRoute('library_show_all');
        }

        return $this->render('library/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Displays a single book by ID.
     *
     * @param Book $book The book entity to display
     *
     * @return Response The rendered book details page
     */
    #[Route('/show/{id}', name: 'library_show', requirements: ['id' => '\d+'])]
    public function show(Book $book): Response
    {
        return $this->render('library/show_one.html.twig', [
            'book' => $book,
        ]);
    }

    /**
     * Displays all books in the library.
     *
     * @param BookRepository $bookRepository The book repository
     *
     * @return Response The rendered list of all books
     */
    #[Route('/show', name: 'library_show_all')]
    public function showAll(BookRepository $bookRepository): Response
    {
        return $this->render('library/show_ALL.html.twig', [
            'books' => $bookRepository->findAll(),
        ]);
    }

    /**
     * Handles the updating of an existing book.
     *
     * @param Request                $request       The HTTP request object
     * @param Book                   $book          The book entity to update
     * @param EntityManagerInterface $entityManager The Doctrine entity manager
     *
     * @return Response The rendered update form or a redirect to the show all page
     */
    #[Route('/update/{id}', name: 'library_update', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function update(Request $request, Book $book, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder($book)
            ->add('title', TextType::class, ['label' => 'Title'])
            ->add('isbn', TextType::class, ['label' => 'ISBN'])
            ->add('author', TextType::class, ['label' => 'Author'])
            ->add('image', TextType::class, ['label' => 'Image URL', 'required' => false])
            ->add('save', SubmitType::class, ['label' => 'Update Book'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Book updated successfully.');

            return $this->redirectToRoute('library_show_all');
        }

        return $this->render('library/update.html.twig', [
            'form' => $form->createView(),
            'book' => $book,
        ]);
    }

    /**
     * Deletes a book by ID if the CSRF token is valid.
     *
     * @param Request                $request       The HTTP request object
     * @param Book                   $book          The book entity to delete
     * @param EntityManagerInterface $entityManager The Doctrine entity manager
     *
     * @return Response A redirect to the show all page
     */
    #[Route('/delete/{id}', name: 'library_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Request $request, Book $book, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$book->getId(), (string)$request->request->get('_token'))) {
            $entityManager->remove($book);
            $entityManager->flush();
            $this->addFlash('success', 'Book deleted successfully.');
        }

        return $this->redirectToRoute('library_show_all');
    }

    /**
     * Resets the database by deleting all books and seeding with default books.
     *
     * @param EntityManagerInterface $entityManager The Doctrine entity manager
     *
     * @return Response A redirect to the show all page
     */
    #[Route('/reset', name: 'library_reset')]
    public function reset(EntityManagerInterface $entityManager): Response
    {
        $entityManager->createQuery('DELETE FROM App\Entity\Book')->execute();

        $books = [
            ['title' => 'To Kill a Mockingbird', 'isbn' => '9780446310789', 'author' => 'Harper Lee', 'image' => '/images/to-kill-a-mockingbird.jpg'],
            ['title' => '1984', 'isbn' => '9780451524935', 'author' => 'George Orwell', 'image' => '/images/1984.jpg'],
            ['title' => 'Pride and Prejudice', 'isbn' => '9780141439518', 'author' => 'Jane Austen', 'image' => '/images/pride-and-prejudice.jpg'],
        ];

        foreach ($books as $bookData) {
            $book = new Book();
            $book->setTitle($bookData['title']);
            $book->setIsbn($bookData['isbn']);
            $book->setAuthor($bookData['author']);
            $book->setImage($bookData['image']);
            $entityManager->persist($book);
        }

        $entityManager->flush();
        $this->addFlash('success', 'Database reset successfully.');

        return $this->redirectToRoute('library_show_all');
    }
}

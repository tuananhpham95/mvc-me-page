<?php

namespace App\Controller;

use App\Card\DeckOfCards;
use App\Entity\Book;
use App\Repository\BookRepository;
use App\Service\DeckSessionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Global API Controller for the course project.
 *
 * Provides various JSON endpoints used throughout the course:
 * - Random quote and current time
 * - Card deck operations (view, shuffle, draw one or multiple cards)
 * - Library book data (all books or by ISBN)
 *
 * All routes are under /api
 * Includes a landing page listing all available endpoints.
 */
class ApiController extends AbstractController
{
    public function __construct(
        private readonly DeckSessionService $deckService
    ) {
    }

    /**
     * API landing page with overview of all available endpoints.
     *
     * Displays a human-readable list of all JSON API routes with descriptions and example links.
     *
     * @return Response Rendered API documentation page
     */
    #[Route('/api', name: 'api_landing')]
    public function apiLanding(): Response
    {
        $routes = [
            ['route' => 'GET /api/quote', 'description' => 'Returns a random quote with date and timestamp.'],
            ['route' => 'GET /api/time', 'description' => 'Returns the current time, date, and timezone.'],
            ['route' => 'GET /api/deck', 'description' => 'Returns the sorted deck as JSON.'],
            ['route' => 'POST /api/deck/shuffle', 'description' => 'Shuffles the deck and returns it as JSON.'],
            ['route' => 'POST /api/deck/draw', 'description' => 'Draws one card and returns it with remaining count as JSON.'],
            ['route' => 'POST /api/deck/draw/{number}', 'description' => 'Draws specified number of cards and returns them with remaining count as JSON.'],
            ['route' => 'GET /api/game', 'description' => 'Returns the current state of the game 21 as JSON.'],
            ['route' => 'GET /api/library/books', 'description' => 'Returns all books in the library as JSON.'],
            ['route' => 'GET /api/library/book/{isbn}', 'description' => 'Returns a book by ISBN as JSON. Example: /api/library/book/9780446310789'],
        ];

        return $this->render('api/index.html.twig', [
            'routes' => $routes,
        ]);
    }

    /**
     * Returns a random inspirational quote with current date and timestamp.
     *
     * @return JsonResponse JSON with quote, date and timestamp (Stockholm timezone)
     */
    #[Route('/api/quote', name: 'api_quote')]
    public function quote(): JsonResponse
    {
        $quotes = [
            'Det finns ingen bättre utbildning än motgångar. - Benjamin Disraeli',
            'Sjömannen ber inte om medvind, han lär sig segla. - Gustaf Lindborg',
            'Att misslyckas är bara ett annat sätt att lära sig hur man gör något rätt. - Marian Wright Edelman',
            'Du behöver inte bli någon du inte är för att bli bättre än du var. - Sidney Poitier',
            'Det är klokare att gå sin egen väg än att gå vilse i andras fotspår. - Okänd',
        ];

        $randomQuote = $quotes[array_rand($quotes)];
        $date = new \DateTime('now', new \DateTimeZone('Europe/Stockholm'));

        $data = [
            'quote' => $randomQuote,
            'date' => $date->format('Y-m-d'),
            'timestamp' => $date->format('Y-m-d H:i:s'),
        ];

        return new JsonResponse($data);
    }

    /**
     * Returns current time and date information.
     *
     * @return JsonResponse JSON with current time, date and timezone (Stockholm)
     */
    #[Route('/api/time', name: 'api_time')]
    public function time(): JsonResponse
    {
        $date = new \DateTime('now', new \DateTimeZone('Europe/Stockholm'));

        $data = [
            'current_time' => $date->format('H:i:s'),
            'current_date' => $date->format('Y-m-d'),
            'timezone' => $date->getTimezone()->getName(),
        ];

        return new JsonResponse($data);
    }

    /**
     * Returns the current deck in sorted order.
     *
     * Uses session-stored deck via DeckSessionService.
     *
     * @param SessionInterface $session Current user session
     *
     * @return JsonResponse JSON with sorted cards and remaining count
     */
    #[Route('/api/deck', name: 'api_deck', methods: ['GET'])]
    public function apiDeck(SessionInterface $session): JsonResponse
    {
        $deck = $this->deckService->getDeck($session);
        $cards = array_map(fn ($card) => $card->getAsString(), $deck->getSortedCards());

        return $this->json([
            'cards' => $cards,
            'remaining' => $deck->getCardCount(),
        ]);
    }

    /**
     * Shuffles the deck and returns the new order.
     *
     * Creates a fresh shuffled deck and stores it in session.
     *
     * @param SessionInterface $session Current user session
     *
     * @return JsonResponse JSON with shuffled cards and remaining count
     */
    #[Route('/api/deck/shuffle', name: 'api_deck_shuffle', methods: ['POST'])]
    public function apiShuffle(SessionInterface $session): JsonResponse
    {
        $deck = new DeckOfCards();
        $deck->shuffle();
        $this->deckService->saveDeck($session, $deck);
        $cards = array_map(fn ($card) => $card->getAsString(), $deck->getCards());

        return $this->json([
            'cards' => $cards,
            'remaining' => $deck->getCardCount(),
        ]);
    }

    /**
     * Draws one card from the deck.
     *
     * @param SessionInterface $session Current user session
     *
     * @return JsonResponse JSON with drawn card and remaining count
     */
    #[Route('/api/deck/draw', name: 'api_deck_draw', methods: ['POST'])]
    public function apiDraw(SessionInterface $session): JsonResponse
    {
        $deck = $this->deckService->getDeck($session);
        $drawnCards = $deck->draw();
        $this->deckService->saveDeck($session, $deck);
        $cards = array_map(fn ($card) => $card->getAsString(), $drawnCards);

        return $this->json([
            'drawn' => $cards,
            'remaining' => $deck->getCardCount(),
        ]);
    }

    /**
     * Draws a specified number of cards from the deck.
     *
     * @param SessionInterface $session Current user session
     * @param int              $number  Number of cards to draw (validated as digit)
     *
     * @return JsonResponse JSON with drawn cards and remaining count
     */
    #[Route('/api/deck/draw/{number<\d+>}', name: 'api_deck_draw_number', methods: ['POST'])]
    public function apiDrawNumber(SessionInterface $session, int $number): JsonResponse
    {
        $deck = $this->deckService->getDeck($session);
        $drawnCards = $deck->draw($number);
        $this->deckService->saveDeck($session, $deck);
        $cards = array_map(fn ($card) => $card->getAsString(), $drawnCards);

        return $this->json([
            'drawn' => $cards,
            'remaining' => $deck->getCardCount(),
        ]);
    }

    /**
     * Returns all books from the library database.
     *
     * @param BookRepository $repo Injected book repository
     *
     * @return JsonResponse JSON array of all books (serialized entities)
     */
    #[Route('/api/library/books', name: 'api_books')]
    public function books(BookRepository $repo): JsonResponse
    {
        return $this->json($repo->findAll());
    }

    /**
     * Returns a single book by ISBN.
     *
     * @param BookRepository $repo Injected book repository
     * @param string         $isbn The book's ISBN
     *
     * @return JsonResponse JSON representation of the book or null if not found
     */
    #[Route('/api/library/book/{isbn}', name: 'api_book')]
    public function book(BookRepository $repo, string $isbn): JsonResponse
    {
        $book = $repo->findOneBy(['isbn' => $isbn]);

        return $this->json($book);
    }

    /**
     * Converts a Book entity to a simple array for JSON response.
     *
     * @param Book $book The book entity
     *
     * @return array{id: int|null, title: string, isbn: string, author: string, image: string|null} Book data as array
     */
    private function bookToArray(Book $book): array
    {
        return [
            'id' => $book->getId(),
            'title' => $book->getTitle(),
            'isbn' => $book->getIsbn(),
            'author' => $book->getAuthor(),
            'image' => $book->getImage(),
        ];
    }

    /**
     * Alternative endpoint: Returns all books as clean arrays (without entity serialization issues).
     *
     * @param BookRepository $bookRepository Injected repository
     *
     * @return JsonResponse JSON array of book data
     */
    #[Route('/library/books', name: 'api_library_books')]
    public function showAllBooks(BookRepository $bookRepository): JsonResponse
    {
        $books = $bookRepository->findAll();
        $data = array_map([$this, 'bookToArray'], $books);

        return $this->json($data);
    }

    /**
     * Alternative endpoint: Returns a single book by ISBN as clean array.
     *
     * Throws 404 if book not found.
     *
     * @param string         $isbn            The book's ISBN
     * @param BookRepository $bookRepository Injected repository
     *
     * @return JsonResponse JSON representation of the book
     */
    #[Route('/library/book/{isbn}', name: 'api_library_book')]
    public function showBookByIsbn(string $isbn, BookRepository $bookRepository): JsonResponse
    {
        /** @var Book|null $book */
        $book = $bookRepository->findOneBy(['isbn' => $isbn]);
        if (!$book) {
            throw $this->createNotFoundException('Book not found');
        }

        return $this->json($this->bookToArray($book));
    }
}

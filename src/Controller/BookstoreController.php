<?php 

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
use Doctrine\DBAL\Exception;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class BookstoreController extends AbstractController
{
    private $logger;
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    #[Route('/books', name: 'book_list')]
    public function list(Request $request, BookRepository $repository)
    {
        $response = new JsonResponse();
        $this->logger->info("Petición del nuevo listado de libros");
        $books = $repository->findAll();

        $booksArray = [];
        foreach ($books as $book) {
            $booksArray[] = [
                'id' => $book->getId(),
                'title' => $book->getTitle(),
                'image' => $book->getImage(),
            ];
        }

        return $response->setData([
            'data' => $booksArray
        ]);
        
    }
    #[Route('/books/create', name: 'book_create')]
    public function create(Request $request, EntityManagerInterface $entityManagerInterface)
    {
        $response = new JsonResponse();

        $book = new Book();
        $book->setTitle($request->get('title'));
        try{

            $entityManagerInterface->persist($book);
            $entityManagerInterface->flush();

        }catch(Exception $exception) {

            return $response->setData(['error' => $exception->getMessage()]);
        }
        return $response->setData(['created']);

    }
}
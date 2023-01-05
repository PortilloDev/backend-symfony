<?php 

namespace App\Services;
use App\Entity\Book;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;


class BookManager
{
    private $entityManagerInterface;
    private $bookRepository;

    public function __construct(EntityManagerInterface $entityManagerInterface, BookRepository $bookRepository)
    {
        $this->entityManagerInterface = $entityManagerInterface;
        $this->bookRepository = $bookRepository;
    }
 
    public function getRepository() :BookRepository
    {
        return $this->bookRepository;
        
    }      
    public function find(int $id) :?Book
    {
        return $this->bookRepository->find($id);
    }

    public function create() :Book
    {
        $book = new Book();
        return  $book;
    }

    public function persist(Book $book) :Book
    {
        $this->entityManagerInterface->persist($book);

        return  $book;
    }
    public function save(Book $book) :Book
    {
        $this->entityManagerInterface->persist($book);
        $this->entityManagerInterface->flush();

        return  $book;
    }

    public function reload(Book $book) :Book
    {
        $this->entityManagerInterface->refresh($book);

        return  $book;
    }

    public function delete(Book $book) :void
    {
        $this->entityManagerInterface->remove($book);
        $this->entityManagerInterface->flush();

    }
}
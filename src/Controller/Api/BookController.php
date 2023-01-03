<?php 

namespace App\Controller\Api;



use App\Entity\Book;
use App\Form\Model\BookDto;
use App\Form\Type\BookFormType;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\View as ViewAttribute;
use FOS\RestBundle\Controller\Annotations\{Delete, Get, Post, Put, Patch};
use FOS\RestBundle\View\View;

class BookController extends AbstractFOSRestController
{

    #[Get(path: "/books")]
    #[ViewAttribute(serializerGroups: ['book'], serializerEnableMaxDepthChecks: true)]
    public function getAction(BookRepository $repository)
    {
        return $repository->findAll();
        
    }

    #[Post(path: "/books", name: "books_create")]
    #[ViewAttribute(serializerGroups: ['book'], serializerEnableMaxDepthChecks: true)]
    public function postAction(Request $request, EntityManagerInterface $entityManagerInterface, FilesystemOperator $defaultStorage)
    {
        $bookDto = new BookDto();

        $form = $this->createForm(BookFormType::class, $bookDto);

        $form->handleRequest($request);

        if( $form->isSubmitted() && $form->isValid()) {
            $book = new Book();
            $image = $this->getFile($bookDto->base64Image, $defaultStorage);

            $book->setTitle($bookDto->title);
            $book->setImage($image);
            $entityManagerInterface->persist($book);
            $entityManagerInterface->flush();

            return $book;
        }

        return $form;
        
    }

    private function getFile(string $img, $defaultStorage) :string
    {
        $extension = explode('/', mime_content_type($img))[1];
        $data = explode(',', $img);
        $filename = sprintf('%s.%s', uniqid('book_', true), $extension);
        $defaultStorage->write($filename, base64_decode($data[1]));
        return $filename;
    }

}
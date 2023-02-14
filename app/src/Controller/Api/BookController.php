<?php 

namespace App\Controller\Api;




use App\Services\BookFormProcessor;
use App\Services\BookManager;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\View as ViewAttribute;
use FOS\RestBundle\Controller\Annotations\{Delete, Get, Post, Put, Patch};
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BookController extends AbstractFOSRestController
{

    #[Get(path: "/books")]
    #[ViewAttribute(serializerGroups: ['book'], serializerEnableMaxDepthChecks: true)]
    public function getAction(BookManager $bookManager)
    {
        $bookManager->getRepository()->findByTitle('dafadsfadsfasdfasdfdsafds');
        return $bookManager->getRepository()->findAll();
        
    }

    #[Post(path: "/books", name: "books_create")]
    #[ViewAttribute(serializerGroups: ['book'], serializerEnableMaxDepthChecks: true)]
    public function postAction(
        Request $request,
        BookManager $bookManager,
        BookFormProcessor $bookFormProcessor, 
        )
    {
        $book = $bookManager->create();

        [$book, $error] = ($bookFormProcessor)($book, $request);

        $statusCode = $book ? Response::HTTP_CREATED : Response::HTTP_BAD_REQUEST;
        $data = $book ? $book : $error;

        return View::create($data, $statusCode);

    }

    #[Get(path: "/books/{id}", name:"book_find_id")]
    #[ViewAttribute(serializerGroups: ['book'], serializerEnableMaxDepthChecks: true)]
    public function getSingleAction(
        BookManager $bookManager,
        int $id,
        )
    {
        $book = $bookManager->find($id);

        if ( !$book ) {
            return View::create('Book not found', Response::HTTP_BAD_REQUEST);
        }

        return View::create($book, Response::HTTP_OK);

    }
    #[Post(path: "/books/{id}", name: "books_edit")]
    #[ViewAttribute(serializerGroups: ['book'], serializerEnableMaxDepthChecks: true)]
    public function editAction(
        int $id,
        BookFormProcessor $bookFormProcessor,
        BookManager $bookManager,
        Request $request,
        )
    {
        $book = $bookManager->find($id);

        if ( !$book ) {
            return View::create('Book not found', Response::HTTP_BAD_REQUEST);
        }

        [$book, $error] = ($bookFormProcessor)($book, $request);
        
        $statusCode = $book ? Response::HTTP_CREATED : Response::HTTP_BAD_REQUEST;
        $data = $book ? $book : $error;

        return View::create($data, $statusCode);



    }

    #[Delete(path: "/books/{id}", name: "books_delete")]
    #[ViewAttribute(serializerGroups: ['book'], serializerEnableMaxDepthChecks: true)]
    public function deleteAction(
        int $id,
        BookManager $bookManager,
        )
    {
        $book = $bookManager->find($id);

        if ( !$book ) {
            return View::create('Book not found', Response::HTTP_BAD_REQUEST);
        }

        $bookManager->delete($book);

        return View::create(null, Response::HTTP_NO_CONTENT);



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
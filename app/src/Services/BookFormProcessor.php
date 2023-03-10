<?php 
namespace App\Services;


use App\Entity\Book;
use App\Form\Model\BookDto;
use App\Form\Model\CategoryDto;
use App\Form\Type\BookFormType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\FormFactoryInterface;


class BookFormProcessor
{

    private $bookManager;
    private $categoryManager;
    private $fileUploader;
    private $formFactory;

    public function __construct(
        BookManager $bookManager,
        CategoryManager $categoryManager,
        FileUploader $fileUploader,
        FormFactoryInterface $formFactory
    )
    {
        $this->bookManager      = $bookManager;
        $this->categoryManager  = $categoryManager;
        $this->fileUploader     = $fileUploader;
        $this->formFactory      = $formFactory;
    }

    public function __invoke(Book $book, Request $request) :array
    {
        $bookDto = BookDto::createFromBook($book);


        $originalCategories = new ArrayCollection();

        foreach($book->getCategories() as $category) {
            $categoryDto = CategoryDto::createFromBook($category);
            $bookDto->categories[] = $categoryDto;
            $originalCategories->add($categoryDto);
        }

        $form = $this->formFactory->create(BookFormType::class, $bookDto);

        $form->handleRequest($request);
        
        if( ! $form->isSubmitted() ) {

            return [null, 'Form is not submitted'];
        }
        if( $form->isValid()) {
            
            
            //Remove old categories
            foreach($originalCategories as $originalCategoryDto) {
                if (!in_array($originalCategoryDto, $bookDto->categories)) {
                    $category = $this->categoryManager->find($originalCategoryDto->id);
                    $book->removeCategory($category);
                }
            }

            //Add new categories
            foreach($bookDto->categories as $newCategoryDto) {
                if ( !$originalCategories->contains($newCategoryDto)) {
                    $category = $this->categoryManager->find($newCategoryDto->id ?? 0);

                    if (!$category) {
                        $category = $this->categoryManager->create();
                        $category->setName($newCategoryDto->name);
                        $this->categoryManager->persist($category);
                    }

                    $book->addCategory($category);
                }
            }

            $book->setTitle($bookDto->title);

            if ($bookDto->base64Image) {
                
                $image = $this->fileUploader->uploadBase64File($bookDto->base64Image);
                $book->setImage($image);

            }

            $this->bookManager->save($book);
            $this->bookManager->reload($book);
            return [$book, null];
        }

        return  [null, $form];
    }
}
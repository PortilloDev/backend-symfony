<?php 

namespace App\Services;
use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;


class CategoryManager
{
    private $entityManagerInterface;
    private $categoryRepository;

    public function __construct(EntityManagerInterface $entityManagerInterface, CategoryRepository $categoryRepository)
    {
        $this->entityManagerInterface = $entityManagerInterface;
        $this->categoryRepository = $categoryRepository;
    }

    public function getRepository() :CategoryRepository
    {
        return $this->categoryRepository;
        
    }  
    
    public function find(int $id) :?Category
    {
        return $this->categoryRepository->find($id);
    }

    public function create() :Category
    {
        $category = new Category();
        return  $category;
    }
    public function persist(Category $category) :Category
    {
        $this->entityManagerInterface->persist($category);

        return  $category;
    }
    public function save(Category $category) :Category
    {
        $this->entityManagerInterface->persist($category);
        $this->entityManagerInterface->flush();

        return  $category;
    }

    public function reload(Category $category) :Category
    {
        $this->entityManagerInterface->refresh($category);

        return  $category;
    }
}
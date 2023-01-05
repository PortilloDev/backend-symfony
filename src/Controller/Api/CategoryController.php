<?php 
namespace App\Controller\Api;

use FOS\RestBundle\View\View;
use App\Form\Model\CategoryDto;
use App\Services\CategoryManager;
use App\Form\Type\CategoryFormType;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\View as ViewAttribute;
use FOS\RestBundle\Controller\Annotations\{Delete, Get, Post, Put, Patch};

class CategoryController  extends AbstractFOSRestController
{

    #[Get(path: "/categories", name: "categories")]
    #[ViewAttribute(serializerGroups: ['category'], serializerEnableMaxDepthChecks: true)]
    public function getAction(CategoryManager $categoryManager)
    {
        return $categoryManager->getRepository()->findAll();
        
    }

    #[Post(path: "/categories", name: "categories_create")]
    #[ViewAttribute(serializerGroups: ['category'], serializerEnableMaxDepthChecks: true)]
    public function postAction(
        Request $request, 
        CategoryManager $categoryManager
        )
    {
        $categoryDto = new CategoryDto();

        $form = $this->createForm(CategoryFormType::class, $categoryDto);
        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid() ) {

            $category = $categoryManager->create();
            $category->setName($categoryDto->name);
            $categoryManager->save($category);
            return $category;
        }

        return $form;
    }
 }
<?php
namespace App\Form\Type;

use App\Entity\Book;
use App\Form\Model\BookDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class BookFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class)
            ->add('base64Image');
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BookDto::class,
        ]);
    }

    // Se define estos dos métodos para no tener que indicar el nombre del formulario en la llamada del endpoint de tipo POST
    /**
     *  {
     *    "book_form": {
     *         "title": "La piel del tambor"
     *      }
     *   }
     */
    public function getBlockPrefix() 
    {
        return '';
    }

    public function getName()
    {
        return '';
    }
}
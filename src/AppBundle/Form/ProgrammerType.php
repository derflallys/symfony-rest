<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class ProgrammerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nickname','text',[
                'disabled' => $options['is_edit']
            ])
            ->add('avatarNumber','choice',[
                'choices' => [
                    1 => 'Girl (green)',
                    2 => 'Boy',
                    3 => 'Cat',
                    4 => 'Boy with Hat',
                    5 => 'Happy Robot',
                    6 => 'Girl (purple)',
                ]
            ])
            ->add('tagLine','textarea');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Programmer',
            'is_edit' => false,
            'csrf_protection' =>false
        ]);
    }

    public function getName()
    {
        return 'programmer';
    }
}
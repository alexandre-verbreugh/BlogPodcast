<?php

namespace App\Form;

use App\Entity\Episode;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class EpisodeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('description', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'episode_description', // Changed ID to episode_description
                ]
            ])
            ->add('createdAt', null, [
                'widget' => 'single_text',
            ])
            ->add('audioFile', FileType::class, [
                'label' => 'Fichier audio',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '250M',
                        'mimeTypes' => [
                            'audio/mpeg',
                            'audio/mp3',
                            'audio/x-wav',
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger un fichier audio valide',
                    ])
                ],
            ])
            ->add('coverImageFile', FileType::class, [
                'label' => 'Image de couverture',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger une image valide',
                    ])
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Episode::class,
        ]);
    }
}

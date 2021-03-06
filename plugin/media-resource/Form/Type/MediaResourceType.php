<?php

namespace Innova\MediaResourceBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Description of MediaResourceType.
 */
class MediaResourceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class, ['required' => true])
                ->add(FileType::class, FileType::class, ['required' => true, 'mapped' => false, 'constraints' => [
                    new NotBlank(),
                    new File([
                                    'mimeTypes' => [
                                        'audio/mpeg',
                                        'audio/wav',
                                        'audio/x-wav',
                                    ],
                                    'mimeTypesMessage' => 'The type of the file is invalid ({{ type }}). Allowed types are {{ types }}. Please check that your file is well encoded.',
                              ]),
                  ],
                ]);
    }

    public function getDefaultOptions()
    {
        return [
            'data_class' => 'Innova\MediaResourceBundle\Entity\MediaResource',
            'translation_domain' => 'resource',
        ];
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults($this->getDefaultOptions());

        return $this;
    }

    public function getName()
    {
        return 'media_resource';
    }
}

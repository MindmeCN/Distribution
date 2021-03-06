<?php

/*
 * This file is part of the Claroline Connect package.
 *
 * (c) Claroline Consortium <consortium@claroline.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Claroline\SurveyBundle\Form;

use Claroline\CoreBundle\Form\Field\TinymceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'title',
            TextType::class
        );
        $builder->add(
            'question',
            TinymceType::class
        );
        $builder->add(
            'type',
            ChoiceType::class,
            [
                'choices' => [
                    'simple_text' => 'simple_text',
                    'open_ended' => 'open_ended',
                    'open_ended_bare' => 'open_ended_bare',
                    'multiple_choice_single' => 'multiple_choice_single_answer',
                    'multiple_choice_multiple' => 'multiple_choice_multiple_answers',
                ],
                'required' => true,
            ]
        );
        $builder->add(
            'commentAllowed',
            CheckboxType::class,
            ['required' => true]
        );
        $builder->add(
            'commentLabel',
            TextType::class,
            ['required' => false]
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['translation_domain' => 'survey']);
    }
}

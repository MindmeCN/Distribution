<?php
/**
 * This file is part of the Claroline Connect package.
 *
 * (c) Claroline Consortium <consortium@claroline.net>
 *
 * Author: Panagiotis TSAVDARIS
 *
 * Date: 5/17/16
 */

namespace Claroline\CoreBundle\Form\Administration;

use Claroline\CoreBundle\Manager\PortalManager;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PortalConfigurationType.
 *
 * @DI\FormType
 */
class PortalConfigurationType extends AbstractType
{
    protected $choices;

    /**
     * PortalConfigurationType constructor.
     *
     * @DI\InjectParams({
     *     "portalManager" = @DI\Inject("claroline.manager.portal_manager")
     * })
     *
     * @param PortalManager $portalManager
     */
    public function __construct(PortalManager $portalManager)
    {
        $this->choices = $portalManager->getAllResourceTypesAsChoices();
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'portalResources',
                ChoiceType::class,
                [
                    'required' => false,
                    'choices' => $options['choices'],
                    'label' => 'portal_resources_configuration',
                    'expanded' => true,
                    'multiple' => true,
                    'choices_as_values' => true,
                    'empty_data' => [],
                ]
            )
            ->add(
                'portalHiddenHelpField',
                HiddenType::class
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'translation_domain' => 'platform', 'choices' => [],
            ]
        );
    }
}

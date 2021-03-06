<?php

/*
 * This file is part of the Claroline Connect package.
 *
 * (c) Claroline Consortium <consortium@claroline.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Claroline\CoreBundle\Form\Extension;

use Gregwar\CaptchaBundle\Type\CaptchaType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class CaptchaExtension extends AbstractTypeExtension
{
    private $container;

    public function __construct($data)
    {
        $this->container = $data['container'];
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (array_key_exists('no_captcha', $options) && true === $options['no_captcha']) {
            return;
        }

        //if the captcha option is activated
        $ch = $this->container->get('claroline.config.platform_config_handler');

        if ($ch->getParameter('form_captcha')) {
            $securityToken = $this->container->get('security.token_storage')->getToken();

            if (null !== $securityToken && 'anon.' === $securityToken->getUser()) {
                $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                    $form = $event->getForm();
                    $data = $event->getData();

                    if ($form->isRoot() && $form->getConfig()->getOption('compound')) {
                        $form->add('captcha', CaptchaType::class, ['label' => 'Captcha']);
                    }

                    $event->setData($data);
                });
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return FormType::class;
    }
}

<?php

namespace HeVinci\CompetencyBundle\Installation;

use Claroline\CoreBundle\Entity\Resource\MaskDecoder;
use Claroline\CoreBundle\Entity\Resource\MenuAction;
use Claroline\InstallationBundle\Additional\AdditionalInstaller as BaseInstaller;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

class AdditionalInstaller extends BaseInstaller implements ContainerAwareInterface
{
    public function postInstall()
    {
        $this->addResourceCustomAction();
        $this->addCompetencyManagerRole();
    }

    private function addResourceCustomAction()
    {
        // this should not be done like this
        $this->log('Adding custom action to resource...');

        $em = $this->container->get('doctrine.orm.entity_manager');
        $typeRepo = $em->getRepository('ClarolineCoreBundle:Resource\ResourceType');
        $actionRepo = $em->getRepository('ClarolineCoreBundle:Resource\MenuAction');

        $exoType = $typeRepo->findOneByName('ujm_exercise');
        $action = $actionRepo->findOneByName('manage-competencies');

        if (!$action) {
            $action = new MenuAction();
            $action->setName('manage-competencies');
            $action->setResourceType($exoType);
            // the new action will be bound to the 'open' permission
            $action->setDecoder('open');
            $em->persist($action);
        }

        $em->flush();
    }

    private function addCompetencyManagerRole()
    {
        $this->log('Adding competency manager role...');

        $role = $this->container
            ->get('doctrine.orm.entity_manager')
            ->getRepository('ClarolineCoreBundle:Role')
            ->findOneByName('ROLE_COMPETENCY_MANAGER');

        if (!$role) {
            $this->container
                ->get('claroline.manager.role_manager')
                ->createBaseRole('ROLE_COMPETENCY_MANAGER', 'competency_manager');
        }
    }
}

<?php

/*
 * This file is part of the Claroline Connect package.
 *
 * (c) Claroline Consortium <consortium@claroline.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Claroline\CursusBundle\Entity;

use Claroline\CoreBundle\Entity\Model\UuidTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Claroline\CursusBundle\Repository\SessionEventSetRepository")
 * @ORM\Table(
 *     name="claro_cursusbundle_session_event_set",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="event_set_unique_name_session", columns={"set_name", "session_id"})
 *     }
 * )
 * @DoctrineAssert\UniqueEntity({"session", "name"})
 */
class SessionEventSet
{
    use UuidTrait;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="set_name")
     * @Assert\NotBlank()
     */
    protected $name;

    /**
     * @ORM\ManyToOne(
     *     targetEntity="Claroline\CursusBundle\Entity\CourseSession",
     *     inversedBy="events"
     * )
     * @ORM\JoinColumn(name="session_id", nullable=true, onDelete="CASCADE")
     */
    protected $session;

    /**
     * @ORM\Column(name="set_limit", nullable=false, type="integer")
     */
    protected $limit = 1;

    /**
     * @ORM\OneToMany(
     *     targetEntity="Claroline\CursusBundle\Entity\SessionEvent",
     *     mappedBy="eventSet"
     * )
     * @ORM\OrderBy({"startDate" = "ASC"})
     */
    protected $events;

    public function __construct()
    {
        $this->refreshUuid();
        $this->events = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getSession()
    {
        return $this->session;
    }

    public function setSession(CourseSession $session = null)
    {
        $this->session = $session;
    }

    public function getLimit()
    {
        return $this->limit;
    }

    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    public function getEvents()
    {
        return $this->events->toArray();
    }
}

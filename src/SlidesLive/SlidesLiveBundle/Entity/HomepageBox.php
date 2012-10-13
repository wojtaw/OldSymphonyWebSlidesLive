<?php

namespace SlidesLive\SlidesLiveBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SlidesLive\SlidesLiveBundle\Entity\HomepageBox
 *
 * @ORM\Entity(repositoryClass="SlidesLive\SlidesLiveBundle\Repository\HomepageBoxRepository")
 * @ORM\Table(name="homepage_box")
 */
class HomepageBox
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\OneToOne(targetEntity="Presentation")
     * @ORM\JoinColumn(name="presentation_id", referencedColumnName="id")     
     */
    protected $presentation;

// -----------------------------------------------------------------------------

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set presentation
     *
     * @param SlidesLive\SlidesLiveBundle\Entity\Presentation $presentation
     */
    public function setPresentation(\SlidesLive\SlidesLiveBundle\Entity\Presentation $presentation)
    {
        $this->presentation = $presentation;
    }

    /**
     * Get presentation
     *
     * @return SlidesLive\SlidesLiveBundle\Entity\Presentation 
     */
    public function getPresentation()
    {
        return $this->presentation;
    }
}
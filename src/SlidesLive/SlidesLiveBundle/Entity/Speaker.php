<?php

namespace Meta\MetaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Meta\MetaBundle\Entity\Speaker
 *
 * @ORM\Table(name="speaker")
 * @ORM\Entity(repositoryClass="Meta\MetaBundle\Repository\SpeakerRepository")
 */
class Speaker
{

    public function __construct() {
        $this->presentations = new ArrayCollection();
    }

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;
    
    /**
     * @var string $legend
     *
     * @ORM\Column(name="legend", type="string", length=255)
     */
    protected $legend;
    
    /**
     * @ORM\ManyToMany(targetEntity="Presentation", mappedBy="speakers")
     */         
    protected $presentations;

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
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * Add presentations
     *
     * @param Meta\MetaBundle\Entity\Presentation $presentations
     */
    public function addPresentation(\Meta\MetaBundle\Entity\Presentation $presentations)
    {
        $this->presentations[] = $presentations;
    }

    /**
     * Get presentations
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getPresentations()
    {
        return $this->presentations;
    }

    /**
     * Set legend
     *
     * @param string $legend
     */
    public function setLegend($legend)
    {
        $this->legend = $legend;
    }

    /**
     * Get legend
     *
     * @return string 
     */
    public function getLegend()
    {
        return $this->legend;
    }
}
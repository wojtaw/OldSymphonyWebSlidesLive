<?php

namespace SlidesLive\SlidesLiveBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * SlidesLive\SlidesLiveBundle\Entity\Category
 *
 * @ORM\Table(name="category")
 * @ORM\Entity(repositoryClass="SlidesLive\SlidesLiveBundle\Repository\CategoryRepository")
 */
class Category
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
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;
	
    /**
     * @var string $name
     *
     * @ORM\Column(name="canonical_name", type="string", length=255)
     */
    protected $canonicalName;	

    /**
     * @ORM\ManyToMany(targetEntity="Presentation", mappedBy="categories")
     */
    protected $presentations;

// -----------------------------------------------------------------------------

    public function __construct() {
        $this->presentations = new ArrayCollection();
    }
    
// =============================================================================

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
     * Set canonical name
     *
     * @param string $canonicalName
     */
    public function setCanonicalName($canonicalName)
    {
        $this->canonicalName = $canonicalName;
    }

    /**
     * Get canonical name
     *
     * @return string 
     */
    public function getCanonicalName()
    {
        return $this->canonicalName;
    }	

    /**
     * Add presentations
     *
     * @param SlidesLive\SlidesLiveBundle\Entity\Presentation $presentations
     */
    public function addPresentation(\SlidesLive\SlidesLiveBundle\Entity\Presentation $presentations)
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
	
    public function getImage() {
        $thumbnail = './bundles/static/images/category/' . $this->getId() . '.png';
        return $thumbnail;
    }	
}
<?php

namespace Meta\MetaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Meta\MetaBundle\Entity\Folder
 *
 * @ORM\Table(name="folder")
 * @ORM\Entity(repositoryClass="Meta\MetaBundle\Repository\FolderRepository")
 */
class Folder
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
     * @var string $CanonicalName
     *
     * @ORM\Column(name="canonical_name", type="string", length=255)
     */
    protected $canonicalName;
    
    /**
     * @ORM\OneToMany(targetEntity="Presentation", mappedBy="folder")
     */         
    protected $presentations;
    
    /**
     * @ORM\ManyToOne(targetEntity="Channel", inversedBy="folders")     
     */         
    protected $channel;
    
// -----------------------------------------------------------------------------

    public function __construct()
    {
        $this->presentations = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    public function canonizeName() {
      $result = iconv("UTF-8", 'ASCII//TRANSLIT', $this->name);
      $patterns = array(
        0 => '/[[:space:]]+/',
        1 => '/\'/'
      );
      $this->canonicalName = preg_replace($patterns, array(0 => '', 1 => ''), $result);
      return $this->canonicalName;
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
     * Set channel
     *
     * @param Meta\MetaBundle\Entity\Channel $channel
     */
    public function setChannel(\Meta\MetaBundle\Entity\Channel $channel)
    {
        $this->channel = $channel;
    }

    /**
     * Get channel
     *
     * @return Meta\MetaBundle\Entity\Channel 
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * Set canonicalName
     *
     * @param string $canonicalName
     */
    public function setCanonicalName($canonicalName)
    {
        $this->canonicalName = $canonicalName;
    }

    /**
     * Get canonicalName
     *
     * @return string 
     */
    public function getCanonicalName()
    {
        return $this->canonicalName;
    }
}
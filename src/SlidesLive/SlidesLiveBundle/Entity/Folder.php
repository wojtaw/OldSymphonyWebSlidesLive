<?php

namespace SlidesLive\SlidesLiveBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use SlidesLive\SlidesLiveBundle\DependencyInjection\Privacy;

/**
 * SlidesLive\SlidesLiveBundle\Entity\Folder
 *
 * @ORM\Table(name="folder")
 * @ORM\Entity(repositoryClass="SlidesLive\SlidesLiveBundle\Repository\FolderRepository")
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
     * @var small privacy 
     *      
     * @ORM\Column(name="privacy", type="smallint")     
     */
     protected $privacy;
     
     /**
     * @var string hash
     * 
     * @ORM\Column(name="hash", type="string", length="64")
     */                   
    protected $hash;
    
    /**
     * @ORM\OneToMany(targetEntity="Presentation", mappedBy="folder")
     */         
    protected $presentations;
    
    /**
     * @ORM\ManyToOne(targetEntity="Account", inversedBy="folders")     
     */         
    protected $account;
    
// -----------------------------------------------------------------------------

    public function __construct()
    {
        $this->presentations = new \Doctrine\Common\Collections\ArrayCollection();
        $this->privacy = Privacy::P_PUBLIC;
        $this->hash = $this->generateHash();
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
    
    public function generateHash() {
        $this->hash = md5(microtime());
        return $this->hash;    
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

    /**
     * Set account
     *
     * @param SlidesLive\SlidesLiveBundle\Entity\Account $account
     */
    public function setAccount(\SlidesLive\SlidesLiveBundle\Entity\Account $account)
    {
        $this->account = $account;
    }

    /**
     * Get account
     *
     * @return SlidesLive\SlidesLiveBundle\Entity\Account 
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * Set privacy
     *
     * @param smallint $privacy
     */
    public function setPrivacy($privacy)
    {
        $this->privacy = $privacy;
    }

    /**
     * Get privacy
     *
     * @return smallint 
     */
    public function getPrivacy()
    {
        return $this->privacy;
    }

    /**
     * Set hash
     *
     * @param string $hash
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
    }

    /**
     * Get hash
     *
     * @return string 
     */
    public function getHash()
    {
        return $this->hash;
    }
}
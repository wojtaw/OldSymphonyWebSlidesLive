<?php

namespace SlidesLive\SlidesLiveBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use SlidesLive\SlidesLiveBundle\DependencyInjection\Privacy;

/**
 * SlidesLive\SlidesLiveBundle\Entity\Account
 *
 * @ORM\Table(name="account")
 * @ORM\Entity(repositoryClass="SlidesLive\SlidesLiveBundle\Repository\AccountRepository")  
 */
class Account implements AdvancedUserInterface {

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /** LOGIN EMAILEM !!!
     * @var string $username
     *
     * @ORM\Column(name="username", type="string", length=255)
     */
    protected $username;

    /**
     * @var string $salt
     *
     * @ORM\Column(name="salt", type="string", length=255)
     */
    protected $salt;

    /**
     * @var string $password
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    protected $password;

    /**
     * @var boolean $isActive
     *
     * @ORM\Column(name="is_active", type="boolean")
     */
    protected $isActive;

    /**
     * @var string $role
     *
     * @ORM\Column(name="role", type="string", length=255)
     */
    protected $role;

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;
    
    /**
     * @var string $canonicalName
     *
     * @ORM\Column(name="canonical_name", type="string", length="255")
     */
    protected $canonicalName;                   

    /**
     * @var text $description
     *
     * @ORM\Column(name="description", type="text")
     */
    protected $description;                         
     
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
      *  @var string $website
      *        
      *  @ORM\Column(name="website", type="string", length="255")
      */                 
     protected $website;
     
     /**
      * @var boolean $isMeta
      * 
      * @ORM\Column(name="is_meta", type="boolean")
      */
     protected $isMeta;
     
     /**
      * @var boolean $showHeader
      * 
      * @ORM\Column(name="show_header", type="boolean")
      */     
     protected $showHeader = true;

     /**
      * @var boolean $showFooter
      * 
      * @ORM\Column(name="show_footer", type="boolean")
      */
     protected $showFooter = true;                                                         

    /**
     * @ORM\OneToMany(targetEntity="Folder", mappedBy="account")
     */         
    protected $folders;
    
    /**
     * @ORM\OneToOne(targetEntity="Folder")
     * @ORM\JoinColumn(name="primary_folder", referencedColumnName="id")     
     */
     protected $primaryFolder;
     
     /**
      * @ORM\OneToMany(targetEntity="Presentation", mappedBy="account")
      */           
     protected $presentations;         
    
    /**
     * @ORM\OneToMany(targetEntity="Subscribe", mappedBy="account")
     */         
    protected $subscribes;
    
// -----------------------------------------------------------------------------

    public function getRoles() {
        return array($this->role);
    }
    
    public function equals(UserInterface $user) {
        return $user->getUsername() === $this->username;
    }
    
    public function eraseCredentials() {
      $this->password = NULL;
      $this->salt = NULL;
    }
    
    public function isAccountNonExpired() {
      return true;
    }
    
    public function isCredentialsNonExpired() {
      return true;
    }
    
    public function isEnabled() {
      return $this->isActive;
    }
    
    public function isAccountNonLocked() {
      return true;
    }
    
    public function getPassword() {
      return $this->password;
    }
    
    public function getSalt() {
      return $this->salt;
    }
    
    //public function getUserName() { return $this->username; }

    public function __construct() {
        $this->username = '@';
        $this->salt = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
        $this->password = '';        
        $this->isActive = true;
        $this->role = 'ROLE_USER';
        $this->isMeta = false;
    
        $this->subscribes = new ArrayCollection();
        $this->name = '';
        $this->canonicalName = '';
        $this->description = '';
        $this->hash = $this->generateHash();
        $this->ratig = 0;
        $this->website = '';
        $this->privacy = Privacy::P_PUBLIC;  
    }
    
    /**
     * Vraci nazev tridy bez jmen namespacu.
     * @return string     
     */
    public function getClass() {
      return 'Account';
    }
    
    public function generateHash() {
        $this->hash = md5(microtime());
        return $this->hash;    
    }
        
    /**
     * Vraci cestu, na ktere je ulozen obrazek kanalu
     *
     * @param string $type - typ obrazku, urcuje nazev obrazku kanalu
     * @param boolean $mandatory - true pokud ma metoda vratit adresu obrazku (pokud obrazek nenajde vraci univerzalni no-image obrazek), jinak vraci null          
     * @return string or null if image was not found     
     */              
    public function getImage($type, $mandatory = false) {
        $imgFormats = array(
            "jpg", "png", "bmp", "jpeg", "jpeg2000", "gif", "JPG", "PNG", "BMP", "JPEG", "JPEG2000", "GIF"
        );
        $thumbnail = null;

        foreach ($imgFormats as $format) {
            if (file_exists( $_SERVER['DOCUMENT_ROOT'].'/data/accounts/' . $type . '/' . $this->getId() . '.' . $format)) {
                $thumbnail = '/data/accounts/' . $type . '/' . $this->getId() . '.' . $format;
                break;
            }
        }
        if ($mandatory && !$thumbnail) {
          $thumbnail = './bundles/slideslive/images/no-image.jpg';        
        }
        return $thumbnail;
    }
        
    /**
     * Kanonizace jmena kanalu.
     * Ze jmena kanalu odstrani bile znaky a diakritiku.
     *                
     * @return string kanonizovane jmeno kanalu 
     */                   
    public function canonizeName() {    
      $result = iconv("UTF-8", 'ASCII//TRANSLIT', $this->name);
      $patterns = array(
        0 => '/[[:space:]]+/',
        1 => '/\'/'
      );
      $this->canonicalName = preg_replace($patterns, array(0 => '', 1 => ''), $result);
      return $this->canonicalName;
    }
    
    public function getCountOfPresentations() {
      return count($this->presentations);
    }
    
    /**
     * Zakodovani hesla sifrovacim algoritmem urcenym v konfiguraci
     *
     * @param Controller za ktereho je metoda volana (nutne pro ziskani pristupu ke sluzbe Encoder)    
     */                      
    public function encodePassword($controller) {
        $encoder = $controller->get('security.encoder_factory')->getEncoder($this);
        $this->password = $encoder->encodePassword($this->password, $this->salt);
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
     * Set username
     *
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set salt
     *
     * @param string $salt
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
    }

    /**
     * Set password
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }

    /**
     * Get isActive
     *
     * @return boolean 
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set role
     *
     * @param string $role
     */
    public function setRole($role)
    {
        $this->role = $role;
    }

    /**
     * Get role
     *
     * @return string 
     */
    public function getRole()
    {
        return $this->role;
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
     * Set description
     *
     * @param text $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get description
     *
     * @return text 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set privacy
     *
     * @param integer $privacy
     */
    public function setPrivacy($privacy)
    {
        $this->privacy = $privacy;
    }

    /**
     * Get privacy
     *
     * @return integer 
     */
    public function getPrivacy()
    {
        return $this->privacy;
    }

    /**
     * Set website
     *
     * @param string $website
     */
    public function setWebsite($website)
    {
        $this->website = $website;
    }

    /**
     * Get website
     *
     * @return string 
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * Add folders
     *
     * @param SlidesLive\SlidesLiveBundle\Entity\Folder $folders
     */
    public function addFolder(\SlidesLive\SlidesLiveBundle\Entity\Folder $folders)
    {
        $this->folders[] = $folders;
    }

    /**
     * Get folders
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getFolders()
    {
        return $this->folders;
    }

    /**
     * Set primaryFolder
     *
     * @param SlidesLive\SlidesLiveBundle\Entity\Folder $primaryFolder
     */
    public function setPrimaryFolder(\SlidesLive\SlidesLiveBundle\Entity\Folder $primaryFolder)
    {
        $this->primaryFolder = $primaryFolder;
    }

    /**
     * Get primaryFolder
     *
     * @return SlidesLive\SlidesLiveBundle\Entity\Folder 
     */
    public function getPrimaryFolder()
    {
        return $this->primaryFolder;
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
     * Add subscribes
     *
     * @param SlidesLive\SlidesLiveBundle\Entity\Subscribe $subscribes
     */
    public function addSubscribe(\SlidesLive\SlidesLiveBundle\Entity\Subscribe $subscribes)
    {
        $this->subscribes[] = $subscribes;
    }

    /**
     * Get subscribes
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getSubscribes()
    {
        return $this->subscribes;
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

    /**
     * Set isMeta
     *
     * @param boolean $isMeta
     */
    public function setIsMeta($isMeta)
    {
        $this->isMeta = $isMeta;
    }

    /**
     * Get isMeta
     *
     * @return boolean 
     */
    public function getIsMeta()
    {
        return $this->isMeta;
    }

    /**
     * Set showHeader
     *
     * @param boolean $showHeader
     */
    public function setShowHeader($showHeader)
    {
        $this->showHeader = $showHeader;
    }

    /**
     * Get showHeader
     *
     * @return boolean 
     */
    public function getShowHeader()
    {
        return $this->showHeader;
    }

    /**
     * Set showFooter
     *
     * @param boolean $showFooter
     */
    public function setShowFooter($showFooter)
    {
        $this->showFooter = $showFooter;
    }

    /**
     * Get showFooter
     *
     * @return boolean 
     */
    public function getShowFooter()
    {
        return $this->showFooter;
    }
}
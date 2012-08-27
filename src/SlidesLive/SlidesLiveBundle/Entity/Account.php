<?php

namespace SlidesLive\SlidesLiveBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * SlidesLive\SlidesLiveBundle\Entity\Account
 *
 * @ORM\Table(name="account")
 * @ORM\Entity(repositoryClass="SlidesLive\SlidesLiveBundle\Repository\AccountRepository")  
 */
class Account implements AdvancedUserInterface {

    public function __construct() {
        $this->username = '';
        $this->salt = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
        $this->password = '';        
        $this->isActive = true;
        $this->role = 'ROLE_USER';
        $this->email = '@';
        $this->purpose = '';
    
        $this->subscribes = new ArrayCollection();
        $this->name = '';
        $this->canonicalName = '';
        $this->description = '';
        $this->privateCode = $this->generatePrivateCode();
        $this->ratig = 0;
        $this->private = false;  
    }
    
    /**
     * Vraci nazev tridy bez jmen namespacu.
     * @return string     
     */
    public function getClass() {
      return 'Account';
    }
    
    public function generatePrivateCode() {
        $this->privateCode = md5(microtime());
        return $this->privateCode;    
    }
        
    /**
     * Vraci cestu, na ktere je ulozen obrazek kanalu
     *
     * @param string $type - typ obrazku, urcuje nazev obrazku kanalu     
     * @return string     
     */              
    public function getThumbnail($type) {
        $types = array(
            'header' => $this->getId(),
            'timeline' => $this->getId(),
        );
        $imgFormats = array(
            "jpg", "png", "bmp", "jpeg", "jpeg2000", "gif"
        );
        $thumbnail = null;

        foreach ($imgFormats as $format) {
            if (file_exists('./bundles/slideslive/images/channelThumbnails/' . $type . '/' . $types[$type] . '.' . $format)) {
                $thumbnail = './bundles/slideslive/images/channelThumbnails/' . $type . '/' . $types[$type] . '.' . $format;
                break;
            }
        }
        if (\is_null($thumbnail)) {
            $thumbnail = './bundles/slideslive/images/channelThumbnails/noname-ch-header2.jpg';
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
     * @ORM\OneToMany(targetEntity="Folder", mappedBy="channel")
     */         
    protected $folders;
    
    /**
     * @ORM\OneToOne(targetEntity="Folder")
     * @ORM\JoinColumn(name="primary_folder", referencedColumnName="id")     
     */
     protected $primaryFolder;
     
     /**
      * @ORM\OneToMany(targetEntity="Presentation", mappedBy="channel")
      */           
     protected $presentations;         
    
    /**
     * @ORM\OneToMany(targetEntity="Subscribe", mappedBy="channel")
     */         
    protected $subscribes;
    
// =============================================================================

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
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
     * @ORM\Column(name="isActive", type="boolean")
     */
    protected $isActive;

    /**
     * @var string $role
     *
     * @ORM\Column(name="role", type="string", length=255)
     */
    protected $role;

    /**
     * @var string $email
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    protected $email;

    /**
     * @var text $purpose
     *
     * @ORM\Column(name="purpose", type="text")
     */
    protected $purpose;

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;
    
    /**
     * @var string $canonicalName
     *
     * @ORM\Column(name="canonicalName", type="string", length="255")
     */
    protected $canonicalName;                   

    /**
     * @var text $description
     *
     * @ORM\Column(name="description", type="text")
     */
    protected $description;
    
    /**
     * @var string privateCode
     * 
     * @ORM\Column(name="private_code", type="string", length="255")
     */
     protected $privateCode;                         
     
     /**
     * @var boolean private 
     * 
     * @ORM\Column(name="private", type="boolean")
     */
     protected $private;

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
     * Add subscribes
     *
     * @param Meta\MetaBundle\Entity\Subscribe $subscribes
     */
    public function addSubscribe(\Meta\MetaBundle\Entity\Subscribe $subscribes)
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
     * Set private
     *
     * @param boolean $private
     */
    public function setPrivate($private)
    {
        $this->private = $private;
    }

    /**
     * Get private
     *
     * @return boolean 
     */
    public function getPrivate()
    {
        return $this->private;
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
     * Set privateCode
     *
     * @param string $privateCode
     */
    public function setPrivateCode($privateCode)
    {
        $this->privateCode = $privateCode;
    }

    /**
     * Get privateCode
     *
     * @return string 
     */
    public function getPrivateCode()
    {
        return $this->privateCode;
    }

    /**
     * Add folders
     *
     * @param Meta\MetaBundle\Entity\Folder $folders
     */
    public function addFolder(\Meta\MetaBundle\Entity\Folder $folders)
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
     * @param Meta\MetaBundle\Entity\Folder $primaryFolder
     */
    public function setPrimaryFolder(\Meta\MetaBundle\Entity\Folder $primaryFolder)
    {
        $this->primaryFolder = $primaryFolder;
    }

    /**
     * Get primaryFolder
     *
     * @return Meta\MetaBundle\Entity\Folder 
     */
    public function getPrimaryFolder()
    {
        return $this->primaryFolder;
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
     * Set email
     *
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set purpose
     *
     * @param text $purpose
     */
    public function setPurpose($purpose)
    {
        $this->purpose = $purpose;
    }

    /**
     * Get purpose
     *
     * @return text 
     */
    public function getPurpose()
    {
        return $this->purpose;
    }
    
}

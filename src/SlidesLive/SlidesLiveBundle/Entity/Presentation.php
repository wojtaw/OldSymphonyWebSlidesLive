<?php

namespace SlidesLive\SlidesLiveBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * SlidesLive\SlidesLiveBundle\Entity\Presentation
 *
 * @ORM\Table(name="presentation") 
 * @ORM\Entity(repositoryClass="SlidesLive\SlidesLiveBundle\Repository\PresentationRepository")
 */
class Presentation
{

    public function __construct() {
        $this->speakers = new ArrayCollection();
    }
    
    public function getCountOfPresentations() {
      return count($this->presentations);
    }
    
    public function listSpeakersNames() {
      $list = '';
      $i = 1;
      foreach($this->speakers as $s) {
        if ($i == 1) {
            $list .= $s->getName();            
            $i = 0;                  
        }
        else {
          $list .= ', '.$s->getName();
        }
      }
      return $list;
    }
    
    /**
     * Vraci nazev tridy bez jmen namespacu.
     * @return string     
     */
    public function getClass() {
      return 'Presentation';
    }
    
    /**
     * Vraci datum a cas v lidsky citelnem formatu
     * @return string     
     */         
    public function getFormatedDateTime () {
      return date_format($this->dateRecorded,'Y-m-d G:i:s');
    }
    
    /**
     * Vraci datum v lidsky citelnem formatu
     * @return string     
     */         
    public function getFormatedDate () {
      return date_format($this->dateRecorded,'j.n.Y');
    }
    
    public function getThumbnail () {
        $imgFormats = array(
            "jpg", "png", "bmp", "jpeg", "jpeg2000", "gif"
        );
        $thumbnail = null;
        foreach ($imgFormats as $format) {
            if (file_exists('./bundles/slideslive/player/VideoThumbs/'.sprintf("%d",$this->id).'.'.$format)) {
                $thumbnail = './bundles/slideslive/player/VideoThumbs/'.sprintf("%d",$this->id).'.'.$format;
                break;
            }
        }
        if (\is_null($thumbnail)) {
            $thumbnail = './bundles/slideslive/images/no-image.jpg';
        }
        return $thumbnail;
    }
    
// =============================================================================

    /**
     *@ORM\ManyToOne(targetEntity="Account", inversedBy="presentations")
     */              
    protected $account;
    
    /**
     * @ORM\ManyToMany(targetEntity="Speaker", inversedBy="presentations")
     * @ORM\JoinTable(name="presentations_speakers")
     *         
     */
    protected $speakers;
    
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string $title
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    protected $title;

    /**
     * @var string $description
     *
     * @ORM\Column(name="description", type="text")
     */
    protected $description;
        
    /**
     * @var string $lang
     * 
     * @ORM\Column(name="lang", type="string", length=255)          
     */         
    protected $lang;

    /**
     * @var datetime $dateRecorded
     *
     * @ORM\Column(name="dateRecorded", type="datetime")
     */
    protected $dateRecorded;
    
    /**
     * @var string $service
     * 
     * @ORM\Column(name="service", type="string", length="255")
     */                   
    protected $service;

    /**
     * @var string $service
     *
     * @ORM\Column(name="service_id", type="string", length="255")
     */
    protected $service_id;

    /**
     * @var smallint $length
     *
     * @ORM\Column(name="length", type="smallint")
     */
    protected $length;

    /**
     * @var boolean $slides
     * 
     * @ORM\Column(name="slides", type="boolean")          
     */         
    protected $slides;
    
    /**
     * @var boolean $video
     * 
     * @ORM\Column(name="video", type="boolean")          
     */         
    protected $video;
    
    /**
     * @var small privacy 
     *      
     * @ORM\Column(name="privacy", type="smallint")     
     */
     protected $privacy;
     
     /**
     * 0 - NOTHIG - nic se nedìje
     * 1 - WAITING - má být sesynchronizováno, ale zatím není
     * 2 - SYNCHRONIZING- má být synchronizováno, ale zrovna je ve frontì a pracuje se na tom 
     * 3 - FAIL - došlo k chybì                
     *           
     * @var small flag           
     *      
     * @ORM\Column(name="flag", type="smallint")     
     */
     protected $flag;
    
    /**
     * @ORM\ManyToOne(targetEntity="Folder", inversedBy="presentations")     
     */         
    protected $folder;
    
    /**
     * @var boolean $showSpeaker
     * 
     * @ORM\Column(name="show_speaker", type="boolean")          
     */         
    protected $showSpeaker;
    
    /**
     * @var string video_source
     *
     * @ORM\Column(name="video_source", type="string", length="255")
     */                   
    protected $videoSource;

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
     * Set title
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
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
     * Set lang
     *
     * @param string $lang
     */
    public function setLang($lang)
    {
        $this->lang = $lang;
    }

    /**
     * Get lang
     *
     * @return string 
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * Set dateRecorded
     *
     * @param datetime $dateRecorded
     */
    public function setDateRecorded($dateRecorded)
    {
        $this->dateRecorded = $dateRecorded;
    }

    /**
     * Get dateRecorded
     *
     * @return datetime 
     */
    public function getDateRecorded()
    {
        return $this->dateRecorded;
    }

    /**
     * Set service
     *
     * @param string $service
     */
    public function setService($service)
    {
        $this->service = $service;
    }

    /**
     * Get service
     *
     * @return string 
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * Set service_id
     *
     * @param string $serviceId
     */
    public function setServiceId($serviceId)
    {
        $this->service_id = $serviceId;
    }

    /**
     * Get service_id
     *
     * @return string 
     */
    public function getServiceId()
    {
        return $this->service_id;
    }

    /**
     * Set length
     *
     * @param smallint $length
     */
    public function setLength($length)
    {
        $this->length = $length;
    }

    /**
     * Get length
     *
     * @return smallint 
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * Set slides
     *
     * @param boolean $slides
     */
    public function setSlides($slides)
    {
        $this->slides = $slides;
    }

    /**
     * Get slides
     *
     * @return boolean 
     */
    public function getSlides()
    {
        return $this->slides;
    }

    /**
     * Set video
     *
     * @param boolean $video
     */
    public function setVideo($video)
    {
        $this->video = $video;
    }

    /**
     * Get video
     *
     * @return boolean 
     */
    public function getVideo()
    {
        return $this->video;
    }

    /**
     * Set showSpeaker
     *
     * @param boolean $showSpeaker
     */
    public function setShowSpeaker($showSpeaker)
    {
        $this->showSpeaker = $showSpeaker;
    }

    /**
     * Get showSpeaker
     *
     * @return boolean 
     */
    public function getShowSpeaker()
    {
        return $this->showSpeaker;
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
     * Add speakers
     *
     * @param SlidesLive\SlidesLiveBundle\Entity\Speaker $speakers
     */
    public function addSpeaker(\SlidesLive\SlidesLiveBundle\Entity\Speaker $speakers)
    {
        $this->speakers[] = $speakers;
    }

    /**
     * Get speakers
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getSpeakers()
    {
        return $this->speakers;
    }

    /**
     * Set folder
     *
     * @param SlidesLive\SlidesLiveBundle\Entity\Folder $folder
     */
    public function setFolder(\SlidesLive\SlidesLiveBundle\Entity\Folder $folder)
    {
        $this->folder = $folder;
    }

    /**
     * Get folder
     *
     * @return SlidesLive\SlidesLiveBundle\Entity\Folder 
     */
    public function getFolder()
    {
        return $this->folder;
    }

    /**
     * Set videoSource
     *
     * @param string $videoSource
     */
    public function setVideoSource($videoSource)
    {
        $this->videoSource = $videoSource;
    }

    /**
     * Get videoSource
     *
     * @return string 
     */
    public function getVideoSource()
    {
        return $this->videoSource;
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
     * Set flag
     *
     * @param smallint $flag
     */
    public function setFlag($flag)
    {
        $this->flag = $flag;
    }

    /**
     * Get flag
     *
     * @return smallint 
     */
    public function getFlag()
    {
        return $this->flag;
    }
}
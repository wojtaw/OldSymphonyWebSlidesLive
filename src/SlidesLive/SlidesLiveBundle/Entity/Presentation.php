<?php

namespace Meta\MetaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Meta\MetaBundle\Entity\Presentation
 *
 * @ORM\Table(name="presentation") 
 * @ORM\Entity(repositoryClass="Meta\MetaBundle\Repository\PresentationRepository")
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
            if (file_exists('./bundles/meta/player/VideoThumbs/'.sprintf("%d",$this->id).'.'.$format)) {
                $thumbnail = './bundles/meta/player/VideoThumbs/'.sprintf("%d",$this->id).'.'.$format;
                break;
            }
        }
        if (\is_null($thumbnail)) {
            $thumbnail = './bundles/meta/images/no-image.jpg';
        }
        return $thumbnail;
    }
    
// =============================================================================

    /**
     *@ORM\ManyToOne(targetEntity="Channel", inversedBy="presentations")
     */              
    protected $channel;
    
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
     * @ORM\ManyToOne(targetEntity="Folder", inversedBy="presentations")     
     */         
    protected $folder;
    
    /**
     * @var boolean $showSpeaker
     * 
     * @ORM\Column(name="show_speaker", type="boolean")          
     */         
    protected $showSpeaker;

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
     * Set vimeo
     *
     * @param integer $vimeo
     */
    public function setVimeo($vimeo)
    {
        $this->vimeo = $vimeo;
    }

    /**
     * Get vimeo
     *
     * @return integer 
     */
    public function getVimeo()
    {
        return $this->vimeo;
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
     * @param integer $serviceId
     */
    public function setServiceId($serviceId)
    {
        $this->service_id = $serviceId;
    }

    /**
     * Get service_id
     *
     * @return integer 
     */
    public function getServiceId()
    {
        return $this->service_id;
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
     * Add speakers
     *
     * @param Meta\MetaBundle\Entity\Speaker $speakers
     */
    public function addSpeaker(\Meta\MetaBundle\Entity\Speaker $speakers)
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
     * Set folder
     *
     * @param Meta\MetaBundle\Entity\Folder $folder
     */
    public function setFolder(\Meta\MetaBundle\Entity\Folder $folder)
    {
        $this->folder = $folder;
    }

    /**
     * Get folder
     *
     * @return Meta\MetaBundle\Entity\Folder 
     */
    public function getFolder()
    {
        return $this->folder;
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
}
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
     * @var string $lang
     *
     * @ORM\Column(name="lang", type="string", length="2", nullable="true")
     */
    protected $lang;	
    
    /**
     * @ORM\OneToOne(targetEntity="Presentation")
     * @ORM\JoinColumn(name="presentation_id", referencedColumnName="id")     
     */
    protected $presentation;

    // #### ZALOHA KVULI HOMEPAGE BOXU NA EDUMATE ####
            /**
             * @ORM\OneToOne(targetEntity="Account")
             * @ORM\JoinColumn(name="account_id", referencedColumnName="id")     
             */         
            protected $account;

            /**
             * @var string $lang
             *
             * @ORM\Column(name="lang", type="string", length=2)
             */    
            protected $lang;
    // ###############################################

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
    
}
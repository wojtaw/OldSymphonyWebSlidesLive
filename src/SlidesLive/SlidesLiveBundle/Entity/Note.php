<?php

namespace SlidesLive\SlidesLiveBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use SlidesLive\SlidesLiveBundle\DependencyInjection\Privacy;

/**
 * SlidesLive\SlidesLiveBundle\Entity\Note
 *
 * @ORM\Table(name="note")
 * @ORM\Entity(repositoryClass="SlidesLive\SlidesLiveBundle\Repository\NoteRepository")
 */
class Note
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
     * @var string $textContent
     *
     * @ORM\Column(name="text_content", type="string", length=255)
     */
    protected $textContent;

    /**
     * @var integer $timecode
     *
     * @ORM\Column(name="timecode", type="integer")
     */
    protected $timecode;

    /**
     * @ORM\ManyToOne(targetEntity="Presentation", inversedBy="notes")
     */
    protected $presentation;

    /**
     * @ORM\ManyToOne(targetEntity="Account", inversedBy="notes")
     */
    protected $account;

// -----------------------------------------------------------------------------

    public function __construct()
    {

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
     * Set textContent
     *
     * @param string $textContent
     */
    public function setTextContent($textContent)
    {
        $this->textContent = $textContent;
    }

    /**
     * Get textContent
     *
     * @return string
     */
    public function getTextContent()
    {
        return $this->textContent;
    }

    /**
     * Set timecode
     *
     * @param integer $timecode
     */
    public function setTimecode($timecode)
    {
        $this->timecode = $timecode;
    }

    /**
     * Get timecode
     *
     * @return integer
     */
    public function getTimecode()
    {
        return $this->timecode;
    }

    /**
     * Add presentations
     *
     * @param SlidesLive\SlidesLiveBundle\Entity\Presentation $presentation
     */
    public function addPresentation(\SlidesLive\SlidesLiveBundle\Entity\Presentation $presentation)
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
     * Set presentation
     *
     * @param SlidesLive\SlidesLiveBundle\Entity\Presentation $presentation
     */
    public function setPresentation(\SlidesLive\SlidesLiveBundle\Entity\Presentation $presentation)
    {
        $this->presentation = $presentation;
    }
}
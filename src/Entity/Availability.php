<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AvailabilityRepository")
 */
class Availability
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean")
     */
    private $choice;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\MeetingGuest", inversedBy="availabilities")
     * @ORM\JoinColumn(nullable=false)
     */
    private $meeting_guest;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\MeetingDate", inversedBy="availabilities")
     * @ORM\JoinColumn(nullable=false)
     */
    private $meeting_date;

    /**
     * @ORM\Column(type="datetime")
     */
    private $chosen_at;

    public function __construct()
    {
        $this->meeting = new ArrayCollection();
        $this->chosen_at = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChoice(): ?bool
    {
        return $this->choice;
    }

    public function setChoice(bool $choice): self
    {
        $this->choice = $choice;

        return $this;
    }
    
    public function removeMeeting(MeetingDate $meetingId): self
    {
        if ($this->meeting_id->contains($meetingId)) {
            $this->meeting_id->removeElement($meetingId);
        }

        return $this;
    }

    public function getMeetingGuest(): ?MeetingGuest
    {
        return $this->meeting_guest;
    }

    public function setMeetingGuest(?MeetingGuest $meeting_guest): self
    {
        $this->meeting_guest = $meeting_guest;

        return $this;
    }

    public function getMeetingDate(): ?MeetingDate
    {
        return $this->meeting_date;
    }

    public function setMeetingDate(?MeetingDate $meeting_date): self
    {
        $this->meeting_date = $meeting_date;

        return $this;
    }

    public function getChosenAt(): ?\DateTimeInterface
    {
        return $this->chosen_at;
    }

    public function setChosenAt(\DateTimeInterface $chosen_at): self
    {
        $this->chosen_at = $chosen_at;

        return $this;
    }
}

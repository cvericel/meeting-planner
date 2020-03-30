<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MeetingDateRepository")
 */
class MeetingDate
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $day;

    /**
     * @ORM\Column(type="time")
     */
    private $start_at;

    /**
     * @ORM\Column(type="time")
     */
    private $end_at;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Meeting", inversedBy="dates")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $meeting;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Availability", mappedBy="meeting_date", orphanRemoval=true)
     */
    private $availabilities;

    /**
     * @ORM\Column(type="datetime")
     */
    private $create_at;


    public function __construct()
    {
        $this->user_id = new ArrayCollection();
        $this->availabilities = new ArrayCollection();
        $this->create_at = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartAt(): ?\DateTimeInterface
    {
        return $this->start_at;
    }

    public function setStartAt(\DateTimeInterface $start_at): self
    {
        $this->start_at = $start_at;

        return $this;
    }

    public function getEndAt(): ?\DateTimeInterface
    {
        return $this->end_at;
    }

    public function setEndAt(\DateTimeInterface $end_at): self
    {
        $this->end_at = $end_at;

        return $this;
    }

    public function getMeeting(): ?Meeting
    {
        return $this->meeting;
    }

    public function setMeeting(?Meeting $meeting): self
    {
        $this->meeting = $meeting;

        return $this;
    }

    /**
     * @return Collection|Availability[]
     */
    public function getAvailabilities(): Collection
    {
        return $this->availabilities;
    }

    public function addAvailability(Availability $availability): self
    {
        if (!$this->availabilities->contains($availability)) {
            $this->availabilities[] = $availability;
            $availability->setMeetingDateId($this);
        }

        return $this;
    }

    public function removeAvailability(Availability $availability): self
    {
        if ($this->availabilities->contains($availability)) {
            $this->availabilities->removeElement($availability);
            // set the owning side to null (unless already changed)
            if ($availability->getMeetingDateId() === $this) {
                $availability->setMeetingDateId(null);
            }
        }

        return $this;
    }

    public function getDay(): ?\DateTimeInterface
    {
        return $this->day;
    }

    public function setDay(\DateTimeInterface $day): self
    {
        $this->day = $day;

        return $this;
    }

    /**
     * Return liste of people who are available for an meeting date
     * @return Collection
     */
    public function getDisposable(): Collection
    {
        $liste = new ArrayCollection();
        foreach($this->availabilities as $avaibilities) {
            if ($avaibilities->getChoice()) {
                $liste->add($avaibilities);
            }
        }
        return $liste;
    }

    public function getCreateAt(): ?\DateTimeInterface
    {
        return $this->create_at;
    }

    public function setCreateAt(\DateTimeInterface $create_at): self
    {
        $this->create_at = $create_at;

        return $this;
    }
}

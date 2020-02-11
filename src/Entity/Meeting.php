<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Cocur\Slugify\Slugify;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass="App\Repository\MeetingRepository")
 */
class Meeting
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min="10", max="255")
     */
    private $title;

    /**
     * @ORM\Column(type="text",  nullable=true)
     * @Assert\Length(min="60", max="400")
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\MeetingDate", mappedBy="meeting")
     */
    private $dates;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="meetings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\MeetingGuest", mappedBy="meeting", orphanRemoval=true)
     */
    private $meetingGuests;


    public function __construct()
    {
        $this->created_at = new \DateTime();
        $this->dates = new ArrayCollection();
        $this->guests = new ArrayCollection();
        $this->meetingGuests = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug () : string
    {
        return (new Slugify())->slugify($this->title);
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function formatedCreatedAt () : string
    {
        return $this->getCreatedAt()->format('H:m:s d-m-Y');
    }

    /**
     * @return Collection|MeetingDate[]
     */
    public function getDates(): Collection
    {
        return $this->dates;
    }

    public function addDate(MeetingDate $date): self
    {
        if (!$this->dates->contains($date)) {
            $this->dates[] = $date;
            $date->setMeeting($this);
        }

        return $this;
    }

    public function removeDate(MeetingDate $date): self
    {
        if ($this->dates->contains($date)) {
            $this->dates->removeElement($date);
            // set the owning side to null (unless already changed)
            if ($date->getMeeting() === $this) {
                $date->setMeeting(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User|null $user
     * @return $this
     */
    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection|Guest[]
     */
    public function getGuests(): Collection
    {
        return $this->guests;
    }

    /**
     * @return Collection|MeetingGuest[]
     */
    public function getMeetingGuests(): Collection
    {
        return $this->meetingGuests;
    }

    public function addMeetingGuest(MeetingGuest $meetingGuest): self
    {
        if (!$this->meetingGuests->contains($meetingGuest)) {
            $this->meetingGuests[] = $meetingGuest;
            $meetingGuest->setMeeting($this);
        }

        return $this;
    }

    public function removeMeetingGuest(MeetingGuest $meetingGuest): self
    {
        if ($this->meetingGuests->contains($meetingGuest)) {
            $this->meetingGuests->removeElement($meetingGuest);
            // set the owning side to null (unless already changed)
            if ($meetingGuest->getMeeting() === $this) {
                $meetingGuest->setMeeting(null);
            }
        }

        return $this;
    }
}

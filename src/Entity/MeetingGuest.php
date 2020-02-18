<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MeetingGuestRepository")
 */
class MeetingGuest
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Meeting", inversedBy="meetingGuests")
     * @ORM\JoinColumn(nullable=false)
     */
    private $meeting;

    /**
     * @ORM\Column(type="datetime")
     */
    private $invited_at;

    /**
     * @ORM\Column(type="boolean")
     */
    private $valid;


    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Availability", mappedBy="meeting_guest", orphanRemoval=true)
     */
    private $availabilities;

    /**
     * @ORM\Column(type="string", length=40)
     */
    private $role;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\GuestWithAccount", mappedBy="meeting_guest", cascade={"persist", "remove"})
     */
    private $guestWithAccount;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\GuestWithoutAccount", mappedBy="meeting_guest", cascade={"persist", "remove"})
     */
    private $guestWithoutAccount;

    public function __construct()
    {
        $this->invited_at = new DateTime();
        $this->valid = false;
        $this->role = "GUEST";
        $this->availabilities = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getInvitedAt(): ?\DateTimeInterface
    {
        return $this->invited_at;
    }

    public function setInvitedAt(\DateTimeInterface $invited_at): self
    {
        $this->invited_at = $invited_at;

        return $this;
    }

    public function getValid(): ?bool
    {
        return $this->valid;
    }

    public function setValid(bool $valid): self
    {
        $this->valid = $valid;

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
            $availability->setMeetingGuestId($this);
        }

        return $this;
    }

    public function removeAvailability(Availability $availability): self
    {
        if ($this->availabilities->contains($availability)) {
            $this->availabilities->removeElement($availability);
            // set the owning side to null (unless already changed)
            if ($availability->getMeetingGuestId() === $this) {
                $availability->setMeetingGuestId(null);
            }
        }

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getGuestWithAccount(): ?GuestWithAccount
    {
        return $this->guestWithAccount;
    }

    public function setGuestWithAccount(GuestWithAccount $guestWithAccount): self
    {
        $this->guestWithAccount = $guestWithAccount;

        // set the owning side of the relation if necessary
        if ($guestWithAccount->getMeetingGuest() !== $this) {
            $guestWithAccount->setMeetingGuest($this);
        }

        return $this;
    }

    public function getGuestWithoutAccount(): ?GuestWithoutAccount
    {
        return $this->guestWithoutAccount;
    }

    public function setGuestWithoutAccount(GuestWithoutAccount $guestWithoutAccount): self
    {
        $this->guestWithoutAccount = $guestWithoutAccount;

        // set the owning side of the relation if necessary
        if ($guestWithoutAccount->getMeetingGuest() !== $this) {
            $guestWithoutAccount->setMeetingGuest($this);
        }

        return $this;
    }
}

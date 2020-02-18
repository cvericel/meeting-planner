<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GuestWithAccountRepository")
 */
class GuestWithAccount
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="guestWithAccounts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\MeetingGuest", inversedBy="guestWithAccount", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $meeting_guest;



    public function __construct(MeetingGuest $meeting_guest, User $user)
    {
        $this->meeting_guest = $meeting_guest;
        $this->user = $user;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getMeetingGuest(): ?MeetingGuest
    {
        return $this->meeting_guest;
    }

    public function setMeetingGuest(MeetingGuest $meeting_guest): self
    {
        $this->meeting_guest = $meeting_guest;

        return $this;
    }
}

<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bundle\MakerBundle\Str;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GuestWithoutAccountRepository")
 */
class GuestWithoutAccount
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\MeetingGuest", inversedBy="guestWithoutAccount")
     * @ORM\JoinColumn(nullable=false)
     */
    private $meeting_guest;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $token;

    public function __construct($meeting_guest, $email)
    {
        $this->meeting_guest = $meeting_guest;
        $this->email = $email;
        $this->token = substr(
            str_shuffle(
                str_repeat(
                    "0123456789azertyuiopmlkjhgfdsqwxcvbnAZERTYUIOPMLKJHGFDSQWXCVBN",
                    255
                )
            ), 0, 255);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

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

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }
}

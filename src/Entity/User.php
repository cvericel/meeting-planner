<?php

namespace App\Entity;

use App\Service\UploaderHelper;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\User\UserInterface;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Cocur\Slugify\Slugify;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @Vich\Uploadable
 * @UniqueEntity("username")
 * @UniqueEntity("email")
 */
class User implements UserInterface, \Serializable
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
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Meeting", mappedBy="user", orphanRemoval=true)
     */
    private $meetings;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $imageFilename;


    private $plainPassword;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\MeetingGuest", mappedBy="user", orphanRemoval=true)
     */
    private $meetingGuests;

    public function __construct()
    {
        $this->meetings = new ArrayCollection();
        $this->guests = new ArrayCollection();
        $this->updated_at = new DateTime();
        $this->events = new ArrayCollection();
        $this->meetingGuests = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getPlainPassword() : ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
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

    /**
     * @inheritDoc
     */
    public function getRoles()
    {
        return ['ROLE_ADMIN'];
    }

    /**
     * @inheritDoc
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {
    }

    /**
     * @inheritDoc
     */
    public function serialize()
    {
        return serialize([
            $this->id,
            $this->username,
            $this->password,
            $this->email
        ]);
    }

    /**
     * @inheritDoc
     */
    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->username,
            $this->password,
            $this->email
            ) = unserialize($serialized, ['allowed_classes' => false]);
    }

    /**
     * @return Collection|Meeting[]
     */
    public function getMeetings(): Collection
    {
        return $this->meetings;
    }

    public function addMeeting(Meeting $meeting): self
    {
        if (!$this->meetings->contains($meeting)) {
            $this->meetings[] = $meeting;
            $meeting->setUser($this);
        }

        return $this;
    }

    public function removeMeeting(Meeting $meeting): self
    {
        if ($this->meetings->contains($meeting)) {
            $this->meetings->removeElement($meeting);
            // set the owning side to null (unless already changed)
            if ($meeting->getUser() === $this) {
                $meeting->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return string|null
     */
    public function getImageFilename(): ?string
    {
        return $this->imageFilename;
    }

    /**
     * @param string|null $imageFilename
     * @return User
     */
    public function setImageFilename(?string $imageFilename): self
    {
        $this->imageFilename = $imageFilename;

        return $this;
    }

    public function getImagePath()
    {
        return  UploaderHelper::USER_IMAGE . '/' . $this->getImageFilename();
    }

    public function getSlug () : string
    {
        return (new Slugify())->slugify($this->username);
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
            $meetingGuest->setUser($this);
        }

        return $this;
    }

    public function removeMeetingGuest(MeetingGuest $meetingGuest): self
    {
        if ($this->meetingGuests->contains($meetingGuest)) {
            $this->meetingGuests->removeElement($meetingGuest);
            // set the owning side to null (unless already changed)
            if ($meetingGuest->getUser() === $this) {
                $meetingGuest->setUser(null);
            }
        }

        return $this;
    }

}

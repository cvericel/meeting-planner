<?php


namespace App\Entity;
use Symfony\Component\Validator\Constraints as Assert;

class Invitation
{
    /**
     * @var string|null
     * @Assert\NotBlank()
     * @Assert\Length(min="2", max="3")
     */
    private $firstname;

    /**
     * @var string|null
     * @Assert\NotBlank()
     * @Assert\Length(min="2", max="3")
     */
    private $lastname;

    /**
     * @var string|null
     * @Assert\NotBlank()
     * @Assert\Email
     */
    private $email;

    /**
     * @return string|null
     */
    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    /**
     * @param string|null $firstname
     * @return Invitation
     */
    public function setFirstname(?string $firstname): Invitation
    {
        $this->firstname = $firstname;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    /**
     * @param string|null $lastname
     * @return Invitation
     */
    public function setLastname(?string $lastname): Invitation
    {
        $this->lastname = $lastname;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     * @return Invitation
     */
    public function setEmail(?string $email): Invitation
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return Meeting|null
     */
    public function getMeeting(): ?Meeting
    {
        return $this->meeting;
    }

    /**
     * @param Meeting|null $meeting
     * @return Invitation
     */
    public function setMeeting(?Meeting $meeting): Invitation
    {
        $this->meeting = $meeting;
        return $this;
    }

    /**
     * @var Meeting|null
     */
    private $meeting;
    
    
}
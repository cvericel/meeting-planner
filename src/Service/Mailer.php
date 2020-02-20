<?php


namespace App\Service;


use App\Entity\Meeting;
use App\Entity\MeetingGuest;
use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class Mailer
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }


    public function sendWelcomeMessage(User $user)
    {
        $email = (new TemplatedEmail())
            ->from(new Address('test@test.fr', 'test'))
            ->to(new Address($user->getEmail(), $user->getUsername()))
            ->subject('Welcome to meeting planner')
            ->htmlTemplate('email/welcome.html.twig')
        ;
        $this->mailer->send($email);
    }


    public function sendWeeklyReportMessage(MeetingGuest $guest, Meeting $meeting)
    {
        $email = (new TemplatedEmail())
            ->from(new Address("test@test.fr", 'Meeting Planner'))
            ->to(new Address($guest->getEmail(), $guest->getUsername()))
            ->subject("Meeting is coming !")
            ->htmlTemplate('email/weekly-report.html.twig')
            ->context([
                'meeting' => $meeting
            ]);
        $this->mailer->send($email);
    }
}
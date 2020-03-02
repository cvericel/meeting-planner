<?php


namespace App\Service;


use App\Entity\Meeting;
use App\Entity\MeetingDate;
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
            ->to(new Address($user->getEmail(), $user->getUsername()))
            ->subject('Welcome to meeting planner')
            ->htmlTemplate('email/welcome.html.twig')
        ;
        $this->mailer->send($email);
    }


    public function sendWeeklyReportMessage(MeetingGuest $guest, Meeting $meeting)
    {
        $email = (new TemplatedEmail())
            ->to(new Address($guest->getEmail(), $guest->getUsername()))
            ->subject("Meeting is coming !")
            ->htmlTemplate('email/weekly-report.html.twig')
            ->context([
                'meeting' => $meeting
            ]);
        $this->mailer->send($email);
    }

    public function sendMeetingDateChoosen(MeetingGuest $guest, MeetingDate $meetingDate)
    {
        $email = (new TemplatedEmail())
            ->to(new Address($guest->getEmail(), $guest->getUsername()))
            ->subject("A date has been fixed !")
            ->htmlTemplate('email/chosen_meeting_date.html.twig')
            ->context([
                'date' => $meetingDate
            ]);
        $this->mailer->send($email);
    }

}
<?php

namespace App\Command;

use App\Repository\MeetingGuestRepository;
use App\Repository\MeetingRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class WeeklyReportSendCommand extends Command
{
    protected static $defaultName = 'app:weekly-report:send';



    private $meetingRepository;

    private $meetingGuestRepository;
    /**
     * @var Mailer
     */
    private $mailer;


    public function __construct(MeetingRepository $meetingRepository, MeetingGuestRepository $meetingGuestRepository, MailerInterface $mailer)
    {
        parent::__construct(null);
        $this->meetingRepository = $meetingRepository;
        $this->meetingGuestRepository = $meetingGuestRepository;
        $this->mailer = $mailer;
    }

    protected function configure()
    {
        $this
            ->setDescription('Send weekly reports to authors')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);


        try {
            $meetings = $this->meetingRepository
                ->findAllMeetingWhoHasFinalDate();
        } catch (\Exception $e) {
            // SQL ERROR
            return -1;
        }

        $io->progressStart();
        foreach ($meetings as $meeting) {
            $guests = $this->meetingGuestRepository
                ->findAllInMeeting($meeting->getId());

            foreach ($guests as $guest) {
                $io->progressAdvance();

                // sent mail
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
        $io->progressFinish();

        $io->success("Weekly reports were sent to user !");
        return 0;
    }
}

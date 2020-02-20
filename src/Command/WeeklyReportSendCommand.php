<?php

namespace App\Command;

use App\Repository\MeetingGuestRepository;
use App\Repository\MeetingRepository;
use App\Service\Mailer;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;


class WeeklyReportSendCommand extends Command
{
    protected static $defaultName = 'app:weekly-report:send';

    private $meetingRepository;
    private $meetingGuestRepository;
    private $mailer;


    public function __construct(MeetingRepository $meetingRepository, MeetingGuestRepository $meetingGuestRepository, Mailer $mailer)
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

                $this->mailer->sendWeeklyReportMessage($guest, $meeting);

            }
        }
        $io->progressFinish();

        $io->success("Weekly reports were sent to user !");
        return 0;
    }
}

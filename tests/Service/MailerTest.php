<?php

namespace App\Tests\Service;

use App\Entity\User;
use App\Service\Mailer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mailer\MailerInterface;

class MailerTest extends TestCase
{
    public function testSendWelcomeMessage()
    {
        $symfonyMailer = $this->createMock(MailerInterface::class);
        $symfonyMailer->expects($this->once())
            ->method('send');


        $user = new User();
        $user->setUsername('testUnitaire');
        $user->setEmail('testUnitaire@symfony.com');

        $mailer = new Mailer($symfonyMailer);
        $mailer->sendWelcomeMessage($user);
    }
}

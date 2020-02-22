<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegistrationControllerTest extends WebTestCase
{
    public function testRegister()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/register');

        $this->assertResponseIsSuccessful();

        $button = $crawler->selectButton('Register');
        $form = $button->form();
        $form['user[username]']->setValue('TestIntegration');
        $form['user[email]']->setValue('testInt@mail.fr');
        $form['user[plainPassword][first]']->setValue('testint');
        $form['user[plainPassword][second]']->setValue('testint');
        $client->submit($form);

        $this->assertResponseRedirects();

        /*
        $this->assertEmailCount(1);
        $email = $this->getMailerMessage(0);
        $this->assertEmailHeaderSame($email, 'To', 'fabien@symfony.fr');
        */
    }
}

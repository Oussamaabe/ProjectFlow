<?php
// src/Command/TestEmailCommand.php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class TestEmailCommand extends Command
{
    protected static $defaultName = 'app:test-email';
    
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        parent::__construct();
        $this->mailer = $mailer;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = (new Email())
            ->from('contactclimavision@gmail.com')
            ->to('oussama.be005@gmail.com')
            ->subject('Test d\'envoi SendGrid')
            ->text('Ceci est un test d\'envoi email.');

        $this->mailer->send($email);

        $output->writeln('Email envoyé avec succès!');
        return Command::SUCCESS;
    }
}
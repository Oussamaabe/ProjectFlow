<?php
// src/Command/CleanupTokensCommand.php
namespace App\Command;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CleanupTokensCommand extends Command
{
    protected static $defaultName = 'app:cleanup-tokens';

    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Supprime les tokens de réinitialisation expirés.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $expiredUsers = $this->userRepository->findExpiredResetTokenUsers();

        foreach ($expiredUsers as $user) {
            $user->setResetToken(null);
            $user->setTokenExpiresAt(null);
        }

        $this->entityManager->flush();

        $output->writeln(sprintf('Nettoyé %d tokens expirés.', count($expiredUsers)));
        return Command::SUCCESS;
    }
}
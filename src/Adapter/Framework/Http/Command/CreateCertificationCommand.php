<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Command;

use App\Application\Handler\CreateCertificationHandler;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('app:create-certifications')]
class CreateCertificationCommand extends Command
{
    public function __construct(
        private readonly CreateCertificationHandler $handler,
    ) {
        parent::__construct();
    }

    public function configure(): void
    {
        $this
            ->setName('app:create-certifications')
            ->setDescription('This command create all the certifications');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->handler->handle();
        $output->writeln('Certifications created successfully!');

        return Command::SUCCESS;
    }
}

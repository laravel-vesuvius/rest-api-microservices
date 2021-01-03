<?php

declare(strict_types=1);

namespace App\Application\Command;

use App\Domain\User\Repository\UserRepositoryInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateAdminUserCommand extends Command
{
    protected static $defaultName = 'app:create-admin';

    /**
     * @var SymfonyStyle
     */
    private $io;

    /**
     * @var MessageBusInterface
     */
    private $commandBus;

    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(
        MessageBusInterface $commandBus,
        UserRepositoryInterface $userRepository,
        ValidatorInterface $validator
    ) {
        parent::__construct();

        $this->commandBus = $commandBus;
        $this->userRepository = $userRepository;
        $this->validator = $validator;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Creates admins and stores them in the database')
            ->addArgument('email', InputArgument::REQUIRED, 'The email of the new admin')
            ->addArgument('password', InputArgument::REQUIRED, 'The plain password of the new admin')
            ->addArgument('firstName', InputArgument::REQUIRED, 'The first name of the new admin')
            ->addArgument('lastName', InputArgument::REQUIRED, 'The last name of the new admin')
        ;
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        if (null !== $input->getArgument('email') && null !== $input->getArgument('password') && null !== $input->getArgument('firstName') && null !== $input->getArgument('lastName')) {
            return;
        }

        $this->io->title('Add AdminUser Command Interactive Wizard');
        $this->io->text([
            'If you prefer to not use this interactive wizard, provide the',
            'arguments required by this command as follows:',
            '',
            ' $ php bin/console app:create-admin email@example.com password FirstName LastName',
            '',
            'Now we\'ll ask you for the value of all the missing command arguments.',
        ]);

        // Ask for the email if it's not defined
        $email = $input->getArgument('email');
        if (null !== $email) {
            $this->io->text(' > <info>Email</info>: ' . $email);
        } else {
            $email = $this->io->ask('Email', null, [$this, 'validateEmail']);
            $input->setArgument('email', $email);
        }

        // Ask for the password if it's not defined
        $password = $input->getArgument('password');
        if (null !== $password) {
            $this->io->text(' > <info>Password</info>: ' . str_repeat('*', mb_strlen($password)));
        } else {
            $password = $this->io->askHidden('Password (your type will be hidden)', [$this, 'validatePassword']);
            $input->setArgument('password', $password);
        }

        // Ask for the first name if it's not defined
        $firstName = $input->getArgument('firstName');
        if (null !== $firstName) {
            $this->io->text(' > <info>First Name</info>: ' . $firstName);
        } else {
            $firstName = $this->io->ask('First Name', null, [$this, 'validateFirstName']);
            $input->setArgument('firstName', $firstName);
        }

        // Ask for the last name if it's not defined
        $lastName = $input->getArgument('lastName');
        if (null !== $lastName) {
            $this->io->text(' > <info>Last Name</info>: ' . $lastName);
        } else {
            $lastName = $this->io->ask('Last Name', null, [$this, 'validateLastName']);
            $input->setArgument('lastName', $lastName);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $stopwatch = new Stopwatch();
        $stopwatch->start('app:create-admin');

        $email = $input->getArgument('email');
        $plainPassword = $input->getArgument('password');
        $firstName = $input->getArgument('firstName');
        $lastName = $input->getArgument('lastName');

        $this->validateAdminUserData($email, $plainPassword, $firstName, $lastName);

        $command = new \App\Domain\User\UseCase\CreateAdminUserCommand(
            Uuid::uuid4()->toString(),
            $email,
            $plainPassword,
            $firstName,
            $lastName
        );

        $this->commandBus->dispatch($command);

        $this->io->success(sprintf('Administrator user was successfully created: %s', $email));

        $event = $stopwatch->stop('app:create-admin');
        if ($output->isVerbose()) {
            $this->io->comment(sprintf('New user database id: %d / Elapsed time: %.2f ms / Consumed memory: %.2f MB', $command, $event->getDuration(), $event->getMemory() / (1024 ** 2)));
        }

        return 0;
    }

    private function validateAdminUserData($email, $plainPassword, $firstName, $lastName): void
    {
        $this->validateEmail($email);
        $this->validatePassword($plainPassword);
        $this->validateFirstName($firstName);
        $this->validateLastName($lastName);
    }

    public function validateEmail($email): string
    {
        if ($email && null !== $this->userRepository->findByEmail($email)) {
            throw new RuntimeException(sprintf('There is already a user registered with the "%s" email.', $email));
        }

        $constraints = $this->validator->validate($email, [
            new NotBlank(),
            new Email(),
            new Type('string'),
            new Length(['max' => 255]),
        ]);
        if ($constraints->count()) {
            throw new InvalidArgumentException('Invalid email');
        }

        return $email;
    }

    public function validatePassword($password): string
    {
        $constraints = $this->validator->validate($password, [
            new NotBlank(),
            new Type('string'),
            new Length(['min' => 6, 'max' => 20]),
        ]);
        if ($constraints->count()) {
            throw new InvalidArgumentException('Invalid password');
        }

        return $password;
    }

    public function validateFirstName($firstName): string
    {
        $constraints = $this->validator->validate($firstName, [
            new NotBlank(),
            new Type('string'),
            new Length(['max' => 255]),
        ]);
        if ($constraints->count()) {
            throw new InvalidArgumentException('Invalid first name');
        }

        return $firstName;
    }

    public function validateLastName($lastName): string
    {
        $constraints = $this->validator->validate($lastName, [
            new NotBlank(),
            new Type('string'),
            new Length(['max' => 255]),
        ]);
        if ($constraints->count()) {
            throw new InvalidArgumentException('Invalid last name');
        }

        return $lastName;
    }
}

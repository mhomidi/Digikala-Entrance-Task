<?php
/**
 * Created by PhpStorm.
 * User: hadi
 * Date: 1/15/21
 * Time: 11:55 PM
 */

namespace App\Command;


use App\Config\Defines;
use App\Entity\Message;
use App\Tasks\SendMessage;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ResendMessagesCommand extends Command
{
    private $managerRegistry;

    protected static $defaultName = "app:messages:resend";

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $messages = $this->managerRegistry->getRepository(Message::class)->findBy(
          ["status" => Defines::SENDING]
        );

        if ($messages){
            foreach ($messages as $message) {
                $sendMessageTask = new SendMessage($message, $this->managerRegistry);
                $sendMessageTask->start();
            }
        }
        else
            $io->success(sprintf("There is no message that is not sent."));

        return 0;
    }
}
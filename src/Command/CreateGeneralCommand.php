<?php
/**
 * Created by PhpStorm.
 * User: hadi
 * Date: 1/16/21
 * Time: 4:04 PM
 */

namespace App\Command;


use App\Config\Defines;
use App\Entity\Info;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CreateGeneralCommand extends Command
{
    private $managerRegistry;

    protected static $defaultName = "app:general_info:create";

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $generalInfo = $this->managerRegistry
            ->getRepository(Info::class)->find(Defines::GENERAL_NUMBER);
        if (!$generalInfo)
        {
            $generalInfo = new Info(Defines::GENERAL_NUMBER);
            $this->managerRegistry->getManager()->persist($generalInfo);
            $this->managerRegistry->getManager()->flush();
            $io->success("Create general info successfully");
            return 0;
        }
        $io->warning("General info exists");
        return 0;
    }
}
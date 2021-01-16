<?php
/**
 * Created by PhpStorm.
 * User: hadi
 * Date: 1/14/21
 * Time: 6:51 PM
 */

namespace App\Tasks;


use App\Config\Defines;
use App\Entity\Info;
use App\Entity\Message;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;

class SendMessage extends Thread
{
    private $message;
    private $doctrine;
    private $info;
    private $generalInfo;
    public function __construct(Message $message, ManagerRegistry $doctrine)
    {
        $this->message = $message;
        $this->doctrine = $doctrine;
        $this->generalInfo = $this->doctrine->getRepository(Info::class)
            ->find(Defines::GENERAL_NUMBER);
    }

    public function run()
    {
        $number = $this->message->getNumber();
        $info = $this->doctrine->getRepository(Info::class)->find($number);
        if (!$info){
            $info = new Info($number);
        }

        $this->info = $info;
        if (!$this->requestAPI($number))
            $this->finalizeFail();
    }

    private function sendFirstAPI(string $number): Response
    {
        $random = rand() % 1000;
        if ($random < 20)
            return new Response("Not responded", Defines::NOT_FOUND_STATUS);
        return new Response("Succeed",Defines::SUCCEED_STATUS);
    }

    private function sendSecondAPI(string $number)
    {
        $random = rand() % 1000;
        if ($random < 20)
            return new Response("Not responded", Defines::NOT_FOUND_STATUS);
        return new Response("Succeed",Defines::SUCCEED_STATUS);
    }

    private function finalizeSuccess(int $apiNumber)
    {

        $entityManager = $this->doctrine->getManager();
        $this->info->setAllSent($this->info->getAllSent() + 1);
        $this->generalInfo->setAllSent($this->generalInfo->getAllSent() + 1);
        if ($apiNumber == Defines::FIRST_API) {
            $this->info->setFirstAPISent($this->info->getFirstAPISent() + 1);
            $this->generalInfo->setFirstAPISent($this->generalInfo->getFirstAPISent() + 1);
        }
        else {
            $this->info->setSecondAPISent($this->info->getSecondAPISent() + 1);
            $this->generalInfo->setSecondAPISent($this->generalInfo->getSecondAPISent() + 1);
        }
        $this->message->setSentBy($apiNumber);
        $this->message->setStatus(Defines::SENT);
        $entityManager->persist($this->message);
        $entityManager->persist($this->info);
        $entityManager->persist($this->generalInfo);
        $entityManager->flush();
    }

    private function finalizeFail()
    {
        $entityManager = $this->doctrine->getManager();
        $this->message->setStatus(Defines::SENDING);
        $entityManager->persist($this->message);
        $entityManager->persist($this->info);
        $entityManager->persist($this->generalInfo);
        $entityManager->flush();
    }

    private function requestAPI(string $number) : bool
    {
        /**
         * Use randomness for load balancing
         */
        $random = rand() % Defines::NUMBER_OF_API;
        if ($random == Defines::FIRST_API) {
            $res = $this->sendFirstAPI($number);
            if ($res->getStatusCode() == Defines::SUCCEED_STATUS) {
                $this->finalizeSuccess(Defines::FIRST_API);
                return true;
            }
            $this->info->setFirstAPIFail($this->info->getFirstAPIFail() + 1);
            $this->generalInfo->setFirstAPIFail($this->generalInfo->getFirstAPIFail() + 1);
            $res = $this->sendSecondAPI($number);
            if ($res->getStatusCode() == Defines::SUCCEED_STATUS) {
                $this->finalizeSuccess(Defines::SECOND_API);
                return true;
            }
            $this->info->setSecondAPIFail($this->info->getSecondAPIFail() + 1);
            $this->generalInfo->setSecondAPIFail($this->generalInfo->getSecondAPIFail() + 1);
        }
        else {
            $res = $this->sendSecondAPI($number);
            if ($res) {
                $this->finalizeSuccess(Defines::SECOND_API);
                return true;
            }
            $this->info->setSecondAPIFail($this->info->getSecondAPIFail() + 1);
            $this->generalInfo->setSecondAPIFail($this->generalInfo->getSecondAPIFail() + 1);
            $res = $this->sendFirstAPI($number);
            if ($res) {
                $this->finalizeSuccess(Defines::FIRST_API);
                return true;
            }
            $this->info->setFirstAPIFail($this->info->getFirstAPIFail() + 1);
            $this->generalInfo->setFirstAPIFail($this->generalInfo->getFirstAPIFail() + 1);
        }
        return false;
    }
}
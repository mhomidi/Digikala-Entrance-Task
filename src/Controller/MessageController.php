<?php

namespace App\Controller;

use App\Entity\Message;
use App\Tasks\SendMessage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MessageController extends AbstractController
{
    /**
     * @Route("/sms/send", name="send_sms")
     */
    public function index(Request $request): Response
    {
        $number = $request->query->get("number");

        $entityManager = $this->getDoctrine()->getManager();
        $message = new Message($number);
        $entityManager->persist($message);
        $entityManager->flush();

        $sendMessageTask = new SendMessage($message, $this->getDoctrine());
        $sendMessageTask->start();

        return $this->json($message);
    }

    /**
     * @Route("/sms/status", name="get_status")
     */
    public function getStatus(Request $request): Response
    {
        $number = $request->query->get("number");

        $messages = $this->getDoctrine()->getRepository(Message::class)->findBy(
            ['number' => $number]
        );

        return $this->json($messages);
    }

}

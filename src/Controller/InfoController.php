<?php

namespace App\Controller;

use App\Config\Defines;
use App\Entity\Info;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InfoController extends AbstractController
{
    /**
     * @Route("/info", name="info")
     */
    public function index(Request $request): Response
    {
        $number = $request->query->get("number", Defines::GENERAL_NUMBER);
        $info = $this->getDoctrine()->getRepository(Info::class)->find($number);
        if (!$info)
            throw $this->createNotFoundException("Never sent any message to ".$number);
        return $this->render('info/index.html.twig', ['info' => $info]);
    }

    /**
     * @Route("/max_sents", name="max_sents")
     */
    public function maxSents(Request $request): Response
    {
        $infos = $this->getDoctrine()->getRepository(Info::class)->findTops(Defines::TOP_QUERY_AMOUNT);
        return $this->render('info/max_sents.html.twig', ['infos' => $infos]);
    }
}

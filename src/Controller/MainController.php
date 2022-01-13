<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



class MainController extends AbstractController
{

    /**
     * @Route("/", name="main_home")
     */
    public function home()
    {
        return $this->render('main/home.html.twig');
    }

    /**
     * @Route("/test/", name="main_test")
     */
    public function test()
    {
        $bucket = [
            "title" => "Game of Thrones",
            "year" => 2000,
        ];
        return $this->render('main/test.html.twig', [
            "bucket" => $bucket,
            "autreVar" => "Hoplala ...",
        ]);
    }

    /**
     * @Route("/Aboutus/", name="main_aboutus")
     */
    public function aboutus()
    {
        $datas = file_get_contents("../data/team.json");
        $decodeDatas = json_decode($datas, true);

        return $this->render('main/aboutus.html.twig', [
            'members' => $decodeDatas,
        ]);
    }
}

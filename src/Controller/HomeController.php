<?php

/**
 * Created by PhpStorm.
 * User: aurelwcs
 * Date: 08/04/19
 * Time: 18:40
 */

namespace App\Controller;

use App\Model\AdvertManager;
use App\Model\UserManager;

class HomeController extends AbstractController
{
    /**
     * Display home page
     *
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function index()
    {
        $userManger = new UserManager();
        $advertManager = new AdvertManager();

        //var_dump($userManger->bestUser());die();
        return $this->twig->render('Home/index.html.twig', [
            'bestUser' => $userManger->bestUser(),
            'bestAdvert' => $advertManager->bestAdvert()
        ]);
    }
}

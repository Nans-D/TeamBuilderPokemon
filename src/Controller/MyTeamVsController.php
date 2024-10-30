<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MyTeamVsController extends AbstractController
{
    #[Route('/my_team_vs', name: 'app_my_team_vs')]
    public function index(): Response
    {
        $file = file_get_contents('../public/data/pokemon_dressers.json');
        $fileGymLeader = json_decode($file, true);
        return $this->render('my_team_vs/index.html.twig', [
            'gymLeaders' => $fileGymLeader,
        ]);
    }
}

<?php

namespace App\Controller;

use App\Entity\PokemonTeam;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MyTeamVsController extends AbstractController
{
    #[Route('/my_team_vs', name: 'app_my_team_vs')]
    public function index(EntityManagerInterface $em): Response
    {

        $user = $this->getUser();
        $file = file_get_contents('../public/data/pokemon_dressers.json');
        $fileGymLeader = json_decode($file, true);
        $repository = $em->getRepository(PokemonTeam::class);
        $teams = $repository->findTeamsWithPokemonsByUser($user);
        // dd($teams);
        return $this->render('my_team_vs/index.html.twig', [
            'gymLeaders' => $fileGymLeader,
            'teams' => $teams
        ]);
    }

    // public function getTeams(EntityManagerInterface $em): Response
    // {

    //     $repository = $em->getRepository(PokemonTeam::class);
    //     $team = $repository->findAll();
    //     dd($team);
    //     return $team;
    // }
}

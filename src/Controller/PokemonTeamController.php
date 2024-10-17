<?php

namespace App\Controller;

use App\Entity\PokemonTeam;
use App\Repository\PokemonTeamRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class PokemonTeamController extends AbstractController

{

    #[Route('/pokemon_team', name: 'pokemon_team')]
    public function index(PokemonTeamRepository $pokemonTeamRepo)
    {
        $user = $this->getUser();
        $fileJsonTypeDirectory = '../public/data/type_damages.json';

        if (!file_exists($fileJsonTypeDirectory)) {

            return $this->redirectToRoute('app_index');
        } else {
            $fileTypeJson = json_decode(file_get_contents($fileJsonTypeDirectory), true);
        }

        if (!$user) {
            return $this->redirectToRoute('app_index');
        }

        // Récupère les équipes avec seulement ID, User et les pokémons
        $findPokemonTeams = $pokemonTeamRepo->findTeamsWithPokemonsByUser($user);

        // Structure des données pour le front-end
        $teamsData = [];

        foreach ($findPokemonTeams as $team) {
            // On traite le champ JSON 'pokemons' ici en PHP
            $pokemons = ($team['pokemons']);

            $teamsData[] = [
                'id' => $team['id'],
                'user_id' => $team['user_id'], // On utilise 'user_id' provenant de la jointure
                'pokemons' => $pokemons
            ];
        }

        // dd($fileTypeJson, $teamsData);

        return $this->render('pokemon_team/index.html.twig', [
            'pokemon_teams' => $teamsData,
            'types' => $fileTypeJson,
        ]);
    }



    #[Route('/save_team', name: 'save_team', methods: ['POST'])]
    public function saveTeam(EntityManagerInterface $em, Request $request, PokemonTeamRepository $pokemonTeam)
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'You should be logged in'], 401);
        }

        $pokemonTeams = $pokemonTeam->findByExampleField($user);
        if (count($pokemonTeams) >= 3) {
            return new JsonResponse(['error' => 'You can only have 3 teams'], 400);
        }


        // if($user->getPokemonTeams()) {
        // dd($user->getPokemonTeams());

        $data = json_decode($request->getContent(), true);

        if (!isset($data)) {
            return new JsonResponse(['error' => 'Invalid data'], 400);
        }

        $pokemonTeam = new PokemonTeam;
        $pokemonTeam->setUser($user);
        $pokemonTeam->setPokemons($data);
        $pokemonTeam->setCreatedAt(new \DateTimeImmutable());

        $em->persist($pokemonTeam);
        $em->flush();

        return new JsonResponse(['message' => 'Team saved successfully!'], 200);
    }

    #[Route('/pokemon_team/delete_team/{id}', name: 'delete_team', methods: ['POST'])]
    public function deleteTeam(EntityManagerInterface $em, Request $request, PokemonTeamRepository $pokemonTeam)
    {


        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 401);
        }

        $data = json_decode($request->getContent(), true);

        if (!isset($data)) {
            return new JsonResponse(['error' => 'Invalid data'], 400);
        }

        $pokemonTeam = $pokemonTeam->findOneBy(['id' => $data['id']]);

        if (!$pokemonTeam) {
            return new JsonResponse(['error' => 'Team not found'], 404);
        }

        $em->remove($pokemonTeam);
        $em->flush();

        return new JsonResponse(['message' => 'Team deleted successfully!'], 200);
    }
}

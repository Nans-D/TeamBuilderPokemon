<?php

namespace App\Controller;

use App\Entity\PokemonTeam;
use App\Repository\PokemonTeamRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
// use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PokemonTeamController extends AbstractController

{

    #[Route('/pokemon_team', name: 'pokemon_team')]
    public function index(EntityManagerInterface $em, Request $request, PokemonTeamRepository $pokemonTeam)
    {
        $user = $this->getUser();

        $findPokemonTeams = $pokemonTeam->findByExampleField();

        $teamsData = [];

        foreach ($findPokemonTeams as $team) {
            $pokemons = $team->getPokemons(); // Récupère les Pokémon de l'équipe
            $pokemonDetails = [];

            foreach ($pokemons as $pokemon) {
                // Supposons que chaque Pokémon est un tableau ou un objet avec 'name' et 'image'
                $pokemonDetails[] = [
                    'name' => $pokemon['name'],   // Ou $pokemon->getName() si c'est un objet
                    'image' => $pokemon['image'], // Ou $pokemon->getImage() si c'est un objet
                ];
            }

            // Ajoute l'équipe dans le tableau $teamsData
            $teamsData[] = [
                'pokemons' => $pokemonDetails
            ];
        }

        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 401);
        }

        return $this->render('pokemon_team/index.html.twig', [
            'pokemon_teams' => $teamsData,
        ]);
    }
    #[Route('/save_team', name: 'save_team', methods: ['POST'])]
    public function saveTeam(EntityManagerInterface $em, Request $request, PokemonTeamRepository $pokemonTeam)
    {
        $user = $this->getUser();

        $pokemonTeams = $pokemonTeam->findByExampleField();
        if (count($pokemonTeams) >= 3) {
            return new JsonResponse(['error' => 'You can only have 3 teams'], 400);
        }

        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 401);
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
}

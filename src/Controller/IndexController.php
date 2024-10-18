<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use PokePHP\PokeApi;

class IndexController extends AbstractController
{
    private PokeApi $pokeApi;

    public function __construct()
    {
        $this->pokeApi = new PokeApi();
    }

    #[Route('/', name: 'app_index')]
    public function index(): Response
    {

        $user = $this->getUser();  // Vérifie si l'utilisateur est connecté

        $directory = $this->getParameter('kernel.project_dir') . '/public/data';
        $responseJson = null;
        if (!file_exists($directory . '/pokemons_version_1.json')) {
            $fileJson = $directory . '/pokemons_version_1.json';

            $imagesPokemonArray = [];

            $responseName = $this->pokeApi->resourceList('pokemon', 151);
            $pokemonData = json_decode($responseName, true);
            foreach ($pokemonData['results'] as $name) {
                $responsePokemonName = $this->pokeApi->pokemon($name['name']);
                $pokemonDetails = json_decode($responsePokemonName, true);

                // je peux avoir les stats ici en faisant 

                $typesCompiled = [];
                foreach ($pokemonDetails['types'] as $types) {
                    $typesCompiled[] = [
                        'name' => $types['type']['name'],
                        'url' => $types['type']['url']
                    ];
                }

                $imagesPokemonArray[] = [
                    'id' => $pokemonDetails['id'],
                    'name' => $name['name'],
                    'image' => $pokemonDetails['sprites']['front_default'] ?? null, // Utilise l'image par défaut s'il y en a une
                    'types' => $typesCompiled
                ];
            }

            $directory = $this->getParameter('kernel.project_dir') . '/public/data';
            if (!is_dir($directory)) {
                mkdir($directory, 0777, true);
            }
            $responseJson = file_put_contents($fileJson, json_encode($imagesPokemonArray, JSON_PRETTY_PRINT));
        } else {
            $responseJson = file_get_contents($directory . '/pokemons_version_1.json');
        }

        // Boucle sur chaque Pokémon pour récupérer les noms et les images

        // Encoder les données en JSON et les enregistrer dans un fichier

        return $this->render('index/index.html.twig', [
            'pokemons' => json_decode($responseJson),
            'isAuthentificated' => $user ? true : false,
        ]);
    }
}

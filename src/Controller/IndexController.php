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

    #[Route('/{id}', name: 'app_index', defaults: ['id' => 1])]
    public function index(int $id): Response
    {


        $user = $this->getUser();  // Vérifie si l'utilisateur est connecté

        $directory = $this->getParameter('kernel.project_dir') . '/public/data';
        $file = $directory . '/pokemons_version_' . $id . '.json';
        $responseJson = null;

        // Appel de la fonction de traitement pour chaque cas (version de Pokédex)
        switch ($id) {
            case 1:
                $responseJson = $this->processPokedex(2, $file, $directory);
                break;
            case 2:
                $responseJson = $this->processPokedex(3, $file, $directory);
                break;
            case 3:
                $responseJson = $this->processPokedex(4, $file, $directory);
                break;
            case 4:
                $responseJson = $this->processPokedex(5, $file, $directory);
                break;
            default:
                throw $this->createNotFoundException("Pokedex version not found");
        }

        return $this->render('index/index.html.twig', [
            'pokemons' => json_decode($responseJson),
            'isAuthentificated' => $user ? true : false,
        ]);
    }

    /**
     * Fonction pour récupérer et traiter les données du Pokédex.
     */
    private function processPokedex(int $pokedexId, string $file, string $directory): ?string
    {
        // Vérifie si le fichier existe déjà
        if (file_exists($file)) {
            return file_get_contents($file);
        }

        // Récupère les données du Pokédex via l'API
        $responseName = $this->pokeApi->pokedex($pokedexId);
        $pokemonData = json_decode($responseName, true);
        $imagesPokemonArray = [];

        // Boucle sur chaque entrée du Pokédex pour traiter les détails des Pokémon
        foreach ($pokemonData['pokemon_entries'] as $name) {
            $responsePokemonName = $this->pokeApi->pokemon($name['pokemon_species']['name']);
            $pokemonDetails = json_decode($responsePokemonName, true);


            // if ($name['pokemon_species']['name'] === 'deoxys') {
            //     // Gérer le cas particulier de Deoxys ici, par exemple en vérifiant les formes
            //     continue;  // Vous pouvez choisir de l'ignorer ou d'ajouter une logique spécifique
            // }

            // Mettre à zéro les types
            $typesCompiled = [];
            // Ajoutez ceci pour afficher le contenu et vérifier s'il est correct

            if (isset($pokemonDetails['types']) && is_array($pokemonDetails['types'])) {
                foreach ($pokemonDetails['types'] as $types) {
                    $typesCompiled[] = [
                        'name' => $types['type']['name'],
                        'url' => $types['type']['url']
                    ];
                }
            } else {
                continue;
            }

            $imagesPokemonArray[] = [
                'id' => $pokemonDetails['id'],
                'name' => $name['pokemon_species']['name'],
                'image' => $pokemonDetails['sprites']['front_default'] ?? null, // Utilise l'image par défaut s'il y en a une
                'types' => $typesCompiled
            ];
        }

        // Crée le répertoire s'il n'existe pas
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        // Sauvegarde les données dans un fichier JSON
        file_put_contents($file, json_encode($imagesPokemonArray, JSON_PRETTY_PRINT));

        return json_encode($imagesPokemonArray);
    }
}

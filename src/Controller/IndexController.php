<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use PokePHP\PokeApi;

set_time_limit(0);


class IndexController extends AbstractController
{
    private PokeApi $pokeApi;

    public function __construct()
    {
        $this->pokeApi = new PokeApi();
    }

    #[Route('/{id}', name: 'app_index', defaults: ['id' => 1], requirements: ['id' => '\d+'])]
    public function index(int $id): Response
    {

        $user = $this->getUser();  // Vérifie si l'utilisateur est connecté

        $directory = $this->getParameter('kernel.project_dir') . '/public/data';
        $file = $directory . '/pokemons_version_' . $id . '.json';
        $responseJson = null;

        // Appel de la fonction de traitement pour chaque cas (version de Pokédex)
        switch ($id) {
            case 1:
                $version = [
                    'yellow',
                    'blue',
                    'red'
                ];
                $responseJson = $this->processPokedex(2, $file, $directory, $version);
                break;
            case 2:
                $version = [
                    'gold',
                    'silver',
                    'crystal'
                ];
                $responseJson = $this->processPokedex(3, $file, $directory, $version);
                break;
            case 3:
                $version = [
                    'ruby',
                    'sapphire',
                    'emerald'
                ];
                $responseJson = $this->processPokedex(4, $file, $directory, $version);
                break;
            case 4:
                $version = [
                    'firered',
                    'leafgreen'
                ];
                $responseJson = $this->processPokedex(5, $file, $directory, $version);
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
    private function processPokedex(int $pokedexId, string $file, string $directory, array $version): ?string
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
            $uniqueTypes = [];

            if (isset($pokemonDetails['types']) && is_array($pokemonDetails['types'])) {
                // Gestion des anciens types
                if (!empty($pokemonDetails['past_types'])) {
                    foreach ($pokemonDetails['past_types'] as $pastType) {
                        foreach ($pastType['types'] as $type) {
                            $typeName = $type['type']['name'];
                            // Ajouter le type uniquement s'il n'est pas déjà dans la liste
                            if (!in_array($typeName, $uniqueTypes)) {
                                $typesCompiled[] = [
                                    'name' => $typeName,
                                    'url' => $type['type']['url']
                                ];
                                $uniqueTypes[] = $typeName;
                            }
                        }
                    }
                }

                // Gestion des types actuels, exclusion de 'fairy'
                foreach ($pokemonDetails['types'] as $type) {
                    $typeName = $type['type']['name'];
                    // Ajouter le type uniquement s'il n'est pas déjà dans la liste et que ce n'est pas 'fairy'
                    if ($typeName !== 'fairy' && !in_array($typeName, $uniqueTypes)) {
                        $typesCompiled[] = [
                            'name' => $typeName,
                            'url' => $type['type']['url']
                        ];
                        $uniqueTypes[] = $typeName;
                    }
                }
            } else {
                continue;
            }

            switch ($pokedexId) {
                case 2:
                    $encounterArea = $this->getLocationVersion($name['pokemon_species']['name'], $version);
                    break;
                case 3:
                    $encounterArea = $this->getLocationVersion($name['pokemon_species']['name'], $version);
                    break;
                case 4:
                    $encounterArea = $this->getLocationVersion($name['pokemon_species']['name'], $version);

                    break;
                case 5:
                    $encounterArea = $this->getLocationVersion($name['pokemon_species']['name'], $version);
                    break;
            }

            $imagesPokemonArray[] = [
                'id' => $pokemonDetails['id'],
                'name' => $name['pokemon_species']['name'],
                'image' => $pokemonDetails['sprites']['front_default'] ?? null, // Utilise l'image par défaut s'il y en a une
                'types' => $typesCompiled,
                'encounters' => $encounterArea,
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

    public function getLocationVersion($pokemonName, $version): array
    {
        $encounterArea = [];
        foreach (json_decode($this->pokeApi->pokemon($pokemonName . '/encounters'), true) as $encounter) {

            foreach ($encounter['version_details'] as $versionDetails) {
                $versionName = $versionDetails['version']['name'];
                $locationName = $encounter['location_area']['name'];

                // Ajouter uniquement pour les versions spécifiées
                if (in_array($versionName, $version)) {
                    // Vérifier si la version existe déjà dans le tableau
                    if (!isset($encounterArea[$versionName])) {
                        // Créer un nouvel objet pour cette version
                        $encounterArea[$versionName] = [
                            'version' => $versionName,
                            'locations' => []
                        ];
                    }

                    // Ajouter le nom de la localisation à l'objet de cette version
                    if (!in_array($locationName, $encounterArea[$versionName]['locations'])) {
                        $encounterArea[$versionName]['locations'][] = $locationName;
                    }
                }
            }
        }

        // Réindexer le tableau pour qu'il soit un tableau d'objets
        $encounterArea = array_values($encounterArea);
        return $encounterArea;
    }
}

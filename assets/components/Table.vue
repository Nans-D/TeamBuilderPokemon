<script setup>
import { onMounted, ref } from "vue";
const typesIcon = ref([]);

const fetchPokemonsData = async (url) => {
  const response = await fetch(url, {
    headers: {
      "Content-type": "application/json",
    },
  });
  if (!response.ok) {
    throw new Error(`Failed to fetch pokemons data: ${response.statusText}`);
  }

  const data = await response.json();
  return data;
};

const props = defineProps({
  types: {
    type: Object,
    required: true,
  },
  pokemonTeam: {
    type: Object,
    required: true,
  },
});

let newPokemon = ref([]);

onMounted(async () => {
  // Traitement pour créer `newPokemon`
  for (const pokemon of props.pokemonTeam.pokemons) {
    for (const key of Object.keys(props.types)) {
      if (pokemon.types[0].name.toLowerCase() == key.toLowerCase()) {
        newPokemon.value.push({
          name: pokemon.name,
          typeName: key,
          effects: Object.values(props.types[key]),
        });
      }
    }
  }

  // Créer un tableau de promesses pour `Promise.all`
  const fetchPromises = Object.keys(props.types).map((icon) => {
    return fetchPokemonsData(
      `https://pokeapi.co/api/v2/type/${icon.toLowerCase()}/`
    );
  });

  // Attendre que toutes les promesses soient résolues
  typesIcon.value = await Promise.all(fetchPromises);
});
</script>

<template>
  <div
    class="table-responsive text-light"
    style="width: 100%; border: 1px solid #104d87"
  >
    <table style="width: 100%">
      <thead>
        <tr class="text-center" style="background-color: #003362 !important">
          <th scope="col">Move</th>
          <th v-for="pokemon in newPokemon" :key="pokemon.name">
            {{ pokemon.name }}
          </th>
          <th scope="col">Strong</th>
          <th scope="col">Resist</th>
        </tr>
      </thead>
      <tbody>
        <tr
          class="text-center"
          v-for="(iconData, typeIndex) in typesIcon"
          :key="typeIndex"
        >
          <td>
            <img
              :src="iconData.sprites['generation-iii']['emerald']['name_icon']"
              alt="Type Icon"
              v-if="
                iconData.sprites &&
                iconData.sprites['generation-iii'] &&
                iconData.sprites['generation-iii']['emerald']
              "
            />
          </td>
          <!-- Pour chaque Pokémon, afficher l'effet associé à ce type -->
          <td v-for="pokemon in newPokemon" :key="pokemon.name">
            x {{ pokemon.effects[typeIndex] }}
          </td>
          <td></td>
          <td></td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

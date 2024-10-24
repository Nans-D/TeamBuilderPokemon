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
  return await response.json();
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

  for (let icon of Object.keys(props.types)) {
    typesIcon.value.push(
      await fetchPokemonsData(
        `https://pokeapi.co/api/v2/type/${icon.toLowerCase()}/`
      )
    );
  }
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
          v-for="(iconUrl, typeIndex) in typesIcon"
          :key="iconUrl"
        >
          <td>
            <img
              :src="
                iconUrl['sprites']['generation-iv']['diamond-pearl'][
                  'name_icon'
                ]
              "
              alt=""
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

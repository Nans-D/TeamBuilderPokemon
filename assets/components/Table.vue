<script setup>
import { ref } from "vue";

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
          v-for="(typeName, typeIndex) in Object.keys(types)"
          :key="typeName"
        >
          <td>{{ typeName }}</td>
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

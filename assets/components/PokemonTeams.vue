<script setup>
import { ref } from "vue";
import Header from "./Header.vue";

const pokemonTeams = ref([]);

const pokemonTeamsData =
  document.getElementById("team-app").dataset.pokemonTeams;

if (pokemonTeamsData) {
  pokemonTeams.value = JSON.parse(pokemonTeamsData);
  console.log(pokemonTeams.value); // Vérifie que les données sont bien chargées
}
</script>

<template>
  <Header />
  <div style="background-color: #111927; height: 100vh">
    <div class="container py-4"></div>
    <div
      v-for="pokemonTeam in pokemonTeams"
      :key="pokemonTeam.team_id"
      class="mb-4"
    >
      <div class="border border-danger rounded p-2">
        <div class="d-flex justify-content-center">
          <div
            v-for="(pokemon, index) in pokemonTeam.pokemons"
            :key="index"
            class="m-2"
          >
            <img :src="pokemon.image" :alt="pokemon.name" />
            <div class="text-light text-center">{{ pokemon.name }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

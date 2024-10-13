<script setup>
import { ref } from "vue";
import Header from "./Header.vue";

const pokemonTeams = ref([]);

const pokemonTeamsData =
  document.getElementById("team-app").dataset.pokemonTeams;

if (pokemonTeamsData) {
  pokemonTeams.value = JSON.parse(pokemonTeamsData);
}

const deleteTeam = (id) => {
  pokemonTeams.value = pokemonTeams.value.filter((p) => p.id !== id);
  fetch("/pokemon_team/delete_team/" + id, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({ id: id }),
  });
};
</script>

<template>
  <Header />
  <div style="background-color: #111927; height: 100vh">
    <div class="container py-4">
      <div class="text-light text-center" v-if="pokemonTeams.length <= 0">
        No teams
      </div>
      <div
        v-for="pokemonTeam in pokemonTeams"
        :key="pokemonTeam.id"
        class="mb-4"
      >
        <div class="rounded p-2" style="border: 1px solid #104d87">
          <div class="d-flex justify-content-center">
            <div
              v-for="(pokemon, index) in pokemonTeam.pokemons"
              :key="index"
              class="m-2"
            >
              <img :src="pokemon.image" :alt="pokemon.name" />
              <div class="text-light text-center">
                {{
                  pokemon.name.charAt(0).toUpperCase() + pokemon.name.slice(1)
                }}
              </div>
            </div>
          </div>
        </div>
        <div class="d-flex justify-content-center gap-3 p-3">
          <button class="btn btn-primary">See more</button>
          <button @click="deleteTeam(pokemonTeam.id)" class="btn btn-danger">
            Delete
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

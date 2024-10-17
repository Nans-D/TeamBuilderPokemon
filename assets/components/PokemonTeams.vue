<script setup>
import { ref } from "vue";
import Header from "./Header.vue";
import Table from "./Table.vue";

const pokemonTeams = ref([]);
const types = ref([]);

// a modifier sur false
const textButtonSeeOrNot = ref("See more");

const pokemonTeamsData =
  document.getElementById("team-app").dataset.pokemonTeams;

if (pokemonTeamsData) {
  const parsedTeams = JSON.parse(pokemonTeamsData);
  pokemonTeams.value = parsedTeams.map((team) => ({
    ...team,
    seeMore: false,
  }));
  textButtonSeeOrNot.value = parsedTeams.map(() => "See more");
}

const typesData = document.getElementById("team-app").dataset.types;

if (typesData) {
  types.value = JSON.parse(typesData);
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

const seeMore = (team) => {
  // Inverser l'état `seeMore` de l'équipe sélectionnée
  team.seeMore = !team.seeMore;

  // Changer le texte du bouton en fonction de l'état `seeMore`
  const index = pokemonTeams.value.findIndex((p) => p.id === team.id);
  textButtonSeeOrNot.value[index] = team.seeMore ? "See less" : "See more";
};
</script>

<template>
  <Header />
  <div style="background-color: #111927">
    <div class="container py-4">
      <div class="text-light text-center" v-if="pokemonTeams.length <= 0">
        No teams
      </div>
      <div
        v-for="(pokemonTeam, teamIndex) in pokemonTeams"
        :key="pokemonTeam.id"
        class="mb-4"
      >
        <div class="rounded p-2" style="border: 1px solid #104d87">
          <div class="row justify-content-center">
            <div
              v-for="(pokemon, index) in pokemonTeam.pokemons"
              :key="index"
              class="col-4 col-md-2"
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
        <Table
          id="tableComponent"
          class="mt-3"
          v-show="pokemonTeam.seeMore"
          :types="types"
          :pokemonTeam="pokemonTeam"
        />
        <div class="d-flex justify-content-center gap-3 p-3">
          <button @click="seeMore(pokemonTeam)" class="btn btn-primary">
            {{ textButtonSeeOrNot[teamIndex] }}
          </button>
          <button @click="deleteTeam(pokemonTeam.id)" class="btn btn-danger">
            Delete
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from "vue";
import Header from "./Header.vue";

const gymLeaders = ref([]);
const gymLeadersKanto = ref([]);
const pokemonTeams = ref([]);

onMounted(() => {
  const gymLeadersData =
    document.getElementById("myTeamVs-app").dataset.gymLeaders;
  const pokemonTeamsData =
    document.getElementById("myTeamVs-app").dataset.teamsPokemon;

  if (gymLeadersData) {
    gymLeaders.value = JSON.parse(gymLeadersData);
  }
  if (pokemonTeamsData) {
    pokemonTeams.value = JSON.parse(pokemonTeamsData);
  }

  for (let region of gymLeaders.value) {
    if (region.region === "Kanto") gymLeadersKanto.value.push(region);
  }
  console.log(gymLeadersKanto.value);
});
</script>

<template>
  <Header />
  <div class="container">
    <div v-if="gymLeadersKanto.length > 0">
      <div class="row justify-content-center">
        <div
          class="col-auto d-flex"
          v-for="(stage, index) in Object.keys(
            gymLeadersKanto[0]['dressors'][0]
          )"
          :key="index"
        >
          <div>
            <img
              v-if="stage.startsWith('stage_')"
              :src="gymLeadersKanto[0]['dressors'][0][stage][0]['image']"
              alt=""
              style="width: 50px; height: 50px"
            />

            <div class="text-center text-light">
              {{ gymLeadersKanto[0]["dressors"][0][stage][0]["type"] }}
            </div>
          </div>
        </div>
      </div>
    </div>
    <div
      v-for="pokemonTeam in pokemonTeams"
      :key="pokemonTeams.id"
      class="mb-4"
    >
      <div class="rounded p-2" style="border: 1px solid #104d87">
        <div class="row justify-content-center">
          <div
            v-for="(pokemon, index) in pokemonTeam.pokemons"
            :key="index"
            class="col-4 col-md-2 d-flex flex-column align-items-center justify-content-center"
          >
            <img :src="pokemon.image" :alt="pokemon.name" />
            <div class="text-light text-center">
              {{ pokemon.name.charAt(0).toUpperCase() + pokemon.name.slice(1) }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped></style>

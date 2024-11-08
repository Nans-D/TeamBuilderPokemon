<script setup>
import { onMounted, ref } from "vue";
import Header from "./Header.vue";
import Table from "./Table.vue";

const pokemonTeams = ref([]);
const types = ref([]);

// a modifier sur false
const textButtonSeeOrNot = ref("See more");

const gymLeaders = ref([]);
const gymLeadersKanto = ref([]);

const typesGymLeader = ref([]);
const gymLeaderSprites = ref([]);

onMounted(async () => {
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

  //GYM LEADER

  const fetchType = async (type) => {
    const response = await fetch(`https://pokeapi.co/api/v2/type/${type}`, {
      headers: {
        "Content-type": "application/json",
      },
    });
    if (!response.ok) {
      throw new Error(`Failed to fetch pokemons data: ${response.statusText}`);
    }

    const data = await response.json();

    return data.sprites["generation-iii"]["emerald"]["name_icon"]; // Retourne null si aucun sprite n'est trouvé
  };

  const gymLeadersData = document.getElementById("team-app").dataset.gymLeaders;

  if (gymLeadersData) {
    gymLeaders.value = JSON.parse(gymLeadersData);
  }
  if (pokemonTeamsData) {
    pokemonTeams.value = JSON.parse(pokemonTeamsData);
  }

  for (let region of gymLeaders.value) {
    if (region.region === "Kanto") gymLeadersKanto.value.push(region);
  }

  for (let gymLeader of gymLeadersKanto.value[0]["dressors"]) {
    if (gymLeader.name) {
      typesGymLeader.value.push(gymLeader.type);
    }
  }

  const fetchPromises = gymLeadersKanto.value[0].dressors.map((gymLeader) =>
    fetchType(gymLeader.type)
  );

  gymLeaderSprites.value = await Promise.all(fetchPromises);
});

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
              class="col-4 col-md-2 d-flex flex-column align-items-center justify-content-center"
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
        <div v-show="pokemonTeam.seeMore">
          <Table
            id="tableComponent"
            class="mt-3"
            :types="types"
            :pokemonTeam="pokemonTeam"
          />
          <div class="mt-3" v-if="gymLeadersKanto.length > 0">
            <div class="row justify-content-center mb-5 gap-2">
              <div
                class="col-auto d-flex rounded p-3"
                v-for="(gymLeader, index) in gymLeadersKanto[0].dressors"
                :key="index"
                style="border: 1px solid #104d87"
              >
                <div class="text-center">
                  <img
                    :src="gymLeader['image']"
                    alt=""
                    style="width: 50px; height: 50px"
                  />

                  <div>
                    <img :src="gymLeaderSprites[index]" />
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
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

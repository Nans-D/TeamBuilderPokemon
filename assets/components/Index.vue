<script setup>
import Header from "./Header.vue";
import { ref } from "vue";
import { onMounted } from "vue";

const pokemons = ref([]);
const myTeam = ref([]);

const addIntoTeam = (pokemon) => {
  if (myTeam.value.includes(pokemon)) {
    return;
  }

  if (myTeam.value.length >= 6) {
    return;
  }

  myTeam.value.push(pokemon);
  console.log(myTeam.value);
};

const removeFromTeam = (pokemon) => {
  myTeam.value = myTeam.value.filter((p) => p.name !== pokemon.name);
};

onMounted(() => {
  const pokemonsData = document.getElementById("app").dataset.pokemons;

  if (pokemonsData) {
    pokemons.value = JSON.parse(pokemonsData);
  }
});
</script>
<template>
  <Header />
  <div style="background-color: #111927">
    <section class="container py-4">
      <div
        id="myTeam"
        class="border border-danger row row-cols-3 row-cols-lg-6 justify-content-center"
        :style="myTeam.length > 0 ? 'auto' : 'height: 122px'"
      >
        <div
          @click="removeFromTeam(pokemon)"
          v-for="pokemon in myTeam"
          class="col"
        >
          <div
            class="d-flex flex-column justify-content-center align-items-center"
          >
            <img :src="pokemon.image" alt="Pokemon" />
            <div class="text-light text-center">
              {{ pokemon.name.charAt(0).toUpperCase() + pokemon.name.slice(1) }}
            </div>
          </div>
        </div>
      </div>
      <div class="d-flex justify-content-center mt-3">
        <button :disabled="!myTeam.length" class="btn btn-primary">Save</button>
      </div>
    </section>
    <section class="container pb-5">
      <ul
        class="row row-cols-2 row-cols-md-4 row-cols-lg-auto justify-content-center p-0"
      >
        <li
          style="cursor: pointer"
          class="list-unstyled col my-2 pokemon"
          v-for="pokemon in pokemons"
          :key="pokemon"
          @click="addIntoTeam(pokemon)"
        >
          <div class="border border-danger rounded p-2">
            <div class="d-flex justify-content-center">
              <img :src="pokemon.image" alt="Pokemon" />
            </div>
            <div class="text-light text-center">
              {{ pokemon.name.charAt(0).toUpperCase() + pokemon.name.slice(1) }}
            </div>
          </div>
        </li>
      </ul>
    </section>
  </div>
</template>

<style scoped></style>

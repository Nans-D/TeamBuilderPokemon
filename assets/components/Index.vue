<script setup>
import Header from "./Header.vue";
import { ref } from "vue";
import { onMounted } from "vue";
import { useToast } from "vue-toastification";

const pokemons = ref([]);
const myTeam = ref([]);
const toast = useToast();

const triggerInfoToast = (type, error) => {
  if (type == "success") {
    toast.success("Team Saved!", {
      timeout: 2000,
    });
  } else {
    toast.error(error, {
      timeout: 2000,
    });
  }
};
const addIntoTeam = (pokemon) => {
  if (myTeam.value.includes(pokemon)) {
    return;
  }

  if (myTeam.value.length >= 6) {
    return;
  }

  myTeam.value.push(pokemon);
};

const removeFromTeam = (pokemon) => {
  myTeam.value = myTeam.value.filter((p) => p.name !== pokemon.name);
};

const saveTeam = async () => {
  try {
    const response = await fetch("/save_team", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(myTeam.value),
    });
    const data = await response.json();
    if (data.error) {
      triggerInfoToast("danger", data.error);
    } else {
      triggerInfoToast("success");
    }
  } catch {}
};

document.addEventListener("scroll", () => {
  const section = document.getElementById("sectionTeamBuilder");
  const myTeam = document.getElementById("myTeam");
  const invisibleTeamBuilder = document.getElementById("invisibleTeamBuilder");

  if (window.scrollY > 350) {
    invisibleTeamBuilder.classList.remove("d-none");
    section.classList.add("fixed-top");
    myTeam.classList.add("backgroundTeamBuilder");
    section.classList.add("z-1");
  } else {
    invisibleTeamBuilder.classList.add("d-none");
    section.classList.remove("fixed-top");
    myTeam.classList.remove("backgroundTeamBuilder");
  }
});

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
    <div id="invisibleTeamBuilder" class="d-none" style="height: 224px"></div>
    <section id="sectionTeamBuilder" class="container py-4">
      <div
        id="myTeam"
        class="row row-cols-3 row-cols-lg-6 justify-content-center rounded p-2"
        :style="myTeam.length > 0 ? 'auto' : 'height: 122px'"
        style="border: 1px solid #104d87"
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
        <button
          @click="saveTeam"
          :disabled="!myTeam.length"
          class="btn btn-primary"
        >
          Save
        </button>
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
          <div style="border: 1px solid #104d87" class="rounded p-2">
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

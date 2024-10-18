<script setup>
import Header from "./Header.vue";
import { ref } from "vue";
import { onMounted } from "vue";
import { useToast } from "vue-toastification";

const pokemons = ref([]);
const myTeam = ref([]);
const toast = useToast();
const dataApiPokemon = ref({});
const secondApiData = ref({});

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
const addIntoTeam = async (pokemon) => {
  if (myTeam.value.includes(pokemon)) {
    return;
  }

  if (myTeam.value.length >= 6) {
    return;
  }

  myTeam.value.push(pokemon);

  try {
    const response = await fetch(
      `https://pokeapi.co/api/v2/pokemon/${
        myTeam.value[myTeam.value.length - 1].id
      }`,
      {
        headers: {
          "Content-type": "application/json",
        },
      }
    );
    dataApiPokemon.value = await response.json();

    // get type image
    const secondResponse = await fetch(
      `https://pokeapi.co/api/v2/type/${dataApiPokemon.value["types"][0]["type"]["name"]}`,
      {
        headers: {
          "Content-type": "application/json",
        },
      }
    );
    secondApiData.value = await secondResponse.json();
  } catch (error) {}
};

const removeFromTeam = (pokemon) => {
  myTeam.value = myTeam.value.filter((p) => p.name !== pokemon.name);
};

const saveTeam = async () => {
  try {
    const loadingToast = toast.info("Saving team, please wait...", {
      timeout: false, // Désactiver le timeout pour qu'il reste visible pendant le chargement
    });
    const response = await fetch("/save_team", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(myTeam.value),
    });
    const data = await response.json();
    toast.dismiss(loadingToast);

    if (data.error) {
      triggerInfoToast("danger", data.error);
    } else {
      triggerInfoToast("success");
    }
  } catch (error) {
    toast.dismiss(loadingToast);
  }
};

document.addEventListener("scroll", () => {
  const section = document.getElementById("sectionTeamBuilder");
  const myTeam = document.getElementById("myTeam");
  const invisibleTeamBuilder = document.getElementById("invisibleTeamBuilder");

  if (window.scrollY > 350) {
    invisibleTeamBuilder.classList.remove("d-none");
    section.classList.add("fixed-custom");
    myTeam.classList.add("backgroundTeamBuilder");
    section.classList.add("z-1");
  }
  if (window.scrollY < 580) {
    invisibleTeamBuilder.classList.add("d-none");
    section.classList.remove("fixed-custom");
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
  <div class="row m-0" style="max-width: 100%; min-height: 100vh">
    <div class="col-12 col-lg-9 position-relative">
      <div class="container p-0">
        <Header />
        <div style="background-color: #111927">
          <div
            id="invisibleTeamBuilder"
            class="d-none"
            style="height: 224px"
          ></div>
          <section id="sectionTeamBuilder" class="m-3" style="z-index: 1">
            <div
              id="myTeam"
              class="row row-cols-6 justify-content-center rounded p-2"
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
                  <img
                    id="pokemonImageTeam"
                    :src="pokemon.image"
                    alt="Pokemon"
                  />
                  <div class="d-none d-md-block text-light text-center">
                    {{
                      pokemon.name.charAt(0).toUpperCase() +
                      pokemon.name.slice(1)
                    }}
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
                    {{
                      pokemon.name.charAt(0).toUpperCase() +
                      pokemon.name.slice(1)
                    }}
                  </div>
                </div>
              </li>
            </ul>
          </section>
        </div>
      </div>
    </div>
    <div id="fixedMobileBottom" class="col-12 col-lg-3 text-light">
      <div v-show="Object.keys(dataApiPokemon).length <= 0">
        No selected pokemon
      </div>
      <div
        class="row sticky-top p-3 p-lg-0"
        v-show="Object.keys(dataApiPokemon).length > 0"
      >
        <div class="col-4 col-lg-12">
          <div>
            <div class="col-12 col-lg-4">
              <img
                :src="
                  dataApiPokemon['sprites']
                    ? dataApiPokemon['sprites']['front_default']
                    : ''
                "
                alt=""
              />
            </div>
            <div class="col-12 col-lg-8 align-self-center">
              <div class="row">
                <div class="col-12">
                  {{
                    dataApiPokemon["name"]
                      ? dataApiPokemon["name"].charAt(0).toUpperCase() +
                        dataApiPokemon["name"].slice(1)
                      : ""
                  }}
                </div>
                <div class="col-12">
                  <img
                    :src="
                      secondApiData['sprites']
                        ? secondApiData['sprites']['generation-iii']['emerald'][
                            'name_icon'
                          ]
                        : ''
                    "
                    alt=""
                  />
                </div>
              </div>
            </div>
          </div>
        </div>
        <div id="arrayStats" class="col-8 col-lg-12">
          <table class="w-100">
            <tbody>
              <!-- Boucle sur les stats pour afficher chaque nom et sa valeur dans deux colonnes -->
              <tr v-for="stats in dataApiPokemon.stats" :key="stats.stat.name">
                <th class="text-size-array" scope="row">
                  {{ stats.stat.name.toUpperCase() }}
                </th>
                <td class="text-size-array text-center">
                  {{ stats.base_stat }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
@media (min-width: 992px) {
  #fixedMobileBottom {
    border-left: 1px solid #104d87;
  }
}
@media (max-width: 991.98px) {
  #pokemonImageTeam {
    width: 50px;
  }
  #fixedMobileBottom {
    background-color: #111927;
    position: fixed;
    bottom: 0;
    border-top: 1px solid #104d87;
  }

  .text-size-array {
    font-size: 10px;
  }
}
.fixed-custom {
  position: sticky;
  top: 20px;
  z-index: 1;
}
</style>

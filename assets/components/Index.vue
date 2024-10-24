<script setup>
import Header from "./Header.vue";
import { ref } from "vue";
import { onMounted } from "vue";
import { useToast } from "vue-toastification";

const pokemons = ref([]);
const myTeam = ref([]);
const toast = useToast();
const dataApiPokemon = ref({});
const secondApiData = ref([]);
const evolutionApiData = ref({});
const urlFromSpecies = ref("");
const arrayEvolutions = ref([]);
const urlSpecies = ref([]);
const showSheets = ref(true);
let pokemonsNewArray = ref({});
const currentRequestId = ref(0);

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

const addIntoTeam = async (pokemon) => {
  if (myTeam.value.length >= 6) {
    toast.error("You can't add more than 6 Pokémon in your team!", {
      timeout: 2000,
    });
    return;
  }
  if (myTeam.value.includes(pokemon)) {
    toast.error("Bro, don't play the same pokemon, think about spirit", {
      timeout: 2000,
    });
    return;
  }

  myTeam.value.push(pokemon);

  let loadingToast;

  try {
    // Affiche un toast de chargement
    loadingToast = toast.info("Fetching Pokémon data...", {
      timeout: false, // Désactiver le timeout pour qu'il reste visible pendant le chargement
    });

    const requestId = ++currentRequestId.value;

    // Fetch Pokémon data
    dataApiPokemon.value = await fetchPokemonsData(
      `https://pokeapi.co/api/v2/pokemon/${
        myTeam.value[myTeam.value.length - 1].id
      }`
    );

    if (requestId !== currentRequestId.value) {
      toast.dismiss(loadingToast);
      return;
    }

    // Fetch type image
    secondApiData.value.length = 0;
    for (let type of myTeam.value[myTeam.value.length - 1]["types"]) {
      if (type && type["url"]) {
        // Vérification supplémentaire pour éviter les erreurs
        try {
          const data = await fetchPokemonsData(type["url"]);
          secondApiData.value.push(data);
        } catch (error) {
          console.error("Error fetching data: ", error);
        }
      } else {
        console.error("Type or URL is undefined:", type);
      }
    }

    if (requestId !== currentRequestId.value) {
      toast.dismiss(loadingToast);
      return;
    }
    // Fetch Pokémon species data
    urlFromSpecies.value = await fetchPokemonsData(
      `https://pokeapi.co/api/v2/pokemon-species/${
        myTeam.value[myTeam.value.length - 1].name
      }`
    );

    if (requestId !== currentRequestId.value) {
      toast.dismiss(loadingToast);
      return;
    }
    // Fetch evolution chain
    evolutionApiData.value = await fetchPokemonsData(
      urlFromSpecies.value["evolution_chain"]["url"]
    );

    if (requestId !== currentRequestId.value) {
      toast.dismiss(loadingToast);
      return;
    }
    // Check evolutions
    arrayEvolutions.value = [];
    if (
      evolutionApiData.value["chain"] &&
      evolutionApiData.value["chain"]["species"]
    ) {
      arrayEvolutions.value.push({
        name: evolutionApiData.value["chain"]["species"]["name"],
        url: evolutionApiData.value["chain"]["species"]["url"],
      });
    }

    if (
      evolutionApiData.value["chain"]["evolves_to"] &&
      evolutionApiData.value["chain"]["evolves_to"].length > 0
    ) {
      arrayEvolutions.value.push({
        name: evolutionApiData.value["chain"]["evolves_to"][0]["species"][
          "name"
        ],
        url: evolutionApiData.value["chain"]["evolves_to"][0]["species"]["url"],
      });

      if (
        evolutionApiData.value["chain"]["evolves_to"][0]["evolves_to"] &&
        evolutionApiData.value["chain"]["evolves_to"][0]["evolves_to"].length >
          0
      ) {
        arrayEvolutions.value.push({
          name: evolutionApiData.value["chain"]["evolves_to"][0][
            "evolves_to"
          ][0]["species"]["name"],
          url: evolutionApiData.value["chain"]["evolves_to"][0][
            "evolves_to"
          ][0]["species"]["url"],
        });
      }
    }

    // Fetch data for each evolution

    if (
      urlFromSpecies.value["generation"]["name"] === "generation-i" &&
      (window.location.href.includes("1") || window.location.href.includes(""))
    ) {
      urlSpecies.value.length = 0;
      for (let evolution of arrayEvolutions.value) {
        if (evolution.url) {
          const test = await fetchPokemonsData(evolution.url);
          if (test.id < 151) {
            urlSpecies.value.push(test);
          }
        } else {
          throw new Error(`${evolution.name} does not have a URL.`);
        }
      }
      pokemonsNewArray = pokemons.value.reduce((acc, current) => {
        acc[current.id] = current;
        return acc;
      }, {});
    } else {
      urlSpecies.value.length = 0;
      for (let evolution of arrayEvolutions.value) {
        if (evolution.url) {
          const test = await fetchPokemonsData(evolution.url);
          urlSpecies.value.push(test);
        } else {
          throw new Error(`${evolution.name} does not have a URL.`);
        }
      }
      pokemonsNewArray = pokemons.value.reduce((acc, current) => {
        acc[current.id] = current;
        return acc;
      });
    }

    // Succès : Fermer le toast de chargement et afficher un toast de succès
    toast.dismiss(loadingToast);
    toast.success("Pokémon data fetched successfully!", {
      timeout: 2000,
    });
  } catch (error) {
    // En cas d'erreur, on ferme le toast de chargement et affiche un toast d'erreur
    toast.dismiss(loadingToast);
    toast.error(`Error fetching data: ${error.message}`);
  }
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
  const scrollY = window.scrollY;
  const section = document.getElementById("sectionTeamBuilder");
  const myTeam = document.getElementById("myTeam");
  const invisibleTeamBuilder = document.getElementById("invisibleTeamBuilder");

  const isScrolled = scrollY > 350;
  const isNotFullyScrolled = scrollY < 580;

  // Gérer l'affichage de l'élément invisible et la position fixe
  invisibleTeamBuilder.classList.toggle("d-none", !isScrolled);
  section.classList.toggle("fixed-custom", isScrolled);
  myTeam.classList.toggle("backgroundTeamBuilder", isScrolled);

  // Gérer la profondeur visuelle
  section.classList.toggle("z-1", isScrolled && isNotFullyScrolled);
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
          <section class="container pb-5 margin-bottom-mobile">
            <div class="d-flex d-lg-none gap-2">
              <div class="checkbox-wrapper-3">
                <input v-model="showSheets" type="checkbox" id="cbx-3" />
                <label for="cbx-3" class="toggle"><span></span></label>
              </div>
              <div class="text-light">Display Sheets</div>
            </div>

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
          <div class="text-light text-center">
            I could build an entire site for next generations after 4th, but if
            you're a real fan like me, you'll agree that there is no more gen
            after 4th ! Take heed. :)
          </div>
          <section></section>
        </div>
      </div>
    </div>
    <div
      v-show="showSheets"
      id="fixedMobileBottom"
      class="col-12 col-lg-3 text-light"
    >
      <div v-show="Object.keys(dataApiPokemon).length <= 0">
        No selected pokemon
      </div>
      <div
        id="stickyTop"
        class="row sticky-top p-3 p-lg-0"
        v-show="Object.keys(dataApiPokemon).length > 0"
      >
        <div class="col-4 col-lg-12 align-self-center">
          <div class="row text-center">
            <div class="col-12 col-lg-4 p-9 mx-auto" style="width: 96px">
              <img
                :src="
                  dataApiPokemon['sprites']
                    ? dataApiPokemon['sprites']['front_default']
                    : ''
                "
                alt=""
                style="width: 100%"
              />
            </div>
            <div class="col-12 col-lg-8 mx-auto align-self-center">
              <div class="row">
                <div class="col-12" style="font-size: 12px">
                  {{
                    dataApiPokemon["name"]
                      ? dataApiPokemon["name"].charAt(0).toUpperCase() +
                        dataApiPokemon["name"].slice(1)
                      : ""
                  }}
                </div>
                <div class="col-12">
                  <div class="row justify-content-center">
                    <div class="col-auto px-1" v-for="type in secondApiData">
                      <img
                        :src="
                          type['sprites']
                            ? type['sprites']['generation-iii'][
                                'firered-leafgreen'
                              ]['name_icon']
                            : ''
                        "
                        alt=""
                      />
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div id="arrayStats" class="col-8 col-lg-12 pt-3">
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
        <div class="col-12 d-none d-lg-block pt-3">
          <div class="row justify-content-center" v-if="urlSpecies.length > 0">
            <div
              class="col-3 p-0 d-flex justify-content-center"
              v-for="specie in urlSpecies"
              :key="specie.id"
            >
              <img
                v-if="pokemonsNewArray[specie.id]"
                :src="pokemonsNewArray[specie.id].image"
                alt="Pokemon"
                class=""
                style="
                  width: 70px;
                  border: 2px solid #104d87;
                  border-radius: 50%;
                  -moz-border-radius: 50%;
                  -webkit-border-radius: 50%;
                "
              />
            </div>
          </div>
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
  #stickyTop {
    top: 30px;
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
  .margin-bottom-mobile {
    margin-bottom: 150px;
  }
}

.fixed-custom {
  position: sticky;
  top: 20px;
  z-index: 1;
}

.checkbox-wrapper-3 input[type="checkbox"] {
  visibility: hidden;
  display: none;
}

.checkbox-wrapper-3 .toggle {
  position: relative;
  display: block;
  width: 40px;
  height: 20px;
  cursor: pointer;
  -webkit-tap-highlight-color: transparent;
  transform: translate3d(0, 0, 0);
}
.checkbox-wrapper-3 .toggle:before {
  content: "";
  position: relative;
  top: 3px;
  left: 3px;
  width: 34px;
  height: 14px;
  display: block;
  background: #9a9999;
  border-radius: 8px;
  transition: background 0.2s ease;
}
.checkbox-wrapper-3 .toggle span {
  position: absolute;
  top: 0;
  left: 0;
  width: 20px;
  height: 20px;
  display: block;
  background: white;
  border-radius: 10px;
  box-shadow: 0 3px 8px rgba(154, 153, 153, 0.5);
  transition: all 0.2s ease;
}
.checkbox-wrapper-3 .toggle span:before {
  content: "";
  position: absolute;
  display: block;
  margin: -18px;
  width: 56px;
  height: 56px;
  background: #3b9eff;
  border-radius: 50%;
  transform: scale(0);
  opacity: 1;
  pointer-events: none;
}

.checkbox-wrapper-3 #cbx-3:checked + .toggle:before {
  background: #3b9eff;
}
.checkbox-wrapper-3 #cbx-3:checked + .toggle span {
  background: #104d87;
  transform: translateX(20px);
  transition: all 0.2s cubic-bezier(0.8, 0.4, 0.3, 1.25), background 0.15s ease;
  box-shadow: 0 3px 8px #0d2847;
}
.checkbox-wrapper-3 #cbx-3:checked + .toggle span:before {
  transform: scale(1);
  opacity: 0;
  transition: all 0.4s ease;
}
</style>

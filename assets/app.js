// import "./bootstrap.js";
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import "./styles/app.css";

import { createApp } from "vue";
import Index from "./components/index.vue";
import PokemonTeams from "./components/PokemonTeams.vue";

if (document.getElementById("team-app")) {
  createApp(PokemonTeams).mount("#team-app");
}
if (document.getElementById("app")) {
  createApp(Index).mount("#app");
}

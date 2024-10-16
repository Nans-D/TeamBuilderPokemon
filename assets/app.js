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
import Toast from "vue-toastification";
import Login from "./components/Login.vue";
// Import the CSS or use your own!
import "vue-toastification/dist/index.css";

const options = {
  position: "top-right",
  timeout: 5000,
  closeOnClick: true,
  pauseOnFocusLoss: true,
  pauseOnHover: true,
  draggable: true,
  draggablePercent: 0.6,
  showCloseButtonOnHover: false,
  hideProgressBar: true,
  closeButton: "button",
  icon: true,
  rtl: false,
};

if (document.getElementById("team-app")) {
  createApp(PokemonTeams).mount("#team-app");
  createApp(PokemonTeams).use(Toast, options);
}
if (document.getElementById("app")) {
  createApp(Index).mount("#app");
  createApp(Index).use(Toast, options);
}

if (document.getElementById("login-app")) {
  createApp(Login).mount("#login-app");
  createApp(Login).use(Toast, options);
}

<script setup>
import { ref, onMounted, onBeforeUnmount } from "vue";

const burgerMenuButton = ref(false);

const toggleBurgerMenu = () => {
  burgerMenuButton.value = !burgerMenuButton.value;
};

const isAuthentificated = ref(
  document.body.dataset.authentificated === "false"
);

// Gérer l'événement de clic en dehors du menu pour le fermer
const handleClickOutside = (e) => {
  const burgerButton = document.querySelector(".fa-burger"); // Icone du burger
  const menuContainer = document.getElementById("burgerMenu"); // Le conteneur du menu

  if (
    burgerButton &&
    menuContainer &&
    !burgerButton.contains(e.target) &&
    !menuContainer.contains(e.target)
  ) {
    // Ferme le menu si le clic est à l'extérieur du bouton ou du menu
    burgerMenuButton.value = false;
  }
};

onMounted(() => {
  document.addEventListener("click", handleClickOutside);
});

onBeforeUnmount(() => {
  document.removeEventListener("click", handleClickOutside);
});
</script>

<template>
  <div style="background-color: #111927">
    <div class="container">
      <nav
        class="d-flex justify-content-between align-items-center"
        style="width: 100%"
      >
        <div>
          <a href="/"
            ><img
              src="../images/logo-team-builder.png"
              alt=""
              style="width: 120px"
          /></a>
        </div>
        <ul
          class="d-none d-md-flex list-unstyled gap-3 justify-content-center align-content-center p-2 m-0"
        >
          <li>
            <a
              class="link-light link-underline-opacity-0 link-offset-2 link-underline-opacity-100-hover"
              href="/"
              >Home</a
            >
          </li>
          <li v-if="isAuthentificated">
            <a
              class="link-light link-underline-opacity-0 link-offset-2 link-underline-opacity-100-hover"
              href="/register"
              >Register</a
            >
          </li>
          <li v-if="isAuthentificated">
            <a
              class="link-light link-underline-opacity-0 link-offset-2 link-underline-opacity-100-hover"
              href="/login"
              >Login</a
            >
          </li>
          <li v-if="isAuthentificated">
            <a
              class="link-light link-underline-opacity-0 link-offset-2 link-underline-opacity-100-hover"
              href="/logout"
              >Logout</a
            >
          </li>
          <li v-if="!isAuthentificated">
            <a
              class="link-light link-underline-opacity-0 link-offset-2 link-underline-opacity-100-hover"
              href="/pokemon_team"
              >My Team</a
            >
          </li>
        </ul>

        <!-- BURGER MENU -->
        <div @click="toggleBurgerMenu" class="d-block d-md-none me-4">
          <i style="font-size: 24px" class="text-light fa-solid fa-burger"></i>
        </div>
        <div
          id="burgerMenu"
          v-show="burgerMenuButton"
          class="z-2 position-fixed top-0 end-0 p-3"
          style="
            background-color: #111927;
            border-left: 1px solid #104d87;
            height: 100vh;
            width: 35%;
          "
        >
          <ul class="d-flex flex-column list-unstyled align-items-end gap-2">
            <li>
              <a
                class="fs-2 link-light link-underline-opacity-0 link-offset-2 link-underline-opacity-100-hover"
                href="/"
                >Home</a
              >
            </li>
            <li v-if="isAuthentificated">
              <a
                class="fs-2 link-light link-underline-opacity-0 link-offset-2 link-underline-opacity-100-hover"
                href="/register"
                >Register</a
              >
            </li>
            <li v-if="isAuthentificated">
              <a
                class="fs-2 link-light link-underline-opacity-0 link-offset-2 link-underline-opacity-100-hover"
                href="/login"
                >Login</a
              >
            </li>
            <li v-if="!isAuthentificated">
              <a
                class="fs-2 link-light link-underline-opacity-0 link-offset-2 link-underline-opacity-100-hover"
                href="/logout"
                >Logout</a
              >
            </li>
            <li v-if="!isAuthentificated">
              <a
                class="fs-2 link-light link-underline-opacity-0 link-offset-2 link-underline-opacity-100-hover"
                href="/pokemon_team"
                >My Team</a
              >
            </li>
          </ul>
        </div>
      </nav>
    </div>
  </div>
</template>

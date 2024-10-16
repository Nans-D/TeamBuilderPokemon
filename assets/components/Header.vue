<script setup>
import { ref } from "vue";

const isAuthentificated = ref(
  document.body.dataset.authentificated === "false"
);
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
          class="d-flex list-unstyled gap-3 justify-content-center align-content-center p-2 m-0"
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
          <li>
            <a
              class="link-light link-underline-opacity-0 link-offset-2 link-underline-opacity-100-hover"
              href="/pokemon_team"
              >My Team</a
            >
          </li>
        </ul>
      </nav>
    </div>
  </div>
</template>

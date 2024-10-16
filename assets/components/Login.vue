<script setup>
import { onMounted, ref } from "vue";
import { useToast } from "vue-toastification";

const error = ref(null);
const csrfToken = ref("");
const toast = useToast();

onMounted(() => {
  toast.success("Test toast affiché", {
    timeout: 2000,
  });
  const appElement = document.getElementById("login-app");
  csrfToken.value = appElement.dataset.csrfToken;
  error.value = appElement.dataset.error;

  // Vérifie ici après que error.value a été assigné
  requestAnimationFrame(() => {
    if (error.value !== "null") {
      toast.error(JSON.parse(error.value), {
        timeout: 2000,
      });
    }
  });
});
</script>

<template>
  <form @submit="handleSubmit" method="post">
    <div style="background-color: #111927">
      <div class="container" style="height: 100vh">
        <div
          class="d-flex flex-column justify-content-center align-items-center h-100 text-light"
        >
          <div
            class="d-flex flex-column justify-content-center align-items-center h-100 text-light"
          >
            <h1 class="h3 mb-3 font-weight-normal">Please sign in</h1>
            <div class="mt-3">
              <label for="inputEmail">Email</label>
              <input
                type="email"
                name="email"
                id="inputEmail"
                class="form-control"
                autocomplete="email"
                required
                autofocus
              />
            </div>
            <div class="mt-3">
              <label for="inputPassword">Password</label>
              <input
                type="password"
                name="password"
                id="inputPassword"
                class="form-control"
                autocomplete="current-password"
                required
              />
            </div>
            <input type="hidden" name="_csrf_token" :value="csrfToken" />
            <button class="btn btn-lg btn-primary mt-3">Sign in</button>
          </div>
        </div>
      </div>
    </div>
  </form>
</template>

<script setup>
import { onMounted, ref } from "vue";
const gymLeaders = ref([]);
const gymLeadersKanto = ref([]);

onMounted(() => {
  const gymLeadersData =
    document.getElementById("myTeamVs-app").dataset.gymLeaders;

  if (gymLeadersData) {
    gymLeaders.value = JSON.parse(gymLeadersData);
  }

  for (let region of gymLeaders.value) {
    if (region.region === "Kanto") gymLeadersKanto.value.push(region);
  }
  console.log(gymLeadersKanto.value[0]["dressors"]);
});
</script>

<template>
  <div v-if="gymLeadersKanto.length > 0">
    <img
      :src="gymLeadersKanto[0]['dressors'][0]['stage_1'][0]['image']"
      alt=""
      style="width: 100%; height: 100%"
    />
  </div>
</template>

<style scoped></style>

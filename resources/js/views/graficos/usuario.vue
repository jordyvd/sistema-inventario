<template>
  <div class="container">
    <div v-if="user_reportes>0">
      <grafico-vendedor></grafico-vendedor>
    </div>
    <div v-else class="pdd-30">
      <notpermiso></notpermiso>
    </div>
  </div>
</template>

<template>
  <div class="container">
    <div v-if="user_report_ingre>0">
      <grafico-clientes></grafico-clientes>
    </div>
    <div v-else class="pdd-30">
      <notpermiso></notpermiso>
    </div>
  </div>
</template>

<template>
  <div class="container">
    <div v-if="user_report_ingre>0">
      <grafico-ganancia></grafico-ganancia>
    </div>
    <div v-else class="pdd-30">
      <notpermiso></notpermiso>
    </div>
  </div>
</template>
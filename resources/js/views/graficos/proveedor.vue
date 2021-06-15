<template>
  <div class="container">
    <div v-if="user_report_egre>0">
      <grafico-proveedor></grafico-proveedor>
    </div>
    <div v-else class="pdd-30">
      <notpermiso></notpermiso>
    </div>
  </div>
</template>
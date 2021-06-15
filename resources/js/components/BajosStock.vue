<template>
  <div class="card my-2">
    <div class="card-header" v-if="carga">
      <b>calculando...</b>
    </div>
    <div class="card-header" v-else>
      <b>Productos con bajo stock(3 - 1) - Límite(30)</b>
    </div>
    <line-chart :chart-data="datacollection" :height="120"></line-chart>
  </div>
</template>

<script>
import LineChart from "../LineChart.js";
export default {
  components: {
    LineChart,
  },
  data() {
    return {
      datacollection: {},
      productos: [],
      codigo: [],
      stock: [],
      carga:true,
    };
  },
  mounted() {
    this.fillData();
  },
  methods: {
    fillData() {
      this.carga = true;
      let url = "/api/bajostock/" + this.user_sucursal;
      axios
        .get(url)
        .then((res) => {
          this.productos = res.data;
          if (this.productos) {
            this.productos.forEach((element) => {
              this.codigo.push(element.codigo);
              this.stock.push(element.stock_almacen);
            });
            // *******
            this.datacollection = {
              labels: this.codigo,
              datasets: [
                {
                  label: "stock",
                  backgroundColor: "#DB4333",
                  borderColor: "#DB4333",
                  lineTension: 0,
                  fill: false,
                  data: this.stock,
                },
              ],
            };
          }
          this.carga = false;
        })
        .catch((error) => {
          location.reload();
        });
    },
  },
};
</script>

<style lang="css">
.small {
  max-width: 800px;
  /* max-height: 500px; */
  margin: 50px auto;
}
</style>
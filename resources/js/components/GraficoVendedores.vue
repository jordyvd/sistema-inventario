<template>
  <div class="card my-2">
    <div class="card-header">
      <div class="text-center">
        <input type="date" v-model="consulta.desde" class="input-search" />
        <input type="date" v-model="consulta.hasta" class="input-search" />
        <select
          class="input-search"
          v-model="consulta.vendedor"
          style="padding: 15px 2px"
        >
          <option value>vendedor...</option>
          <option
            v-for="(item, index) in usuarios"
            :key="index"
            :value="item.name"
          >
            {{ item.name }}
          </option>
        </select>
        <button class="input-search bg-white" @click="validar">
          <i class="fas fa-search"></i> Buscar
        </button>
      </div>
    </div>
    <div v-if="spinner" class="centrar">cargando...</div>
    <div v-else class="title_total">
      <div class="title-css" v-if="total_general > 0">
        <b>total general: S/. {{ total_general.toFixed(2) }}</b>
      </div>
      <bar-chart :chart-data="datacollection" :height="200"></bar-chart>
    </div>
  </div>
</template>
<script>
import BarChart from "../BarChart.js";
export default {
  components: {
    BarChart,
  },
  data() {
    return {
      datacollection: {},
      consulta: {
        desde: "",
        hasta: "",
        vendedor: "",
      },
      usuarios: [],
      fecha: [],
      total: [],
      ventas: [],
      spinner: false,
    };
  },
  created() {
    let url = "/api/listusuarios";
    axios.get(url).then((res) => {
      this.usuarios = res.data.usuarios.data;
    });
  },
  methods: {
    validar() {
      if (
        this.consulta.desde.trim() === "" ||
        this.consulta.hasta.trim() === "" ||
        this.consulta.vendedor.trim() === ""
      ) {
        swal({
          text: "completar todos los campos",
          icon: "error",
          dangerMode: "aceptar",
        });
      } else {
        this.GetDate();
      }
    },
    GetDate() {
      this.spinner = true;
      this.$data.fecha = [];
      this.$data.total = [];
      let url =
        "/api/grafico_vendedor/" +
        this.consulta.desde +
        "/" +
        this.consulta.hasta +
        "/" +
        this.consulta.vendedor;
      axios.get(url).then((res) => {
        this.ventas = res.data;
        if (this.ventas.length > 0) {
          this.ventas.forEach((element) => {
            this.fecha.push(element.fecha);
            this.total.push(element.total_venta);
          });
          //   ***** GRAFICO *****
          this.datacollection = {
            labels: this.fecha,
            datasets: [
              {
                label: "total S/",
                backgroundColor: "rgba(55, 145, 22,0.6)",
                borderColor: "#379116",
                borderWidth: 2,
                data: this.total,
              },
            ],
          };
          this.spinner = false;
        } else {
          this.spinner = false;
          swal({
            text: "no existen registros",
            icon: "error",
            dangerMode: true,
          });
        }
      });
    },
  },
  computed: {
    total_general() {
      return this.ventas.reduce((total, item) => {
        return (total += item.total_venta);
      }, 0);
    },
  },
};
</script>
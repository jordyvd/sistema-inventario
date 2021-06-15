<template>
  <div class="card my-2">
    <div class="card-header">
      <div class="text-center">
        <input type="date" v-model="consulta.desde" class="input-search" />
        <input type="date" v-model="consulta.hasta" class="input-search" />
        <select
          class="input-search"
          v-model="consulta.sucursal"
          style="padding: 15px 2px"
        >
          <option value disabled>sucursal...</option>
          <option value="todos">todos</option>
          <option
            v-for="(item, index) in sucursal"
            :key="index"
            :value="item.nombre"
          >
            {{ item.nombre }}
          </option>
          }
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
        sucursal: "",
      },
      sucursal: [],
      fecha: [],
      total: [],
      ventas: [],
      spinner: false,
    };
  },
  created() {
    axios.get("/api/listsucursal").then((res) => {
      this.sucursal = res.data.sucursal.data;
    });
  },
  methods: {
    validar() {
      if (
        this.consulta.desde.trim() === "" ||
        this.consulta.hasta.trim() === "" ||
        this.consulta.sucursal.trim() === ""
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
        "/api/grafico_proveedor/" +
        this.consulta.desde +
        "/" +
        this.consulta.hasta +
        "/" +
        this.consulta.sucursal;
      axios.get(url).then((res) => {
        this.ventas = res.data;
        if (this.ventas.length > 0) {
          this.ventas.forEach((element) => {
            this.fecha.push(element.fecha);
            this.total.push(element.total_egreso);
          });
          //   ***** GRAFICO *****
          this.datacollection = {
            labels: this.fecha,
            datasets: [
              {
                label: "total S/",
                backgroundColor: "rgba(205, 27, 9,0.7)",
                borderColor: "#CD1B09",
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
        return (total += item.total_egreso);
      }, 0);
    },
  },
};
</script>
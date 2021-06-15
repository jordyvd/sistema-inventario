<template>
  <div>
    <div class="container">
      <br />
      <div class="row">
        <div class="col-sm-3">
          <div class="card">
            <div class="card-body">
              <div class="card-header bg-white">
                <b>agotados</b>
              </div>
              <h2 class="card-title">
                <p v-if="cargando" class="text-grande">calculando...</p>
                <b v-else>{{ total_agotado }}</b>
              </h2>
              <p class="card-text" style="text-align: right; font-size: 30px">
                <i class="fas fa-skull-crossbones"></i>
              </p>
            </div>
          </div>
        </div>
        <div class="col-sm-3">
          <div class="card">
            <div class="card-body">
              <div class="card-header bg-white">
                <b>ventas</b>
              </div>
              <h2 class="card-title">
                <p v-if="cargando" class="text-grande">calculando...</p>
                <b v-else>{{ cantidad }}</b>
              </h2>
              <p class="card-text" style="text-align: right; font-size: 30px">
                <i class="fas fa-hand-holding-usd"></i>
              </p>
            </div>
          </div>
        </div>
        <div class="col-sm-3">
          <div class="card">
            <div class="card-body">
              <div class="card-header bg-white">
                <b>efectivo S/.</b>
              </div>
              <h2 class="card-title">
                <p v-if="cargando" class="text-grande">calculando...</p>
                <b v-else>{{ parseFloat(total_efectivo_general).toFixed(2) }}</b>
              </h2>
              <p class="card-text" style="text-align: right; font-size: 30px">
                <i class="fas fa-wallet"></i>
              </p>
            </div>
          </div>
        </div>
        <div class="col-sm-3">
          <div class="card">
            <div class="card-body">
              <div class="card-header bg-white">
                <b>total de ventas S/.</b>
              </div>
              <h2 class="card-title">
                <p v-if="cargando" class="text-grande">calculando...</p>
                <b v-else>{{ parseFloat(total_double).toFixed(2) }}</b>
              </h2>
              <p class="card-text" style="text-align: right; font-size: 30px">
                <i class="fas fa-credit-card"></i>
              </p>
            </div>
          </div>
        </div>
      </div>
      <!-- FIN CARDS -->
      <bajos-stock></bajos-stock>
    </div>
  </div>
</template>
<script>
export default {
  data() {
    return {
      cantidad: [],
      spinner_cantidad: true,
      total: [],
      salidas: [],
      ingresos: [],
      total_pendiente: [],
      total_agotado: [],
      total_double: 0,
      total_efectivo: 0,
      cargando: false,
      condicion: "+",
    };
  },
  created() {
    this.total_pendientes();
    this.total_salida();
    this.total_ingreso();
    this.total_agotados();
    this.cantidad_ve();
    this.total_ventas();
  },
  methods: {
    total_salida() {
      axios
        .get("/api/total_salida/" + this.user_sucursal)
        .then((res) => {
          this.salidas = res.data;
        })
        .catch((error) => {
          Vue.$toast.error("algo salio mal...");
          location.reload();
        });
    },
    total_ingreso() {
      axios
        .get("/api/total_ingresos/" + this.user_sucursal)
        .then((res) => {
          this.ingresos = res.data;
        })
        .catch((error) => {
          Vue.$toast.error("algo salio mal...");
          location.reload();
        });
    },
    cantidad_ve() {
      axios
        .get("/api/contar_ventas/" + this.user_sucursal)
        .then((res) => {
          this.cantidad = res.data;
        })
        .catch((error) => {
          Vue.$toast.error("algo salio mal...");
          location.reload();
        });
    },
    total_pendientes() {
      let url = "/api/total_pendientes/" + this.user_sucursal;
      axios
        .get(url)
        .then((res) => {
          this.total_pendiente = res.data;
          this.total_pendiente.forEach((element) => {
            this.total_efectivo = element.total_venta;
          });
          if (this.total_efectivo < 1) {
            this.total_efectivo = 0;
          }
        })
        .catch((error) => {
          Vue.$toast.error("algo salio mal...");
          location.reload();
        });
    },
    total_agotados() {
      let url = "/api/total_agotados/" + this.user_sucursal;
      axios
        .get(url)
        .then((res) => {
          this.total_agotado = res.data;
        })
        .catch((error) => {
          Vue.$toast.error("algo salio mal...");
          location.reload();
        });
    },
    total_ventas() {
      this.cargando = true;
      let url = "/api/total_venta_actual/" + this.user_sucursal;
      axios
        .get(url)
        .then((res) => {
          this.total = res.data;
          this.total.forEach((element) => {
            this.total_double = element.total_venta;
          });
          if (this.total_double < 1) {
            this.total_double = 0;
          }
          this.cargando = false;
        })
        .catch((error) => {
          Vue.$toast.error("algo salio mal...");
          location.reload();
        });
    },
  },
  computed: {
    movimiento() {
      return this.ingresos.monto_ingreso + this.total_efectivo;
    },
    total_efectivo_general() {
      return this.movimiento - this.salidas.monto_salida;
    },
  },
};
</script>
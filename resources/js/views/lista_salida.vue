<template>
  <div>
    <div class="card-header">
      <input type="date" class="input-search" v-model="fecha.desde" />
      <input type="date" class="input-search" v-model="fecha.hasta" @change="buscar_fecha" />
      <button class="button" @click="refrescar">
        <i class="fas fa-redo"></i> refrescar
      </button>
      <b v-if="cargando">Perdida: calculando...</b>
      <b v-else>Perdida: {{ total_double.toFixed(2) }}</b>
    </div>
    <div class="table-scroll">
      <div v-if="spinner" class="centrar">
         cargando...
      </div>
      <table class="table" v-else>
        <thead>
          <tr>
            <th scope="col">#</th>
            <th scope="col">nro</th>
            <th scope="col">descripcion</th>
            <th scope="col">condición</th>
            <th scope="col">total</th>
            <th scope="col">fecha</th>
            <th scope="col">anular</th>
          </tr>
        </thead>
        <tbody v-for="(item, index) in lista" :key="index">
          <tr>
            <th scope="row">{{ index + 1 }}</th>
            <td>
              {{ item.cod_sucursal }}
              <button
                data-toggle="modal"
                @click="detalles_salida(item)"
                data-target="#exampleModal"
              >
                <i class="fas fa-eye"></i>
              </button>
            </td>
            <td>
              {{ item.descripcion }}
            </td>
            <td>{{ item.condicion }}</td>
            <td>{{ parseFloat(item.total).toFixed(2) }}</td>
            <td>{{ item.fecha }}</td>
            <td>
              <button class="text-danger" @click="anular(item)">
                <i class="fas fa-ban"></i>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <!-- ******* PAGINACION ********* -->
    <nav>
      <ul class="pagination">
        <li v-if="pagination.current_page > 1" class="page-item">
          <a href="#" @click.prevent="changePage(pagination.current_page - 1)">
            <span>
              <b>Atras</b>
            </span>
          </a>
        </li>
        <li
          class="page-item"
          v-for="(page, index) in pagesNumber"
          v-bind:class="[page == isActived ? 'active_pagination' : '']"
          :key="index"
        >
          <a href @click.prevent="changePage(page)">
            <b>{{ page }}</b>
          </a>
        </li>
        <li
          class="page-item"
          v-if="pagination.current_page < pagination.last_page"
        >
          <a href="#" @click.prevent="changePage(pagination.current_page + 1)">
            <span>
              <b>Siguiente</b>
            </span>
          </a>
        </li>
      </ul>
    </nav>
    <!-- Modal -->
    <div
      class="modal fade"
      id="exampleModal"
      tabindex="-1"
      aria-labelledby="exampleModalLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-xl">
        <div class="modal-content border-modal modal-colo-p">
          <div class="modal-header">
            <h5 class="modal-title text-system" id="exampleModalLabel">
              {{ nrof }}({{ parseFloat(total).toFixed(2) }})
            </h5>
            <button
              type="button"
              class="close"
              data-dismiss="modal"
              aria-label="Close"
            >
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <!-- *** DATELLES *** -->
            <div v-if="spinner_details" class="centrar text-white">
              cargando...
            </div>
            <!-- <span class="sr-only">Loading...</span> -->
            <!-- </div> -->
            <table class="table table-bordered" v-else>
              <thead class="tbl-text-white">
                <tr>
                  <th scope="col">#</th>
                  <th scope="col">Producto</th>
                  <th scope="col">Precio</th>
                  <th scope="col">Cantidad</th>
                  <th scope="col">Descuento</th>
                  <th scope="col">Importe</th>
                </tr>
              </thead>
              <tbody v-for="(item, index) in detalles" :key="index" class="tbl-text-white">
                <tr>
                  <th scope="row">{{ index + 1 }}</th>
                  <td>{{ item.nompro }}</td>
                  <td>{{ parseFloat(item.precio).toFixed(2) }}</td>
                  <td>{{ item.cantidad }}</td>
                  <td>{{ parseFloat(item.descuento).toFixed(2) }}</td>
                  <td>
                    {{
                      (
                        parseFloat(item.precio) * parseFloat(item.cantidad) -
                        parseFloat(item.descuento)
                      ).toFixed(2)
                    }}
                  </td>
                </tr>
              </tbody>
            </table>
            <!-- *** FIN DETALLES *** -->
          </div>
        </div>
      </div>
    </div>
    <!-- ** modal ** -->
  </div>
</template>
<script>
export default {
  data() {
    return {
      spinner: false,
      nrof: "",
      total: "",
      lista: [],
      fecha: {
        desde: "1",
        hasta: "1",
      },
      detalles: [],
      spinner_details: false,
      total_generado: [],
      cargando: false,
      total_double: 0,
    };
  },
  created() {
    this.listar();
  },
  methods: {
    listar(page) {
      this.spinner = true;
      this.cargando = true;
      let url =
        "/api/listarsalidas/" +
        this.user_sucursal +
        "/" +
        this.fecha.desde +
        "/" +
        this.fecha.hasta +
        "?page=" +
        page;
      axios.get(url).then((res) => {
        this.lista = res.data.salida.data;
        this.pagination = res.data.paginate;
        this.spinner = false;
        this.total_perdida();
      });
    },
    changePage(page) {
      this.pagination.current_page = page;
      this.listar(page);
    },
    detalles_salida(item) {
      this.spinner_details = true;
      this.nrof = item.cod_sucursal;
      this.total = item.total;
      let url =
        "/api/detalle_salidas/" + item.cod_sucursal + "/" + this.user_sucursal;
      axios.get(url).then((res) => {
        this.detalles = res.data;
        this.spinner_details = false;
      });
    },
    total_perdida() {
      let url =
        "/api/total_perdida/" +
        this.user_sucursal +
        "/" +
        this.fecha.desde +
        "/" +
        this.fecha.hasta;
      axios.get(url).then((res) => {
        this.total_generado = res.data;
        if (this.total_generado.total_salida === null) {
          this.total_double = 0;
        } else {
          this.total_double = this.total_generado.total_salida;
        }
        this.cargando = false;
      });
    },
    refrescar() {
      this.$data.fecha.desde = "1";
      this.$data.fecha.hasta = "1";
      this.listar();
    },
    buscar_fecha() {
        this.listar();
    },
    anular(item) {
      swal({
        text: "¿estás seguro?",
        icon: "error",
        buttons: ["no", "sí"],
        dangerMode: true,
      }).then((willDelete) => {
        if (willDelete) {
          let url =
            "/api/detalle_salidas/" +
            item.cod_sucursal +
            "/" +
            this.user_sucursal;
          axios.get(url).then((res) => {
            Vue.$toast.success("enviado...");
            this.detalles = res.data;
            let i = 0;
            for (i = 0; i < this.detalles.length; i++) {
              const params = {
                barra: this.detalles[i].barra,
                cantidad: this.detalles[i].cantidad,
                sucursal: this.user_sucursal,
                nrof:this.detalles[i].nrof
              };
              axios.post("/bajarstockSalida", params).then((res) => {
                Vue.$toast.info("enviado");
              });
            }
            axios.get("/api/deletesalidas/" + item.cod_sucursal).then((res) => {
              this.listar();
            });
          });
        }
      });
    },
  },
};
</script>
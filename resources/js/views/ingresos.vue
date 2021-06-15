<template>
  <div>
    <div v-if="user_traslado < 1">
      <notpermiso />
    </div>
    <div class="row" v-else>
      <div class="container">
        <div class="card-header">
          <button class="button" @click="refrescar">
            <i class="fas fa-redo-alt"></i> refrescar
          </button>
          <input
            type="search"
            class="input-search"
            placeholder="Buscar número"
            v-model="search"
          />
          <button class="button" @click="buscar_sucursal">
            <i class="fas fa-search"></i> Buscar
          </button>
          <input
            type="date"
            class="input-search"
            v-model="fecha"
            @change="buscar_fecha"
          />
        </div>
        <div v-if="spinner" class="centrar">
          <p>cargando...</p>
        </div>
        <div class="table-scroll" v-else>
          <table class="table">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">nro</th>
                <th scope="col">sucursal</th>
                <th scope="col">fecha</th>
                <th scope="col">recibido</th>
              </tr>
            </thead>
            <tbody v-for="(item, index) in ingresos" :key="index">
              <!-- ************** ESTADO RECIBIDO ************* -->
              <tr v-if="item.recibido > 0">
                <th scope="row">{{ index + 1 }}</th>
                <td>
                  {{ item.cod_sucursal }}
                  <button
                    data-toggle="modal"
                    @click="listar_detalle(item)"
                    data-target="#detalleModalingresado"
                  >
                    <i class="fa fa-eye"></i>
                  </button>
                </td>
                <td>{{ item.sucursal }}</td>
                <td>{{ item.fecha }}</td>
                <td>
                  <p class="text-grande text-success" title="enviado">
                    <i class="fas fa-check-circle"></i>
                  </p>
                </td>
              </tr>
              <!-- ************ ESTADO NO RECIBIDO ************* -->
              <tr v-else>
                <th scope="row">{{ index + 1 }}</th>
                <td>
                  {{ item.cod_sucursal }}
                  <button
                    data-toggle="modal"
                    @click="listar_detalle(item)"
                    data-target="#detalleModal"
                  >
                    <i class="fa fa-eye"></i>
                  </button>
                </td>
                <td>{{ item.sucursal }}</td>
                <td>{{ item.fecha }}</td>
                <td class="text-danger text-grande" title="no enviado">
                  <i class="fas fa-times-circle"></i>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <!-- ******* PAGINACION ********* -->
        <nav>
          <ul class="pagination">
            <li v-if="pagination.current_page > 1" class="page-item">
              <a
                href="#"
                @click.prevent="changePage(pagination.current_page - 1)"
              >
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
              <a
                href="#"
                @click.prevent="changePage(pagination.current_page + 1)"
              >
                <span>
                  <b>Siguiente</b>
                </span>
              </a>
            </li>
          </ul>
        </nav>
        <!-- **************** MODAL DETALLE ****************  -->
        <div
          class="modal fade"
          id="detalleModal"
          tabindex="-1"
          aria-labelledby="exampleModalLabel"
          aria-hidden="true"
        >
          <div class="modal-dialog modal-xl">
            <div class="modal-content border-modal modal-colo-p">
              <div class="modal-header">
                <h5 class="modal-title text-system" id="exampleModalLabel">
                  {{ detalle.nro }} - {{ detalle.para }}
                  <button class="text-white" @click="aceptar_products">
                    <i class="fas fa-clipboard-check"></i>
                  </button>
                </h5>
                <button
                  type="button"
                  class="close"
                  id="close_detalle"
                  data-dismiss="modal"
                  aria-label="Close"
                >
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <div v-if="spinner_detalle" class="centrar">
                  <div class="spinner-grow text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                  </div>
                  <div class="spinner-grow text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                  </div>
                </div>
                <table class="table table-bordered" v-else>
                  <thead class="tbl-text-white">
                    <tr>
                      <th scope="col">#</th>
                      <th scope="col">c.barra</th>
                      <th scope="col">c.prod.</th>
                      <th scope="col">producto</th>
                      <th scope="col">marca</th>
                      <th scope="col">cantidad</th>
                    </tr>
                  </thead>
                  <tbody
                    v-for="(item, index) in detalles"
                    :key="index"
                    class="tbl-text-white"
                  >
                    <tr>
                      <th scope="row">{{ index + 1 }}</th>
                      <td>{{ item.barra }}</td>
                      <td>{{ item.codigo }}</td>
                      <td>{{ item.nompro }}</td>
                      <td>{{ item.marca }}</td>
                      <td>{{ item.cantidad }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        <!-- ************* FIN DETALLE ************** -->
        <!-- ******** MODAL DETALLE INGRESADO ***********  -->
        <!-- Button trigger modal -->
        <div
          class="modal fade"
          id="detalleModalingresado"
          tabindex="-1"
          aria-labelledby="exampleModalLabel"
          aria-hidden="true"
        >
          <div class="modal-dialog modal-xl">
            <div class="modal-content border-modal modal-colo-p">
              <div class="modal-header">
                <h5 class="modal-title text-system" id="exampleModalLabel">
                  {{ detalle.nro }} - {{ detalle.para }} (<i
                    class="fas fa-check text-grande"
                  ></i
                  >)
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
                <div v-if="spinner_detalle" class="centrar">
                  <p class="text-white">cargando...</p>
                </div>
                <table class="table table-bordered" v-else>
                  <thead class="tbl-text-white">
                    <tr>
                      <th scope="col">#</th>
                      <th scope="col">c.barra</th>
                      <th scope="col">c.prod.</th>
                      <th scope="col">producto</th>
                      <th scope="col">marca</th>
                      <th scope="col">cantidad</th>
                    </tr>
                  </thead>
                  <tbody
                    v-for="(item, index) in detalles"
                    :key="index"
                    class="tbl-text-white"
                  >
                    <tr>
                      <th scope="row">{{ index + 1 }}</th>
                      <td>{{ item.barra }}</td>
                      <td>{{ item.codigo }}</td>
                      <td>{{ item.nompro }}</td>
                      <td>{{ item.marca }}</td>
                      <td>{{ item.cantidad }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        <!-- ************* FIN DETALLE ************** -->
      </div>
    </div>
  </div>
</template>
<script>
export default {
  data() {
    return {
      ingresos: [],
      ingreso: {
        id: "",
      },
      spinner: "",
      search: "",
      detalles: [],
      detalle: {
        nro: "",
        para: "",
      },
      spinner_detalle: false,
      search: "",
      fecha: "1",
      i: 0,
    };
  },
  created() {
    this.listar();
  },
  methods: {
    listar(page) {
      this.spinner = true;
      let url = `/api/ingresos_requer/${this.user_sucursal}/${this.fecha}`;
      axios.get(url).then((res) => {
        this.ingresos = res.data.ingresos.data;
        this.pagination = res.data.paginate;
        this.spinner = false;
      });
    },
    refrescar() {
      this.$data.search = "";
      this.$data.fecha = "1";
      this.listar();
    },
    listar_detalle(item) {
      this.ingreso.id = item.id;
      this.detalle.nro = item.cod_sucursal;
      this.detalle.para = item.para;
      this.spinner_detalle = true;
      axios.get(`/api/detallerequer/${item.cod_sucursal}`).then((res) => {
        this.detalles = res.data;
        this.spinner_detalle = false;
      });
    },
    buscar_sucursal() {
      if (this.search.trim() === "") {
        Vue.$toast.error("campo vacío");
      } else {
        this.spinner = true;
        let url = `/api/buscar_numero_ingresos/${this.user_sucursal}/${this.search}/1`;
        axios.get(url).then((res) => {
          this.ingresos = res.data.ingresos.data;
          this.pagination = res.data.paginate;
          this.spinner = false;
        });
      }
    },
    buscar_fecha() {
      this.listar();
    },
    aceptar_products() {
      swal({
        text: "¿estás seguro?",
        icon: "error",
        buttons: ["no", "sí"],
        dangerMode: true,
      }).then((willDelete) => {
        if (willDelete) {
          document.getElementById("close_detalle").click();
          this.spinner = true;
          this.subir_stock();
          this.estado();
        }
      });
    },
    subir_stock() {
      const params = {
        ArrayDate: this.detalles,
      };
      let url = "/stock_ingresos_tras/" + this.user_sucursal;
      axios.post(url, params).then((res) => {
        Vue.$toast.info("ingresado con éxito");
      });
    },
    estado() {
      let url = "/estado_ingresos/" + this.ingreso.id;
      axios.post(url).then((res) => {
        swal({
          text: "se ingresó con exito",
          icon: "success",
          button: "aceptar",
        });
        this.listar();
      });
    },
    changePage(page) {
      this.pagination.current_page = page;
      this.listar(page);
    },
  },
};
</script>
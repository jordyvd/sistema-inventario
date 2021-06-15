<template>
  <div>
    <div v-if="user_traslado < 1">
      <notpermiso />
    </div>
    <div class="row" v-else>
      <div class="carga-total" v-if="carga_guia">
        <div
          class="spinner-border"
          style="width: 3rem; height: 3rem"
          role="status"
        >
          <span class="sr-only">Loading...</span>
        </div>
      </div>
      <div class="container">
        <div class="card-header">
          <input
            type="search"
            placeholder="Buscar número"
            v-model="search"
            class="input-search"
          />
          <button class="button" @click="buscar">
            <i class="fas fa-search"></i> Buscar
          </button>
          <button class="button" @click="refrescar">
            <i class="fas fa-redo-alt"></i>
            refrescar
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
                <th scope="col">destino</th>
                <th scope="col">fecha</th>
                <th scope="col">imprimir</th>
                <th scope="col">enviado</th>
              </tr>
            </thead>
            <tbody v-for="(item, index) in pendientes" :key="index">
              <!-- ************ ESTADO ENVIADO ************** -->
              <tr v-if="item.estado > 0">
                <th scope="row">{{ index + 1 }}</th>
                <td>
                  {{ item.cod_sucursal }}
                  <button
                    data-toggle="modal"
                    data-target="#detallesModalenviado"
                    @click="detalles_pen(item)"
                  >
                    <i class="fas fa-eye"></i>
                  </button>
                </td>
                <td>{{ item.para }}</td>
                <td>{{ item.updated_at.substring(0, 10) }}</td>
                <td>
                  <button class="text-primary" @click="imprimir_guia(item)">
                    <i class="fas fa-print"></i>
                  </button>
                </td>
                <td>
                  <p class="text-success" title="enviado">
                    <i class="fas fa-check-circle"></i>
                  </p>
                </td>
              </tr>
              <!-- ************** ESTADO PENDIENTE ************** -->
              <tr v-else>
                <th scope="row">{{ index + 1 }}</th>
                <td>
                  {{ item.cod_sucursal }}
                  <button
                    data-toggle="modal"
                    data-target="#detallesModal"
                    @click="detalles_pen(item)"
                  >
                    <i class="fas fa-eye"></i>
                  </button>
                </td>
                <td>{{ item.para }}</td>
                <td>{{ item.fecha }}</td>
                <td>
                  <button class="text-primary" @click="imprimir_guia(item)">
                    <i class="fas fa-print"></i>
                  </button>
                </td>
                <td class="text-danger" title="no enviado">
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
        <!-- *************** MODAL DETALLE ESTADO SIN ENVIAR ******************* -->
        <div
          class="modal fade"
          id="detallesModal"
          tabindex="-1"
          aria-labelledby="exampleModalLabel"
          aria-hidden="true"
        >
          <div class="modal-dialog modal-xl">
            <div class="modal-content border-modal modal-colo-p">
              <div class="modal-header">
                <h5 class="modal-title text-system" id="exampleModalLabel">
                  {{ detalle.nro }} - {{ detalle.para }}
                  <button
                    class="text-white"
                    @click="estado"
                    v-if="detalles.length > 0"
                  >
                    <i class="fas fa-clipboard-check"></i>
                  </button>
                  <i
                    class="fas fa-exclamation-triangle"
                    v-else
                    title="no hay productos agregados"
                  ></i>
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
        <!-- **************** FIN MODAL DETALLE ******************* -->
        <!-- *************** MODAL DETALLE ESTADO ENVIADO ******************* -->
        <div
          class="modal fade"
          id="detallesModalenviado"
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
                  id="close_detalle"
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
        <!-- **************** FIN MODAL DETALLE ******************* -->
        <!-- ********* IMPRIMIR FACTURA ********** -->
        <!-- Modal -->
        <div
          class="modal fade"
          id="FacturaModal"
          tabindex="-1"
          aria-labelledby="exampleModalLabel"
          aria-hidden="true"
        >
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
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
                <button @click="print">pe</button>
                <div id="printMe" style="display: none">
                  <div class="container">
                    <div class="row">
                      <div class="col-sm" style="width: 400px; left: -13.6%">
                        <img src="images/GIOMAR.jpg" width="350" height="150" />
                      </div>
                      <div class="col-sm">
                        <b style="font-size: 31px">GIOMAR SPORT</b>
                        <p>
                          Direccion legal: Morales Bermudez 342, Huaral 15201
                        </p>
                        <p>telefono: 955 347 316</p>
                        <p>fecha: {{ detalle.fecha }}</p>
                      </div>
                      <div
                        class="col-sm border border-dark rounded"
                        style="left: 14%"
                      >
                        <br />
                        <p
                          style="
                            display: flex;
                            align-items: center;
                            justify-content: center;
                          "
                        >
                          R.U.C. 10405163131
                        </p>
                        <p
                          style="
                            display: flex;
                            align-items: center;
                            justify-content: center;
                          "
                        >
                          GUÍA DE TRASLADO
                        </p>
                        <p
                          style="
                            display: flex;
                            align-items: center;
                            justify-content: center;
                          "
                        >
                          N° {{ detalle.nro }}
                        </p>
                      </div>
                    </div>
                  </div>
                  <div
                    class="rounded border border-dark"
                    style="
                      margin: 20px 0 !important;
                      height: 1000px;
                      text-transform: uppercase;
                    "
                  >
                    <div
                      class="row"
                      style="padding: 10px 20px; border-bottom: 1px solid black"
                    >
                      <div class="col-6">
                        <p>SUCURSAL DESTINO: {{ detalle.para }}</p>
                      </div>
                      <div class="col-6" style="left: 22% !important">
                        <p>SUCURSAL DE SALIDA: {{ user_sucursal }}</p>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-12" style="padding: 10px 50px !important">
                        <table class="table">
                          <thead>
                            <tr>
                              <th scope="col">#</th>
                              <th scope="col">producto</th>
                              <th scope="col">marca</th>
                              <th scope="col">cantidad</th>
                            </tr>
                          </thead>
                          <tbody v-for="(item, index) in detalles" :key="index">
                            <tr>
                              <th scope="row">{{ index + 1 }}</th>
                              <td>{{ item.nompro }}</td>
                              <td>{{ item.marca }}</td>
                              <td>{{ item.cantidad }}</td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                  <div class="container">
                    <div class="row" style="padding: 40px 0px">
                      <div class="col-sm">
                        <p style="border-top: 1px solid black">recibido</p>
                      </div>
                      <div class="col-sm">
                        <p style="border-top: 1px solid black">enviado</p>
                      </div>
                      <div class="col-sm">
                        <p style="border-top: 1px solid black">
                          nombre del encargado
                        </p>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- OUTPUT -->
              </div>
            </div>
          </div>
        </div>
        <!-- ***** FIN IMPRIMIR ****** -->
      </div>
    </div>
  </div>
</template>
<script>
export default {
  data() {
    return {
      fecha: "1",
      pendientes: [],
      pendiente: {
        id: "",
      },
      spinner: false,
      search: "",
      detalles: [],
      detalle: {
        nro: "",
        para: "",
        fecha: "",
      },
      spinner_detalle: false,
      i: 0,
      carga_guia: false,
      page: "",
    };
  },
  created() {
    this.listar();
  },
  methods: {
    listar(page) {
      this.spinner = true;
      this.page = page;
      axios
        .get(
          `/api/listarpendientes/${this.user_sucursal}/${this.fecha}?page=${page}`
        )
        .then((res) => {
          this.pendientes = res.data.pendientes.data;
          this.pagination = res.data.paginate;
          this.spinner = false;
        });
    },
    refrescar() {
      this.$data.fecha = "";
      this.$data.fecha = "1";
      this.listar();
      this.$data.search = "";
    },
    actualizar() {
      this.listar(this.page);
    },
    buscar() {
      if (this.search.trim() === "") {
        Vue.$toast.error("campo vacío");
      } else {
        this.spinner = true;
        let url = `/api/buscar_pendientes/${this.user_sucursal}/${this.search}/`;
        axios.get(url).then((res) => {
          this.pendientes = res.data.pendientes.data;
          this.pagination = res.data.paginate;
          this.spinner = false;
        });
      }
    },
    buscar_fecha() {
      this.listar();
    },
    estado() {
      swal({
        text: "¿estas seguro?",
        icon: "error",
        buttons: ["no", "sí"],
        dangerMode: true,
      }).then((willDelete) => {
        if (willDelete) {
          document.getElementById("close_detalle").click();
          this.spinner = true;
          this.bajar_stock();
          this.cambiar_estado();
        }
      });
    },
    bajar_stock() {
      const params = {
        ArrayDate: this.detalles,
      };
      let url = "/stock_pendiente/" + this.user_sucursal;
      axios.post(url, params).then((res) => {
        Vue.$toast.info("enviado con éxito");
      });
    },
    cambiar_estado() {
      axios.post("/estado_pendiente/" + this.pendiente.id).then((res) => {
        swal({
          text: "se envió con éxito",
          icon: "success",
          button: "aceptar",
        });
        this.actualizar();
      });
    },
    detalles_pen(item) {
      this.pendiente.id = item.id;
      this.spinner_detalle = true;
      this.detalle.nro = item.cod_sucursal;
      this.detalle.para = item.para;
      let url = `/api/detalles_pen/${item.cod_sucursal}/${this.user_sucursal}/${item.para}`;
      axios.get(url).then((res) => {
        this.detalles = res.data;
        this.spinner_detalle = false;
      });
    },
    print() {
      // Pass the element id here
      this.$htmlToPaper("printMe");
    },
    imprimir_guia(item) {
      this.carga_guia = true;
      this.detalle.fecha = item.fecha;
      this.detalles_pen(item);
      let url = `/api/detalles_pen/${item.cod_sucursal}/${this.user_sucursal}/${item.para}`;
      axios.get(url).then((res) => {
        this.detalles = res.data;
        this.detalle.nro = item.cod_sucursal;
        this.detalle.para = item.para;
        this.carga_guia = false;
        this.print();
      });
    },
    changePage(page) {
      this.pagination.current_page = page;
      this.listar(page);
    },
  },
};
</script>
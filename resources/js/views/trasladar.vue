<template>
  <div>
    <div v-if="user_traslado < 1">
      <notpermiso />
    </div>
    <div class="row" v-else>
      <div class="container">
        <div class="card-header">
          <button
            class="button"
            data-toggle="modal"
            @click="generar_numero"
            data-target="#NumeroModal"
          >
            <i class="fa fa-plus"></i> nuevo
          </button>
          <input
            type="search"
            class="button"
            v-model="search"
            placeholder="Buscar número"
          />
          <button class="button" @click="buscar_sucursal">
            <i class="fas fa-search"></i> Buscar
          </button>
          <button class="button" @click="refrescar">
            <i class="fas fa-redo-alt"></i> refrescar
          </button>
          <input
            type="date"
            id="input-date"
            v-model="fecha"
            class="input-search"
            @change="buscar_fecha"
          />
        </div>
        <div class="text-center text-white" v-if="spinner">cargando...</div>
        <div class="table-scroll" v-else>
          <table class="table">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">nro</th>
                <th scope="col">destino</th>
                <th scope="col">fecha</th>
                <th scope="col">eliminar</th>
              </tr>
            </thead>

            <tbody v-for="(item, index) in requerimientos" :key="index">
              <tr>
                <th scope="row">{{ index + 1 }}</th>
                <td>
                  {{ item.cod_sucursal }}
                  <button
                    class="text-dark"
                    data-toggle="modal"
                    @click="modal_detalle(item)"
                    data-target="#modalrequerimiento"
                  >
                    <i class="fa fa-edit"></i>
                  </button>
                </td>
                <td>{{ item.para }}</td>
                <td>{{ FormatDate(item.fecha) }}</td>
                <td>
                  <button
                    class="text-dark"
                    @click="eliminar_requerimiento(item, index)"
                  >
                    <i class="fas fa-trash-alt"></i>
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
        <!-- MODAL AGREGAR  -->
        <div
          class="modal fade"
          id="modalrequerimiento"
          tabindex="-1"
          aria-labelledby="exampleModalLabel"
          aria-hidden="true"
        >
          <div class="modal-dialog modal-xl">
            <div class="modal-content border-modal modal-colo-p">
              <div class="modal-header">
                <h5
                  class="modal-title text-system"
                  id="exampleModalLabel"
                  style="color: white"
                >
                  {{ requerimiento.nro }} - {{ requerimiento.destino }}
                </h5>
                <button
                  type="button"
                  class="close"
                  data-dismiss="modal"
                  aria-label="Close"
                  id="close_product"
                >
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <!-- DATOS -->
                <div class="card-header">
                  <button
                    class="btn btn-modal-tbl"
                    @click="cerrar_modal"
                    data-toggle="modal"
                    data-target="#staticBackdrop"
                  >
                    <i class="fas fa-plus"></i> agregar
                  </button>
                </div>
                <div v-if="spinner_detalle" class="centrar">
                  <p class="text-white">cargando...</p>
                </div>
                <table class="table table-bordered" v-else>
                  <thead class="tbl-text-white">
                    <tr>
                      <th scope="col">#</th>
                      <th scope="col">producto</th>
                      <th scope="col">marca</th>
                      <th scope="col">cantidad</th>
                      <th scope="col">eliminar</th>
                    </tr>
                  </thead>
                  <tbody
                    v-for="(item, index) in detalles"
                    :key="index"
                    class="tbl-text-white"
                  >
                    <tr>
                      <th scope="row">{{ index + 1 }}</th>
                      <td>{{ item.nompro }}</td>
                      <td>{{ item.marca }}</td>
                      <td>
                        {{ item.cantidad }}
                        <button
                          class="text-white"
                          data-toggle="modal"
                          data-target="#cantidadModal"
                          @click="editar_cantidad_modal(item)"
                        >
                          <i class="fas fa-edit"></i>
                        </button>
                      </td>
                      <td>
                        <button
                          class="text-white"
                          @click="eliminar_pro(item, index)"
                        >
                          <i class="fas fa-trash-alt"></i>
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
                <!---->
              </div>
            </div>
          </div>
        </div>
        <!-- FIN MODAL -->
        <!-- MODAL NUEVO NUMERO  -->
        <div
          class="modal fade"
          id="NumeroModal"
          tabindex="-1"
          aria-labelledby="exampleModalLabel"
          aria-hidden="true"
        >
          <div class="modal-dialog">
            <div class="modal-content border-modal">
              <div class="modal-header">
                <h5 class="modal-title text-system" id="exampleModalLabel">
                  G00{{ user_id_sucursal }}-{{ requerimiento.nro }}
                </h5>
                <button
                  type="button"
                  class="close"
                  data-dismiss="modal"
                  id="close_nuevo"
                  aria-label="Close"
                >
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <form @submit.prevent="agregar_numero">
                  <div class="form-group">
                    <select
                      class="form-control"
                      v-model="requerimiento.destino"
                    >
                      <option
                        v-for="(item, index) in sucursal"
                        :key="index"
                        :value="item.nombre"
                      >
                        {{ item.nombre }}
                      </option>
                    </select>
                  </div>
                  <div class="form-group centrar">
                    <button class="btn btn-system">
                      <i class="fas fa-save"></i> Guardar
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
        <!-- FIN MODAL NUMERO -->
        <!-- MODAL AGREGAR PRODUCTOS -->
        <div
          class="modal fade"
          id="staticBackdrop"
          data-backdrop="static"
          data-keyboard="false"
          tabindex="-1"
          aria-labelledby="staticBackdropLabel"
          aria-hidden="true"
        >
          <div class="modal-dialog">
            <div class="modal-content border-modal">
              <div class="modal-header">
                <h5 class="modal-title text-system" id="staticBackdropLabel">
                  {{ requerimiento.nro }} - {{ requerimiento.destino }}
                </h5>
                <button
                  type="button"
                  class="close"
                  data-dismiss="modal"
                  id="close_prod"
                  aria-label="Close"
                  hidden
                >
                  <span aria-hidden="true">&times;</span>
                </button>
                <button class="text-grande" @click="finalizar">
                  <i class="fas fa-times"></i>
                </button>
              </div>
              <div class="modal-body">
                <div class="centrar">
                  <form @submit.prevent="escanear">
                    <div class="form-group">
                      <small class="form-text text-muted"
                        >codigo de barra</small
                      >
                      <input
                        type="search"
                        class="form-control"
                        v-model="requerimiento.barra"
                      />
                    </div>
                  </form>
                </div>
                <div class="centrar">
                  <form @submit.prevent="agregar_productos">
                    <div class="form-group">
                      <small class="form-text text-muted">producto</small>
                      <input
                        type="text"
                        class="form-control"
                        v-model="requerimiento.producto"
                        disabled
                      />
                    </div>
                    <div class="form-group">
                      <small class="form-text text-muted">marca</small>
                      <input
                        type="text"
                        class="form-control"
                        v-model="requerimiento.marca"
                        disabled
                      />
                    </div>
                    <div class="form-group">
                      <small class="form-text text-muted">stock</small>
                      <input
                        type="text"
                        class="form-control"
                        v-model="requerimiento.stock"
                        disabled
                      />
                    </div>
                    <div class="form-group">
                      <small class="form-text text-muted">cantidad</small>
                      <input
                        type="text"
                        class="form-control solonumero"
                        v-model="requerimiento.cantidad"
                      />
                    </div>
                    <div class="form-group">
                      <button type="submit" v-if="btn" class="btn btn-system">
                        <i class="fas fa-save"></i> Guardar
                      </button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- FIN MODAL PRODUCTOS -->
        <!-- MODAL EDITAR CANTIDAD -->
        <div
          class="modal fade"
          id="cantidadModal"
          tabindex="-1"
          aria-labelledby="exampleModalLabel"
          aria-hidden="true"
        >
          <div class="modal-dialog">
            <div class="modal-content bg-system border-modal modal-opacity">
              <div class="modal-header">
                <h5 class="text-white">modificar cantidad</h5>
                <button
                  type="button"
                  class="close"
                  id="close_cantidad"
                  data-dismiss="modal"
                  aria-label="Close"
                >
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <form @submit.prevent="editar_requer">
                  <div class="form-group">
                    <label class="text-dark">cantidad</label>
                    <input
                      type="text"
                      class="form-control solonumero"
                      v-model="requerimiento.cantidad"
                    />
                  </div>
                  <button type="submit" class="btn btn-system centrar">
                    Guardar
                  </button>
                </form>
              </div>
            </div>
          </div>
        </div>
        <!-- FIN DETALLE MODAL -->
      </div>
    </div>
  </div>
</template>
<script>
import moment from 'moment';
export default {
  data() {
    return {
      nro: [],
      requerimiento: {
        id: "",
        codigo_barra: "",
        nro: "",
        producto: "",
        stock: "",
        marca: "",
        barra: "",
        destino: "",
        cantidad: "",
      },
      sucursal: [],
      requerimientos: [],
      btn: true,
      detalles: [],
      spinner: false,
      spinner_detalle: false,
      fecha: "1",
      search: "",
      page: "",
    };
  },
  created() {
    this.generar_numero();
    axios.get("/api/sucursales_list/" + this.user_sucursal).then((res) => {
      this.sucursal = res.data;
    });
    this.listar();
  },
  methods: {
    listar(page) {
      this.spinner = true;
      this.page = page;
      let url =
        "/api/listarrequerimientos/" +
        this.user_sucursal +
        "/" +
        this.fecha +
        "?page=" +
        page;
      axios.get(url).then((res) => {
        this.requerimientos = res.data.traslados.data;
        this.pagination = res.data.paginate;
        this.spinner = false;
      });
    },
    actualizar() {
      this.listar(this.page);
    },
    generar_numero() {
      this.$data.requerimientos.destino = "";
      let url = "/api/gennro/" + this.user_sucursal;
      axios.get(url).then((res) => {
        if (res.data === "") {
          this.nro = "0";
          this.nro = parseFloat(this.nro) + 1;
          this.nro = zeroPad(this.nro, 9);
          this.requerimiento.nro = this.nro;
        } else {
          this.nro = res.data;
          this.nro = parseFloat(this.nro) + 1;
          this.nro = zeroPad(this.nro, 9);
          this.requerimiento.nro = this.nro;
        }
      });
    },
    agregar_numero() {
      if (this.requerimiento.destino.trim() === "") {
        Vue.$toast.error("seleccionar sucursal");
      } else {
        document.getElementById("close_nuevo").click();
        this.spinner = true;
        const params = {
          nro: this.requerimiento.nro,
          cod_sucursal:
            "G00" + this.user_id_sucursal + "-" + this.requerimiento.nro,
          de: this.user_sucursal,
          para: this.requerimiento.destino,
        };
        axios.post("/agregar_numero", params).then((res) => {
          Vue.$toast.info("se generó correctamente!");
          this.listar();
          this.generar_numero();
        });
      }
    },
    cerrar_modal() {
      document.getElementById("close_product").click();
    },
    modal_product(item) {
      this.requerimiento.nro = item.cod_sucursal;
      this.requerimiento.destino = item.para;
      this.listar_detalle(item.cod_sucursal);
    },
    escanear() {
      let url =
        "/api/escanera_requer/" +
        this.requerimiento.barra +
        "/" +
        this.user_sucursal;
      axios.get(url).then((res) => {
        this.requerimiento.codigo_barra = res.data.barra;
        this.requerimiento.producto = res.data.nompro;
        this.requerimiento.marca = res.data.marca;
        this.requerimiento.stock = res.data.stock_almacen;
      });
    },
    agregar_productos() {
      if (
        this.requerimiento.barra.trim() === "" ||
        this.requerimiento.producto.trim() === "" ||
        this.requerimiento.marca.trim() === "" ||
        this.requerimiento.cantidad < 1
      ) {
        Vue.$toast.error("completar todos los campos");
      } else {
        if (this.detalles.length > 16) {
          Vue.$toast.error("llegó al límite");
        } else {
          this.btn = false;
          const params = {
            nro_tras: this.requerimiento.nro,
            barra_tras: this.requerimiento.codigo_barra,
            cantidad: this.requerimiento.cantidad,
            de: this.user_sucursal,
            para: this.requerimiento.destino,
          };
          axios.post("/agregarRequer", params).then((res) => {
            this.limpiar_form();
            swal({
              text: "agregado con éxito!",
              icon: "success",
              button: "aceptar",
            });
            this.btn = true;
            this.listar_detalle(this.requerimiento.nro);
          });
        }
      }
    },
    modal_detalle(item) {
      this.listar_detalle(item.cod_sucursal);
      this.requerimiento.nro = item.cod_sucursal;
      this.requerimiento.destino = item.para;
    },
    listar_detalle(nro) {
      this.spinner_detalle = true;
      axios.get(`/api/detallerequer/${nro}`).then((res) => {
        this.detalles = res.data;
        this.spinner_detalle = false;
      });
    },
    limpiar_form() {
      this.$data.requerimiento.barra = "";
      this.$data.requerimiento.producto = "";
      this.$data.requerimiento.marca = "";
      this.$data.requerimiento.stock = "";
      this.$data.requerimiento.cantidad = "";
    },
    finalizar() {
      swal({
        text: "¿finalizar?",
        icon: "error",
        buttons: ["no", "sí"],
        dangerMode: true,
      }).then((willDelete) => {
        if (willDelete) {
          document.getElementById("close_prod").click();
        }
      });
    },
    editar_cantidad_modal(item) {
      this.requerimiento.id = item.id;
      this.requerimiento.nro = item.nro_tras;
    },
    editar_requer() {
      if (
        this.requerimiento.cantidad <= 0 ||
        this.requerimiento.cantidad.trim() === ""
      ) {
        Vue.$toast.error("Dato incorrecto!");
      } else {
        Vue.$toast.info("actualizando datos...");
        document.getElementById("close_cantidad").click();
        const params = { cantidad: this.requerimiento.cantidad };
        axios
          .post(`/editrequer/${this.requerimiento.id}`, params)
          .then((res) => {
            swal({
              text: "Guardado!",
              icon: "success",
              button: "aceptar",
            });
            this.listar_detalle(this.requerimiento.nro);
            this.$data.requerimiento.cantidad = "";
          });
      }
    },
    eliminar_pro(item, index) {
      swal({
        text: "¿eliminar?",
        icon: "error",
        buttons: ["no", "si"],
        dangerMode: true,
      }).then((willDelete) => {
        if (willDelete) {
          Vue.$toast.info("actualizando datos...");
          axios.get(`/deleterequer/${item.id}`).then((res) => {
            this.detalles.splice(index, 1);
          });
        }
      });
    },
    eliminar_requerimiento(item, index) {
      swal({
        text: "¿eliminar?",
        icon: "error",
        buttons: ["no", "si"],
        dangerMode: true,
      }).then((willDelete) => {
        if (willDelete) {
          this.spinner = true;
          axios
            .get(`/deleterequerimiento/${item.id}/${item.cod_sucursal}`)
            .then((res) => {
              Vue.$toast.success("Eliminado!");
              this.actualizar();
              this.spinner = false;
            });
        }
      });
    },
    refrescar() {
      this.$data.fecha = "1";
      this.listar();
      this.$data.search = "";
    },
    buscar_sucursal() {
      if (this.search.trim() === "") {
        Vue.$toast.error("campo vacío");
        // } else if (this.search.length < 4) {
        //   Vue.$toast.error("muy poca referencia");
      } else {
        this.spinner = true;
        let url = `/api/buscar_numero_tras/${this.user_sucursal}/${this.search}/0`;
        axios.get(url).then((res) => {
          this.requerimientos = res.data.ingresos.data;
          this.pagination = res.data.paginate;
          this.spinner = false;
        });
      }
    },
    buscar_fecha() {
      this.listar();
    },
    changePage(page) {
      this.pagination.current_page = page;
      this.listar(page);
    },
    FormatDate(fecha){
       return moment(fecha).format('DD-MM-YYYY');
    },
  },
};
</script>
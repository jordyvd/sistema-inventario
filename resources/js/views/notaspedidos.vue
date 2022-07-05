<template>
  <div>
    <div v-if="user_compras < 1">
      <notpermiso />
    </div>
    <div class="container" v-else>
      <div class="card-header">
        <input
          type="text"
          class="input-search"
          v-model="search"
          placeholder="Buscar número"
        />
        <select
          class="input-search"
          style="padding: 14px 2px"
          v-model="sucursal_search"
          @change="buscar_numer"
        >
          <option value>sucursal...</option>
          <option
            v-for="(item, index) in sucursal"
            :key="index"
            :value="item.nombre"
          >
            {{ item.nombre }}
          </option>
          }
        </select>
        <input
          type="date"
          class="input-search"
          @change="buscar_fecha"
          v-model="fecha"
        />
        <button class="button" @click="refrescar">
          <i class="fas fa-redo-alt"></i> refrescar
        </button>
      </div>
      <div class="centrar" v-if="spinner">cargando...</div>
      <div class="table-scroll" v-else>
        <table class="table">
          <thead>
            <tr>
              <th scope="col">nro</th>
              <th scope="col">total</th>
              <th scope="col">proveedor</th>
              <th scope="col">fecha</th>
              <th scope="col">ingresado</th>
              <th scope="col">trasladar</th>
              <th scope="col">eliminar</th>
            </tr>
          </thead>
          <tbody v-for="(item, index) in pedidos" :key="index">
            <tr v-if="item.estado > 0">
              <td>
                {{ item.codigo_nota }}
                <button
                  class="text-dark"
                  @click="detalles_view(item)"
                  data-toggle="modal"
                  data-target="#ingresadoModal"
                >
                  <i class="fas fa-eye"></i>
                </button>
              </td>
              <td>{{ parseFloat(item.total).toFixed(2) }}</td>
              <td>{{ item.empresa }}</td>
              <td>{{ item.fecha }}</td>
              <td class="text-success">
                <i class="fas fa-check-circle"></i>
              </td>
              <td>
                <button @click="abrirModalTraslado(item)">
                  <i class="fas fa-people-carry"></i>
                </button>
              </td>
              <td v-if="permiso_eliminar">
                <button
                  class="text-danger"
                  @click="anular_nota(item)"
                  title="anular"
                >
                  <i class="fas fa-ban"></i>
                </button>
              </td>
              <td v-else>
                <button class="text-danger" @click="notpermise" title="anular">
                  <i class="fas fa-ban"></i>
                </button>
              </td>
            </tr>
            <!-- ******* NO INGRESADO ****** -->
            <tr v-else>
              <td>
                {{ item.codigo_nota }}
                <button
                  class="text-dark"
                  @click="detalles_view(item)"
                  data-toggle="modal"
                  data-target="#exampleModal"
                >
                  <i class="fas fa-eye"></i>
                </button>
              </td>
              <td>{{ parseFloat(item.total).toFixed(2) }}</td>
              <td>{{ item.empresa }}</td>
              <td>{{ item.fecha }}</td>
              <td class="text-danger">
                <i class="far fa-times-circle"></i>
              </td>
              <td>
                <button @click="abrirModalTraslado(item)">
                  <i class="fas fa-people-carry"></i>
                </button>
              </td>
              <td v-if="permiso_eliminar">
                <button class="text-dark" @click="eliminar_nota(item)">
                  <i class="far fa-trash-alt"></i>
                </button>
              </td>
              <td v-else>
                <button class="text-dark" @click="notpermise">
                  <i class="far fa-trash-alt"></i>
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
      <!-- ******* MODAL DETALLES SIN INGRESAR ******* -->
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
                {{ codigo_nota }}
                <button
                  v-if="icon_aceptar"
                  class="text-white"
                  @click="generar_nota_ingreso"
                  title="agregar productos"
                >
                  <i class="fas fa-clipboard-check"></i>
                </button>
              </h5>
              <button
                type="button"
                class="close"
                id="cerrar_modal"
                data-dismiss="modal"
                aria-label="Close"
              >
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="centrar text-white" v-if="spinner_modal">
                cargando...
              </div>
              <table class="table table-bordered" v-else>
                <thead class="tbl-text-white">
                  <tr>
                    <th scope="col">n°</th>
                    <th scope="col">codigo</th>
                    <th scope="col">producto</th>
                    <th scope="col">marca</th>
                    <th scope="col">precio</th>
                    <th scope="col">cantidad</th>
                    <th scope="col">descuento</th>
                    <th scope="col">importe</th>
                  </tr>
                </thead>
                <tbody
                  v-for="(item, index) in detalles"
                  :key="index"
                  class="tbl-text-white"
                >
                  <tr>
                    <td>{{ index + 1 }}</td>
                    <td>{{ item.codigo }}</td>
                    <td>{{ item.nompro }}</td>
                    <td>{{ item.marca }}</td>
                    <td>{{ item.precio_com }}</td>
                    <td>{{ item.cantidad }}</td>
                    <td>{{ item.descuento }}</td>
                    <td>
                      {{
                        parseFloat(
                          parseFloat(item.precio_com) *
                            parseFloat(item.cantidad) -
                            parseFloat(item.descuento)
                        ).toFixed(2)
                      }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <!-- ******* MODAL DETALLES INGRESADO ******* -->
      <div
        class="modal fade"
        id="ingresadoModal"
        tabindex="-1"
        aria-labelledby="exampleModalLabel"
        aria-hidden="true"
      >
        <div class="modal-dialog modal-xl">
          <div class="modal-content border-modal modal-colo-p">
            <div class="modal-header">
              <h5 class="modal-title text-system" id="exampleModalLabel">
                {{ codigo_nota }}
                <i class="fas fa-check"></i>
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
              <div class="centrar text-white" v-if="spinner_modal">
                cargando...
              </div>
              <table class="table table-bordered" v-else>
                <thead class="tbl-text-white">
                  <tr>
                    <th scope="col">n°</th>
                    <th scope="col">producto</th>
                    <th scope="col">marca</th>
                    <th scope="col">precio</th>
                    <th scope="col">cantidad</th>
                    <th scope="col">descuento</th>
                    <th scope="col">importe</th>
                  </tr>
                </thead>
                <tbody
                  v-for="(item, index) in detalles"
                  :key="index"
                  class="tbl-text-white"
                >
                  <tr>
                    <td>{{ index + 1 }}</td>
                    <td>{{ item.nompro }}</td>
                    <td>{{ item.marca }}</td>
                    <td>{{ item.precio_com }}</td>
                    <td>{{ item.cantidad }}</td>
                    <td>{{ item.descuento }}</td>
                    <td>
                      {{
                        parseFloat(
                          parseFloat(item.precio_com) *
                            parseFloat(item.cantidad) -
                            parseFloat(item.descuento)
                        ).toFixed(2)
                      }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
    <b-modal
      v-model="modalTrasladar"
      hide-footer
      :title="item.codigo_nota"
      content-class="border-modal"
    >
      <div class="container">
        <div class="text-center">
          <select class="form-control" v-model="sucursalTraslado">
            <option :value="null" disabled>select sucursal</option>
            <option
              v-for="(item, index) in sucursal"
              :key="index"
              v-show="item.nombre != user_sucursal"
              :value="item.nombre"
            >
              {{ item.nombre }}
            </option>
          </select>
        </div>
        <div class="text-center my-2">
          <button class="btn btn-system" @click="generarTraslado()">
            GENERAR
          </button>
        </div>
      </div>
    </b-modal>
  </div>
</template>
<script>
export default {
  data() {
    return {
      pedidos: [],
      spinner: false,
      fecha: "1",
      detalles: [],
      id_nota: "",
      codigo_nota: "",
      spinner_modal: false,
      icon_aceptar: false,
      i: 0,
      search: "",
      sucursal: [],
      sucursal_search: "",
      sucursal_actual: "",
      permiso_eliminar: true,
      item: {},
      modalTrasladar: false,
      sucursalTraslado: null,
    };
  },
  created() {
    this.listar();
    axios.get("/api/listsucursal").then((res) => {
      this.sucursal = res.data.sucursal.data;
    });
  },
  methods: {
    listar(page) {
      this.spinner = true;
      let url =
        "/api/listar_pedidos/" +
        this.user_sucursal +
        "/" +
        this.fecha +
        "?page=" +
        page;
      axios.get(url).then((res) => {
        this.pedidos = res.data.notas.data;
        this.pagination = res.data.paginate;
        this.spinner = false;
      });
    },
    buscar_numer() {
      if (
        this.search.trim() === "" ||
        this.search <= 0 ||
        this.sucursal_search.trim() === ""
      ) {
        Vue.$toast.error("campo vacio");
      } else {
        Vue.$toast.info("buscando...");
        this.spinner = true;
        let url =
          "/api/buscar_num_pedido/" + this.search + "/" + this.sucursal_search;
        axios.get(url).then((res) => {
          this.pedidos = res.data.notas.data;
          this.pagination = res.data.paginate;
          if (this.sucursal_search === this.user_sucursal) {
            this.permiso_eliminar = true;
          } else {
            this.permiso_eliminar = false;
          }
          this.spinner = false;
        });
      }
    },
    buscar_fecha() {
      this.listar();
    },
    refrescar() {
      this.$data.fecha = "1";
      this.permiso_eliminar = true;
      this.listar();
      this.$data.sucursal_search = "";
      this.$data.search = "";
    },
    detalles_view(item) {
      this.icon_aceptar = false;
      this.spinner_modal = true;
      this.id_nota = item.id;
      this.sucursal_actual = item.sucursal;
      this.codigo_nota = item.codigo_nota;
      let url = "/api/detallesnotas_pedido/" + item.codigo_nota;
      axios.get(url).then((res) => {
        this.detalles = res.data;
        this.spinner_modal = false;
        this.icon_aceptar = true;
      });
    },
    generar_nota_ingreso() {
      swal({
        text: "¿estas seguro?",
        icon: "error",
        buttons: ["no", "si"],
        dangerMode: true,
      }).then((willDelete) => {
        if (willDelete) {
          Vue.$toast.info("actualizando datos...");
          document.getElementById("cerrar_modal").click();
          this.cambiar_estado();
          this.agregarproductos();
        }
      });
    },
    cambiar_estado() {
      axios.post("/cambiarestado_nota/" + this.id_nota).then((res) => {});
    },
    agregarproductos() {
      const params = { ArrayDate: this.detalles };
      axios
        .post("/subirstock_nota/" + this.user_sucursal, params)
        .then((res) => {
          Vue.$toast.info("finalizado");
          this.listar();
        });
    },
    eliminar_nota(item) {
      swal({
        text: "¿estas seguro?",
        icon: "error",
        buttons: ["no", "si"],
        dangerMode: true,
      }).then((willDelete) => {
        if (willDelete) {
          axios.get("/eliminarnota/" + item.codigo_nota).then((res) => {
            this.listar();
            Vue.$toast.info("eliminado");
          });
        }
      });
    },
    abrirModalTraslado(item) {
      this.item = item;
      this.modalTrasladar = true;
    },
    generarTraslado() {
      this.preloader();
      const params = {
        codigo: this.item.codigo_nota,
        sucursal: this.item.sucursal,
        para: this.sucursalTraslado,
      };
      axios.post("/api/compras/generar-traslado", params).then((res) => {
        this.preloader();
        this.modalTrasladar = false;
      });
    },
    bajar_stock_nota(item) {
      let url = "/api/detallesnotas_pedido/" + item.codigo_nota;
      axios.get(url).then((res) => {
        this.detalles = res.data;
        this.spinner_modal = false;
        const paramsB = {
          ArrayDate: this.detalles,
          cod_nota: item.codigo_nota,
        };
        axios
          .post("/bajarstock_nota/" + this.user_sucursal, paramsB)
          .then((res) => {
            Vue.$toast.info("enviado");
          });
      });
    },
    notpermise() {
      swal(
        "Denegado!",
        "solicitar acción al encargado de " + this.sucursal_search + "",
        "info"
      );
    },
    anular_nota(item) {
      swal({
        text: "¿estas seguro?",
        icon: "error",
        buttons: ["no", "si"],
        dangerMode: true,
      }).then((willDelete) => {
        if (willDelete) {
          this.bajar_stock_nota(item);
          axios
            .get("/DarBajanota/" + item.codigo_nota + "/" + item.id)
            .then((res) => {
              this.listar();
            });
        }
      });
    },
    changePage(page) {
      this.pagination.current_page = page;
      this.listar(page);
    },
  },
};
</script>
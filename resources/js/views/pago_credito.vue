<template>
  <div>
    <div v-if="user_ventas < 1">
      <notpermiso />
    </div>
    <div class="row" v-else>
      <div class="carga-total" v-if="carga_factura">
        <div
          class="spinner-border"
          style="width: 3rem; height: 3rem"
          role="status"
        >
          <span class="sr-only">Loading...</span>
        </div>
      </div>
      <div class="container">
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
              <div class="modal-header bg-system">
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
                <div v-if="spinner_details" class="centrar">
                  <p>cargando...</p>
                </div>
                <!-- <span class="sr-only">Loading...</span> -->
                <!-- </div> -->
                <table class="table table-bordered" v-else>
                  <thead class="tbl-text-white">
                    <tr>
                      <th scope="col">#</th>
                      <th scope="col">c.prod.</th>
                      <th scope="col">Producto / descripción</th>
                      <th scope="col">Precio</th>
                      <th scope="col">Cantidad</th>
                      <th scope="col">Descuento</th>
                      <th scope="col">Importe</th>
                    </tr>
                  </thead>
                  <tbody
                    v-for="(item, index) in detalles"
                    :key="index"
                    class="tbl-text-white"
                  >
                    <tr>
                      <th scope="row">{{ index + 1 }}</th>
                      <td>{{ item.codigo }}</td>
                      <td>{{ item.nompro }}</td>
                      <td>{{ parseFloat(item.precio).toFixed(2) }}</td>
                      <td>{{ item.cantidad }}</td>
                      <td>{{ parseFloat(item.descuento).toFixed(2) }}</td>
                      <td>
                        {{
                          (
                            parseFloat(item.precio) *
                              parseFloat(item.cantidad) -
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
        <div class="card-header">
          <input
            type="search"
            placeholder="Buscar número"
            class="input-search"
            v-model="search"
          />
          <button class="button" @click="buscar">
            <i class="fas fa-search"></i> Buscar
          </button>
          <button class="button" @click="refrescar">
            <i class="fas fa-redo-alt"></i> refrescar
          </button>
          <input
            type="date"
            id="input-date"
            @change="mostrar_x_fechas"
            class="input-search"
            v-model="fechadesde"
            min="2020-01-01"
          />
          <input
            type="date"
            @change="mostrar_x_fechas"
            id="input-date"
            class="input-search"
            v-model="fecha"
            min="2020-01-01"
          />
          <select
            class="input-search"
            v-if="sucursal_ventas > 0"
            @change="listar"
            style="padding: 14px 2px"
            v-model="sucursal_seleccionado"
          >
            <option value="" disabled>seleccionar...</option>
            <option
              v-for="(item, index) in sucursal"
              :key="index"
              :value="item.nombre"
            >
              {{ item.nombre }}
            </option>
          </select>
          creditos({{ pagination.total }})
        </div>
        <!-- **** LOADING **** -->
        <div v-if="loading_table" class="centrar">
          <p>cargando...</p>
        </div>
        <div v-else>
          <div class="alert centrar" v-if="ventas.length < 1">
            no hay registros
          </div>
          <div class="table-scroll" v-else>
            <table class="table">
              <thead>
                <tr>
                  <th scope="col">#</th>
                  <th scope="col">nro</th>
                  <th scope="col">cliente</th>
                  <th scope="col">descripcion</th>
                  <th scope="col">acumulado</th>
                  <th scope="col">total a pagar</th>
                  <th scope="col">pagado</th>
                  <th scope="col">documento</th>
                  <th scope="col">fecha</th>
                  <th scope="col">print</th>
                  <th scope="col">pagos</th>
                  <th scope="col">anular</th>
                </tr>
              </thead>
              <tbody v-for="(item, index) in ventas" :key="index">
                <tr>
                  <th scope="row">{{ index + 1 }}</th>
                  <td>
                    {{ item.cod_sucursal }}
                    <button
                      data-toggle="modal"
                      @click="detalle_venta(item)"
                      data-target="#exampleModal"
                    >
                      <i class="fas fa-eye"></i>
                    </button>
                  </td>
                  <td v-if="item.nombre_cliente === null" class="text-danger">
                    --
                  </td>
                  <td v-else>
                    <p
                      data-toggle="tooltip"
                      data-placement="top"
                      :title="item.nombre_cliente"
                    >
                      {{ cortarTexto(item.nombre_cliente, 18) }}
                    </p>
                  </td>
                  <td>
                    <p
                      data-toggle="tooltip"
                      data-placement="top"
                      :title="item.descripcion"
                    >
                      {{ cortarTexto(item.descripcion, 10) }}
                    </p>
                  </td>
                  <td>
                    {{ parseFloat(item.estado_pago).toFixed(2) }}
                    <button
                      class="text-dark"
                      data-toggle="modal"
                      data-target="#modalAgregarPago"
                      @click="openModalAgregarPago(item)"
                      title="sumar monto"
                    >
                      <i class="fas fa-piggy-bank"></i>
                    </button>
                  </td>
                  <td>
                    {{ parseFloat(item.total_v).toFixed(2) }}({{
                      (
                        parseFloat(item.total_v) - parseFloat(item.estado_pago)
                      ).toFixed(2)
                    }})
                  </td>
                  <td v-if="item.estado > 0" class="text-success">
                    <i class="fas fa-check-circle"></i>
                  </td>
                  <td v-else>
                    <button class="text-danger" @click="cambiarestado(item)">
                      <i class="fas fa-times-circle"></i>
                    </button>
                  </td>
                  <td>
                    {{ item.tipo_v }}
                  </td>
                  <td>{{ item.fecha }}</td>
                  <td>
                    <button class="text-daek" @click="imprimir(item)">
                      <img src="images/printer.png" class="text-icon" alt="" />
                    </button>
                  </td>
                  <td>
                    <button
                      @click="getPaymentsCredit(item)"
                      data-toggle="modal"
                      data-target="#modalPagos"
                      v-if="parseFloat(item.estado_pago) > 0"
                    >
                      <img src="images/wallet.png" class="text-icon" alt="" />
                    </button>
                    <img
                      v-else
                      src="images/wallet.png"
                      class="text-icon filter-img"
                      alt=""
                    />
                  </td>
                  <td>
                    <button
                      class="text-danger"
                      @click="confimacionAnular(item, index)"
                    >
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
        </div>
      </div>
    </div>
    <!-- ************* modal pagos ************* -->
    <div
      class="modal fade"
      id="modalPagos"
      tabindex="-1"
      aria-labelledby="exampleModalLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-xl">
        <div class="modal-content border-modal">
          <div class="modal-header">
            <h5 class="modal-title text-system" id="exampleModalLabel">
              {{ nrof }}
            </h5>
            <button
              type="button"
              class="close"
              id="modalPagosClose"
              data-dismiss="modal"
              aria-label="Close"
            >
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <table class="table table-bordered">
              <thead class="tbl-text-white">
                <tr>
                  <th scope="col">#</th>
                  <th scope="col">Monto</th>
                  <th scope="col">Descripcion</th>
                  <th scope="col">Caja</th>
                  <th scope="col">Fecha</th>
                  <th scope="col">Eliminar</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(item, index) in payments" :key="index">
                  <th scope="row">{{ index + 1 }}</th>
                  <td v-if="item.input_update">
                    <form @submit.prevent="editarMontoPago(item)">
                      <input
                        type="search"
                        class="form-control"
                        v-model="item.monto"
                      />
                    </form>
                    <i
                      class="fas fa-times cursor-pointer"
                      @click="item.input_update = false"
                    ></i>
                  </td>
                  <td v-else>
                    {{ item.monto }}
                    <i
                      class="fas fa-edit cursor-pointer"
                      v-if="mostrarEditMonto(item)"
                      @click="item.input_update = true"
                    ></i>
                  </td>
                  <td>{{ item.descripcion }}</td>
                  <td>
                    <p v-if="item.caja == 1">Si</p>
                    <p v-else>No</p>
                  </td>
                  <td>{{ formatFecha(item.created_at) }}</td>
                  <td>
                    <button @click="eliminarPago(item)">
                      <i class="fas fa-trash-alt"></i>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <!-- ************ modal agregar pago ************** -->
    <div
      class="modal fade"
      id="modalAgregarPago"
      tabindex="-1"
      aria-labelledby="exampleModalLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog">
        <div class="modal-content border-modal">
          <div class="modal-header">
            <h5 class="modal-title text-system" id="exampleModalLabel">
              {{ nrof }}
            </h5>
            <button
              type="button"
              class="close"
              id="cerrarPagoCredit"
              data-dismiss="modal"
              aria-label="Close"
            >
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="agregarPagoCredit">
              <div class="form-group">
                <label>Monto</label>
                <input
                  type="text"
                  class="form-control"
                  v-model="pago.monto"
                  required
                />
              </div>
              <div class="form-group">
                <label>Condicion</label>
                <select class="form-control" v-model="pago.condicion" required>
                  <option value="1">Efectico</option>
                  <option value="2">Tranferencia</option>
                </select>
              </div>
              <div class="form-group">
                <label>Descripción</label>
                <input
                  type="text"
                  class="form-control"
                  v-model="pago.descripcion"
                  required
                />
              </div>
              <div class="form-group">
                <label>Caja</label>
                <select class="form-control" v-model="pago.caja" required>
                  <option value="1">Si</option>
                  <option value="0">No</option>
                </select>
              </div>
              <div class="text-center">
                <button type="submit" class="btn btn-system">Guardar</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import moment from "moment";
export default {
  data() {
    return {
      ventas: [],
      search: "",
      detalles: [],
      nrof: "",
      total: "",
      cliente_ruc: "",
      cliente_dni: "",
      sucursal_seleccionado: "",
      nombre_cliente: "",
      spinner: false,
      fecha: "1",
      fechadesde: "1",
      loading_table: false,
      spinner_details: false,
      carga_factura: false,
      da_factura: "",
      cliente_detalle: [],
      sucursal: [],
      cliente: {
        telefono: "",
        direccion: "",
      },
      datos_impresion: {
        estado: "",
        tipo: "",
        vendedor: "",
        sucursal: "",
        fecha: "",
      },
      ruc_dni_cli: "",
      payments: [],
      pago: {
        monto: null,
        descripcion: null,
        caja: 1,
        condicion: null,
      },
      item: {},
      curdateDate: null,
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
      this.loading_table = true;
      if (this.sucursal_seleccionado === "") {
        this.sucursal_seleccionado = this.user_sucursal;
      }
      let url =
        "/api/listcreditos/" +
        this.sucursal_seleccionado +
        "/" +
        this.fecha +
        "/" +
        this.fechadesde +
        "?page=" +
        page;
      axios.get(url).then((res) => {
        this.ventas = res.data.ventas.data;
        this.pagination = res.data.paginate;
        this.loading_table = false;
      });
    },
    mostrar_x_fechas() {
      if (this.fecha === "1") {
        Vue.$toast.info("seleccionar ambas fechas");
      } else if (this.fechadesde === "1") {
        Vue.$toast.info("seleccionar ambas fechas");
      } else {
        this.listar();
      }
    },
    refrescar() {
      this.loading_table = true;
      this.$data.fecha = "1";
      this.$data.fechadesde = "1";
      this.$data.search = "";
      this.listar();
      document.getElementById("input-date").innerHTML = "";
    },
    editarmonto_acumulado(item) {
      swal("modificar monto acumulado:", {
        content: "input",
      }).then((value) => {
        if (value > 0) {
          Vue.$toast.info("actualizando datos...");
          const params = { acumulado: value };
          axios.post("/modificaracumulado/" + item.id, params).then((res) => {
            this.listar();
          });
        } else {
          Vue.$toast.error("monto incorrecto");
        }
      });
    },
    detalle_venta(item) {
      this.clickSpinner();
      this.spinner_details = true;
      this.nrof = item.cod_sucursal;
      this.total = item.total_v;
      let url = `/api/listdetalles/${item.cod_sucursal}/${this.user_sucursal}`;
      axios.get(url).then((res) => {
        this.detalles = res.data;
        this.spinner_details = false;
        this.clickSpinner();
      });
    },
    changePage(page) {
      this.pagination.current_page = page;
      this.listar(page);
    },
    imprimir(item) {
      if (item.doc_sunat != null) {
        this.openDocumento(item.serie);
      } else {
        Full_W_P(item);
      }
    },
    cambiarestado(item) {
      if (parseFloat(item.estado_pago) >= parseFloat(item.total_v)) {
        Vue.$toast.success("actualizando información...");
        axios
          .post("/cambiar_estado_ventas/" + item.id)
          .then((res) => {
            this.listar();
          })
          .catch((error) => {
            swal("ERROR", "comprobar conexión", "info");
          });
      } else {
        swal({
          text: "el monto aún no se ha completado",
          icon: "error",
          button: "aceptar",
          dangerMode: true,
        });
      }
    },
    confimacionAnular(item, index) {
      swal({
        text: "¿estás seguro?",
        icon: "error",
        buttons: ["no", "sí"],
        dangerMode: true,
      }).then((willDelete) => {
        if (willDelete) {
          this.anularVenta(item, index);
        }
      });
    },
    anularVenta(item, index) {
      this.clickSpinner();
      const params = {
        id: item.id,
        nrof: item.cod_sucursal,
        sucursal: this.user_sucursal,
        serie: item.serie,
        d_sunat: item.doc_sunat != null ? 1 : 0,
      };
      axios.post("/anularfactura", params).then((res) => {
        this.ventas.splice(index, 1);
        this.clickSpinner();
      });
    },
    buscar() {
      if (this.search.trim() === "" || this.search <= 0) {
        Vue.$toast.error("campo vacío");
      } else {
        this.loading_table = true;
        let url =
          "/api/search_ventas/" +
          this.search +
          "/" +
          this.user_sucursal +
          "/0?page";
        axios.get(url).then((res) => {
          this.ventas = res.data.ventas.data;
          this.pagination = res.data.paginate;
          this.loading_table = false;
        });
      }
    },
    getPaymentsCredit(item) {
      this.clickSpinner();
      this.item = item;
      this.nrof = item.cod_sucursal;
      const params = { id: item.id };
      axios.post("/api/get-payments-credit", params).then((res) => {
        this.payments = res.data.data;
        this.curdateDate = res.data.curdate_date;
        this.clickSpinner();
      });
    },
    cortarNombre(nombre) {
      if (nombre.length > 18) {
        return nombre.slice(0, 18) + "...";
      } else {
        return nombre;
      }
    },
    formatFecha(fecha) {
      return moment(fecha).format("DD-MM-YYYY h:mm:ss a");
    },
    openModalAgregarPago(item) {
      this.nrof = item.cod_sucursal;
      this.item = item;
    },
    agregarPagoCredit() {
      this.clickSpinner();
      document.getElementById("cerrarPagoCredit").click();
      const params = {
        acumulado:
          parseFloat(this.pago.monto) + parseFloat(this.item.estado_pago),
        monto: this.pago.monto,
        condicion: this.pago.condicion,
        descripcion: this.pago.descripcion,
        caja: this.pago.caja,
        id: this.item.id,
        user_id: this.user_id,
        sucursal: this.user_sucursal,
        nrof: this.nrof,
      };
      axios
        .post("api/agregar-pago-credit", params)
        .then((res) => {
          this.listar();
          Vue.$toast.success("Guardado");
          this.limpiarFormPago();
          this.clickSpinner();
        })
        .catch((error) => {
          swal("ERROR", "comprobar conexión", "info");
          this.limpiarFormPago();
          this.clickSpinner();
        });
    },
    limpiarFormPago() {
      this.pago.monto = null;
      this.pago.descripcion = null;
      this.pago.caja = "1";
    },
    mostrarEditMonto(item) {
      if (
        this.curdateDate == moment(item.created_at).format("YYYY-MM-DD") &&
        item.edit_user == null
      ) {
        return true;
      } else {
        return false;
      }
    },
    editarMontoPago(item) {
      this.clickSpinner();
      document.getElementById("modalPagosClose").click();
      const params = {
        id: item.id,
        monto: item.monto,
        user_id: this.user_id,
        venta_id: this.item.id,
      };
      axios.post("/api/update-monto-pago-credit", params).then((res) => {
        item.input_update = false;
        this.clickSpinner();
        this.item.estado_pago = res.data;
      });
    },
    eliminarPago(item) {
      swal({
        text: "¿estás seguro?",
        icon: "error",
        buttons: ["no", "sí"],
        dangerMode: true,
      }).then((willDelete) => {
        if (willDelete) {
          this.clickSpinner();
          document.getElementById("modalPagosClose").click();
          axios
            .post("/api/eliminar-pago-credito/" + this.user_id, item)
            .then((res) => {
              this.clickSpinner();
              this.item.estado_pago = res.data;
            });
        }
      });
    },
  },
  computed: {
    total_venta() {
      return this.detalles.reduce((total, item) => {
        return total + item.precio * item.cantidad;
      }, 0);
    },
    total_descuento() {
      return this.detalles.reduce((total, item) => {
        return parseFloat(total) + parseFloat(item.descuento);
      }, 0);
    },
    total_general() {
      return this.detalles.reduce((total, item) => {
        return total + item.precio * item.cantidad - item.descuento;
      }, 0);
    },
  },
};
</script>
<style scoped>
.w-input {
  width: 100px !important;
}
.cursor-pointer {
  cursor: pointer;
}
</style>
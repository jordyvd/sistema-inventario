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
          <!-- <router-link
            class="button"
            style="padding: 12.5px 15px"
            :to="{ name: 'nuevaventa' }"
          >Generar venta </router-link
          >&nbsp; -->
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
            class="input-search"
            v-model="fechadesde"
            @change="mostrar_x_fechas"
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
          ventas({{ pagination.total }})
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
                  <th scope="col">total</th>
                  <th scope="col">condición</th>
                  <th scope="col">documento</th>
                  <th scope="col">fecha</th>
                  <th scope="col">print</th>
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
                    <i class="fas fa-user-times"></i>
                  </td>
                  <td v-else>{{ item.nombre_cliente }}</td>
                  <td>{{ parseFloat(item.total_v).toFixed(2) }}</td>
                  <td v-if="item.estado > 0" class="text-success">efectivo</td>
                  <td v-else>
                    {{ item.estado }}
                  </td>
                  <td>
                    {{ item.tipo_v }}
                  </td>
                  <td>
                    {{ item.fecha }}
                  </td>
                  <td>
                    <button class="text-dark" @click="imprimir(item)">
                      <img src="images/printer.png" class="text-icon" alt="" />
                    </button>
                  </td>
                  <td>
                    <button
                      class="text-danger"
                      @click="anular_factura_items(item, index)"
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
  </div>
</template>
<script>
export default {
  data() {
    return {
      ventas: [],
      sucursal: [],
      detalle_id_venta: [],
      search: "",
      detalles: [],
      nrof: "",
      page: "",
      total: "",
      cliente_ruc: "",
      cliente_dni: "",
      ruc_dni_cli: "",
      nombre_cliente: "",
      spinner: false,
      fecha: "1",
      fechadesde: "1",
      loading_table: false,
      spinner_details: false,
      carga_factura: false,
      da_factura: "",
      sucursal_seleccionado: "",
      cliente_detalle: [],
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
      this.page = page;
      if (this.sucursal_seleccionado === "") {
        this.sucursal_seleccionado = this.user_sucursal;
      }
      let url =
        "/api/listventas/" +
        this.sucursal_seleccionado +
        "/" +
        this.fecha +
        "/" +
        this.fechadesde +
        "/1?page=" +
        page;
      axios
        .get(url)
        .then((res) => {
          this.ventas = res.data.ventas.data;
          this.pagination = res.data.paginate;
          this.loading_table = false;
        })
        .catch((error) => {
          location.reload();
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
      this.listar();
      this.$data.search = "";
      document.getElementById("input-date").innerHTML = "";
    },
    detalle_venta(item) {
      this.spinner_details = true;
      this.detalle_id_venta = item;
      this.nrof = item.cod_sucursal;
      this.total = item.total_v;
      let url = `/api/listdetalles/${item.cod_sucursal}/${this.user_sucursal}`;
      axios
        .get(url)
        .then((res) => {
          this.detalles = res.data;
          this.spinner_details = false;
        })
        .catch((error) => {
          swal("ERROR", "comprobar conexión", "info");
        });
    },
    imprimir(item) {
      Full_W_P(item);
    },
    changePage(page) {
      this.pagination.current_page = page;
      this.listar(page);
    },
    anular_factura_items(item, index) {
      swal({
        text: "¿estás seguro?",
        icon: "error",
        buttons: ["no", "sí"],
        dangerMode: true,
      }).then((willDelete) => {
        if (willDelete) {
          let url = `/api/listdetalles/${item.cod_sucursal}/${this.user_sucursal}`;
          axios
            .get(url)
            .then((res) => {
              this.detalles = res.data;
              const paramsA = { ArrayAnular: this.detalles };
              axios.post("/subir_stock_venta", paramsA).then((res) => {
                Vue.$toast.info("producto actualizado");
              });
              this.anular_factura(item, index);
            })
            .catch((error) => {
              swal("ERROR", "comprobar conexión", "info");
            });
        }
      });
    },
    anular_factura(item, index) {
      let url =
        "/anularfactura/" +
        item.id +
        "/" +
        item.cod_sucursal +
        "/" +
        this.user_sucursal;
      axios.get(url).then((res) => {
        this.ventas.splice(index, 1);
        this.listar(this.page);
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
          "/1?page";
        axios.get(url).then((res) => {
          this.ventas = res.data.ventas.data;
          this.pagination = res.data.paginate;
          this.loading_table = false;
        });
      }
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
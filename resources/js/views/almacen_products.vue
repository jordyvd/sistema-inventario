<template>
  <div>
    <div v-if="user_almacen < 1">
      <notpermiso />
    </div>
    <div class="row" v-else>
      <div class="container">
        <div class="row">
          <div class="col-12 centrar" v-if="calculos_total.length < 1">
            <b v-if="user_ganancias > 0" class="text-success inversion-text">
              Calculando...
            </b>
          </div>
          <div class="col-12 centrar" v-else>
            <b v-if="user_ganancias > 0" class="text-success inversion-text">{{
              parseFloat(calculos_total.total_inversion).toFixed(2)
            }}</b>
          </div>
        </div>
        <div class="card-header">
          <input
            type="search"
            placeholder="Buscar..."
            v-model="search"
            class="input-search"
          />
          <select
            class="input-search"
            style="padding: 13px 2px"
            v-model="selecionar_condicion"
          >
            <option value="barra" selected>codigo de barra</option>
            <option value="codigo">codigo de producto</option>
            <option value="nompro">descripción</option>
          </select>
          <button class="button" @click="buscar" title="buscar">
            <i class="fas fa-search"></i> Buscar
          </button>
          <button class="button" @click="refrescar" title="refrescar">
            <i class="fas fa-redo-alt"></i> refrescar
          </button>
          <button class="button" data-toggle="modal" data-target="#ModalExcel">
            <i class="far fa-file-excel"></i> Exportar
          </button>
          <select
            v-if="user_ganancias > 0"
            class="input-search"
            style="padding: 13px 2px"
            v-model="sucursal_elegir"
            @change="sucursal_seleccion"
          >
            <option value="" disabled>selccionar...</option>
            <option
              :value="item.nombre"
              v-for="(item, index) in sucursal"
              :key="index"
            >
              {{ item.nombre }}
            </option>
          </select>

          productos({{ calculos_total.total_stock }})
        </div>
        <div v-if="spinner" class="centrar">cargando...</div>
        <div class="table-scroll" v-else>
          <table class="table" id="table-productos">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">codigo</th>
                <th scope="col">c.barra</th>
                <th scope="col">producto / descripción</th>
                <th scope="col">marca</th>
                <th scope="col">stock</th>
                <th scope="col">p. venta</th>
                <th scope="col">p. x mayor</th>
                <th scope="col">sucursal</th>
                <th scope="col">imagen</th>
                <th scope="col">editar</th>
              </tr>
            </thead>
            <tbody v-for="(item, index) in productos" :key="index">
              <tr>
                <th scope="row">{{ index + 1 }}</th>
                <td v-if="item.stock_almacen < 1">
                  {{ item.codigo
                  }}<span class="badge badge-danger">agotado</span>
                </td>
                <td v-else>
                  {{ item.codigo }}
                </td>
                <td>{{ item.barra }}</td>
                <td>
                  <button
                    class="text-dark"
                    @click="parsar_barra(item)"
                    data-toggle="modal"
                    data-target="#staticBackdrop"
                  >
                    {{ item.nompro }}
                  </button>
                </td>
                <td>{{ item.marca }}</td>
                <td>{{ item.stock_almacen }}</td>
                <td>{{ parseFloat(item.precio_venta).toFixed(2) }}</td>
                <td v-if="item.precio_mayor < 1">0.00</td>
                <td v-else>{{ parseFloat(item.precio_mayor).toFixed(2) }}</td>
                <td>{{ item.sucursal }}</td>
                <td>
                  <figure>
                    <img
                      :src="'images/productos/' + item.url_imagen"
                      alt
                      class="img-fluid"
                      width="40"
                    />
                    <i
                      class="fa fa-eye"
                      style="cursor: pointer"
                      data-toggle="modal"
                      data-target="#Modalimagen"
                      @click="view_img(item)"
                    ></i>
                  </figure>
                </td>
                <td v-if="user_agregar_modif_mante < 1" @click="notpermise">
                  <button class="text-dark">
                    <i class="fa fa-edit"></i>
                  </button>
                </td>
                <td v-else>
                  <button
                    class="text-dark"
                    data-toggle="modal"
                    data-target="#Modaleditar"
                    @click="editmodal(item)"
                  >
                    <i class="fa fa-edit"></i>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <!-- ******** PAGINACION ********** -->
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
      <!-- MODAL FOTO  -->
      <div
        class="modal fade"
        id="Modalimagen"
        tabindex="-1"
        aria-labelledby="exampleModalLabel"
        aria-hidden="true"
      >
        <div class="modal-dialog">
          <div class="modal-content border-modal">
            <div class="modal-header">
              <h5 class="modal-title text-system" id="exampleModalLabel">
                <i class="fa fa-image"></i>
                <b>{{ this.producto.nompro }}</b>
              </h5>
              <button
                type="button"
                class="close"
                data-dismiss="modal"
                id="close-image"
                aria-label="Close"
              >
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <!-- IMAGEN -->
              <img
                :src="'images/productos/' + foto"
                class="img-product"
                width="400"
                alt
              />
              <!-- FIN IMAGEN -->
            </div>
          </div>
        </div>
      </div>
      <!-- FIN MODAL -->
      <!-- MODAL EDITAR -->
      <div
        class="modal fade"
        id="Modaleditar"
        tabindex="-1"
        aria-labelledby="exampleModalLabel"
        aria-hidden="true"
      >
        <div class="modal-dialog">
          <div class="modal-content border-modal">
            <div class="modal-header bg-danger">
              <h5 class="modal-title" id="exampleModalLabel">
                <i class="fa fa-edit"></i>
                <b>{{ this.producto.nompro }}</b>
              </h5>
              <button
                type="button"
                class="close"
                data-dismiss="modal"
                id="close_editar"
                aria-label="Close"
              >
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <!-- FORMULARIO -->
              <form
                enctype="multipart/form-data"
                class="formularios_sistema"
                @submit.prevent="editar"
              >
                <div class="form-group">
                  <small class="form-text text-muted">precio</small>
                  <input
                    type="text"
                    class="form-control solonumero"
                    v-model="producto.precio_venta"
                  />
                </div>
                 <div class="form-group">
                  <small class="form-text text-muted">precio por mayor</small>
                  <input
                    type="text"
                    class="form-control solonumero"
                    v-model="producto.precio_x_mayor"
                  />
                </div>
                <div class="form-group">
                  <small class="form-text text-muted">stock</small>
                  <input
                    type="text"
                    class="form-control solonumero"
                    v-model="producto.stock"
                  />
                </div>
                <button type="submit" class="btn bg-system">
                  <i class="fa fa-save"></i> Guardar
                </button>
              </form>
              <!-- FIN FORMULARIO -->
            </div>
          </div>
        </div>
      </div>
      <!-- FIN MODAL -->
      <!-- MODAL EXCEL -->
      <div
        class="modal fade"
        id="ModalExcel"
        tabindex="-1"
        aria-labelledby="exampleModalLabel"
        aria-hidden="true"
      >
        <div class="modal-dialog">
          <div class="modal-content border-modal">
            <div class="modal-header bg-success">
              <h5 class="modal-title" id="exampleModalLabel">
                <i class="far fa-file-excel"></i> Exportar
              </h5>
              <button
                type="button"
                class="close"
                data-dismiss="modal"
                id="close"
                aria-label="Close"
              >
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <!-- MARCAS -->
              <form>
                <small class="form-text text-muted">seleccionar marca</small>
                <div class="form-group">
                  <select class="form-control" v-model="producto.marca">
                    <option value="" disabled>seleccionar</option>
                    <option
                      :value="item.nommar"
                      v-for="(item, index) in marcas"
                      :key="index"
                      selected
                    >
                      {{ item.nommar }}
                    </option>
                  </select>
                </div>
                <button class="btn bg-system" @click.prevent="exportarExcel">
                  <i class="fas fa-download"></i> Descargar
                </button>
              </form>
              <br />
              <!-- MARCAS -->
            </div>
          </div>
        </div>
      </div>
      <!-- FIN MODAL -->
      <!-- ************** MODAL KARDEX *************** -->
      <div
        class="modal fade"
        id="staticBackdrop"
        tabindex="-1"
        aria-labelledby="staticBackdropLabel"
        aria-hidden="true"
      >
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content border-modal">
            <div class="modal-body">
              <button
                type="button"
                @click="enviar_kardex"
                class="list-group-item list-group-item-action"
                data-dismiss="modal"
                aria-label="Close"
              >
                KARDEX
              </button>
            </div>
          </div>
        </div>
      </div>
      <!-- ***************** FIN KARDEX ***************** -->
    </div>
  </div>
</template>
<script>
import XLSX from "xlsx";
export default {
  data() {
    return {
      selecionar_condicion: "barra",
      sucursal_elegir: "",
      barra_item: "",
      sucursal: [],
      exportar: [],
      productos: [],
      search: "",
      spinner: false,
      foto: "default.png",
      producto: {
        id: "",
        precio_venta: "",
        precio_x_mayor:"",
        nompro: "",
        varBarra: "0",
        barra: "",
        marca: "",
        stock: "",
      },
      page: "",
      marcas: [],
      calculos_total: [],
    };
  },
  created() {
    this.listar();
    this.calculos();
    axios.get("/api/marcas_select").then((res) => {
      this.marcas = res.data;
    });
    axios.get("/api/listsucursal").then((res) => {
      this.sucursal = res.data.sucursal.data;
    });
  },
  methods: {
    calculos() {
      let url = "/api/total_calculos/" + this.sucursal_elegir;
      axios.get(url).then((res) => {
        this.calculos_total = res.data;
      });
    },
    changePage(page) {
      this.pagination.current_page = page;
      this.listar(page);
    },
    sucursal_seleccion() {
      this.listar();
      this.calculos();
    },
    actualizar() {
      this.listar(this.page);
    },
    listar(page) {
      this.spinner = true;
      this.page = page;
      if (this.sucursal_elegir.trim() === "") {
        this.$data.sucursal_elegir = this.user_sucursal;
      }
      let url = "/api/listadoalmacen/" + this.sucursal_elegir + "?page=" + page;
      axios
        .get(url)
        .then((res) => {
          this.productos = res.data.almacen.data;
          this.pagination = res.data.paginate;
          this.spinner = false;
        })
        .catch((error) => {
          swal("ERROR", "comprobar conexión", "info");
        });
    },
    buscar() {
      if (this.search.trim() === "") {
        Vue.$toast.error("campo vacío");
      } else {
        this.spinner = true;
        let cadena = this.search.replace(/\/+/g, "*");
        let url =
          "/api/buscaralmacen/" + this.selecionar_condicion + "/" + cadena;
        axios
          .get(url)
          .then((res) => {
            this.productos = res.data.almacen.data;
            this.pagination = res.data.paginate;
            this.spinner = false;
          })
          .catch((error) => {
            swal("ERROR", "comprobar conexión", "info");
          });
      }
    },
    refrescar() {
      this.$data.sucursal_elegir = "";
      this.$data.search = "";
      this.listar();
      this.calculos();
    },
    view_img(item) {
      this.producto.nompro = item.nompro;
      this.foto = item.url_imagen;
      this.producto.precio_x_mayor = item.precio_mayor;
    },
    editmodal(item) {
      this.producto.id = item.id;
      this.producto.nompro = item.nompro;
      this.producto.precio_venta = item.precio_venta;
      this.producto.precio_x_mayor = item.precio_mayor;
      this.producto.stock = item.stock_almacen;
    },
    editar() {
      if (
        this.producto.stock.trim() === "" ||
        this.producto.precio_venta.trim() === ""
      ) {
        Vue.$toast.error("completar todos los campos");
      } else {
        document.getElementById("close_editar").click();
        const params = {
          stock: this.producto.stock,
          precio: this.producto.precio_venta,
          precio_x_mayor: this.producto.precio_x_mayor,
        };
        axios
          .post("/editarproductalma/" + this.producto.id, params)
          .then((res) => {
            this.actualizar();
           Vue.$toast.success("Guardado");
          })
          .catch((error) => {
            swal("ERROR", "comprobar conexión", "info");
          });
      }
    },
    notpermise() {
      swal({
        text: "no tienes permiso",
        icon: "error",
        button: "aceptar",
        dangerMode: true,
      });
    },
    exportarExcel() {
      if (this.producto.marca.trim() === "") {
        Vue.$toast.error("selccionar marca");
      } else {
        let url =
          "/api/exportar_excel/" +
          this.producto.marca +
          "/" +
          this.user_sucursal;
        axios.get(url).then((res) => {
          this.exportar = res.data;
          let data = XLSX.utils.json_to_sheet(this.exportar);
          const workbook = XLSX.utils.book_new();
          const filename = this.producto.marca;
          XLSX.utils.book_append_sheet(workbook, data, filename);
          XLSX.writeFile(workbook, `${filename}.xlsx`);
        });
      }
    },
    parsar_barra(item) {
      this.barra_item = item.barra;
    },
    enviar_kardex() {
      this.$router.push({
        name: "kardex",
        params: {
          slug: this.barra_item,
        },
      });
    },
  },
};
</script>
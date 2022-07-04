<template>
  <div class="row">
    <div class="container">
      <div class="card-header">
        <button class="button" data-toggle="modal" data-target="#exampleModal">
          <i class="fas fa-piggy-bank"></i> agregar
        </button>
        <input
          type="date"
          @change="listar"
          id="input-date"
          class="input-search"
          v-model="fecha"
          min="2020-01-01"
        />
        <button class="button" @click="refrescar">
          <i class="fas fa-redo-alt"></i> refrescar
        </button>
        <input
          type="search"
          class="input-search"
          v-model="descripsea"
          placeholder="Descripción"
        />
        <button class="button" @click="buscar_descr">
          <i class="fas fa-search"></i> Buscar
        </button>
        <select
          class="input-search"
          v-model="seleccion_sucursal"
          style="padding: 12.5px 0px"
          v-if="sucursal_ventas > 0"
        >
          <option value="seleccionar...">seleccionar...</option>
          <option
            v-for="(item, index) in sucursal"
            :value="item.nombre"
            :key="index"
          >
            {{ item.nombre }}
          </option>
        </select>
        <a
          class="text-primary pointer"
          @click.prevent="masinfo()"
          data-toggle="modal"
          data-target="#modalIngresos"
          >...más</a
        >
      </div>
      <div v-if="spinner" class="centrar">
        <p>cargando...</p>
      </div>
      <div class="table-scroll" v-else>
        <div class="alert centrar" v-if="ingresos_salidas.length < 1">
          no hay movimiento
        </div>
        <table class="table" v-else>
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">descripcion</th>
              <th scope="col">monto</th>
              <th scope="col">condición</th>
              <th scope="col">fecha</th>
              <th scope="col">archivos</th>
              <th scope="col">eliminar</th>
            </tr>
          </thead>
          <tbody v-for="(item, index) in ingresos_salidas" :key="index">
            <tr>
              <th scope="row">{{ index + 1 }}</th>
              <td>{{ item.descripcion }}</td>
              <td>{{ parseFloat(item.monto).toFixed(2) }}</td>
              <td>{{ item.condicion }}</td>
              <td>{{ item.fecha }}</td>
              <td>
                <button
                  class="text-dark"
                  data-toggle="modal"
                  data-target="#modalFile"
                  @click="getFile(item)"
                >
                  <img src="images/folder.png" class="text-icon" alt="" />
                </button>
              </td>
              <td>
                <button class="text-dark" @click="eliminar(item, index)">
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
      <!-- MODAL AGREGAR  -->
      <div
        class="modal fade"
        id="exampleModal"
        tabindex="-1"
        aria-labelledby="exampleModalLabel"
        aria-hidden="true"
      >
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">
                <i class="fas fa-piggy-bank"></i> agregar ingreso / salida
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
              <!-- FORMULARIO -->
              <form @submit.prevent="agregar">
                <div class="form-group">
                  <label>descripcion</label>
                  <input
                    type="text"
                    v-model="datos.descripcion"
                    class="form-control"
                  />
                </div>
                <div class="form-group">
                  <label>monto</label>
                  <input
                    type="text"
                    class="form-control solonumero"
                    v-model="datos.monto"
                  />
                </div>
                <div class="form-group">
                  <label>condición</label>
                  <select class="form-control" v-model="condicion">
                    <option value="" disabled>selecionar...</option>
                    <option value="ingreso">ingreso</option>
                    <option value="salida">salida</option>
                  </select>
                </div>
                <button type="submit" class="btn bg-system" v-if="boton">
                  <i class="fa fa-save"></i> Guardar
                </button>
              </form>
              <!-- FIN FORMULARIO -->
            </div>
          </div>
        </div>
      </div>
      <!-- FIN MODAL -->
    </div>
    <!-- Modal ingresos -->
    <div
      class="modal fade"
      id="modalIngresos"
      tabindex="-1"
      role="dialog"
      aria-labelledby="exampleModalLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog" role="document">
        <div class="modal-content border-modal">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">
              movimientos económicos
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
            <p v-if="cargando" class="text-center">cargando...</p>
            <ul class="list-group" v-else>
              <li class="list-group-item">
                ingresos a caja:
                <span class="badge badge-xl bg-menu text-white"
                  >S/.
                  {{
                    parseFloat(monto_ingresa.total_ingresos).toFixed(2)
                  }}</span
                >
              </li>
              <li class="list-group-item">
                salidas de caja:
                <span class="badge badge-xl bg-menu text-white"
                  >S/.
                  {{ parseFloat(monto_salida.total_salidas).toFixed(2) }}</span
                >
              </li>
              <li class="list-group-item">
                ventas en efectivo:
                <span class="badge badge-xl bg-menu text-white"
                  >S/. {{ parseFloat(ventas_double).toFixed(2) }}</span
                >
              </li>
              <!-- ****************** PAGO BANCARIO ********************* -->
              <li class="list-group-item" v-if="transferencia < 1">
                ventas - pagos bancarios:
                <span class="badge badge-xl bg-menu text-white">S/. 0.00</span>
              </li>
              <li class="list-group-item" v-else>
                ventas - pagos bancarios:
                <span class="badge badge-xl bg-menu text-white"
                  >S/. {{ parseFloat(transferencia).toFixed(2) }}</span
                >
              </li>
              <!-- ****************** PAGO BANCARIO ********************* -->
              <li class="list-group-item">
                total general:
                <span class="badge badge-xl bg-menu text-white"
                  >S/. {{ total_general.toFixed(2) }}</span
                >
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <!-- Modal -->
    <b-modal
      size="xl"
      v-model="modalFile"
      hide-footer
      :title="item.descripcion"
      content-class="border-modal"
    >
      <listado-archivos
        :files="files"
        @guardarImagen="guardarImagen"
        @guardarFolder="guardarFolder"
        @getArhivosFolder="getArhivosFolder"
        @guardarDescripcion="guardarDescripcion"
        @deleteFile="deleteFile"
      ></listado-archivos>
    </b-modal>
  </div>
</template>
<script>
import ListadoArchivos from "../components/ListadoArchivos.vue";
export default {
  components: { ListadoArchivos },
  data() {
    return {
      modalFile: false,
      files: [],
      ingresos_salidas: [],
      item: {},
      boton: true,
      condicion: "",
      datos: {
        descripcion: "",
        monto: "",
      },
      spinner: false,
      fecha: null,
      monto_ingresa: [],
      monto_salida: [],
      calculando: true,
      calculo_caja: [],
      cargando: false,
      ventas_double: 0,
      descripsea: "",
      seleccion_sucursal: "seleccionar...",
      sucursal: [],
      transferencia: 0,
    };
  },
  created() {
    this.seleccion_sucursal = this.user_sucursal;
    this.listar(1);
    axios.get("/api/listsucursal").then((res) => {
      this.sucursal = res.data.sucursal.data;
    });
  },
  methods: {
    agregar() {
      if (
        this.datos.descripcion.trim() === "" ||
        this.datos.monto == "" ||
        this.condicion.trim() === ""
      ) {
        Vue.$toast.error("completar todos los campos");
      } else {
        this.boton = false;
        const params = {
          descripcion: this.datos.descripcion,
          monto: this.datos.monto,
          condicion: this.condicion,
          sucursal: this.seleccion_sucursal,
        };
        axios.post("/ingresos_salidas_create", params).then((res) => {
          this.listar(1);
          this.limpiar_form();
          this.boton = true;
        });
      }
    },
    listar(page) {
      this.spinner = true;
      this.cargando = true;
      const params = { fecha: this.fecha, sucursal: this.seleccion_sucursal };
      let url = "/api/listaringresos_salidas?page=" + page;
      axios.post(url, params).then((res) => {
        this.ingresos_salidas = res.data.datos.data;
        this.pagination = res.data.paginate;
        this.spinner = false;
      });
    },
    getFile(item, folder = null) {
      this.preloader();
      this.modalFile = true;
      this.item = item;
      axios
        .post("/api/archivos/get-archivos", { id: item.id, folder: folder })
        .then((res) => {
          this.preloader();
          this.files = res.data;
        });
    },
    guardarImagen(files, folder) {
      this.preloader();
      let formData = new FormData();
      files.forEach((element) => {
        formData.append("files[]", element);
      });
      formData.append("id", this.item.id);
      formData.append("folder", folder);
      axios.post("/api/archivos/insert-archivos", formData).then((res) => {
        this.preloader();
        Vue.$toast.success("Guardado");
        this.files = res.data;
      });
    },
    guardarFolder(nombre, folder) {
      this.preloader();
      axios
        .post("/api/archivos/insert-folder", {
          id: this.item.id,
          nombre: nombre,
          folder: folder,
        })
        .then(async (res) => {
          this.preloader();
          this.files = res.data;
        });
    },
    guardarDescripcion(id, descripcion) {
      this.preloader();
      const params = { id: id, descripcion: descripcion };
      axios.post("/api/archivos/guardar-descripcion", params).then((res) => {
        this.preloader();
      });
    },
    deleteFile(id) {
      this.preloader();
      const params = { id: id };
      axios.post("/api/archivos/delete-file", params).then((res) => {
        this.preloader();
      });
    },
    getArhivosFolder(folderId) {
      this.getFile(this.item, folderId);
    },
    masinfo() {
      this.getMontoCajaEfectivo();
    },
    eliminar(item, index) {
      swal({
        text: "¿estás seguro?",
        icon: "error",
        buttons: ["no", "sí"],
        dangerMode: true,
      }).then((willDelete) => {
        if (willDelete) {
          axios.get(`/deleteingreso_salida/${item.id}`).then((res) => {
            Vue.$toast.success("eliminado");
            this.listar();
          });
        }
      });
    },
    refrescar() {
      this.$data.fecha = "1";
      this.$data.descripsea = "";
      this.listar();
    },
    limpiar_form() {
      this.$data.datos.descripcion = "";
      this.$data.datos.monto = "";
      this.$data.condicion = "";
    },
    getMontoCajaEfectivo() {
      this.cargando = true;
      const params = { sucursal: this.seleccion_sucursal, fecha: this.fecha };
      axios.post("/api/monto-caja-efectivo", params).then((res) => {
        this.monto_salida.total_salidas = res.data.salida;
        this.monto_ingresa.total_ingresos = res.data.ingreso;
        this.transferencia = res.data.transferencia;
        this.ventas_double = res.data.ventas;
        this.cargando = false;
      });
    },
    buscar_descr() {
      if (this.descripsea.trim() === "") {
        Vue.$toast.error("campo vacío");
      } else {
        this.spinner = true;
        axios
          .get(
            "/api/descripsea/" +
              this.descripsea +
              "/" +
              this.seleccion_sucursal +
              "/" +
              this.fecha
          )
          .then((res) => {
            this.ingresos_salidas = res.data.datos.data;
            this.pagination = res.data.paginate;
            this.spinner = false;
          });
      }
    },
    changePage(page) {
      this.pagination.current_page = page;
      this.listar(page);
    },
  },
  computed: {
    total_efectivo_fecha() {
      return (
        parseFloat(this.monto_salida.total_salidas) -
        parseFloat(this.monto_ingresa.total_ingresos)
      );
    },
    total_general() {
      return (
        parseFloat(this.monto_ingresa.total_ingresos) +
        parseFloat(this.ventas_double) -
        parseFloat(this.monto_salida.total_salidas)
      );
    },
  },
};
</script>
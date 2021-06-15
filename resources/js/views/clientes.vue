<template>
  <div>
    <div v-if="user_clientes < 1">
      <notpermiso />
    </div>
    <div class="row" v-else>
      <div class="container">
        <div class="card-header">
          <button
            class="button"
            @click="limpiar_formulario"
            data-toggle="modal"
            data-target="#exampleModal"
          >
            <i class="fas fa-user-plus"></i> agregar
          </button>
          <input
            type="search"
            placeholder="Buscar..."
            v-model="search"
            class="input-search"
          />
          <button class="button" @click="buscar" title="buscar">
            <i class="fas fa-search"></i> Buscar
          </button>
          <button class="button" title="refrescar" @click="refrescar">
            <i class="fas fa-sync"></i> refrescar
          </button>
          clientes({{ pagination.total }})
        </div>
        <div v-if="spinner" class="centrar">
          <p>cargando...</p>
        </div>
        <div class="table-scroll" v-else>
          <table class="table">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">nombre</th>
                <th scope="col">ruc o dni</th>
                <!-- <th scope="col">telefono</th> -->
                <th scope="col">direccion</th>
                <th scope="col">editar</th>
                <th scope="col">eliminar</th>
              </tr>
            </thead>
            <tbody v-for="(item, index) in clientes" :key="index">
              <tr>
                <th scope="row">{{ index + 1 }}</th>
                <td>{{ item.nombre }}</td>
                <td>{{ item.ruc_dni }}</td>
                <!-- <td>{{ item.telefono }}</td> -->
                <td>{{ item.direccion }}</td>
                <td>
                  <button
                    class="text-dark"
                    @click="modalEditar(item)"
                    data-toggle="modal"
                    data-target="#ModalEditar"
                  >
                    <i class="fa fa-edit"></i>
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
            <div class="modal-content border-modal">
              <div class="modal-header">
                <h5
                  class="modal-title text-system text-system"
                  id="exampleModalLabel"
                >
                  <i class="fa fa-user-plus"></i> agregar usuario
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
                    <label>nombre</label>
                    <input
                      type="text"
                      class="form-control"
                      v-model="cliente.nombre"
                    />
                  </div>
                  <div class="form-group">
                    <label>ruc o dni</label>
                    <input
                      type="text"
                      class="form-control"
                      v-model="cliente.ruc_dni"
                    />
                  </div>
                  <!-- <div class="form-group">
                    <label>telefono</label>
                    <input
                      type="text"
                      class="form-control"
                      v-model="cliente.telefono"
                    />
                  </div> -->
                  <div class="form-group">
                    <label>direccion</label>
                    <input
                      type="text"
                      class="form-control"
                      v-model="cliente.direccion"
                    />
                  </div>
                  <button
                    type="submit"
                    class="btn bg-system"
                    v-if="boton"
                  >
                    <i class="fa fa-save"></i> Guardar
                  </button>
                </form>
                <!-- FIN FORMULARIO -->
              </div>
            </div>
          </div>
        </div>
        <!-- FIN MODAL -->
        <!-- MODAL EDITAR -->
        <div
          class="modal fade"
          id="ModalEditar"
          tabindex="-1"
          aria-labelledby="exampleModalLabel"
          aria-hidden="true"
        >
          <div class="modal-dialog">
            <div class="modal-content border-modal">
              <div class="modal-header">
                <h5
                  class="modal-title"
                  id="exampleModalLabel"
                >
                  <i class="fa fa-edit"></i> Editar usuario
                </h5>
                <button
                  type="button"
                  class="close"
                  id="close"
                  data-dismiss="modal"
                  aria-label="Close"
                >
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <!-- FORMULARIO -->
                <form @submit.prevent="editar">
                  <input type="text" v-model="cliente.id" hidden />
                  <div class="form-group">
                    <label>nombre</label>
                    <input
                      type="text"
                      class="form-control"
                      v-model="cliente.nombre"
                    />
                  </div>
                  <div class="form-group">
                    <label>ruc o dni</label>
                    <input
                      type="text"
                      class="form-control"
                      v-model="cliente.ruc_dni"
                    />
                  </div>
                  <!-- <div class="form-group">
                    <label>telefono</label>
                    <input
                      type="text"
                      class="form-control"
                      v-model="cliente.telefono"
                    />
                  </div> -->
                  <div class="form-group">
                    <label>direccion</label>
                    <input
                      type="text"
                      class="form-control"
                      v-model="cliente.direccion"
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
      </div>
    </div>
  </div>
</template>
<script>
export default {
  data() {
    return {
      cliente: {
        id: "",
        nombre: "",
        ruc_dni: "",
        telefono: "",
        direccion: "",
      },
      clientes: [],
      spinner: false,
      boton: true,
      search: "",
    };
  },
  created() {
    this.listar();
  },
  methods: {
    listar(page) {
      this.spinner = true;
      let url = "/api/listclientes?page=" + page;
      axios.get(url).then((res) => {
        this.clientes = res.data.cliente.data;
        this.pagination = res.data.paginate;
        this.spinner = false;
      });
    },
    buscar() {
      if (this.search.trim() === "") {
        Vue.$toast.error("campo vacío");
      } else {
        this.spinner = true;
        let url = "/api/buscarclientes/" + this.search;
        axios.get(url).then((res) => {
          this.clientes = res.data.cliente.data;
          this.pagination = res.data.paginate;
          this.spinner = false;
        });
      }
    },
    refrescar() {
      this.$data.search = "";
      this.listar();
    },
    agregar() {
      if (
        this.cliente.nombre.trim() === "" ||
        this.cliente.ruc_dni.trim() === "" ||
        this.cliente.direccion.trim() === ""
      ) {
        Vue.$toast.error("complete todos los campos");
      } else {
        this.boton = false;
        const params = {
          nombre: this.cliente.nombre,
          ruc_dni: this.cliente.ruc_dni,
          telefono: this.cliente.telefono,
          direccion: this.cliente.direccion,
        };
        axios.post("/agregarcliente", params).then((res) => {
          this.listar();
          this.boton = true;
          Vue.$toast.success("Guardado");
          this.limpiar_formulario();
        });
      }
    },
    eliminar(item, index) {
      swal({
        text: "¿estás seguro?",
        icon: "error",
        buttons: ["no", "sí"],
        dangerMode: true,
      }).then((willDelete) => {
        if (willDelete) {
          axios.get(`/deletecliente/${item.id}`).then((res) => {
            Vue.$toast.success("eliminado");
            this.clientes.splice(index, 1);
          });
        }
      });
    },
    modalEditar(item) {
      this.cliente.id = item.id;
      this.cliente.nombre = item.nombre;
      this.cliente.ruc_dni = item.ruc_dni;
      // this.cliente.telefono = item.telefono;
      this.cliente.direccion = item.direccion;
    },
    editar() {
      if (
        this.cliente.nombre.trim() === "" ||
        this.cliente.ruc_dni.trim() === "" ||
        this.cliente.direccion.trim() === ""
      ) {
        Vue.$toast.error("complete todos los campos");
      } else {
        const params = {
          nombre: this.cliente.nombre,
          ruc_dni: this.cliente.ruc_dni,
          telefono: this.cliente.telefono,
          direccion: this.cliente.direccion,
        };
        document.getElementById("close").click(); //cerrar modal
        Vue.$toast.success("actualizando datos");
        axios.post(`/editarcliente/${this.cliente.id}`, params).then((res) => {
          this.listar();
        });
      }
    },
    limpiar_formulario() {
      this.$data.cliente.nombre = "";
      this.$data.cliente.ruc_dni = "";
      this.$data.cliente.telefono = "";
      this.$data.cliente.direccion = "";
    },
    changePage(page) {
      this.pagination.current_page = page;
      this.listar(page);
    },
  },
};
</script>
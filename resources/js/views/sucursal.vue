<template>
  <div>
    <div v-if="user_mantenimiento < 1">
      <notpermiso />
    </div>
    <div class="row" v-else>
      <div class="container">
        <div class="card-header">
          <button
            class="button"
            @click="no_permisos"
            v-if="user_agregar_modif_mante < 1"
          >
            <i class="fas fa-plus"></i> agregar
          </button>
          <button
            v-else
            class="button"
            data-toggle="modal"
            @click="limpiar_formulario"
            data-target="#exampleModal"
          >
            <i class="fas fa-plus"></i> agregar
          </button>
          sucursales({{ pagination.total }})
        </div>
        <div class="centrar" v-if="spinner">
          cargando...
        </div>
        <table class="table" v-else>
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">sucursal</th>
              <!-- <th scope="col">editar</th> -->
              <th scope="col">eliminar</th>
            </tr>
          </thead>
          <tbody v-for="(item, index) in sucursales" :key="index">
            <tr>
              <th scope="row">{{ index + 1 }}</th>
              <td>{{ item.nombre }}</td>
              <!-- <td>
              <button
                class="text-dark"
                data-toggle="modal"
                data-target="#editarModal"
                @click="editarmodal(item)"
              >
                <i class="fa fa-edit"></i>
              </button>
            </td> -->
              <td>
                <button
                  class="text-dark"
                  @click="no_permisos"
                  v-if="user_agregar_modif_mante < 1"
                >
                  <i class="fa fa-trash"></i>
                </button>
                <button v-else class="text-dark" @click="eliminar(item, index)">
                  <i class="fa fa-trash"></i>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
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
            <div class="modal-header bg-system">
              <h5
                class="modal-title text-system"
                id="exampleModalLabel"
              >
                <i class="fa fa-plus"></i> agregar sucursal
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
              <form enctype="multipart/form-data" @submit.prevent="crear">
                <div class="form-group">
                  <input
                    type="text"
                    placeholder="sucursal"
                    class="form-control"
                    v-model="sucursal.nombre"
                  />
                </div>
                <button
                  type="submit"
                  v-if="button"
                  class="btn bg-system"
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
      <!-- MODAL EDITAR  -->
      <div
        class="modal fade"
        id="editarModal"
        tabindex="-1"
        aria-labelledby="exampleModalLabel"
        aria-hidden="true"
      >
        <div class="modal-dialog">
          <div class="modal-content border-modal">
            <div class="modal-header bg-danger">
              <h5
                class="modal-title"
                id="exampleModalLabel"
                style="color: white"
              >
                <i class="fa fa-edit"></i> editar sucursal
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
              <form enctype="multipart/form-data" @submit.prevent="editar">
                <div class="form-group">
                  <input
                    type="text"
                    placeholder="sucursal"
                    class="form-control"
                    v-model="sucursal.nombre"
                  />
                </div>
                <button type="submit" v-if="button" class="btn btn-danger">
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
</template>
<script>
export default {
  data() {
    return {
      sucursal: {
        id: "",
        nombre: "",
      },
      sucursales: [],
      button: true,
      spinner: false,
    };
  },
  created() {
    this.spinner = true;
    this.listar();
  },
  methods: {
    listar(page) {
      let url = "/api/listsucursal?page=" + page;
      axios.get(url).then((res) => {
        this.sucursales = res.data.sucursal.data;
        this.pagination = res.data.paginate;
        this.spinner = false;
      });
    },
    changePage(page) {
      this.pagination.current_page = page;
      this.listar(page);
    },
    crear() {
      this.button = false;
      const params = { nombre: this.sucursal.nombre };
      axios.post("/createsucursal", params).then((res) => {
        this.listar();
        Vue.$toast.success("Guardado");
        this.button = true;
        this.limpiar_formulario();
      });
    },
    eliminar(item, index) {
      swal({
        text: "¿estás seguro?",
        icon: "error",
        buttons: ["no", "si"],
        dangerMode: true,
      }).then((willDelete) => {
        if (willDelete) {
          axios.get(`/deletesucursal/${item.id}`).then((res) => {
            Vue.$toast.success("eliminado");
            this.sucursales.splice(index, 1);
          });
        }
      });
    },
    editarmodal(item) {
      this.sucursal.id = item.id;
      this.sucursal.nombre = item.nombre;
    },
    editar() {
      if (this.sucursal.nombre.trim() === "") {
        Vue.$toast.error("campo vacío");
      } else {
        document.getElementById("close").click();
        const params = { nombre: this.sucursal.nombre };
        axios
          .post("/editarsucursal/" + this.sucursal.id, params)
          .then((res) => {
            Vue.$toast.success("Guardado");
            this.listar();
          });
      }
    },
    limpiar_formulario() {
      this.$data.sucursal.nombre = "";
    },
  },
};
</script>
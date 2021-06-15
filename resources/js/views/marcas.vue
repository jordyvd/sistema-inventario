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
            @click="limpiar_formulario"
            data-toggle="modal"
            data-target="#exampleModal"
          >
            <i class="fas fa-plus"></i> agregar
          </button>
           <button
            class="button"
            @click="refrescar"
          >
            <i class="fas fa-redo-alt"></i> refrescar
          </button>
          marcas({{ pagination.total }})
        </div>
        <div v-if="spinner" class="centrar">
          cargando...
        </div>
        <div class="table-scroll" v-else>
          <table class="table">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">marca</th>
                <th scope="col">editar</th>
                <th scope="col">eliminar</th>
              </tr>
            </thead>
            <tbody v-for="(item, index) in marcas" :key="index">
              <tr>
                <th scope="row">{{ index + 1 }}</th>
                <td>{{ item.nommar }}</td>
                <td>
                  <button
                    class="text-dark"
                    @click="no_permisos"
                    v-if="user_agregar_modif_mante < 1"
                  >
                    <i class="fa fa-edit"></i>
                  </button>
                  <button
                    v-else
                    class="text-dark"
                    data-toggle="modal"
                    data-target="#editarModal"
                    @click="editarmodal(item)"
                  >
                    <i class="fa fa-edit"></i>
                  </button>
                </td>
                <td>
                  <button
                    class="text-dark"
                    @click="no_permisos"
                    v-if="user_agregar_modif_mante < 1"
                  >
                    <i class="far fa-trash-alt"></i>
                  </button>
                  <button
                    v-else
                    class="text-dark"
                    @click="eliminar(item, index)"
                  >
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
            <div class="modal-header">
              <h5
                class="modal-title"
                id="exampleModalLabel"
              >
                <i class="fa fa-plus"></i> agregar marca
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
                    placeholder="marca"
                    class="form-control"
                    v-model="marca.nommar"
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
              >
                <i class="fa fa-edit"></i> editar marca
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
                    placeholder="marca"
                    class="form-control"
                    v-model="marca.nommar"
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
    </div>
  </div>
</template>
<script>
export default {
  data() {
    return {
      page:"",
      marca: {
        id: "",
        nommar: "",
      },
      marcas: [],
      button: true,
      spinner: false,
    };
  },
  created() {
    this.listar();
  },
  methods: {
    listar(page) {
      this.spinner = true;
      this.page = page;
      let url = "/api/listmarca?page=" + page;
      axios.get(url).then((res) => {
        this.marcas = res.data.marca.data;
        this.pagination = res.data.paginate;
        this.spinner = false;
        this.limpiar_formulario();
      });
    },
    refrescar(){
      this.listar();
    },
    crear() {
      if (this.marca.nommar.trim() === "") {
        Vue.$toast.error("campo vacío");
      } else {
        this.button = false;
        const params = { nommar: this.marca.nommar };
        axios.post("/createmarca", params).then((res) => {
          this.listar();
          Vue.$toast.success("Guardado");
          this.button = true;
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
          axios.get(`/deletemarca/${item.id}`).then((res) => {
            Vue.$toast.success("Eliminado");
            this.marcas.splice(index, 1);
          });
        }
      });
    },
    editarmodal(item) {
      this.marca.nommar = item.nommar;
      this.marca.id = item.id;
    },
    editar() {
      if (this.marca.nommar.trim() === "") {
        Vue.$toast.error("campo vacío");
      } else {
        document.getElementById("close").click();
        Vue.$toast.info("actualizando...");
        const params = { nommar: this.marca.nommar };
        axios.post("/editarmarca/" + this.marca.id, params).then((res) => {
          this.listar(this.page);
        });
      }
    },
    limpiar_formulario() {
      this.$data.marca.nommar = "";
    },
    changePage(page) {
      this.pagination.current_page = page;
      this.listar(page);
    },
  },
};
</script>
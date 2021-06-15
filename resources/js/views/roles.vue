<template>
  <div>
    <div v-if="user_permisos < 1">
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
            <i class="fas fa-plus"></i> agregar
          </button>
          roles({{ pagination.total }})
        </div>
        <div class="centrar" v-if="spinner">
          cargando...
        </div>
        <table class="table" v-else>
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">rol</th>
              <th scope="col">editar</th>
              <th scope="col">eliminar</th>
            </tr>
          </thead>
          <tbody v-for="(item, index) in roles" :key="index">
            <tr>
              <th scope="row">{{ index + 1 }}</th>
              <td>{{ item.nomrol }}</td>
              <td>
                <button
                  class="text-dark"
                  @click="editarmodal(item)"
                  data-toggle="modal"
                  data-target="#editarModal"
                >
                  <i class="fa fa-edit"></i>
                </button>
              </td>
              <td>
                <button class="text-dark" @click="eliminar(item, index)" hidden>
                  <i class="far fa-trash-alt"></i>(deshabilitado)
                </button>
                <button class="text-dark" disabled>
                  <i class="far fa-trash-alt"></i>(deshabilitado)
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
            <div class="modal-header">
              <h5
                class="modal-title text-white"
                id="exampleModalLabel"
                style="color: white"
              >
                <i class="fa fa-plus"></i> agregar
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
                    placeholder="nombre de rol"
                    class="form-control"
                    v-model="rol.nomrol"
                  />
                </div>
                <button
                  type="submit"
                  v-if="button"
                  class="btn bg-system text-white"
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
            <div class="modal-header">
              <h5
                class="modal-title text-white"
                id="exampleModalLabel"
                style="color: white"
              >
                <i class="fa fa-edit"></i> editar
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
                    placeholder="nombre de rol"
                    class="form-control"
                    v-model="rol.nomrol"
                  />
                </div>
                <button
                  type="submit"
                  v-if="button"
                  class="btn bg-system text-white"
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
      rol: {
        id: "",
        nomrol: "",
      },
      roles: [],
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
      let url = "/api/listrol?page=" + page;
      axios.get(url).then((res) => {
        this.roles = res.data.roles.data;
        this.pagination = res.data.paginate;
        this.spinner = false;
      });
    },
    crear() {
      if (this.rol.nomrol.trim() === "") {
        Vue.$toast.error("campo vacío");
      } else {
        this.button = false;
        const params = { nomrol: this.rol.nomrol };
        axios.post("/createrol", params).then((res) => {
          this.listar();
          Vue.$toast.success("Guardado");
          this.button = true;
          this.limpiar_formulario();
        });
      }
    },
    eliminar(item, index) {
      axios.get(`/deleteroles/${item.id}`).then((res) => {
        swal({
          text: "¿estás seguro?",
          icon: "error",
          buttons: ["no", "sí"],
          dangerMode: true,
        }).then((willDelete) => {
          if (willDelete) {
            Vue.$toast.success("Eliminado");
            this.roles.splice(index, 1);
          }
        });
      });
    },
    editarmodal(item) {
      this.rol.id = item.id;
      this.rol.nomrol = item.nomrol;
    },
    editar() {
      if (this.rol.nomrol.trim() === "") {
        Vue.$toast.error("campo vacío");
      } else {
        document.getElementById("close").click();
        Vue.$toast.success("actualizando datos");
        const params = { nomrol: this.rol.nomrol };
        axios.post("/editarol/" + this.rol.id, params).then((res) => {
          this.listar();
        });
      }
    },
    limpiar_formulario() {
      this.$data.rol.nomrol = "";
    },
    changePage(page) {
      this.pagination.current_page = page;
      this.listar(page);
    },
  },
};
</script>
<template>
  <div>
    <div v-if="user_permisos < 1">
      <notpermiso />
    </div>
    <div class="row" v-else>
      <div class="container">
        <div class="card-header">
          <!-- Button trigger modal -->
          <button
            type="button"
            class="button"
            @click="limpiar_form"
            data-toggle="modal"
            data-target="#staticBackdrop"
          >
            <i class="fa fa-user-plus"></i> agregar
          </button>
          <button type="button" class="button" @click="listar">
            <i class="fas fa-redo-alt"></i> refrescar
          </button>
          usuarios({{ pagination.total }})
        </div>
        <div class="centrar" v-if="spinner">cargando...</div>
        <div class="table-scroll" v-else>
          <table class="table">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">nombre</th>
                <th scope="col">dni</th>
                <th scope="col">rol</th>
                <th scope="col">sucursal</th>
                <th scope="col">editar</th>
                <th scope="col">eliminar</th>
              </tr>
            </thead>
            <tbody v-for="(item, index) in usuarios" :key="index">
              <tr>
                <th scope="row">{{ index + 1 }}</th>
                <td>{{ item.name }}</td>
                <td>{{ item.dni }}</td>
                <td>{{ item.nomrol }}</td>
                <td>{{ item.sucursal }}</td>
                <td>
                  <button
                    class="text-dark"
                    data-toggle="modal"
                    data-target="#modaleditar"
                    @click="editarmodal(item)"
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
        <!-- Modal -->
        <div
          class="modal fade"
          id="staticBackdrop"
          tabindex="-1"
          aria-labelledby="staticBackdropLabel"
          aria-hidden="true"
        >
          <div class="modal-dialog">
            <div class="modal-content border-modal">
              <div class="modal-header">
                <h5 class="modal-title text-system" id="staticBackdropLabel">
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
              <div class="modal-body centrar">
                <!-- FORM -->
                <form class="formularios_sistema" @submit.prevent="agregar">
                  <div class="form-group">
                    <small id="emailHelp" class="form-text text-muted"
                      >rol de usuario</small
                    >
                    <select
                      class="form-control select-permisos"
                      v-model="usuario.rol"
                    >
                      <option
                        selected
                        v-for="(item, index) in roles"
                        :key="index"
                        :value="item.id"
                      >
                        {{ item.nomrol }}
                      </option>
                    </select>
                  </div>
                  <div class="form-group">
                    <small id="emailHelp" class="form-text text-muted"
                      >sucursal</small
                    >
                    <select
                      class="form-control select-permisos"
                      @change="buscar_id"
                      v-model="usuario.sucursal"
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
                  <div class="form-group">
                    <small class="form-text text-muted"
                      >nombre de usuario</small
                    >
                    <input
                      type="text"
                      v-model="usuario.name"
                      class="form-control"
                    />
                  </div>
                  <div class="form-group">
                    <small class="form-text text-muted">dni personal</small>
                    <input
                      type="text"
                      v-model="usuario.dni"
                      class="form-control"
                    />
                  </div>
                  <div class="form-group">
                    <small class="form-text text-muted">contraseña</small>
                    <input
                      type="password"
                      v-model="usuario.password"
                      class="form-control"
                    />
                  </div>
                  <div class="form-group">
                    <button class="btn bg-system" v-if="boton">
                      <i class="fa fa-save"></i> Guardar
                    </button>
                  </div>
                </form>
                <!-- FIN FORM -->
              </div>
            </div>
          </div>
        </div>
        <!-- FIN MODAL -->
        <!-- Modal editar-->
        <div
          class="modal fade"
          id="modaleditar"
          tabindex="-1"
          aria-labelledby="staticBackdropLabel"
          aria-hidden="true"
        >
          <div class="modal-dialog">
            <div class="modal-content border-modal">
              <div class="modal-header bg-danger">
                <h5 class="modal-title text-system" id="staticBackdropLabel">
                  <i class="fa fa-edit"></i> editar usuario
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
              <div class="modal-body centrar">
                <!-- FORM -->
                <form class="formularios_sistema" @submit.prevent="editar">
                  <div class="form-group">
                    <small id="emailHelp" class="form-text text-muted"
                      >rol de usuario</small
                    >
                    <select
                      class="form-control select-permisos"
                      v-model="usuario.rol"
                    >
                      <option
                        selected
                        v-for="(item, index) in roles"
                        :key="index"
                        :value="item.id"
                      >
                        {{ item.nomrol }}
                      </option>
                    </select>
                  </div>
                  <div class="form-group">
                    <small id="emailHelp" class="form-text text-muted"
                      >sucursal</small
                    >
                    <select
                      class="form-control select-permisos"
                      @change="buscar_id"
                      v-model="usuario.sucursal"
                    >
                      <option
                        selected
                        v-for="(item, index) in sucursal"
                        :key="index"
                        @change="buscar_id"
                        :value="item.nombre"
                      >
                        {{ item.nombre }}
                      </option>
                    </select>
                  </div>
                  <div class="form-group">
                    <small id="emailHelp" class="form-text text-muted"
                      >nombre de usuario</small
                    >
                    <input
                      type="text"
                      v-model="usuario.name"
                      class="form-control"
                    />
                  </div>
                  <div class="form-group">
                    <small id="emailHelp" class="form-text text-muted"
                      >dni personal</small
                    >
                    <input
                      type="text"
                      v-model="usuario.dni"
                      class="form-control"
                    />
                  </div>
                  <div class="form-group">
                    <small id="emailHelp" class="form-text text-muted"
                      >contraseña</small
                    >
                    <input
                      type="password"
                      v-model="usuario.password"
                      class="form-control"
                    />
                  </div>
                  <div class="form-group">
                    <button class="btn bg-system">
                      <i class="fa fa-save"></i> Guardar
                    </button>
                  </div>
                </form>
                <!-- FIN FORM -->
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
      roles: [],
      usuarios: [],
      sucursal: [],
      usuario: {
        id: "",
        rol: "1",
        sucursal: "",
        name: "",
        dni: "",
        password: "",
      },
      boton: true,
      spinner: false,
      id_sucursal: "",
    };
  },
  created() {
    this.listar();
    axios.get("/api/listrol").then((res) => {
      this.roles = res.data.roles.data;
    });
    axios.get("/api/listsucursal").then((res) => {
      this.sucursal = res.data.sucursal.data;
    });
  },
  methods: {
    listar(page) {
      this.spinner = true;
      let url = "/api/listusuarios?page=" + page;
      axios.get(url).then((res) => {
        this.usuarios = res.data.usuarios.data;
        this.pagination = res.data.paginate;
        this.spinner = false;
      });
    },
    agregar() {
      if (
        this.usuario.sucursal.trim() === "" ||
        this.usuario.name.trim() === "" ||
        this.usuario.dni.trim() === "" ||
        this.usuario.password.trim() === ""
      ) {
        Vue.$toast.error("completar todos los campos");
      } else {
        this.boton = false;
        Vue.$toast.success("actualizando datos");
        const params = {
          rol: this.usuario.rol,
          sucursal: this.usuario.sucursal,
          id_sucursal: this.id_sucursal,
          name: this.usuario.name,
          dni: this.usuario.dni,
          password: this.usuario.password,
        };
        axios.post("/newusuario", params).then((res) => {
          this.listar();
          this.boton = true;
          this.limpiar_form();
        });
      }
    },
    eliminar(item, index) {
      swal({
        text: "¿eliminar?",
        icon: "error",
        buttons: ["no", "sí"],
        dangerMode: true,
      }).then((willDelete) => {
        if (willDelete) {
          Vue.$toast.success("actualizando datos");
          axios.get("/deleteuser/" + item.id).then((res) => {
            this.listar();
          });
        }
      });
    },
    editarmodal(item) {
      this.usuario.id = item.id;
      this.usuario.rol = item.rol;
      this.usuario.sucursal = item.sucursal;
      this.buscar_id();
      this.usuario.name = item.name;
      this.usuario.dni = item.dni;
      this.usuario.password = "passvacio123";
    },
    editar() {
      if (
        this.usuario.name.trim() === "" ||
        this.usuario.dni.trim() === "" ||
        this.usuario.password.trim() === "" ||
        this.usuario.sucursal.trim() === ""
      ) {
        Vue.$toast.error("completa todos los campos");
      } else {
        Vue.$toast.success("actualizando datos");
        const params = {
          name: this.usuario.name,
          dni: this.usuario.dni,
          sucursal: this.usuario.sucursal,
          id_sucursal: this.id_sucursal,
          rol: this.usuario.rol,
          password: this.usuario.password,
        };
        document.getElementById("close").click();
        axios.post("/editarusuarios/" + this.usuario.id, params).then((res) => {
          this.listar();
        });
      }
    },
    limpiar_form() {
      this.$data.usuario.name = "";
      this.$data.usuario.sucursal = "";
      this.$data.usuario.dni = "";
      this.$data.usuario.password = "";
    },
    buscar_id() {
      let url = "/api/buscar_id_sucursal/" + this.usuario.sucursal;
      axios.get(url).then((res) => {
        this.id_sucursal = res.data.id;
      });
    },
    changePage(page) {
      this.pagination.current_page = page;
      this.listar(page);
    },
  },
};
</script>
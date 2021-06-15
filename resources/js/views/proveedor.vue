<template>
  <div>
    <div v-if="user_compras  < 1">
      <notpermiso />
    </div>
    <div class="container" v-else>
      <div class="">
        <div class="card-header">
          <button
            class="button"
            data-toggle="modal"
            data-target="#exampleModal"
          >
            agregar nuevo proveedor
          </button>
          &nbsp; proveedores({{ pagination.total }})
        </div>
        <div class="centrar" v-if="spinner">
          cargando...
        </div>
        <div class="table-scroll" v-else>
          <table class="table">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">ruc</th>
                <th scope="col">empresa</th>
                <th scope="col">eliminar</th>
              </tr>
            </thead>
            <tbody v-for="(item, index) in proveedores" :key="index">
              <tr>
                <th scope="row">{{ index + 1 }}</th>
                <td>{{ item.ruc }}</td>
                <td>{{ item.nombre }}</td>
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
      </div>
      <!-- ******* MODAL AGREGAR ******* -->
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
              <h5 class="modal-title text-system">
                <i class="fas fa-building"></i> agregar empresa
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
              <form class="formularios_sistema" @submit.prevent="crearempresa">
                <div class="form-group">
                  <label>ruc de la empresa</label>
                  <input
                    type="text"
                    class="form-control"
                    v-model="proveedor.ruc"
                  />
                </div>
                <div class="form-group">
                  <label>nombre de la empresa</label>
                  <input
                    type="text"
                    v-model="proveedor.empresa"
                    class="form-control"
                  />
                </div>
                <button type="submit" class="btn bg-system">
                  Guardar
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  data() {
    return {
      proveedor: {
        ruc: "",
        empresa: "",
      },
      proveedores: [],
      spinner: false,
    };
  },
  created() {
    this.listar();
  },
  methods: {
    listar(page) {
      this.spinner = true;
      let url = "/api/listarproveedor?page=" + page;
      axios.get(url).then((res) => {
        this.proveedores = res.data.proveedor.data;
        this.pagination = res.data.paginate;
        this.spinner = false;
      });
    },
    crearempresa() {
      if (
        this.proveedor.ruc.trim() === "" ||
        this.proveedor.empresa.trim() === ""
      ) {
        Vue.$toast.error("completar todos los campos");
      } else {
        Vue.$toast.success("datos actualizando...");
        const params = {
          ruc: this.proveedor.ruc,
          nombre: this.proveedor.empresa,
        };
        axios.post("/crearempresa", params).then((res) => {
          this.listar();
          this.limpiar_form();
        });
      }
    },
    eliminar(item, index) {
      swal({
        text: "¿estas seguro?",
        icon: "error",
        buttons: ["no", "sí"],
        dangerMode: true,
      }).then((willDelete) => {
        if (willDelete) {
          Vue.$toast.success("actualizando datos");
          axios.get("/deleteproveedor/" + item.id).then((res) => {
            this.listar();
          });
        }
      });
    },
    limpiar_form() {
      this.$data.proveedor.ruc = "";
      this.$data.proveedor.empresa = "";
    },
    changePage(page) {
      this.pagination.current_page = page;
      this.listar(page);
    },
  },
};
</script>
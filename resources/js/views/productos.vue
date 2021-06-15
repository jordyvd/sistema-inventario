<template>
  <div>
    <div v-if="user_mantenimiento < 1">
      <notpermiso />
    </div>
    <div class="row contenido-sistema" v-else>
      <div class="container">
        <div class="card-header">
          <button
            class="button"
            v-if="user_agregar_modif_mante < 1"
            @click="no_permisos"
          >
            <i class="fas fa-cart-plus"></i> nuevo producto
          </button>
          <button
            v-else
            class="button"
            @click="limpiar_formulario"
            data-toggle="modal"
            data-target="#exampleModal"
          >
            <i class="fas fa-cart-plus"></i> nuevo producto
          </button>
          <input
            type="search"
            placeholder="Buscar..."
            v-model="search"
            class="input-search"
          />
          <button class="button" title="buscar" @click="buscar">
            <i class="fas fa-search"></i> Buscar
          </button>
          <button class="button" title="refrescar" @click="refrescar">
            <i class="fas fa-redo-alt"></i> refrescar
          </button>
          productos({{ pagination.total }})
        </div>
        <!-- <div class="table-system"> -->
        <div class="centrar" v-if="spinner">cargando...</div>
        <div class="table-scroll" v-else>
          <table class="table">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">codigo</th>
                <th scope="col">codigo de barra</th>
                <th scope="col">producto / descripción</th>
                <th scope="col">precio compra</th>
                <th scope="col">marca</th>
                <th scope="col">imagen</th>
                <th scope="col">editar</th>
                <th scope="col">estado</th>
              </tr>
            </thead>
            <tbody v-for="(item, index) in productos" :key="index">
              <tr>
                <th scope="row">{{ index + 1 }}</th>
                <td>{{ item.codigo }}</td>
                <td>{{ item.barra }}</td>
                <td>{{ item.nompro }}</td>
                <td>{{ parseFloat(item.precio).toFixed(2) }}</td>
                <td>{{ item.marca }}</td>
                <td>
                  <figure>
                    <img
                      :src="'images/productos/' + item.url_imagen"
                      class="img-fluid"
                      width="40"
                    />
                    <i
                      class="fa fa-eye"
                      style="cursor: pointer"
                      data-toggle="modal"
                      data-target="#Modalimagen"
                      @click="foto_producto(item)"
                    ></i>
                  </figure>
                </td>
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
                    @click="modalEditar(item)"
                    data-toggle="modal"
                    data-target="#Modaleditar"
                  >
                    <i class="fa fa-edit"></i>
                  </button>
                </td>
                <td>
                  <button v-if="item.baja > 0" class="text-black" disabled>
                    <label class="content-input">
                      <input type="checkbox" @change="quitar_baja(item)" />
                      <i></i>
                    </label>
                  </button>
                  <button v-else class="text-danger">
                    <label class="content-input2">
                      <input type="checkbox" @change="dar_baja(item)" />
                      <i></i>
                    </label>
                  </button>
                </td>
                <!-- <td>
                <button class="text-danger" @click="eliminar(item,index)">
                  <i class="fa fa-trash"></i>
                </button>
            </td>-->
              </tr>
            </tbody>
          </table>
        </div>
        <!-- </div> -->
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
                <h5
                  class="modal-title text-system"
                  id="exampleModalLabel"
                  style="color: white"
                >
                  <i class="fas fa-cart-plus"></i> nuevo producto
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
                <form
                  enctype="multipart/form-data"
                  class="formularios_sistema"
                  @submit.prevent="agregarProduct"
                >
                  <div class="form-group">
                    <small id="codigo_barra" class="form-text text-muted"
                      >escanear codigo de barra</small
                    >
                    <input
                      type="text"
                      class="form-control"
                      v-model="producto.barra"
                    />
                  </div>
                  <div class="form-group">
                    <small id="codigo_barra" class="form-text text-muted"
                      >codigo de producto</small
                    >
                    <input
                      type="text"
                      class="form-control"
                      v-model="producto.codigo"
                    />
                  </div>
                  <div class="form-group">
                    <small class="form-text text-muted">producto</small>
                    <input
                      type="text"
                      class="form-control"
                      v-model="producto.nompro"
                    />
                  </div>
                  <div class="form-group">
                    <small class="form-text text-muted">precio compra</small>
                    <input
                      type="text"
                      class="form-control solonumero"
                      v-model="producto.precio"
                    />
                  </div>
                  <div class="form-group">
                    <small class="form-text text-muted">marca</small>
                    <select class="form-control" v-model="producto.marca">
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
                  <!-- INPUT_FILE -->
                  <input
                    type="file"
                    id="file_input"
                    @change="obtenerimagen"
                    hidden
                  />
                  <!-- INPUT_FILE FIN -->
                  <div class="form-group" style="cursor: pointer">
                    <figure @click="input_imagen">
                      <img width="100" height="80" :src="imagen" />
                    </figure>
                  </div>
                  <button
                    type="submit"
                    v-if="button_view"
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
        <!-- MODAL EDITAR -->
        <div
          class="modal fade"
          id="Modaleditar"
          tabindex="-1"
          aria-labelledby="exampleModalLabel"
          aria-hidden="true"
        >
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                  <i class="fa fa-edit"></i> editar
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
                <!-- FORMULARIO -->
                <form
                  enctype="multipart/form-data"
                  class="formularios_sistema"
                  @submit.prevent="editar(producto)"
                >
                  <input type="text" v-model="producto.id" hidden />
                  <div class="form-group">
                    <small class="form-text text-muted"
                      >codigo de producto</small
                    >
                    <input
                      type="text"
                      class="form-control"
                      v-model="producto.codigo"
                    />
                  </div>
                  <div class="form-group">
                    <small class="form-text text-muted">codigo de barra</small>
                    <input
                      type="text"
                      class="form-control"
                      v-model="producto.barra"
                    />
                  </div>
                  <div class="form-group">
                    <small class="form-text text-muted">producto</small>
                    <input
                      type="text"
                      class="form-control"
                      v-model="producto.nompro"
                    />
                  </div>
                  <div class="form-group">
                    <small class="form-text text-muted">precio</small>
                    <input
                      type="text"
                      class="form-control solonumero"
                      v-model="producto.precio"
                    />
                  </div>
                  <div class="form-group">
                    <small class="form-text text-muted">marca</small>
                    <select class="form-control" v-model="producto.marca">
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
                  <button
                    type="submit"
                    v-if="button_view"
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
              <div class="modal-header bg-danger">
                <h5 class="modal-title" id="exampleModalLabel">
                  <i class="fa fa-image"></i> {{ producto.nompro }}
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
                  width="250"
                  alt
                />
                <br />
                <form
                  @submit.prevent="editarImagen(producto)"
                  enctype="multipart/form-data"
                >
                  <figure>
                    <img
                      width="150"
                      :src="imagen"
                      v-if="seleccionado"
                      class="img-product"
                    />
                  </figure>
                  <input
                    type="file"
                    name="file"
                    id="carpeta"
                    @change="obtenerimagen"
                    hidden
                  />
                  <br />
                  <button
                    class="btn btn-danger img-product"
                    type="submit"
                    v-if="seleccionado"
                  >
                    Guardar
                  </button>
                </form>
                <br />
                <button
                  class="text-dark img-product text-grande"
                  @click="no_permisos"
                  v-if="user_agregar_modif_mante < 1"
                >
                  <i class="fa fa-edit"></i>
                </button>
                <button
                  v-else
                  class="text-dark img-product text-grande"
                  @click="abrirCarpeta"
                >
                  <i class="fa fa-edit"></i>
                </button>
                <!-- FIN IMAGEN -->
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
      button_view: true,
      imagenVer: "",
      producto: {
        page: "",
        id: "",
        codigo: "",
        barra: "",
        nompro: "",
        precio: "",
        stock: "",
        marca: "",
        imagen: "",
      },
      estado_activo: true,
      estado_desactivo: false,
      foto: "default.png",
      productos: [],
      seleccionado: false,
      spinner: false,
      marcas: [],
      search: "",
    };
  },
  created() {
    this.button_view = true;
    this.listar();
    axios.get("/api/marcas_select").then((res) => {
      this.marcas = res.data;
      // document.getElementById("").click;
    });
  },
  methods: {
    obtenerimagen(e) {
      let file = e.target.files[0];
      this.producto.imagen = file;
      this.cargarimagen(file);
    },
    cargarimagen(file) {
      let reader = new FileReader();
      reader.onload = (e) => {
        this.imagenVer = e.target.result;
      };
      reader.readAsDataURL(file);
    },
    listar(page) {
      this.spinner = true;
      this.page = page;
      let url = "/api/productoslista?page=" + page;
      axios.get(url).then((res) => {
        this.productos = res.data.product.data;
        this.pagination = res.data.paginate;
        this.button_view = true;
        this.spinner = false;
      });
    },
    buscar() {
      if (this.search.trim() === "" || this.search <= 0) {
        Vue.$toast.error("campo vacío");
      } else {
        this.spinner = true;
        let cadena = this.search.replace(/\/+/g, "*");
        let url = "/api/productosbuscar/" + cadena;
        axios.get(url).then((res) => {
          this.productos = res.data.product.data;
          this.pagination = res.data.paginate;
          this.spinner = false;
        });
      }
    },
    refrescar() {
      this.$data.search = "";
      this.listar();
    },
    actualizar() {
      this.listar(this.page);
    },
    agregarProduct() {
      this.producto.barra.trim() === "";
      if (
        this.producto.codigo.trim() === "" ||
        this.producto.barra.trim() === "" ||
        this.producto.nompro.trim() === "" ||
        this.producto.precio.trim() === "" ||
        this.producto.marca.trim() === ""
      ) {
        Vue.$toast.error("llenar todos los campos");
      } else {
        this.button_view = false; // ocultar boton
        let formData = new FormData();
        formData.append("codigo", this.producto.codigo);
        formData.append("barra", this.producto.barra);
        formData.append("nompro", this.producto.nompro);
        formData.append("precio", this.producto.precio);
        formData.append("marca", this.producto.marca);
        formData.append("imagen", this.producto.imagen);
        axios.post("/productcreate", formData).then((response) => {
          if (response.data === "agregado") {
            Vue.$toast.success(response.data);
          } else {
            swal("Error", "este producto ya existe", "info");
          }
          this.actualizar();
        });
      }
    },
    dar_baja(item) {
      axios.get("/api/baja/" + item.id + "/1").then((res) => {
        this.refrescar();
      });
    },
    quitar_baja(item) {
      axios.get("/api/baja/" + item.id + "/0").then((res) => {
        this.refrescar();
      });
    },
    prueba() {
      alert("kaka");
    },
    eliminar(item, index) {
      swal({
        text: "¿estás seguro?",
        icon: "error",
        buttons: ["cancelar", "eliminar"],
        dangerMode: true,
      }).then((willDelete) => {
        if (willDelete) {
          axios.get(`/eliminarproduct/${item.id}`).then((res) => {
            Vue.$toast.success("eliminado");
            this.productos.splice(index, 1);
          });
        }
      });
    },
    modalEditar(item) {
      this.producto.id = item.id;
      this.producto.codigo = item.codigo;
      this.producto.barra = item.barra;
      this.producto.nompro = item.nompro;
      this.producto.precio = item.precio;
      this.producto.marca = item.marca;
    },
    editar(producto) {
      if (
        this.producto.codigo.trim() === "" ||
        this.producto.barra.trim() === "" ||
        this.producto.nompro.trim() === "" ||
        this.producto.precio.trim() === "" ||
        this.producto.marca.trim() === ""
      ) {
        Vue.$toast.error("llenar todos los campos");
      } else {
        Vue.$toast.info("actualizando datos...");
        const params = {
          barra: producto.barra,
          nompro: producto.nompro,
          precio: producto.precio,
          marca: producto.marca,
          codigo: producto.codigo,
        };
        document.getElementById("close").click();
        axios.post("/producteditar/" + producto.id, params).then((response) => {
          this.actualizar();
        });
      }
    },
    foto_producto(item) {
      this.seleccionado = false;
      this.foto = item.url_imagen;
      this.producto.id = item.id;
      this.producto.nompro = item.nompro;
    },
    abrirCarpeta() {
      this.seleccionado = true;
      document.getElementById("carpeta").click();
    },
    editarImagen(producto) {
      this.seleccionado = false;
      let formData = new FormData();
      formData.append("imagen", producto.imagen);
      formData.append("id", producto.id);
      axios.post("/editarimagen", formData).then((res) => {
        document.getElementById("close-image").click();
        document.getElementById("close").click();
        this.actualizar();
      });
    },
    input_imagen() {
      document.getElementById("file_input").click();
    },
    limpiar_formulario() {
      this.$data.producto.barra = "";
      this.$data.producto.nompro = "";
      this.$data.producto.codigo = "";
      this.$data.producto.precio = "";
      this.$data.producto.stock = "";
      this.$data.producto.marca = "";
    },
    changePage(page) {
      this.pagination.current_page = page;
      this.listar(page);
    },
  },
  computed: {
    imagen() {
      return this.imagenVer;
    },
  },
};
</script>
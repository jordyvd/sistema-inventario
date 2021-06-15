<template>
  <div class="container">
    <br />
    <div class="card">
      <div class="card-header">
        <b v-if="nrof.length < 1"
          ><i class="fas fa-edit"></i> nueva salida (calculando...)</b
        >
        <b v-else
          ><i class="fas fa-edit"></i> nueva salida (S00{{
            user_id_sucursal
          }}-{{ producto.numfactura }})</b
        >
        &nbsp;
        <input
          type="radio"
          id="barra_r"
          v-model="condicionb"
          name="busqueda"
          value="barra"
        />
        <label for="barra_r">barra</label>
        <input
          type="radio"
          v-model="condicionb"
          id="codigo_r"
          name="busqueda"
          value="codigo"
        />
        <label for="codigo_r">codigo</label>
      </div>
      <div class="card-body">
        <div class="form-row centrar">
          <div class="col-md-3 mb-3">
            <label>codigo de barra</label>
            <form @submit.prevent="escanear">
              <input
                type="search"
                class="form-control"
                v-model="codigo_barra"
              />
            </form>
          </div>
          <div class="col-md-6 mb-3">
            <label>descripción</label>
            <input
              type="search"
              class="form-control"
              v-model="producto.descripcion"
            />
          </div>
        </div>
        <form @submit.prevent="agregar_salida">
          <div class="form-row centrar">
            <div class="col-md-3 mb-3">
              <label> condición</label>
              <select class="custom-select" v-model="producto.condicion">
                <option value disabled>seleccionar...</option>
                <option value="perdida">perdida</option>
                <option value="obsequio">obsequio</option>
                <option value="fallado">fallado</option>
              </select>
            </div>
            <div class="col-md-3 mb-3">
              <label>total perdida</label>
              <input
                type="text"
                class="form-control"
                :value="total_pagar.toFixed(2)"
                disabled
              />
            </div>
            <div class="col-md-3 mb-3">
              <label for="" class="text-white">finalizar</label>
              <button type="submit" class="form-control btn btn-system">
                <i class="fas fa-piggy-bank"></i> guardar
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
    <!-- **** DATOS ***** -->
    <br />
    <div class="card">
      <div class="card-header">
        <b><i class="fas fa-list"></i> lista de productos</b>
      </div>
      <div class="card-body">
        <div class="alert centrar" v-if="agregados.length < 1">
          no hay productos agregados
        </div>
        <div v-else class="table-scroll">
          <div v-if="spinner_table" class="centrar">
            <div class="spinner-grow text-primary" role="status">
              <span class="sr-only">Loading...</span>
            </div>
            <div class="spinner-grow text-primary" role="status">
              <span class="sr-only">Loading...</span>
            </div>
          </div>
          <table class="table" v-else>
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">producto</th>
                <th scope="col">marca</th>
                <th scope="col">precio venta</th>
                <!-- <th scope="col">precio compra</th> -->
                <th scope="col">cantidad</th>
                <th scope="col">descuento</th>
                <th scope="col">importe</th>
                <th scope="col">imagen</th>
              </tr>
            </thead>
            <tbody v-for="(item, index) in agregados" :key="index">
              <tr>
                <th scope="row">{{ index + 1 }}</th>
                <td>
                  {{ item.producto }}
                  <button
                    class="text-dark"
                    @click="eliminaragregado(index)"
                    title="eliminar"
                  >
                    <i class="far fa-trash-alt"></i>
                  </button>
                </td>
                <td>{{ item.marca }}</td>
                <td>{{ parseFloat(item.precio).toFixed(2) }}</td>
                <!-- <td>{{ parseFloat(item.precio_compra).toFixed(2) }}</td> -->
                <td>{{ item.cantidad }}</td>
                <td>{{ parseFloat(item.descuento).toFixed(2) }}</td>
                <td>
                  {{
                    (
                      parseFloat(item.precio) * parseFloat(item.cantidad) -
                      parseFloat(item.descuento)
                    ).toFixed(2)
                  }}
                </td>
                <td>
                  <button
                    data-toggle="modal"
                    data-target="#modalimagen"
                    @click="imagen(item)"
                  >
                    <i class="fas fa-images"></i>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <!-- ************* modal scanner ************** -->
    <!-- Button trigger modal -->
    <button
      type="button"
      id="modalBarra"
      data-toggle="modal"
      data-target="#exampleModal"
    ></button>

    <!-- Modal -->
    <div
      class="modal fade"
      id="exampleModal"
      tabindex="-1"
      aria-labelledby="exampleModalLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog">
        <div class="modal-content border-modal">
          <div class="modal-header bg-primary">
            <h5 class="modal-title text-system" id="exampleModalLabel">
              agregar salida
            </h5>
            <button
              type="button"
              class="close"
              id="cerrar_modal"
              data-dismiss="modal"
              aria-label="Close"
            >
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <!-- ********* FORMULARIO *********** -->
            <div
              class="spinner-border text-primary"
              v-if="spinner_modal"
              role="status"
            >
              <span class="sr-only">Loading...</span>
            </div>
            <form class="formularios_sistema" @submit.prevent="agregar_prod">
              <div class="form-row align-items-center centrar">
                <div class="col-auto">
                  <input type="text" v-model="barra_result" />
                  <small class="form-text text-muted">producto</small>
                  <div class="input-group mb-2">
                    <div class="input-group-prepend">
                      <div class="input-group-text">
                        <i class="fab fa-product-hunt"></i>
                      </div>
                    </div>
                    <input
                      type="text"
                      class="form-control"
                      v-model="producto.nompro"
                      disabled
                    />
                  </div>
                </div>
                <div class="col-auto">
                  <small class="form-text text-muted">precio</small>
                  <div class="input-group mb-2">
                    <div class="input-group-prepend">
                      <div class="input-group-text">
                        <i class="fas fa-credit-card"></i>
                      </div>
                    </div>
                    <input
                      type="text"
                      class="form-control"
                      v-model="producto.precio"
                      disabled
                    />
                  </div>
                </div>
                <div class="col-auto">
                  <small class="form-text text-muted">marca</small>
                  <div class="input-group mb-2">
                    <div class="input-group-prepend">
                      <div class="input-group-text">
                        <i class="fas fa-truck-loading"></i>
                      </div>
                    </div>
                    <input
                      type="text"
                      class="form-control"
                      v-model="producto.marca"
                      disabled
                    />
                  </div>
                </div>
                <div class="col-auto">
                  <small class="form-text text-muted">stock</small>
                  <div class="input-group mb-2">
                    <div class="input-group-prepend">
                      <div class="input-group-text">
                        <i class="fas fa-cubes"></i>
                      </div>
                    </div>
                    <input
                      type="text"
                      class="form-control"
                      v-model="producto.stock"
                      disabled
                    />
                  </div>
                </div>
                <div class="col-auto">
                  <small class="form-text text-muted">cantidad</small>
                  <div class="input-group mb-2">
                    <div class="input-group-prepend">
                      <div class="input-group-text">
                        <i class="fas fa-align-left"></i>
                      </div>
                    </div>
                    <input
                      type="text"
                      class="form-control solonumero"
                      placeholder="Cantidad"
                      v-model="producto.cantidad"
                    />
                  </div>
                </div>
                <div class="col-auto">
                  <small id="codigo_barra" class="form-text text-muted"
                    >descuento</small
                  >
                  <div class="input-group mb-2">
                    <div class="input-group-prepend">
                      <div class="input-group-text">
                        <i class="fas fa-tags"></i>
                      </div>
                    </div>
                    <input
                      type="text"
                      class="form-control solonumero"
                      placeholder="Descuento"
                      v-model="producto.descuento"
                    />
                  </div>
                </div>
              </div>
              <div class="centrar">
                <button class="btn btn-primary" type="submit">agregar</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <!-- ****** MODAL IMAGEN ****** -->
    <div
      class="modal fade"
      id="modalimagen"
      tabindex="-1"
      aria-labelledby="exampleModalLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog">
        <div class="modal-content border-modal">
          <div class="modal-header">
            <h5 class="modal-title text-system">{{ producto.nompro }}</h5>
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
            <img
              class="img-product"
              :src="'images/productos/' + producto.img"
              alt=""
              width="400"
            />
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
      producto: {
        id: "",
        ruc_dni: "",
        nom_cliente: "",
        descripcion: "",
        nompro: "",
        precio: "",
        precio_compra: "",
        marca: "",
        stock: "",
        cantidad: "",
        descuento: "0",
        condicion: "",
        numfactura: "",
        cantidad: "",
        estado: "",
        img: "default.png",
      },
      condicionb: "barra",
      codigo_barra: "",
      spinner_modal: false,
      spinner_table: false,
      nrof: [],
      productos: [],
      agregados: [],
      spinner2: false,
      barra_result: "",
    };
  },
  created() {
    let salidasDB = JSON.parse(localStorage.getItem("salida_product"));
    if (salidasDB === null) {
      this.agregados = [];
    } else {
      this.agregados = salidasDB;
    }
    this.generar_nuevo_numer_f();
  },
  methods: {
    generar_nuevo_numer_f() {
      this.spinner2 = true;
      let url = "/api/nrofsalidas/" + this.user_sucursal;
      axios
        .get(url)
        .then((res) => {
          if (res.data === "") {
            this.nrof = "0";
            this.nrof = parseFloat(this.nrof) + 1;
            this.nrof = zeroPad(this.nrof, 9);
            this.producto.numfactura = this.nrof;
          } else {
            this.nrof = res.data;
            this.nrof = parseFloat(this.nrof) + 1;
            this.nrof = zeroPad(this.nrof, 9);
            this.producto.numfactura = this.nrof;
          }
        })
        .catch((error) => {
          location.reload();
        });
    },
    escanear() {
      this.spinner_modal = true;
      document.getElementById("modalBarra").click();
      axios
        .get(
          "/api/escanearventa/" +
            this.condicionb +
            "/" +
            this.codigo_barra +
            "/" +
            this.user_sucursal
        )
        .then((res) => {
          this.productos = res.data;
          this.barra_result = this.productos.barra;
          this.producto.nompro = this.productos.nompro;
          this.producto.precio = this.productos.precio_venta;
          this.producto.precio_compra = this.productos.precio;
          this.producto.marca = this.productos.marca;
          this.producto.stock = this.productos.stock_almacen;
          this.producto.img = this.productos.url_imagen;
          this.spinner_modal = false;
        })
        .catch((error) => {
          swal("ERROR", "comprobar conexión", "info");
        });
    },
    agregar_prod() {
      if (
        this.producto.nompro.trim() === "" ||
        this.codigo_barra.trim() === "" ||
        this.producto.cantidad.trim() === "" ||
        this.producto.descuento.trim() === ""
      ) {
        Vue.$toast.error("completar todos los campos");
      } else {
        if (this.agregados.length > 11) {
          Vue.$toast.error("llegó al límite");
        } else {
          this.agregados.push({
            barra: this.barra_result,
            producto: this.producto.nompro,
            marca: this.producto.marca,
            precio: this.producto.precio,
            precio_compra: this.producto.precio_compra,
            cantidad: this.producto.cantidad,
            descuento: this.producto.descuento,
            url_imagen: this.producto.img,
            nrof:"S00" + this.user_id_sucursal + "-" + this.producto.numfactura,
            sucursal: this.user_sucursal,
          });
          localStorage.setItem(
            "salida_product",
            JSON.stringify(this.agregados)
          );
          document.getElementById("cerrar_modal").click();
          this.vaciar_form();
        }
      }
    },
    eliminaragregado(index) {
      swal({
        text: "¿eliminar?",
        icon: "error",
        buttons: ["no", "sí"],
        dangerMode: true,
      }).then((willDelete) => {
        if (willDelete) {
          this.agregados.splice(index, 1);
          localStorage.setItem(
            "salida_product",
            JSON.stringify(this.agregados)
          );
          Vue.$toast.info("se eliminó con éxito");
        }
      });
    },
    eliminar_productos(index) {
      this.agregados.splice(index, 1);
      localStorage.setItem("salida_product", JSON.stringify(this.agregados));
      this.spinner_table = false;
    },
    vaciar_form() {
      this.$data.producto.cantidad = ""; // vaciar campo
      this.$data.producto.descuento = "0"; // vaciar campo
      this.$data.producto.nompro = ""; // vaciar campo
      this.$data.producto.marca = ""; // vaciar campo
      this.$data.producto.stock = ""; // vaciar campo
      this.$data.producto.precio = ""; // vaciar campo
      this.$data.codigo_barra = ""; // vaciar campo
    },
    imagen(item) {
      this.producto.img = item.url_imagen;
      this.producto.nompro = item.producto;
    },
    generar_salida() {
      if (
        this.producto.descripcion.trim() === "" ||
        this.producto.condicion.trim() === ""
      ) {
        Vue.$toast.error("completar todos los campos");
      } else {
        const params1 = {
          cod_sucursal:
            "S00" + this.user_id_sucursal + "-" + this.producto.numfactura,
          nrof: this.producto.numfactura,
          condicion: this.producto.condicion,
          total: this.total_pagar,
          descripcion: this.producto.descripcion,
          sucursal: this.user_sucursal,
        };
        axios.post("/agregar_salida", params1).then((res) => {
          const params = {
            arrayDate: this.agregados,
            condicion: this.producto.condicion,
          };
          axios.post("/detalle_product", params).then((res) => {
            Vue.$toast.success("Guardado con éxito");
            this.$data.producto.descripcion = "";
            this.agregados = [];
            localStorage.setItem("salida_product", JSON.stringify(this.agregados));
          });
        });
      }
    },
    agregar_salida() {
      swal({
        text: "¿estas seguro?",
        icon: "error",
        buttons: ["no", "sí"],
        dangerMode: true,
      }).then((willDelete) => {
        if (willDelete) {
          this.generar_salida();
        }
      });
    },
  },
  computed: {
    total_pagar() {
      return this.agregados.reduce((total, item) => {
        return total + item.precio * item.cantidad - item.descuento;
      }, 0);
    },
  },
};
</script>
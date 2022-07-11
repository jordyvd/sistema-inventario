<template>
  <div>
    <div v-if="user_compras < 1">
      <notpermiso />
    </div>
    <div class="container" v-else>
      <div>
        <div class="card">
          <div class="card-header bg-system">
            <i class="fas fa-edit"></i> agregar compra &nbsp;
            <input
              type="radio"
              id="barra_r"
              v-model="condicion"
              name="busqueda"
              value="barra"
            />
            <label for="barra_r">barra</label>
            <input
              type="radio"
              v-model="condicion"
              id="codigo_r"
              name="busqueda"
              value="codigo"
            />
            <label for="codigo_r">codigo</label>
          </div>
          <div class="card-body">
            <div class="form-row">
              <form @submit.prevent="escanear">
                <div class="col">
                  <small>CODIGO DE BARRA:</small>
                  <input
                    type="text"
                    class="form-control"
                    v-model="codigo_barra"
                  />
                </div>
              </form>
              <form @submit.prevent="buscarempresa">
                <div class="col">
                  <small>RUC DEL PROVEEDOR:</small>
                  <input
                    type="text"
                    class="form-control"
                    v-model="proveedor.ruc"
                  />
                </div>
              </form>
              <div class="col">
                <small>NOMBRE DEL PROVEEDOR:</small>
                <input
                  type="text"
                  class="form-control"
                  v-model="proveedor.empresa"
                  disabled
                />
              </div>
            </div>
            <br />
            <div class="form-row">
              <!-- <div class="col">
             <select class="form-control">
                 <option value="">pagado</option>
                 <option value="">credito</option>
             </select>
            </div> -->
              <div class="col">
                <input
                  type="text"
                  class="form-control"
                  :value="total_general.toFixed(2)"
                  disabled
                />
              </div>
              <div class="col">
                <select class="custom-select" v-model="condicionPago">
                  <option value disabled>seleccionar...</option>
                  <option value="1">efectivo</option>
                  <option value="tarjeta">tarjeta</option>
                  <option value="trasnferencia">transferencia</option>
                  <option value="yape">yape</option>
                  <option value="plin">plin</option>
                  <option value="0">credito</option>
                  <option value="deposito">depósito</option>
                  <option value="cancelado">cancelado</option>
                  <option value="cambio">cambio</option>
                </select>
              </div>
              <div class="col">
                <router-link
                  class="btn btn-system form-control"
                  :to="{ name: 'proveedor' }"
                  ><i class="fas fa-user-friends"></i> nuevo
                  proveedor</router-link
                >
              </div>
              <div class="col">
                <button
                  class="btn btn-system form-control"
                  @click.prevent="generar_nota_pedido"
                >
                  <i class="fas fa-save"></i> Guardar
                </button>
              </div>
            </div>
          </div>
        </div>
        <br />
        <div class="card">
          <div class="card-header bg-system">
            <button class="btn text-secondary">
              <b><i class="fas fa-list"></i> lista de productos</b>
            </button>
            <button
              class="btn btn-system float-rigth"
              data-toggle="modal"
              data-target="#modalLlenar"
            >
              <i class="fas fa-fill-drip"></i>
            </button>
          </div>
          <div class="card-body">
            <div class="table-scroll">
              <table class="table">
                <thead>
                  <tr>
                    <th scope="col">#</th>
                    <th scope="col">producto</th>
                    <th scope="col">marca</th>
                    <th scope="col">stock actual</th>
                    <th scope="col">precio costo</th>
                    <th scope="col">descuento</th>
                    <th scope="col">cantidad</th>
                    <th scope="col">importe</th>
                  </tr>
                </thead>
                <tbody v-for="(item, index) in agreados" :key="index">
                  <tr>
                    <th scope="row">{{ index + 1 }}</th>
                    <td>
                      {{ item.nompro }}
                      <button class="text-dark" @click="eliminar(index)">
                        <i class="far fa-trash-alt"></i>
                      </button>
                    </td>
                    <td>{{ item.marca }}</td>
                    <td>{{ item.stock }}</td>
                    <td>{{ parseFloat(item.precio).toFixed(2) }}</td>
                    <td>{{ parseFloat(item.descuento).toFixed(2) }}</td>
                    <td>{{ item.cantidad }}</td>
                    <td>
                      {{
                        (
                          parseFloat(item.precio) * parseFloat(item.cantidad) -
                          parseFloat(item.descuento)
                        ).toFixed(2)
                      }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <!-- ******* MODAL ESCANER ******* -->
      <!-- Button trigger modal -->
      <button
        id="modalBarra"
        data-toggle="modal"
        data-target="#staticBackdrop"
      ></button>
      <div
        class="modal fade"
        id="staticBackdrop"
        data-backdrop="static"
        data-keyboard="false"
        tabindex="-1"
        aria-labelledby="staticBackdropLabel"
        aria-hidden="true"
      >
        <div class="modal-dialog">
          <div class="modal-content border-modal">
            <div class="modal-header">
              <h5 class="modal-title text-system" id="staticBackdropLabel">
                agregar producto
              </h5>
              <button
                type="button"
                class="close"
                data-dismiss="modal"
                aria-label="Close"
              >
                <span aria-hidden="true" id="cerrar_modal">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <form class="formularios_sistema" @submit.prevent="agregados">
                <div
                  class="spinner-border text-primary spinner"
                  v-if="spinner_modal"
                  role="status"
                >
                  <span class="sr-only">Loading...</span>
                </div>
                <div class="form-group">
                  <small>producto</small>
                  <input
                    type="text"
                    class="form-control"
                    v-model="producto.nompro"
                    disabled
                  />
                </div>
                <div class="form-group">
                  <small>marca</small>
                  <input
                    type="text"
                    class="form-control"
                    v-model="producto.marca"
                    disabled
                  />
                </div>
                <div class="form-group">
                  <small>stock</small>
                  <input
                    type="text"
                    class="form-control"
                    v-model="producto.stock"
                    disabled
                  />
                </div>
                <div class="form-group">
                  <small>precio costo</small>
                  <input
                    type="text"
                    class="form-control"
                    v-model="producto.precio"
                  />
                </div>
                <div class="form-group">
                  <small>descuento</small>
                  <input
                    type="text"
                    class="form-control"
                    v-model="producto.descuento"
                  />
                </div>
                <div class="form-group">
                  <small>cantidad</small>
                  <input
                    type="text"
                    class="form-control"
                    v-model="producto.cantidad"
                  />
                </div>
                <button type="submit" class="btn btn-system">agregar</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- *************** modal llenar **************** -->
    <div
      class="modal fade"
      id="modalLlenar"
      tabindex="-1"
      aria-labelledby="exampleModalLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog">
        <div class="modal-content border-modal">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">
              Llenar lista de productos
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
            <div class="input-group mb-3">
              <input
                type="text"
                v-model="cod_document"
                class="form-control"
                placeholder="Documento"
              />
              <div class="input-group-append">
                <span class="input-group-text" id="basic-addon2"
                  ><i class="fas fa-file"></i
                ></span>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-dark" data-dismiss="modal">
              Cerrar
            </button>
            <button type="button" class="btn btn-system" @click="llenar_lista">
              Llenar
            </button>
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
      cod_document: "",
      producto: {
        codigo_barra: "",
        nompro: "",
        marca: "",
        precio: "",
        descuento: "0",
        stock: "",
        cantidad: "",
      },
      condicion: "barra",
      spinner_modal: false,
      codigo_barra: "",
      agreados: [],
      i: 0,
      codigo_nota: [],
      codigo_nota: "",
      codigo: [],
      condicionPago: ""
    };
  },
  created() {
    let DBagregados = JSON.parse(localStorage.getItem("agregar_notapedido"));
    if (DBagregados === null) {
      this.agreados = [];
    } else {
      this.agreados = DBagregados;
    }
    this.generar_nuevo_numer_f();
  },
  methods: {
    generarMostrar() {
      let url = "/api/codigo_generado/" + this.user_sucursal;
      axios.get(url).then((res) => {
        if (res.data === "") {
          this.codigo = "0";
          this.codigo = parseFloat(this.codigo) + 1;
          this.codigo = zeroPad(this.codigo, 9);
          this.codigo_nota = this.codigo;
        } else {
          this.codigo = res.data;
          this.codigo = parseFloat(this.codigo) + 1;
          this.codigo = zeroPad(this.codigo, 9);
          this.codigo_nota = this.codigo;
        }
        this.mostrar_codigo();
      });
    },
    generar_nuevo_numer_f() {
      let url = "/api/codigo_generado/" + this.user_sucursal;
      axios.get(url).then((res) => {
        if (res.data === "") {
          this.codigo = "0";
          this.codigo = parseFloat(this.codigo) + 1;
          this.codigo = zeroPad(this.codigo, 9);
          this.codigo_nota = this.codigo;
        } else {
          this.codigo = res.data;
          this.codigo = parseFloat(this.codigo) + 1;
          this.codigo = zeroPad(this.codigo, 9);
          this.codigo_nota = this.codigo;
        }
      });
    },
    buscarempresa() {
      let url = "/api/buscarproveedor/" + this.proveedor.ruc;
      axios.get(url).then((res) => {
        this.proveedor.empresa = res.data.nombre;
      });
    },
    escanear() {
      this.spinner_modal = true;
      document.getElementById("modalBarra").click();
      axios
        .get(
          "/api/escanearventa/" +
            this.condicion +
            "/" +
            this.codigo_barra +
            "/" +
            this.user_sucursal
        )
        .then((res) => {
          this.productos = res.data;
          this.producto.codigo_barra = this.productos.barra;
          this.producto.nompro = this.productos.nompro;
          this.producto.marca = this.productos.marca;
          this.producto.stock = this.productos.stock_almacen;
          this.producto.precio = this.productos.precio;
          this.spinner_modal = false;
        });
    },
    agregados() {
      if (
        this.producto.nompro.trim() === "" ||
        this.producto.stock === "" ||
        this.producto.cantidad === "" ||
        this.producto.precio === ""
      ) {
        Vue.$toast.error("completar todos los campos");
      } else {
        document.getElementById("cerrar_modal").click();
        this.agreados.push({
          barra: this.producto.codigo_barra,
          nompro: this.producto.nompro,
          marca: this.producto.marca,
          precio: this.producto.precio,
          descuento: this.producto.descuento,
          cantidad: this.producto.cantidad,
          stock: this.producto.stock,
          cod_nota: "N00" + this.user_id_sucursal + "-" + this.codigo_nota,
        });
        console.log(this.agreados);
        localStorage.setItem(
          "agregar_notapedido",
          JSON.stringify(this.agreados)
        );
        this.$data.codigo_barra = "";
        this.$data.producto.precio = "";
        this.$data.producto.nompro = "";
        this.$data.producto.descuento = "0";
        this.$data.producto.cantidad = "";
      }
    },
    eliminar(index) {
      swal({
        text: "¿eliminar?",
        icon: "error",
        buttons: ["no", "si"],
        dangerMode: true,
      }).then((willDelete) => {
        if (willDelete) {
          this.agreados.splice(index, 1);
          localStorage.setItem(
            "agregar_notapedido",
            JSON.stringify(this.agreados)
          );
        }
      });
    },
    generar_nota_pedido() {
      swal({
        text: "¿estas seguro?",
        icon: "error",
        buttons: ["no", "si"],
        dangerMode: true,
      }).then((willDelete) => {
        if (willDelete) {
          if (
            this.proveedor.ruc.trim() === "" ||
            this.proveedor.empresa.trim() === "" ||
            this.condicionPago.trim() === ""
          ) {
            Vue.$toast.error("completar todos los campos");
          } else {
            Vue.$toast.info("enviando datos...");
            this.agregar_nota();
            this.$data.proveedor.ruc = "";
            this.$data.proveedor.empresa = "";
          }
        }
      });
    },
    eliminar_del_detalle(index) {
      this.agreados.splice(index, 1);
      localStorage.setItem("agregar_notapedido", JSON.stringify(this.agreados));
    },
    agregar_nota() {
      const params = {
        cod_nota: "N00" + this.user_id_sucursal + "-" + this.codigo_nota,
        nro: this.codigo_nota,
        sucursal: this.user_sucursal,
        total: this.total_general,
        ruc: this.proveedor.ruc,
        empresa: this.proveedor.empresa,
        condicion_pago: this.condicionPago
      };
      axios.post("/agregar_nota", params).then((res) => {
        this.agregar_detalles();
        this.condicionPago = "";
      });
    },
    agregar_detalles() {
      const params = { ArrayDate: this.agreados };
      axios.post("/agregar_detalle_nota", params).then((res) => {
        Vue.$toast.info("enviado");
        this.eliminar_del_detalle();
        // this.generar_nuevo_numer_f();
        // this.mostrar_codigo();
        this.generarMostrar();
      });
    },
    eliminar_del_detalle() {
      this.agreados = [];
      localStorage.setItem("agregar_notapedido", JSON.stringify(this.agreados));
    },
    mostrar_codigo() {
      swal({
        title:
          "N00" + this.user_id_sucursal + "-" + zeroPad(this.codigo_nota, 8),
        icon: "success",
        button: "aceptar",
      });
    },
    llenar_lista() {
      if (this.cod_document.trim() === "") {
        Vue.$toast.error("escribir documento");
      } else {
        let url = "/api/llenar_listaN/" + this.cod_document;
        axios.get(url).then((res) => {
          this.agreados = res.data;
          localStorage.setItem("agregar_notapedido", JSON.stringify(res.data));
        });
      }
    },
  },
  computed: {
    total_general() {
      return this.agreados.reduce((total, item) => {
        return total + item.precio * item.cantidad - item.descuento;
      }, 0);
    },
  },
};
</script>
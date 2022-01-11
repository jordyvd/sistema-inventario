<template>
  <div>
    <div class="acciones" v-if="user_ventas < 1">
      <notpermiso />
    </div>
    <div class="container" v-else>
      <div class="card">
        <div class="card-header">
          <b v-if="nrof.length < 1"
            ><i class="fas fa-edit"></i> agregar venta (calculando...)</b
          >
          <b v-else
            ><i class="fas fa-edit"></i> agregar venta (V00{{
              user_id_sucursal
            }}-{{ producto.numfactura }})</b
          >
          &nbsp;
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
          <!-- <div class="centrar border">
            <div class="col-md-3 mb-3 my-3">
              <select class="custom-select" v-model="producto.estado">
                <option value disabled>tipo de venta...</option>
                <option value="1">boleta</option>
                <option value="tarjeta">factura</option>
                <option value="trasnferencia">tiked</option>
              </select>
            </div>
          </div> -->
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
            <!-- <div class="col-md-1 mb-3">
                <label>Búsqueda</label>
                <select class="custom-select" v-model="producto.estado">
                  <option value="trasnferencia">transferencia</option>
                  <option value="yape">yape</option>
                  <option value="0">credito</option>
                </select>
              </div> -->
            <div class="col-md-2 mb-3">
              <label>ruc o dni</label>
              <form @submit.prevent="getClient()">
                <input
                  type="search"
                  class="form-control"
                  v-model="producto.ruc_dni"
                />
              </form>
            </div>
            <div class="col-md-2 mb-3">
              <label>nombre</label>
              <input
                type="text"
                class="form-control"
                disabled
                v-model="producto.nom_cliente"
              />
            </div>
            <div class="col-md-2 mb-3">
              <label> documento </label>
              <select class="custom-select" v-model="tipo_v">
                <option value disabled>seleccionar...</option>
                <option value="ticked">ticked</option>
                <option value="boleta">boleta</option>
                <option value="factura">factura</option>
              </select>
            </div>
          </div>
          <form @submit.prevent="generar_venta">
            <div class="form-row centrar">
              <div class="col-md-3 mb-3">
                <label> condición</label>
                <select class="custom-select" v-model="producto.estado">
                  <option value disabled>seleccionar...</option>
                  <option value="1">efectivo</option>
                  <option value="tarjeta">tarjeta</option>
                  <option value="trasnferencia">transferencia</option>
                  <option value="yape">yape</option>
                  <option value="0">credito</option>
                  <option value="cancelado">cancelado</option>
                  <option value="cambio">cambio</option>
                </select>
              </div>
              <div class="col-md-3 mb-3">
                <label>total a pagar</label>
                <input
                  type="text"
                  class="form-control"
                  :value="total_pagar.toFixed(2)"
                  disabled
                />
              </div>
              <div class="col-md-3 mb-3">
                <label for="" class="text-white">finalizar</label>
                <button
                  type="submit"
                  v-if="!false"
                  class="form-control btn btn-system"
                >
                  <i class="fas fa-piggy-bank"></i> guardar
                </button>
                <button
                  v-else
                  class="form-control btn btn-system"
                  type="button"
                  disabled
                >
                  <span
                    class="spinner-grow spinner-grow-sm"
                    role="status"
                    aria-hidden="true"
                  ></span>
                  cargando...
                </button>
              </div>
            </div>
          </form>

          <!-- <button @click="abrir_ventana" class="btn btn-primary">
            ventana
          </button> -->
        </div>
      </div>
      <!-- **** DATOS ***** -->
      <br />
      <div class="card">
        <div class="card-header">
          <button
            class="btn btn-system float-rigth"
            title="limpiar"
            @click="limpiar"
          >
            <i class="fas fa-broom"></i>
          </button>
          <!-- <button
            class="btn btn-system float-rigth"
            title="cotizar"
            @click="cotizar"
          >
            <i class="fas fa-print"></i>
          </button> -->
          <button
            class="btn btn-system float-rigth"
            data-toggle="modal"
            data-target="#modalLlenar"
            title="llenar"
          >
            <i class="fas fa-fill-drip"></i>
          </button>
          <button class="btn text-secondary">
            <b><i class="fas fa-list"></i> lista de productos</b>
          </button>
        </div>
        <div class="card-body">
          <div class="alert centrar" v-if="agregados.length < 1">
            no hay productos agregados
          </div>
          <div v-else class="table-scroll">
            <div v-if="spinner_table" class="centrar">cargando...</div>
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
        <div class="modal-dialog modal-xl">
          <div class="modal-content border-modal modal-colo-p">
            <div class="modal-header">
              <h5 class="modal-title text-system" id="exampleModalLabel">
                <i class="fas fa-shopping-cart"></i> <b>agregar producto</b>
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
              <div class="centrar" v-if="spinner_modal">cargando...</div>
              <table class="table table-bordered" v-else>
                <thead class="tbl-text-white">
                  <tr>
                    <th scope="col">#</th>
                    <th scope="col">producto / descripción</th>
                    <th scope="col">precio</th>
                    <th scope="col">marca</th>
                    <th scope="col">stock</th>
                    <th scope="col">cantidad</th>
                    <th scope="col">descuento</th>
                  </tr>
                </thead>
                <tbody class="tbl-text-white">
                  <tr>
                    <th scope="row">1</th>
                    <td>{{ producto.nompro }}</td>
                    <td>{{ parseFloat(producto.precio).toFixed(2) }}</td>
                    <td>{{ producto.marca }}</td>
                    <td>{{ producto.stock }}</td>
                    <td>
                      <input
                        type="text"
                        class="input-table solonumero"
                        v-model="producto.cantidad"
                      />
                    </td>
                    <td>
                      <input
                        type="text"
                        class="input-table solonumero"
                        v-model="producto.descuento"
                      />
                    </td>
                  </tr>
                </tbody>
              </table>
              <div class="centrar">
                <button class="btn btn-modal-tbl" @click="agregar_prod">
                  <i class="fas fa-shopping-cart"></i> agregar
                </button>
              </div>
            </div>
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
    <!-- modal cliente -->
    <button
      type="button"
      class="btn btn-primary"
      id="modalClienteOpen"
      data-toggle="modal"
      data-target="#modalCliente"
      hidden
    ></button>
    <div
      class="modal fade"
      id="modalCliente"
      tabindex="-1"
      aria-labelledby="exampleModalLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog">
        <div class="modal-content border-modal modal-colo-p">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Cliente</h5>
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
            <form>
              <div class="form-group">
                <label for="exampleInputPassword1">Numero de documento</label>
                <input
                  type="text"
                  class="form-control"
                  v-model="cliente.numeroDocumento"
                />
              </div>
              <div class="form-group">
                <label for="exampleInputEmail1">Nombre</label>
                <input
                  type="text"
                  class="form-control"
                  v-model="cliente.nombre"
                />
              </div>
              <div class="form-group">
                <label for="exampleInputPassword1">Tipo de documento</label>
                <input
                  type="text"
                  class="form-control"
                  v-model="cliente.tipoDocumento"
                />
              </div>
              <div class="form-group">
                <label for="exampleInputPassword1">Direccion</label>
                <input
                  type="text"
                  class="form-control"
                  v-model="cliente.direccion"
                />
              </div>
              <div class="form-group">
                <label for="exampleInputPassword1">Provincia</label>
                <input
                  type="text"
                  class="form-control"
                  v-model="cliente.provincia"
                />
              </div>
              <div class="form-group">
                <label for="exampleInputPassword1">Departamento</label>
                <input
                  type="text"
                  class="form-control"
                  v-model="cliente.departamento"
                />
              </div>
              <div class="form-group">
                <label for="exampleInputPassword1">Distrito</label>
                <input
                  type="text"
                  class="form-control"
                  v-model="cliente.distrito"
                />
              </div>
              <button type="submit" class="btn btn-primary">Guardar</button>
            </form>
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
      tipo_v: "",
      cliente: {
        nombre: null,
        numeroDocumento: null,
        tipoDocumento: null,
        direccion: null,
        provincia: null,
        departamento: null,
        distrito: null,
      },
      sunat: {
        total_texto: "",
      },
      cod_document: "",
      producto: {
        id: "",
        ruc_dni: "",
        nom_cliente: "",
        nompro: "",
        precio: "",
        precio_compra: "",
        marca: "",
        stock: "",
        cantidad: "",
        descuento: "0",
        numfactura: "",
        cantidad: "",
        estado: "",
        img: "default.png",
      },
      condicion: "barra",
      barra_result: "",
      spinner_modal: false,
      codigo_barra: "",
      productos: [],
      cliente: [],
      agregados: [],
      nrof: [],
      spinner_table: false,
      cotizacion: [],
      numeroSunat: null,
      serieSunat: null,
    };
  },
  created() {
    let agregadosDB = JSON.parse(localStorage.getItem("agregado_venta"));
    if (agregadosDB === null) {
      this.agregados = [];
    } else {
      this.agregados = agregadosDB;
    }
    this.generar_nuevo_numer_f();
  },
  methods: {
    accionprueba() {
      const params = { nombre: "roberto" };
      enviarInfoSunat(params);
    },
    escanear() {
      this.spinner_modal = true;
      document.getElementById("modalBarra").click();
      let cadena = this.codigo_barra.replace(/\/+/g, "*");
      let url_scnn =
        "/api/escanearventa/" +
        this.condicion +
        "/" +
        cadena +
        "/" +
        this.user_sucursal;
      axios
        .get(url_scnn)
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
    buscarcliente() {
      if (this.producto.ruc_dni.trim() === "") {
      } else {
        let url = "/api/buscarclienteventa/" + this.producto.ruc_dni;
        axios.get(url).then((res) => {
          this.cliente = res.data;
          this.producto.nom_cliente = this.cliente.nombre;
        });
      }
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
        if (this.agregados.length > 16) {
          Vue.$toast.error("llegó al límite");
        } else {
          parsar_total_a_texto();
          this.agregados.push({
            sucursal: this.user_sucursal,
            barra: this.barra_result,
            producto: this.producto.nompro,
            total_texto: this.sunat.total_texto,
            marca: this.producto.marca,
            precio: this.producto.precio,
            precio_compra: this.producto.precio_compra,
            cantidad: this.producto.cantidad,
            descuento: this.producto.descuento,
            url_imagen: this.producto.img,
          });
          localStorage.setItem(
            "agregado_venta",
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
            "agregado_venta",
            JSON.stringify(this.agregados)
          );
          Vue.$toast.info("se eliminó con éxito");
        }
      });
    },
    generar_nuevo_numer_f() {
      this.spinner2 = true;
      let url = "/api/nrof/" + this.user_sucursal;
      axios
        .get(url)
        .then((res) => {
          if (res.data === "") {
            this.nrof = "0";
            this.nrof = parseFloat(this.nrof) + 1;
            this.nrof = zeroPad(this.nrof, 9);
            cccccthis.producto.numfactura = this.nrof;
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
    generar_venta() {
      const params = {
        nrof: this.producto.numfactura,
        estado: this.producto.estado,
        tipo_v: this.tipo_v,
        total_v: this.total_pagar,
        total_ganancia: this.total_pagar - this.total_precio_compra,
        ruc_dni: this.producto.ruc_dni,
        nom_cliente: this.producto.nom_cliente,
        sucursal: this.user_sucursal,
        cod_sucursal:
          "V00" + this.user_id_sucursal + "-" + this.producto.numfactura,
        usuario: this.user_name,
        xmayor: 0,
        cliente: this.cliente,

        productos: this.agregados,
        condicion:
          this.producto.estado == "1"
            ? "Efectivo"
            : this.producto.estado == "0"
            ? "Credito"
            : this.producto.estado,
      };
      swal({
        text: "¿estás seguro?",
        icon: "error",
        buttons: ["no", "sí"],
        dangerMode: true,
      }).then((willDelete) => {
        if (willDelete) {
          // ***** validar ******
          if (
            this.producto.estado.trim() === "" ||
            this.tipo_v.trim() === "" ||
            this.agregados.length < 1 ||
            this.producto.nom_cliente.trim() === ""
          ) {
            Vue.$toast.error("completar datos");
          } else {
            this.spinner_table = true;
            document.getElementById("clickButtonSpinner").click();
            axios
              .post("/generar_venta", params)
              .then((res) => {
                if (this.tipo_v != "ticked") {
                  this.generarDocumento(res.data);
                } else {
                  this.generar_nuevo_numer_f();
                  this.vaciar_datos();
                  this.eliminar_productos();
                  document.getElementById("clickButtonSpinner").click();
                }
              })
              .catch((error) => {
                this.spinner_table = false;
                swal("ERROR", "comprobar conexión", "info");
              });
          }
        }
      });
    },
    imagen(item) {
      this.producto.img = item.url_imagen;
      this.producto.nompro = item.producto;
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
    vaciar_datos() {
      this.$data.producto.ruc_dni = "";
      this.$data.producto.nom_cliente = "";
    },
    // agregarventa_detalles() {
    //   const params = {
    //     arrayDate: this.agregados,
    //     nrof: "V00" + this.user_id_sucursal + "-" + this.producto.numfactura,
    //     condicion:
    //       this.producto.estado == "1"
    //         ? "Efectivo"
    //         : this.producto.estado == "0"
    //         ? "Credito"
    //         : this.producto.estado,
    //   };
    //   axios.post("/detalle_venta", params).then((res) => {
    //     this.generarDocumento();
    //   });
    // },
    generarDocumento(serie) {
      const params = {
        code: serie,
        nrof: this.producto.numfactura,
        cliente: this.cliente,
        productos: this.agregados,
        condicion: this.producto.estado,
        documento: this.tipo_v,
        sucursal: this.user_sucursal,
      };
      axios.post("/api/generarDocumento", params).then((res) => {
        let formData = new FormData();
        formData.append("emisor", JSON.stringify(res.data.emisor));
        formData.append("cliente", JSON.stringify(res.data.cliente));
        formData.append("tipoDocumento", res.data.tipoDocumento);
        formData.append("code", res.data.code);
        formData.append("codeInterno", res.data.codeInterno);
        formData.append("productos", JSON.stringify(res.data.productos));
        formData.append("fecha", res.data.fecha);
        formData.append("fechaPdf", res.data.fechaPdf);
        formData.append("igv", res.data.igv);
        formData.append("total", res.data.total);
        formData.append("totalText", res.data.totalText);
        formData.append("totalSinIgv", res.data.totalSinIgv);
        formData.append("porcentajeIgv", res.data.porcentajeIgv);
        formData.append("medioPago", res.data.medioPago);
        axios.post(this.facturadorUrl, formData).then((res) => {
          this.openDocumento(res.data.code);
          this.generar_nuevo_numer_f();
          this.vaciar_datos();
          this.eliminar_productos();
          this.editarDocumento(res.data);
          document.getElementById("clickButtonSpinner").click();
        });
      });
    },
    eliminar_productos() {
      this.agregados = [];
      localStorage.setItem("agregado_venta", JSON.stringify(this.agregados));
      this.spinner_table = false;
    },
    llenar_lista() {
      if (this.cod_document.trim() === "") {
        Vue.$toast.error("escribir documento");
      } else {
        let url = "/api/llenar_listaV/" + this.cod_document;
        axios.get(url).then((res) => {
          this.agregados = res.data;
          localStorage.setItem("agregado_venta", JSON.stringify(res.data));
        });
      }
    },
    abrir_ventana() {
      window.open(
        "http://localhost/printticked/print_pdf.php",
        "imprimir-PDF",
        "width=350,height=400,scrollbars=NO,toolbar=no, location=no, directories=no, status=no, menubar=no"
      );
    },
    limpiar() {
      swal({
        text: "¿estás seguro?",
        icon: "error",
        buttons: ["no", "sí"],
        dangerMode: true,
      }).then((willDelete) => {
        if (willDelete) {
          this.eliminar_productos();
        }
      });
    },
    cotizar() {
      this.cotizacion = [];
      for (let i = 0; i < this.agregados.length; i++) {
        this.cotizacion.push({
          barra: this.agregados[i].barra,
          cantidad: this.agregados[i].cantidad,
          descuento: this.agregados[i].descuento,
          producto: this.agregados[i].producto.replace(/\/+/g, "*"),
        });
      }
      const params = { datos: this.agregados };
      axios.post("imprimircotizar", params);
    },
    getClient() {
      document.getElementById("clickButtonSpinner").click();
      if (this.producto.ruc_dni.length > 8) {
        this.getRuc(this.producto.ruc_dni);
      } else {
        this.getDni(this.producto.ruc_dni);
      }
    },
    getDni(numero) {
      axios.get("/api/dni/" + numero).then((res) => {
        this.producto.nom_cliente = res.data.nombre;
        this.cliente = res.data;
        this.tipo_v = "boleta";
        document.getElementById("clickButtonSpinner").click();
        if (this.cliente.numeroDocumento == null) {
          swal("ERROR", "no se encontro un cliente", "error");
          document.getElementById("modalClienteOpen").click();
        }
      });
    },
    getRuc(numero) {
      axios.get("/api/ruc/" + +numero).then((res) => {
        this.producto.nom_cliente = res.data.nombre;
        this.cliente = res.data;
        this.tipo_v = "factura";
        document.getElementById("clickButtonSpinner").click();
        if (this.cliente.numeroDocumento == null) {
          swal("ERROR", "no se encontro un cliente", "error");
          document.getElementById("modalClienteOpen").click();
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
    total_precio_compra() {
      return this.agregados.reduce((total, item) => {
        return total + item.precio_compra * item.cantidad;
      }, 0);
    },
    total_ganancia() {
      return this.agregados.reduce((total, item) => {
        return total + this.total_pagar - this.total_precio_compra;
      }, 0);
    },
  },
};
</script>
<style scoped>
.float-rigth {
  margin: 0px 5px !important;
}
</style>
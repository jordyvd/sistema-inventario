<template>
  <div>
    <div v-if="user_almacen < 1">
      <notpermiso />
    </div>
    <div class="row" v-else>
      <div class="container">
        <br />
        <div class="centrar">
          <!-- FORMULARIO -->
          <div class="formularios_sistema">
            <form @submit.prevent="escanear">
              <div class="form-group">
                <small class="form-text text-muted">codigo de barra</small>
                <input type="search" class="form-control" v-model="escaner" />
              </div>
            </form>
            <form @submit.prevent="agregarnota">
              <div class="form-group">
                <small class="form-text text-muted">codigo de producto</small>
                <input
                  type="text"
                  class="form-control"
                  v-model="codigo"
                  disabled
                />
              </div>
              <div class="form-group">
                <small class="form-text text-muted">producto</small>
                <input
                  type="text"
                  class="form-control"
                  v-model="nompro"
                  disabled
                />
              </div>
              <div class="form-group">
                <small class="form-text text-muted">marca</small>
                <input
                  type="text"
                  class="form-control"
                  v-model="marca"
                  disabled
                />
              </div>
              <div class="form-group">
                <small class="form-text text-muted">cantidad</small>
                <input
                  type="text"
                  :disabled="true"
                  class="form-control solonumero"
                  v-model="stock"
                />
              </div>
              <div class="form-group">
                <small class="form-text text-muted">precio venta</small>
                <input
                  type="text"
                  class="form-control solonumero"
                  v-model="precio_venta"
                />
              </div>
              <div class="form-group">
                <small class="form-text text-muted">precio por mayor</small>
                <input
                  type="text"
                  class="form-control solonumero"
                  v-model="precio_x_mayor"
                />
              </div>
              <button
                type="submit"
                v-if="boton"
                class="btn bg-system"
              >
                <i class="fa fa-save"></i> Guardar
              </button>
            </form>
          </div>
          <!-- FIN FORMULARIO -->
        </div>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  data() {
    return {
      escaner: "",
      producto: [],
      nompro: "",
      marca: "",
      codigo: "",
      stock: 0,
      codigo_barra:"",
      precio_venta: "",
      precio_x_mayor:"",
      boton: true,
    };
  },
  methods: {
    escanear() {
      axios.get("/api/escanear/" + this.escaner).then((res) => {
        this.producto = res.data;
        this.codigo_barra = this.producto.barra;
        this.nompro = this.producto.nompro;
        this.marca = this.producto.marca;
        this.codigo = this.producto.codigo;
      });
    },
    agregarnota() {
      if (this.precio_venta.trim() === "") {
        Vue.$toast.error("completar todos los campos");
      } else {
        this.boton = false;
        const params = {
          barra: this.codigo_barra,
          stock: this.stock,
          precio: this.precio_venta,
          preciomayor:this.precio_x_mayor,
          sucursal: this.user_sucursal,
        };
        axios.post("/nota_ingreso", params).then((res) => {
          if (res.data === "este producto ya existe en tu almacen") {
            swal("", res.data, "info");
          } else {
            Vue.$toast.success(res.data);
          }
          this.limpiar_formulario();
          this.boton = true;
        });
      }
    },
    limpiar_formulario() {
      this.$data.escaner = "";
      this.$data.nompro = "";
      this.$data.codigo = "";
      this.$data.precio = "";
      this.$data.nompro = "";
      this.$data.marca = "";
      this.$data.stock = "";
      this.$data.precio_venta = "";
    },
  },
};
</script>
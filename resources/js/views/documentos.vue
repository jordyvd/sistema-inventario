<template>
  <div class="container">
    <div class="row">
      <button class="btn btn-primary" @click="envioMasivo()" style="margin:5px" v-if="user_sucursal == 'huaral'">
        <i class="fas fa-share"></i> enviar pendientes
      </button>
      <input type="date" v-model="fecha" class="input-search" style="margin:5px" @change="listar()">
      <div class="table-scroll">
        <table class="table">
          <thead>
            <tr>
              <th scope="col" v-if="user_sucursal == 'huaral'"></th>
              <th scope="col">serie</th>
              <th scope="col">estado</th>
              <th scope="col">total</th>
              <th scope="col">pdf</th>
              <th scope="col">xml</th>
            </tr>
          </thead>
          <tbody v-for="(item, index) in documentos" :key="index">
            <tr>
              <td v-if="user_sucursal == 'huaral'">
                <button
                  class="btn btn-primary"
                  v-if="item.estado == null || item.estado == 0"
                  @click="enviarComprobante(item)"
                >
                  <i class="fas fa-share"></i> enviar
                </button>
                <i class="fas fa-check" v-else></i>
              </td>
              <td>{{ item.serie }}</td>
              <td>
                <p v-if="item.estado == null" class="text-warning">pendiente</p>
                <p v-else-if="item.estado == 1" class="text-success">enviado</p>
                <p v-else class="text-danger">envio fallido</p>
              </td>
              <td>{{ item.total }}</td>
              <td>
                <button
                  @click="abrirDocument(item)"
                  class="btn btn-danger file"
                  v-if="item.estado_pdf"
                >
                  <i class="fas fa-file-pdf"></i>
                </button>
              </td>
              <td>
                <button @click="abrirXml(item)" class="btn btn-secondary file">
                  <i class="fas fa-file-invoice"></i>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  data() {
    return {
      documentos: [],
      fecha: null,
    };
  },
  created() {
    this.listar();
  },
  methods: {
    listar() {
      const params = {
        sucursal: this.user_sucursal,
        fecha: this.fecha,
      };
      axios.post("/api/listar-documentos", params).then((res) => {
        this.documentos = res.data;
      });
    },
    abrirDocument(item) {
      this.openDocumento(item.serie);
    },
    abrirXml(item) {
      this.openXmlFe(item.serie);
    },
    enviarComprobante(item) {
      swal({
        text: "¿estas seguro?",
        icon: "error",
        buttons: ["no", "si"],
        dangerMode: true,
      }).then((willDelete) => {
        if (willDelete) {
          document.getElementById("clickButtonSpinner").click();
          const params = { serie: item.serie,tipo: item.tipo };
          axios.post("/api/enviar-comprobante", params).then((res) => {
            swal("", "comprobante enviado", "success");
            this.listar();
            document.getElementById("clickButtonSpinner").click();
          });
        }
      });
    },
    envioMasivo() {
      swal({
        text: "¿estas seguro?",
        icon: "error",
        buttons: ["no", "si"],
        dangerMode: true,
      }).then((willDelete) => {
        if (willDelete) {
          document.getElementById("clickButtonSpinner").click();
          const params = { documentos: this.documentos };
          axios.post("/api/enviar-comprobante-masivo", params).then((res) => {
            swal("", res.data, "info");
            this.listar();
            document.getElementById("clickButtonSpinner").click();
          }).catch((error) =>{
              swal("", "algo salio mal, vuelvo a intentar", "error");
              this.listar();
          });
        }
      });
    },
  },
};
</script>
<style scoped>
.file {
  width: 40px;
  height: 35px;
}
</style>
<template>
  <div class="container">
    <div class="row">
      <button
        class="btn btn-primary"
        @click="envioMasivo()"
        style="margin: 5px"
        v-if="user_id == 1"
      >
        <i class="fas fa-share"></i> enviar pendientes
      </button>
      <input
        type="date"
        v-model="fecha"
        class="input-search"
        style="margin: 5px"
        @change="refrescar()"
      />
      <input
        type="date"
        v-model="fecha_end"
        class="input-search"
        style="margin: 5px"
        @change="refrescar()"
      />
      <button
        class="btn btn-success"
        @click="exportExcel()"
        style="margin: 5px"
      >
        <i class="fas fa-download"></i> Exportar
      </button>
      <div
        class="alert border"
        role="alert"
        style="margin: 5px; background: transparent !important"
      >
        TOTAL: {{ totalDocumentos }}
      </div>
      <div class="table-scroll">
        <table class="table">
          <thead>
            <tr>
              <th scope="col" v-if="user_id == 1">enviar</th>
              <th scope="col">serie</th>
              <th scope="col">documento</th>
              <th scope="col" v-if="user_id == 1">estado</th>
              <th scope="col">afectado</th>
              <th scope="col">total</th>
              <th scope="col">pdf</th>
              <th scope="col">xml</th>
            </tr>
          </thead>
          <tbody v-for="(item, index) in documentos" :key="index">
            <tr>
              <td v-if="user_id == 1">
                <button
                  class="btn btn-primary"
                  @click="enviarComprobante(item)"
                >
                  <i class="fas fa-share"></i>
                </button>
              </td>
              <td>{{ item.serie }}</td>
              <td>{{ tipoDocumento(item.tipo) }}</td>
              <td v-if="user_id == 1">
                <p v-if="item.estado == null" class="text-warning">pendiente</p>
                <p v-else-if="item.estado == 1" class="text-success">enviado</p>
                <p v-else class="text-danger">envio fallido</p>
              </td>
              <td>{{ item.afectado }}</td>
              <td>{{ item.total }}</td>
              <td v-if="item.estado_pdf">
                <button
                  @click="abrirDocument(item)"
                  class="btn btn-danger file"
                  v-if="item.estado_pdf"
                >
                  <i class="fas fa-file-pdf"></i>
                </button>
              </td>
              <td v-else>--</td>
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
import moment from "moment";
export default {
  data() {
    return {
      documentos: [],
      fecha: null,
      fecha_end: null,
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
        fecha_end: this.fecha_end,
      };
      axios.post("/api/listar-documentos", params).then((res) => {
        this.documentos = res.data;
      });
    },
    refrescar() {
      document.getElementById("clickButtonSpinner").click();
      const params = {
        sucursal: this.user_sucursal,
        fecha: this.fecha,
        fecha_end: this.fecha_end,
      };
      axios.post("/api/listar-documentos", params).then((res) => {
        this.documentos = res.data;
        document.getElementById("clickButtonSpinner").click();
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
          const params = { serie: item.serie, tipo: item.tipo };
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
          axios
            .post("/api/enviar-comprobante-masivo", params)
            .then((res) => {
              swal("", res.data, "info");
              this.listar();
              document.getElementById("clickButtonSpinner").click();
            })
            .catch((error) => {
              swal("", "algo salio mal, vuelvo a intentar", "error");
              this.listar();
            });
        }
      });
    },
    tipoDocumento(tipo) {
      if (tipo == 1) {
        return "factura";
      } else if (tipo == 3) {
        return "boleta";
      } else if (tipo == 7) {
        return "nota de credito";
      }
    },
    exportExcel() {
      this.clickSpinner();
      const params = {
        documentos: this.documentos,
        fecha: this.fecha,
        fecha_end: this.fecha_end,
      };
      axios
        .post("/api/exportar-documentos", params, { responseType: "blob" })
        .then((response) => {
          this.forceFileDownload(response);
          this.clickSpinner();
        })
        .catch(() => console.log("error occured"));
    },
    forceFileDownload(response) {
      const url = window.URL.createObjectURL(new Blob([response.data]));
      const link = document.createElement("a");
      link.href = url;
      let fecha =
        this.fecha != null ? this.fecha : moment().format("YYYY-MM-DD");
      let fecha_end =
        this.fecha_end != null ? this.fecha_end : moment().format("YYYY-MM-DD");
      let namefile =
        "FE_" +
        moment(fecha).format("DD-MM-YYYY") +
        "_" +
        moment(fecha_end).format("DD-MM-YYYY");
      link.setAttribute("download", namefile + ".xlsx");
      document.body.appendChild(link);
      link.click();
    },
  },
  computed: {
    totalDocumentos() {
      let array = this.documentos;
      let total = 0;
      array.forEach((element) => {
        if (element.tipo != 7) {
          total += parseFloat(element.total);
        }
      });
      let anulado = 0;
      array.forEach((element) => {
        if (element.tipo == 7) {
          anulado += parseFloat(element.total);
        }
      });
      return parseFloat(total) - parseFloat(anulado);
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
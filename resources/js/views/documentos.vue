<template>
  <div class="container">
    <div class="row">
      <div class="table-scroll">
        <table class="table">
          <thead>
            <tr>
              <th scope="col">serie</th>
              <th scope="col">estado</th>
              <th scope="col">total</th>
              <th scope="col">pdf</th>
              <th scope="col">xml</th>
            </tr>
          </thead>
          <tbody v-for="(item, index) in documentos" :key="index">
            <tr>
              <td>{{ item.serie }}</td>
              <td>
                <p v-if="item.estado == 1" class="text-success">enviado</p>
                <p v-else class="text-danger">envio fallido</p>
              </td>
              <td>{{ item.total }}</td>
              <td>
                <button @click="abrirDocument(item)" class="btn btn-danger" v-if="item.estado_pdf">
                  <i class="fas fa-file-pdf"></i>
                </button>
              </td>
              <td>
                <button @click="abrirXml(item)" class="btn btn-secondary">
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
    };
  },
  created() {
    this.listar();
  },
  methods: {
    listar() {
      const params = {
        sucursal: this.user_sucursal,
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
  },
};
</script>
<style scoped>
.btn {
  width: 40px;
  height: 35px;
}
</style>
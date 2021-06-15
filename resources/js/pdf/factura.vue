<template>
  <pdf :src="imprimirfac"></pdf>
</template>
<script>
import pdf from "vue-pdf";
export default {
  components: {
    pdf,
  },
  methods: {
    printInvoice() {
      window.print();
    },
  },
};
</script>
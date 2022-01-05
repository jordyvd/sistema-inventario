<script>
export default {
  data() {
    return {
      sucursal: [],
    };
  },
  created() {
    //this.listar_sucursal();
  },
  methods: {
    listar_sucursal() {
      axios.get("/api/listsucursal").then((res) => {
        this.sucursal = res.data.sucursal.data;
        // localStorage.setItem("sucursalStorage", JSON.stringify(res.data.sucursal.data));
      });
    },
    preloader(){
       document.getElementById("clickButtonSpinner").click();
    },
    openDocumento(pdf) {
      let url =
        "http://localhost/testcpe/api_cpe/pdf/10405163131-" + pdf + ".pdf";
      let params = "width= 400";
      params += ", height=" + screen.height;
      params += ", top=0, left=500";
      params += ", fullscreen=yes";
      params += ", directories=no";
      params += ", location=no";
      params += ", menubar=no";
      params += ", resizable=no";
      params += ", scrollbars=no";
      params += ", status=no";
      params += ", toolbar=no";

      let newwin = window.open(url, "documento", params);
      if (window.focus) {
        newwin.focus();
      }
      return false;
    },
    openXmlFe(xml) {
      let url =
        "http://localhost/testcpe/api_cpe/BETA/10405163131-" + xml + ".xml";
      let params = "width= "+screen.width;
      params += ", height=" + screen.height;
      params += ", top=0, left=500";
      params += ", fullscreen=yes";
      params += ", directories=no";
      params += ", location=no";
      params += ", menubar=no";
      params += ", resizable=no";
      params += ", scrollbars=no";
      params += ", status=no";
      params += ", toolbar=no";

      let newwin = window.open(url, "xml", params);
      if (window.focus) {
        newwin.focus();
      }
      return false;
    },
  },
};
</script>
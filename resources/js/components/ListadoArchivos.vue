<template>
  <div class="container">
    <b-container class="bv-example-row">
      <b-row>
        <b-badge
          variant="light"
          class="carpetas pointer"
          @click="getInicio()"
          v-if="folders.length > 0"
          >Inicio <i class="fa fa-home"></i
        ></b-badge>
        <b-badge
          variant="light"
          class="carpetas pointer"
          v-for="(item, index) in folders"
          :key="index"
          @click="redireccionarFolder(item.id, index)"
          >{{ item.nombre }}&nbsp;<i class="fa fa-angle-right"></i
        ></b-badge>
        <span></span>
      </b-row>
    </b-container>
    <div class="row">
      <div class="col-sm-2">
        <div class="card pointer my-2" v-popover:nuevo>
          <div class="card-body text-center" style="height: 120px">
            <img src="/images/add-file.png" width="80" alt="" />
          </div>
          <div
            class="card-footer text-center barra-options-files"
            style="height: 30px"
          >
            <p style="margin-top: -9px">agregar</p>
          </div>
        </div>
      </div>
      <div class="col-sm-2" v-for="(item, index) in files" :key="index">
        <div class="card pointer my-2">
          <div
            class="card-body text-center"
            style="height: 120px"
            v-b-tooltip.hover.top="item.nombre"
            @click="pushFolder(item)"
            v-if="item.tipo == 1"
          >
            <img src="/images/open-folder.png" width="80" alt="" /> <br />
            <p style="font-size: 10px">{{ cortarNombre(item) }}</p>
          </div>
          <div
            @click="openFile(item)"
            class="card-body text-center"
            style="height: 120px"
            v-else
          >
            <img
              :src="validateExtension(item)"
              class="img"
              alt=""
              :style="validateExtensionStyle(item)"
            />
            <p style="font-size: 10px">{{ cortarNombre(item) }}</p>
          </div>
          <div
            class="card-footer text-center barra-options-files"
            @click="clickFile(item, index)"
            style="height: 30px"
            v-popover:optionfiles
          >
            <p style="margin-top: -9px"><i class="fa fa-caret-down"></i></p>
          </div>
        </div>
      </div>
    </div>
    <popover name="nuevo">
      <div class="row">
        <div class="col-4">
          <div class="list-group" role="tablist" style="width: 170px">
            <a
              class="list-group-item list-group-item-action pointer"
              @click="agregarCarpeta()"
              ><i class="fa fa-folder"></i> Nueva carpeta</a
            >
            <button
              class="list-group-item list-group-item-action pointer"
              @click="file.modal = true"
            >
              <i class="fa fa-image"></i> Nuevo archivo
            </button>
          </div>
        </div>
      </div>
    </popover>
    <!-- *************** popover ******************-->
    <popover name="optionfiles">
      <div class="row">
        <div class="col-4">
          <div class="list-group" role="tablist" style="width: 170px">
            <a
              class="list-group-item list-group-item-action pointer"
              @click="file.modalDescripcion = true"
              ><i class="fa fa-align-center"></i> Información</a
            >
            <a
              class="list-group-item list-group-item-action pointer"
              @click="deleteFile()"
              ><i class="fa fa-eraser"></i> Borrar</a
            >
          </div>
        </div>
      </div>
    </popover>
    <!-- ************* modal add files ***************** -->
    <b-modal
      v-model="file.modal"
      hide-footer
      centered
      title="agregar archivos"
      size="lg"
      content-class="border-modal"
    >
      <div>
        <div
          class="flex w-full h-screen items-center justify-center text-center"
          id="app"
          @dragover="dragover"
          @dragleave="dragleave"
          @drop="drop"
        >
          <div class="p-12 bg-gray-100 border border-gray-300">
            <input
              type="file"
              v-if="input"
              multiple
              name="fields[assetsFieldHandle][]"
              id="assetsFieldHandle"
              class="w-px h-px opacity-0 overflow-hidden absolute"
              @change="onChange"
              ref="file"
              accept=".jpg,.jpeg,.png"
              hidden
            />
            <div class="container pointer" v-cloak @click="openFolder()">
              <div
                class="text-center"
                style="padding: 30px"
                v-if="filesAdd.length == 0"
              >
                <img src="/images/upload.png" width="80" alt="" /> <br />
                <b>SUBIR ARCHIVOS</b>
              </div>
              <div class="row" style="z-index: 100">
                <div
                  class="col-sm-4"
                  v-for="(file, index) in filesAdd"
                  :key="index"
                >
                  <div class="card my-2">
                    <div
                      class="card-body text-center"
                      style="height: 90px"
                      :title="file.name"
                    >
                      <img src="/images/image.png" width="40" alt="" />
                      <br />
                      <p style="font-size: 10px">{{ file.name }}</p>
                    </div>
                    <div
                      class="
                        card-footer
                        pointer
                        text-center
                        barra-options-files
                      "
                      @click="remove(filesAdd.indexOf(file))"
                      style="height: 30px"
                    >
                      <p style="margin-top: -9px">
                        <i class="fa fa-trash"></i>
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="my-2 text-center">
          <b-button class="border-white btn-system" @click="guardarImagen()"
            >Guardar</b-button
          >
        </div>
      </div>
    </b-modal>
    <!-- ********************** MODAL DESCRIPCION *********************** -->
    <b-modal
      v-model="file.modalDescripcion"
      hide-footer
      centered
      :title="item.nombre"
      size="lg"
      content-class="border-modal"
    >
      <b-form-textarea
        id="textarea"
        v-model="item.descripcion"
        placeholder="Enter something..."
        rows="3"
        max-rows="6"
      ></b-form-textarea>
      <div class="text-center my-2">
        <b-button
          class="border-white btn btn-system"
          @click="guardarDescripcion()"
          >Guardar</b-button
        >
      </div>
    </b-modal>
  </div>
</template>
<script>
export default {
  props: ["files"],
  data() {
    return {
      item: {},
      file: {
        modal: false,
        type: 0,
        modalDescripcion: false,
      },
      filesAdd: [],
      folders: [],
      currentFolder: "",
      input: true,
      index: 0,
    };
  },
  created() {},
  methods: {
    agregarCarpeta() {
      let carpeta = prompt("nombre de carpeta: ", "carpeta sin nombre");
      if (carpeta == null || carpeta == "") {
        return;
      } else {
        this.guardarFolder(carpeta);
      }
    },
    clickFile(item, index) {
      this.item = item;
      this.index = index;
    },
    onChange() {
      let array = [...this.$refs.file.files];
      array.forEach((element) => {
        this.filesAdd.push(element);
      });
    },
    remove(i) {
      this.input = false;
      this.filesAdd.splice(i, 1);
    },
    dragover(event) {
      event.preventDefault();
      // Add some visual fluff to show the user can drop its files
      if (!event.currentTarget.classList.contains("bg-green-300")) {
        event.currentTarget.classList.remove("bg-gray-100");
        event.currentTarget.classList.add("bg-green-300");
      }
    },
    dragleave(event) {
      // Clean up
      event.currentTarget.classList.add("bg-gray-100");
      event.currentTarget.classList.remove("bg-green-300");
    },
    drop(event) {
      event.preventDefault();
      this.$refs.file.files = event.dataTransfer.files;
      this.onChange(); // Trigger the onChange event manually
      // Clean up
      event.currentTarget.classList.add("bg-gray-100");
      event.currentTarget.classList.remove("bg-green-300");
    },
    guardarImagen() {
      if (this.filesAdd.length < 1) {
        return;
      }
      this.file.modal = false;
      this.$emit("guardarImagen", this.filesAdd, this.currentFolder);
      this.filesAdd = [];
    },
    guardarFolder(nombre) {
      this.$emit("guardarFolder", nombre, this.currentFolder);
    },
    pushFolder(item) {
      this.folders.push({
        nombre: item.nombre,
        id: item.id,
      });
      this.currentFolder = item.id;
      this.getArhivosFolder(item.id);
    },
    getArhivosFolder(id) {
      this.$emit("getArhivosFolder", id);
    },
    getInicio() {
      this.getArhivosFolder(null);
      this.currentFolder = "";
      this.folders = [];
    },
    redireccionarFolder(id, index_p) {
      this.getArhivosFolder(id);
      let array = [];
      this.folders.forEach((element, index) => {
        if (index <= index_p) {
          array.push(element);
        }
      });
      this.folders = array;
    },
    guardarDescripcion() {
      this.$emit("guardarDescripcion", this.item.id, this.item.descripcion);
      this.file.modal = false;
    },
    cortarNombre(item) {
      if (item.nombre.length > 16) {
        let extension = item.extension != null ? item.extension : ".";
        return item.nombre.substring(0, 13) + ".." + extension;
      }
      return item.nombre;
    },
    openFolder() {
      if (this.input == false) this.input = true;
      let ref = this.$refs.file;
      if (ref) {
        ref.click();
      }
    },
    deleteFile() {
      swal({
        text: "¿estas seguro?",
        icon: "error",
        buttons: ["no", "si"],
        dangerMode: true,
      }).then((willDelete) => {
        if (willDelete) {
          this.$emit("deleteFile", this.item.id);
          this.files.splice(this.index, 1);
        }
      });
    },
    validateExtension(item) {
      let extension = item.extension;
      if (extension != "png" && extension != "jpg" && extension != "jpeg") {
        return "/images/file.png";
      }
      return item.url;
    },
    validateExtensionStyle(item) {
      let extension = item.extension;
      if (extension != "png" && extension != "jpg" && extension != "jpeg") {
        return "object-fit: contain;";
      }
      return "";
    },
    openFile(item) {
      let extension = item.extension;
      if (extension != "png" && extension != "jpg" && extension != "jpeg") {
        window.open(item.url);
        return;
      }

      var allwidthscreen = screen.width;

      var width = (allwidthscreen * 80) / 100;

      var allheightscreen = screen.height;

      var height = (allheightscreen * 80) / 100;

      window.open(
        item.url,
        "nuevo",
        "directories=no, location=no, menubar=no, scrollbars=yes, statusbar=no, tittlebar=no, width=" +
          width +
          ", height=" +
          height
      );
    },
  },
};
</script>
<style scoped>
.img {
  width: 100%;
  height: 80px;
  object-fit: cover;
}
.carpetas {
  font-size: 13px;
  font-family: system-ui;
  border-radius: 0px;
}
</style>
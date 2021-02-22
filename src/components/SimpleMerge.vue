<template>
  <div id="simpleMerge" class>
    <h3>Automatic Merging</h3>
    <p>
      With the fully automatic approach to merging you only need to provide the
      two files you want to merge. This can either happen trough our API from
      other platform, such as the FAIRDOMHub, from the command line with cUrl or
      trough the user interface on this page.
    </p>
    <div></div>
    <div v-if="!job">
      <div id="fileUpload" class="row custom-color-3">
        <div id="uploadFirst" class="col-sm-6 custom-color-1">
          <div class="custom-file">
            <input
              type="file"
              class="custom-file-input"
              id="inputGroupFile02"
              ref="file1"
              v-on:change="handleFileUpload1()"
            />
            <label
              class="custom-file-label"
              for="inputGroupFile02"
              aria-describedby="inputGroupFileAddon02"
              >{{ file1.name || "Choose file" }}</label
            >
          </div>
        </div>
        <div id="uploadSecond" class="col-sm-6 custom-color-2">
          <div class="custom-file">
            <input
              type="file"
              class="custom-file-input"
              id="inputGroupFile02"
              ref="file2"
              v-on:change="handleFileUpload2()"
            />
            <label
              class="custom-file-label"
              for="inputGroupFile02"
              aria-describedby="inputGroupFileAddon02"
              >{{ file2.name || "Choose file" }}</label
            >
          </div>
        </div>
      </div>
    </div>
    <div id="debugging" class="bg-warning" v-if="debug">
      <h4>Development out</h4>
      <p>set debug to false to disable the dev output</p>
      <p>jobID: {{ $route.query.jobID }}</p>
      <p>goBack: {{ $route.query.goBack }}</p>
    </div>
    <button
      v-if="!goBackexsists"
      ref="download"
      v-on:click="download"
      type="button"
      class="btn btn-primary btn-lg"
    >
      Download SBML
    </button>
    <button
      v-else
      ref="goBackBtn"
      v-on:click="goBackToOrigin"
      type="button"
      class="btn btn-primary btn-lg"
    >
      Return to Starting Page
    </button>
  </div>
</template>

<script>
export default {
  data() {
    return {
      job: this.$route.query.jobID,
      debug: false,
      goBackexsists: this.$route.query.goBack,
      goBack: decodeURIComponent(this.$route.query.goBack),
      file1: "",
      file2: "",
    };
  },
  computed: {},
  methods: {
    download: function () {
      this.produceSimpleMerge();
    },
    goBackToOrigin: function () {
      this.produceSimpleMerge();

      var regex = /.+?(?=merge_versions|[?#])/;
      alert(this.goBack);
      var backRoute = regex.exec(this.goBack);
      backRoute =
        backRoute +
        "#/?mergedModel=" +
        "/tmp/mergestorage/" +
        this.job +
        "/mergedModel";
      alert(backRoute);
      window.open(backRoute, "_self");
    },
    produceSimpleMerge: function () {
      //var file1 = "/tmp/mergestorage/" + this.job + "/f1";
      //var file2 = "/tmp/mergestorage/" + this.job + "/f2";

      var file1 = this.file1;
      var file2 = this.file2;

      this.submitFiles();
    },
    forceFileDownload(response) {
      const url = window.URL.createObjectURL(new Blob([response.data]));
      const link = document.createElement("a");
      link.href = url;
      link.setAttribute("download", "mergedModel.xml"); //or any other extension
      document.body.appendChild(link);
      link.click();
    },
    handleFileUpload1() {
      this.file1 = this.$refs.file1.files[0];
      console.log(this.$refs.file1.files[0]);
    },
    handleFileUpload2() {
      this.file2 = this.$refs.file2.files[0];
      console.log(this.$refs.file2.files[0]);
    },

    submitFiles() {
      /*
          Initialize the form data
        */
      let formData = new FormData();

      /*
          Iteate over any file sent over appending the files
          to the form data.
        */
      let file = this.file1;
      console.log(file);
      formData.append("file1", file);

      file = this.file2;
      formData.append("file2", file);
      console.log(formData);

      /*
          Make the request to the POST /multiple-files URL
        */
      const axios = require("axios");

      axios
        .post("/bives/simpleMerge.php", formData, {
          headers: {
            "Content-Type": "multipart/form-data",
          },
        })
        .then((response) => {
          console.log(response);
          console.log("ID = " + response.data);
          this.job = response.data;

          const paramsBuild = new URLSearchParams();
          paramsBuild.append("jobID", this.job);
          paramsBuild.append("getFile", "mergedModel");

          axios
            .get("/bives/simpleMerge.php", {
              params: paramsBuild,
            })
            .then((response) => {
              console.log("!!!!!!!!!!" + response.data);
              this.forceFileDownload(response);
            });
        })
        .catch(function (e) {
          console.log("FAILURE!!" + e);
        });
    },
  },
};
</script>

<style>
#fileUpload {
  padding-bottom: 1em;
}
</style>
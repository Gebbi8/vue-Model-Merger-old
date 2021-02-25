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
      <p>jobID: {{ this.job }}</p>
      <p>goBack: {{ $route.query.goBack }}</p>
      <p>devCounter: 8</p>
    </div>
    <button
      v-if="!goBackexsists"
      ref="compute merge"
      v-on:click="computeMerge"
      type="button"
      class="btn btn-primary btn-lg"
    >
      Compute Merge
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
    <button
      v-if="true"
      ref="copy"
      v-on:click="copyURL"
      type="button"
      class="btn btn-primary btn-lg"
    >
      Copy URL to merged model
    </button>
  </div>
</template>

<script>
export default {
  data() {
    return {
      job: this.$route.query.jobID,
      debug: true,
      goBackexsists: this.$route.query.goBack,
      goBack: decodeURIComponent(this.$route.query.goBack),
      file1: "",
      file2: "",
    };
  },
  computed: {},
  methods: {
    download: function () {
      const axios = require("axios");
      //alert("job is set");
      const paramsBuild = new URLSearchParams();
      paramsBuild.append("jobID", this.job);
      paramsBuild.append("getFile", "mergedModel");

      axios
        .get("/bives/simpleMerge.php", {
          params: paramsBuild,
        })
        .then((response) => {
          console.log("Response of get File: \n" + response.data);
          this.forceFileDownload(response);
        });
      this.produceSimpleMerge(false);
    },
    computeMerge: function () {
      const axios = require("axios");
      /*
          Initialize the form data
        */
      let formData = new FormData();

      /*
          Iteate over any file sent over appending the files
          to the form data.
        */
      let file = this.file1;
      formData.append("file1", file);
      file = this.file2;
      formData.append("file2", file);

      /*
        Make the request to the POST /multiple-files URL
      */

      console.log("sending files to bives for merge. returning Job ID");
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

          /* //splitting up the method into something mor modular
          const paramsBuild = new URLSearchParams();
          paramsBuild.append("jobID", this.job);
          paramsBuild.append("getFile", "mergedModel");
          alert("check");
          axios
            .get("/bives/simpleMerge.php", {
              params: paramsBuild,
            })
            .then((response) => {
              console.log("Response: " + response.data);
              //this.forceFileDownload(response);
              this.job = response.data;
            }); */
        })
        .catch(function (e) {
          console.log("FAILURE!!" + e);
        });
    },
    copyURL: function () {
      // Create new element
      var el = document.createElement("textarea");
      // Set value (string to be copied)
      var baseURL =
        "https://merge-proto.bio.informatik.uni-rostock.de/bives/simpleMerge.php?getFile=";

      el.value = baseURL + "/tmp/mergestorage/" + this.job + "/mergedModel";

      // Set non-editable to avoid focus and move outside of view
      el.setAttribute("readonly", "");
      el.style = { position: "absolute", left: "-9999px" };
      document.body.appendChild(el);
      // Select text inside element
      el.select();
      // Copy text to clipboard
      document.execCommand("copy");
      // Remove temporary element
      document.body.removeChild(el);

      /* Get the text field */

      /*       /* Select the text field */
      //copyText.select();
      //copyText.setSelectionRange(0, 99999); /* For mobile devices */

      /* Copy the text inside the text field 
      document.execCommand("copy"); */
    },
    goBackToOrigin: function () {
      console.log("calling goBackToOrigin");

      this.produceSimpleMerge(true);

      var regex = /.+?(?=merge_versions|[?#])/;
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
    produceSimpleMerge: function (isExternal) {
      var file1;
      var file2;

      if (isExternal) {
        console.log("external request " + this.job);
        file1 = "/tmp/mergestorage/" + this.job + "/f1";
        file2 = "/tmp/mergestorage/" + this.job + "/f2";
      } else {
        console.log("internal request ");
        file1 = this.file1;
        file2 = this.file2;
      }

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
  },
};
</script>

<style>
#fileUpload {
  padding-bottom: 1em;
}
</style>
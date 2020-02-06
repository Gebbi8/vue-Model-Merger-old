<template>
  <div id="simpleMerge" class>
    <h3>Automatic Merging</h3>
    <p>With the fully automatic approach to merging you only need to provide the two files you want to merge. This can either happen trough our API from other platform, such as the FAIRDOMHub, from the command line with cUrl or with troug the user interface.</p>
    <div></div>
    <div v-if="!job">
      <div id="fileUpload" class="row custom-color-3">
        <div id="uploadFirst" class="col-sm-6 custom-color-1">
          <div class="custom-file">
            <input type="file" class="custom-file-input" id="inputGroupFile02" />
            <label
              class="custom-file-label"
              for="inputGroupFile02"
              aria-describedby="inputGroupFileAddon02"
            >Choose file</label>
          </div>
        </div>
        <div id="uploadSecond" class="col-sm-6 custom-color-2">
          <div class="custom-file">
            <input type="file" class="custom-file-input" id="inputGroupFile02" />
            <label
              class="custom-file-label"
              for="inputGroupFile02"
              aria-describedby="inputGroupFileAddon02"
            >Choose file</label>
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
    >Download SBML</button>
    <button
      v-else
      ref="goBackBtn"
      v-on:click="goBackToOrigin"
      type="button"
      class="btn btn-primary btn-lg"
    >Return to Starting Page</button>
  </div>
</template>

<script>
export default {
  data() {
    return {
      job: this.$route.query.jobID,
      debug: false,
      goBackexsists: this.$route.query.goBack,
      goBack: decodeURIComponent(this.$route.query.goBack)
    };
  },
  computed: {},
  methods: {
    download: function() {
      this.produceSimpleMerge();
    },
    goBackToOrigin: function() {
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
    produceSimpleMerge: function() {
      var file1 = "/tmp/mergestorage/" + this.job + "/f1";
      var file2 = "/tmp/mergestorage/" + this.job + "/f2";

      // Make a request for a user with a given ID
      var bivesJob = {
        files: [file1, file2],
        commands: ["merge"],
        jobID: [this.job]
      };

      const axios = require("axios");
      axios
        .post("/bives/bives.php", "postParams=" + JSON.stringify(bivesJob))
        .then(response => {
          console.log(response);
          this.forceFileDownload(response);
        });
    },
    forceFileDownload(response) {
      const url = window.URL.createObjectURL(new Blob([response.data]));
      const link = document.createElement("a");
      link.href = url;
      link.setAttribute("download", "mergedModel.xml"); //or any other extension
      document.body.appendChild(link);
      link.click();
    }
  }
};
</script>

<style>
#fileUpload {
  padding-bottom: 1em;
}
</style>
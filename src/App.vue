<template>
  <div id="app" class="d-flex flex-column h-100">
    <div class="row custom-color-1" id="head">
      <div class="container">
        <div class="row">
          <h1 class="col-8">Model Merger</h1>
          <div class="col-4 d-flex align-items-center justify-content-end">
            <button
              type="button"
              class="btn btn-primary btn-sm"
              v-on:click="btnSwitchView"
              v-if="debug"
            >
              Switch Mode
            </button>
          </div>
        </div>
      </div>
    </div>
    <div class="row custom-color-2" id="intro">
      <div class="container">
        <div class="row justify-content-center">
          <p class="col-8">
            Model development is often a non-linear and decentralised process.
            It requries time consuming effort to gather the necessary
            information from literature, experimental data and databases. The
            information are either collected in tables or directly put into a
            model using common tools (e. g. CellDesigner). Subseqeuntly, the
            collaborative nature of modelling requires one partner to collect
            and combine the gathered information. Our merging tool aims to ease
            this process by enabling the combination of two model versions.
          </p>
        </div>
      </div>
    </div>
    <div class="row custom-color-2" id="center">
      <div class="container">
        <app-usermerge v-if="!simple"></app-usermerge>
        <app-simpleMerge v-else></app-simpleMerge>
      </div>
    </div>
    <div class="row footer mt-auto custom-color-3" id="foot">
      <div class="container">
        <!--Image-->
        <div class="row">
          <div class="col-2 d-flex align-items-center">
            <a
              href="https://integrative-pathway-models.de/"
              title="INCOME-Website"
            >
              <img src="./assets/csm_logo_INCOME11.png" class="img-fluid" alt />
            </a>
          </div>
          <div class="col-10 d-flex align-items-end flex-column" id="contact">
            <h5>Contact</h5>
            <p>Tom Gebhardt</p>
            <p>tom.gebhardt@uni-rostock.de</p>
            <p>https://www.sbi.uni-rostock.de/</p>
          </div>
        </div>
        <div class="row">
          <div class="col-12 d-flex justify-content-start">
            The project was funded by the BMBF (FKZ: 01ZX1705C)
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import UserMerge from "./components/UserMerge.vue";
import SimpleMerge from "./components/SimpleMerge.vue";

export default {
  name: "app",
  data() {
    return {
      simple: true,
      job: this.$route.query.jobID,
    };
  },
  methods: {
    btnSwitchView: function () {
      this.simple = !this.simple;
    },
  },
};
</script>

<style scoped>
.custom-color-1 {
  background-color: #365b9e;
  color: #feffff;
}

.custom-color-2 {
  background-color: #e5e5e5;
  color: #17252a;
}

.custom-color-3 {
  background-color: white;
  color: #17252a;
}

#intro {
  padding-top: 1rem;
}

#center {
  padding-bottom: 1em;
}

#head {
  padding-top: 1em;
  padding-bottom: 1rem;
}

#foot {
  padding-top: 1em;
  padding-bottom: 1em;
}

#contact > p {
  margin-bottom: auto;
}
</style>
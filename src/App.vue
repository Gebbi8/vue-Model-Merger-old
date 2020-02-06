<template>
  <div id="app" class="container-fluid">
    <div class="row custom-color-1" id="head">
      <div class="container">
        <div class="row">
          <h1 class="col-8">Model Merger</h1>
          <div class="col-4 d-flex align-items-center justify-content-end">
            <button
              type="button"
              class="btn btn-primary btn-sm"
              v-on:click="btnSwitchView"
            >Switch Mode</button>
          </div>
        </div>
      </div>
    </div>
    <div class="row custom-color-2">
      <div class="container">
        <div class="row justify-content-center">
          <p class="col-8">
            Model development is often a non-linear process and happens on different levels of granularity.
            A model can focus on a few molecular interactions, specifc diseases, systems ore even the whole body.
            A lot literature and experimental based information has to be gathered to build a model.
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
    <div class="row custom-color-3" id="foot">
      <div class="container">
        <!--Image-->
        <div class="row">
          <div class="col-2 d-flex align-items-center">
            <a href="https://integrative-pathway-models.de/" title="INCOME-Website">
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
          <div
            class="col-12 d-flex justify-content-start"
          >The project was funded by the BMBF (FKZ: 01ZX1705C)</div>
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
      job: this.$route.query.jobID
    };
  },
  methods: {
    btnSwitchView: function() {
      this.simple = !this.simple;
    }
  }
};
</script>

<style scoped>
.custom-color-1 {
  background-color: #3aafa9;
  color: #feffff;
}

.custom-color-2 {
  background-color: #3aafa9;
  color: #17252a;
}

.custom-color-3 {
  background-color: #2b7a78;
  color: #17252a;
}

#center {
  padding-bottom: 1em;
}

#head {
  padding-top: 1em;
}

#foot {
  padding-top: 1em;
  padding-bottom: 1em;
}
</style>
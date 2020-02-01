<template>
  <div id="app">
    <button v-if="job" type="button" class="btn btn-primary btn-sm" v-on:click="btnSwitchView">Switch Mode</button>
    <app-usermerge v-if="simple && job"></app-usermerge>
    <app-simpleMerge v-else></app-simpleMerge>
  </div>
</template>

<script>
import UserMerge from "./components/UserMerge.vue";
import SimpleMerge from "./components/SimpleMerge.vue";


export default {
  name: "app",
  data() {
    return {
      simple: false,
      job: this.$route.query.jobID
    }
  },
  methods:{
    btnSwitchView: function(){
      this.simple = !this.simple;
    }
  }
}
</script>

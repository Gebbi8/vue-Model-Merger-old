<template>
    <div id="user-merge">
      <div id="generalInfo">
        <h3>Semi-Automatic Merging</h3>
        <h4>Still under development</h4>
        <p>You are in the semi-automatic merging mode. This means you can cherry-pick the changes you want to apply. To base the merging on one model and add parts, choose a main model. </p>
      </div>


        <app-carousel :decArr="decisionArr" @arrChanged="decisionArr = $event" @slideChange="currentSlide = $ent" @gotOldDoc="oldDocument = $event" @gotNewDoc="newDocument = $event"/> 
        <app-selection :decArr="decisionArr" :slideChng="currentSlide" :oldDoc="oldDocument" :newDoc="newDocument"/>
    </div>
</template>


<script>

import Carousel from "./Carousel.vue";
import Selection from "./Selection.vue";


export default {


  name: "user-merge",
  components: {
    Carousel,
    Selection,
  },
  data() {
    return {
      decisionArr: [],
      currentSlide: 0,
      oldDocument: null,
      newDocument: null
    }
  },
  watch: {
    currentSlide: {
      handler: function(){
      }
    },
    decisionArr: {
      handler: function(){
      }
    },
    newDocument: {
      handler: function(){
      }
    }
  },
    mounted() {
    this.$root.$on("arrChanged", data => {
      this.decisionArr = data;
      console.log(data)
    });
    this.$root.$on("slideChange", data => {
      this.currentSlide = data;
      console.log(data);
    });
    this.$root.$on("gotOldDoc", data => {
      this.oldDocument = data;
      console.log(data);
    });
    this.$root.$on("gotNewDoc", data => {
      this.newDocument = data;
      console.log(data);
    });
  }
};
</script>
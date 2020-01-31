<template>
  <div id="app">
    <app-carousel :decArr="decisionArr" @arrChanged="decisionArr = $event" @slideChange="currentSlide = $event" @gotOldDoc="oldDocument = $event" @gotNewDoc="newDocument = $event"/> 
    <app-selection :decArr="decisionArr" :slideChng="currentSlide" :oldDoc="oldDocument" :newDoc="newDocument"/>
    <app-simpleMerge></app-simpleMerge>
  </div>
</template>

<script>




import Carousel from "./components/Carousel.vue";
import Selection from "./components/Selection.vue";
import SimpleMerge from "./components/SimpleMerge.vue";

export default {


  name: "app",
  components: {
    Carousel,
    Selection,
    SimpleMerge
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
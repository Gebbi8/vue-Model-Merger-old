import Vue from 'vue'
import App from './App.vue'
import 'bootstrap'
import 'bootstrap/dist/css/bootstrap.min.css'

import Carousel from './components/Carousel.vue'
import Selection from './components/Selection.vue'

import mathjax from 'mathjax';


MathJax.Hub.Config({
  root: "extensions/",
  showProcessingMessages: true,
  extensions: ["TeX-AMS-MML_HTMLorMML.js"]

});

MathJax.Hub.Register.MessageHook("Math Processing Error",function (message) {
  //  do something with the error.  message[2] is the Error object that records the problem.
  console.log("Error", message);
});

MathJax.Hub.Startup.signal.Interest(
  //function (message) {alert("Startup: "+message)}
);
MathJax.Hub.signal.Interest(
  //function (message) {alert("Hub: "+message)}
);


import hljs from 'highlight.js';


Object.defineProperty(Vue.prototype, '$mathjax', { value: mathjax });
Object.defineProperty(Vue.prototype, '$hljs', { value: hljs });






Vue.component('app-carousel', Carousel);
Vue.component('app-selection', Selection);

Vue.config.productionTip = false

new Vue({
  render: h => h(App),
}).$mount('#app')
<template>
  <div>
    <div>Current Slide: {{ slide }}</div>
    <div id="carouselExampleControls" class="carousel" data-interval="false">
      <div class="carousel-inner">
        <div
          v-for="(change, idx) in changes"
          :key="idx"
          v-bind:class="{'active': idx == 0, 'carousel-item': true}"
        >
        <p>{{ change.type }}</p>
          <div v-if="change.single">

            <div v-if="change.math">
              
              <div> handle math</div>
              <div ref="mathJax" v-html="change.xmlSnippet">
                <!-- {{change.xmlSnippet}} -->
              </div>
            </div>
            <div v-else>
              
              <pre><code ref="xml"> {{ change.xmlSnippet }} </code></pre>
            </div>
          </div>
          <div v-else>
             
            <div v-if="change.math">
              <!-- handle math -->
              <div ref="mathJax" v-html="change.xmlSnippetA"></div>
              <div ref="mathJax" v-html="change.xmlSnippetB"></div>
            </div>
            <div v-else>
             
              <pre><code ref="xml">{{ change.xmlSnippetA }}</code></pre>
              <pre><code ref="xml">{{ change.xmlSnippetB }}</code></pre>
            </div>
          </div>
        </div>
      </div>
      <a
        class="carousel-control-prev"
        href="#carouselExampleControls"
        role="button"
        v-on:click="prevSlide"
        data-slide="prev"
      >
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="sr-only">Previous</span>
      </a>
      <a
        class="carousel-control-next"
        href="#carouselExampleControls"
        role="button"
        v-on:click="nextSlide"
        data-slide="next"
      >
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="sr-only">Next</span>
      </a>
    </div>
  </div>
</template>


<script>
export default {
  data() {
    return {
      changes: [],
      slide: 0,
      slideNum: -1,
      sliding: null,
      oldDoc: null,
      newDoc: null,
      xsl: null
    };
  },

  watch: {
    slide: {
      handler: function() {}
    }
  },

  methods: {
    nextSlide() {
      if (this.slideNum == this.slide) this.slide = 0;
      else this.slide++;
      this.$root.$emit("slideChange", this.slide);
    },
    prevSlide() {
      if (this.slide == 0) this.slide = this.slideNum;
      else this.slide--;
      this.$root.$emit("slideChange", this.slide);
    },

    computeChanges: function(xmlDocDiff, sbmlDocOld, sbmlDocNew) {
      var xDiff, xSbml;

      var deletes = xmlDocDiff.evaluate(
        "/bives/delete/*",
        xmlDocDiff,
        null,
        XPathResult.ANY_TYPE,
        null
      );
      var inserts = xmlDocDiff.evaluate(
        "/bives/insert/*",
        xmlDocDiff,
        null,
        XPathResult.ANY_TYPE,
        null
      );
      var updates = xmlDocDiff.evaluate(
        "/bives/update/*",
        xmlDocDiff,
        null,
        XPathResult.ANY_TYPE,
        null
      );
      var moves = xmlDocDiff.evaluate(
        "/bives/move/*",
        xmlDocDiff,
        null,
        XPathResult.ANY_TYPE,
        null
      );
      this.addChangesChoice(updates, "update", sbmlDocOld, sbmlDocNew);
      this.addChangesChoice(moves, "move", sbmlDocOld, sbmlDocNew);
      this.addChanges(deletes, "delete", sbmlDocOld);
      this.addChanges(inserts, "insert", sbmlDocNew);

    },

    computeSlideElement: function(changePath, type, sbmlDoc){         //lastNoNamespace, 
          // get Element deping on weather it is a delete or insert
          var noNamespace;
            noNamespace = this.getLocalXPath(changePath);

          //check if the change contains math
            //What if it contains Math in a Sub-Node??


          var mathIndex = noNamespace.indexOf("/*[local-name()='math']");
          var mathFlag = false;

          if (mathIndex >= 0) {
            mathFlag = true;
            //get Path to math
            var helpString = noNamespace.substr(mathIndex + 23);
            noNamespace =
              noNamespace.substr(0, mathIndex + 23) +
              helpString.substr(0, helpString.indexOf("/"));
          }
          
          var xPathR;
          var xmlSnippet;

          // Did the XPath change?
          // .evaluate will not reset if the same xpath is used
              //it probably will in a function like this!?

//          if (lastNoNamespace != noNamespace) { //always true for the first one
//            lastNoNamespace = noNamespace;

            xPathR = sbmlDoc.evaluate(
              noNamespace,
              sbmlDoc,
              null,
              XPathResult.ANY_TYPE,
              null
            );

            //get element from Xpath Result. Has to be interated although it is only one result
            xmlSnippet = xPathR.iterateNext();
//          }

          //convert math
          if (mathFlag) {
            
            //convert content MathML to presentation MathML with XSLT
            var xsltProc = new XSLTProcessor();
            //the XSL is loaded with axios
            xsltProc.importStylesheet(this.xsl.data);

            //transform the the Snippet 
            var transformed = xsltProc.transformToFragment(xmlSnippet, document);
            xmlSnippet = transformed.firstChild.outerHTML;

            // MathML fix: Operators like 'invisible multiplicator' are not well rendered due to the &
            xmlSnippet = xmlSnippet.replace(/\&amp;/g, "&");

          } else if (xmlSnippet.nodeName == "#text") {
            xmlSnippet = xmlSnippet.data;
          } else {
            xmlSnippet = xmlSnippet.outerHTML;
          }

          return {single: true, type: type, decision: -1, math: mathFlag, xmlSnippet: xmlSnippet}; //, lastNoNamespace: lastNoNamespace};


    },

    addChanges: function(xPathResult, type, sbmlDoc) {
      var lastNoNamespace = "";      
      var change = xPathResult.iterateNext();

      while (change != null) {
        if (!change.attributes.triggeredBy) {
          var changePath;
          if(type == 'delete'){
            changePath = change.attributes.oldPath.value;
          } else {
            changePath = change.attributes.newPath.value;
          }
          var slideElement = this.computeSlideElement(changePath, type, sbmlDoc);   //lastNoNamespace, 
          this.changes.push({
             single: true,
             type: type,
             decision: -1,
             math: slideElement.math,
             xmlSnippet: slideElement.xmlSnippet
           });
          this.slideNum++;
          //lastNoNamespace = slideElement.lastNoNamespace;
        }
        change = xPathResult.iterateNext();
      }

      //pass array back to the parent component

      this.$root.$emit("arrChanged", this.changes);
    },

    addChangesChoice: function(xPathResult, type, sbmlDocOld, sbmlDocNew) {
        var change = xPathResult.iterateNext();
       // var lastNoNamespace = "";
       while (change != null) {
         if (!change.attributes.triggeredBy) {
          var slideElementA = this.computeSlideElement(change.attributes.oldPath.value, type, sbmlDocOld); //lastNoNamespace, 

          var slideElementB = this.computeSlideElement(change.attributes.newPath.value, type, sbmlDocNew); //lastNoNamespace, 


           this.changes.push({
             single: false,
             type: type,
             decision: -1,
             math: slideElementA.math,
             xmlSnippetA: slideElementA.xmlSnippet,
             xmlSnippetB: slideElementB.xmlSnippet
           });
           this.slideNum++;
          // lastNoNamespace = slideElementA.lastNoNamespace;
         }

         change = xPathResult.iterateNext();
       }
      //pass array back to the parent component
       this.$root.$emit("arrChanged", this.changes);
    },

    getLocalXPath: function(path) {
      var pathArray;
      var returnPath = "";

      path = path.substr(1);
      pathArray = path.split("/");
      for (var j = 0; j < pathArray.length; j++) {
        var splitArr = pathArray[j].split("[");

        if (splitArr[0] == "text()") {
          //add special cases
          returnPath += "/" + splitArr[0] + "[" + splitArr[1];
        } else
          returnPath += "/*[local-name()='" + splitArr[0] + "'][" + splitArr[1];
      }
      //returnPath += '/*:' + pathArray[pathArray.length-1];
      return returnPath;
    },

    loadData() {

      const axios = require("axios");
      var file1 =  //"https://scratch.binfalse.de/diss/version1.xml";
      //"http://most.sems.uni-rostock.de/resources/d3d3LmViaS5hYy51ay9iaW9tb2RlbHMtbWFpbi8=/L0JJT01EMDAwMDAwMDQ3NQ==/MjAxMy0xMS0wNA==";
      //"https://budhat.bio.informatik.uni-rostock.de/download?downloadModel=24";
      //"https://most.bio.informatik.uni-rostock.de/resources/ZGF0YS5iaW8uaW5mb3JtYXRpay51bmktcm9zdG9jay5kZS9CZW5jaG1hcmstTW9kZWxzL0JlbmNobWFyay1Nb2RlbHMv/L0Z1aml0YV9TY2lTaWduYWwyMDEw/bW9kZWwxX2RhdGExX2wydjQ=";

      //two changes of each type
      //"http://most.sems.uni-rostock.de/resources/d3d3LmViaS5hYy51ay9iaW9tb2RlbHMtbWFpbi8=/L0JJT01EMDAwMDAwMDA4NQ==/MjAwNy0wNi0wNQ==";

      //small math example
      "http://most.sems.uni-rostock.de/resources/bW9kZWxzLmNlbGxtbC5vcmcvd29ya3NwYWNlLzI0Yy8=/L2RpZnJhbmNlc2NvX25vYmxlXzE5ODUuY2VsbG1s/MzgzOGQxZDI2ZjRlMDY4ZTdlOGZmMDlhOWIxNDk4OWNlZGM1YzdlNw==";

      //math example
      //"http://most.sems.uni-rostock.de/resources/d3d3LmViaS5hYy51ay9iaW9tb2RlbHMtbWFpbi8=/L0JJT01EMDAwMDAwMDA1MQ==/MjAwNy0wNi0wNQ=="
      var file2 =  //"https://scratch.binfalse.de/diss/version2.xml";
      //"http://most.sems.uni-rostock.de/resources/d3d3LmViaS5hYy51ay9iaW9tb2RlbHMtbWFpbi8=/L0JJT01EMDAwMDAwMDQ3NQ==/MjAxNC0wNC0xMQ==";
      //"https://budhat.bio.informatik.uni-rostock.de/download?downloadModel=25";
       //"https://most.bio.informatik.uni-rostock.de/resources/ZGF0YS5iaW8uaW5mb3JtYXRpay51bmktcm9zdG9jay5kZS9CZW5jaG1hcmstTW9kZWxzL0JlbmNobWFyay1Nb2RlbHMv/L0Z1aml0YV9TY2lTaWduYWwyMDEw/bW9kZWwxX2RhdGEyX2wydjQ=";


      //two changes of each type;
      //"http://most.sems.uni-rostock.de/resources/d3d3LmViaS5hYy51ay9iaW9tb2RlbHMtbWFpbi8=/L0JJT01EMDAwMDAwMDA4NQ==/MjAwNy0wOS0yNQ==";


      //small math example
      "http://most.sems.uni-rostock.de/resources/bW9kZWxzLmNlbGxtbC5vcmcvd29ya3NwYWNlLzI0Yy8=/L2RpZnJhbmNlc2NvX25vYmxlXzE5ODUuY2VsbG1s/NGViZWZjMzM0ZmRiYWE1NTAyMzE2MDczZWRiNDNhN2VmYTI4ZWYwNg==";

       //math example
       "http://most.sems.uni-rostock.de/resources/d3d3LmViaS5hYy51ay9iaW9tb2RlbHMtbWFpbi8=/L0JJT01EMDAwMDAwMDA1MQ==/MjAwNy0wOS0yNQ=="
      
      // Make a request for a user with a given ID
      var bivesJob = {
        files: [file1, file2],
        commands: ["xmlDiff"]
      };


      //config mathjax


      axios
        .post("/bives/bives.php", "bivesJob=" + JSON.stringify(bivesJob))
        .then(response => {
          //get first doc
          axios({
            method: "get",
            url: "/bives/docs.php?" + "url=" + file1,
            responseType: "text/xml"
          }).then(doc1 => {
            //get second doc
            axios({
              method: "get",
              url: "/bives/docs.php?" + "url=" + file2,
              responseType: "text/xml"
            })
              .then(doc2 => {
                // handle success
                var parser = new DOMParser();
                var xmlDocDiff = parser.parseFromString(
                  response.data.xmlDiff,
                  "text/xml"
                );
                var xmlDoc1 = parser.parseFromString(doc1.data, "text/xml");
                var xmlDoc2 = parser.parseFromString(doc2.data, "text/xml");
                this.oldDoc = xmlDoc1;
                this.newDoc = xmlDoc2;
                //console.log(doc1.data);
                //console.log(this.oldDoc);
                this.computeChanges(xmlDocDiff,  this.oldDoc, this.newDoc);
                this.$root.$emit("gotOldDoc", this.oldDoc);
                this.$root.$emit("gotNewDoc", this.newDoc);
               // console.log("Doc with the Diff:", xmlDocDiff);
                
              })
                .then(() => {

                      
                     // console.log(xsl);
                                         //alert("test");



                  this.$nextTick(() => {
                    this.$hljs.configure({ languages: ["xml"] });
                    //this.$hljs.configure({useBR: true});
                    this.$refs.xml.forEach((block, id) => {
                 //     console.log("highlighting block# ", id);
                 //     console.log("block ", block);
                      this.$hljs.highlightBlock(block);
                    });

                    this.$refs.mathJax.forEach((block, id) => {
                      MathJax.Hub.Queue(["Typeset", MathJax.Hub, block]);
                  });
                 });
                });
          });
        })
        .catch(function(error) {
          // handle error
          alert("error");
          console.log(error);
        })
        .then(function() {
          // always executed
        });
    }
  },

  //  computed() {
  // //   //$mathjax.
  // //   renderClass: function () {
  // //     console.log(this);
  // //     return;
  // //   }

  // //   MathJax.Hub.Queue(["Typeset",MathJax.Hub]);

  // //   console.log(this.$el);
  //   // $('pre code').last().each(function(i, block) {
  //   //   hljs.highlightBlock(block);
  //   // });

  // },

  beforeCreate(){
                  //XSL Transformer
                  const axios = require("axios"); 
                  axios({
                    method: "get",
                    url: "/src/xslt/ctop2.xsl",
                    responseType: "document"
                  })
                    .then( xsl => {
                      this.xsl = xsl;
                    });

  },

  mounted() {
    this.loadData();
  }
};
</script>

<style>
.carousel-inner {
  width: 80%;
  margin: auto;
}
.carousel-control-next,
.carousel-control-prev {
  width: 10%;
}

.carousel-control-prev-icon {
  background-image: url("data:image/svg+xml;charset=utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='007bff' viewBox='0 0 8 8'%3E%3Cpath d='M5.25 0l-4 4 4 4 1.5-1.5-2.5-2.5 2.5-2.5-1.5-1.5z'/%3E%3C/svg%3E");
}

.carousel-control-next-icon {
  background-image: url("data:image/svg+xml;charset=utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='007bff' viewBox='0 0 8 8'%3E%3Cpath d='M2.75 0l-1.5 1.5 2.5 2.5-2.5 2.5 1.5 1.5 4-4-4-4z'/%3E%3C/svg%3E");
}

.container-fluid {
  padding-left: 0;
  padding-right: 0;
}

.main {
  margin-bottom: 20px;
}
.progress {
  margin-bottom: 5px;
}

.decisionCarousel > .carousel-caption {
  position: initial;
}

/* style for code highlights */
/*

Atom One Light by Daniel Gamage
Original One Light Syntax theme from https://github.com/atom/one-light-syntax

base:    #fafafa
mono-1:  #383a42
mono-2:  #686b77
mono-3:  #a0a1a7
hue-1:   #0184bb
hue-2:   #4078f2
hue-3:   #a626a4
hue-4:   #50a14f
hue-5:   #e45649
hue-5-2: #c91243
hue-6:   #986801
hue-6-2: #c18401

*/

.hljs {
  display: block;
  overflow-x: auto;
  padding: 0.5em;
  color: #383a42;
  background: #fafafa;
}

.hljs-comment,
.hljs-quote {
  color: #a0a1a7;
  font-style: italic;
}

.hljs-doctag,
.hljs-keyword,
.hljs-formula {
  color: #a626a4;
}

.hljs-section,
.hljs-name,
.hljs-selector-tag,
.hljs-deletion,
.hljs-subst {
  color: #e45649;
}

.hljs-literal {
  color: #0184bb;
}

.hljs-string,
.hljs-regexp,
.hljs-addition,
.hljs-attribute,
.hljs-meta-string {
  color: rgb(80, 161, 79);
}

.hljs-built_in,
.hljs-class .hljs-title {
  color: #c18401;
}

.hljs-attr,
.hljs-variable,
.hljs-template-variable,
.hljs-type,
.hljs-selector-class,
.hljs-selector-attr,
.hljs-selector-pseudo,
.hljs-number {
  color: #986801;
}

.hljs-symbol,
.hljs-bullet,
.hljs-link,
.hljs-meta,
.hljs-selector-id,
.hljs-title {
  color: #4078f2;
}

.hljs-emphasis {
  font-style: italic;
}

.hljs-strong {
  font-weight: bold;
}

.hljs-link {
  text-decoration: underline;
}

.hljs span span {
  opacity: 0.5;
}
.hljs .change {
  opacity: 1;
  font-weight: bold;
}
</style>
<template>
  <div>
    <div class="btn-toolbar justify-content-center">
      <div
        v-if="isSingleDif"
        class="btn-group-vertical btn-group-toggle leftBtns"
        id="bgSingle"
        data-toggle="buttons"
      >
        <label
          key='keep-label'
          v-on:click="btnKeep"
          :class='{"btn": true, "btn-outline-primary": true, "choiceBtns": true, "active": keepActive}'
        >
          <input type="radio" name="options">Keep
        </label>
        <label
          key='dsicard-label'
          v-on:click="btnDiscard"
          :class='{"btn": true, "btn-outline-primary": true, "choiceBtns": true, "active": discardActive}'
        >
          <input type="radio" name="options">Discard
        </label>
      </div>
      <div
        v-else
        class="btn-group-vertical btn-group-toggle leftBtns"
        id="bgChoice"
        data-toggle="buttons"
      >
        <label
          key='fromA-label'
          v-on:click="btnFromA"
          :class='{"btn": true, "btn-outline-primary": true, "choiceBtns": true, "active": fromAActive}'
        >
          <input type="radio" name="options">From Model A
        </label>
        <label
          key='fromB-label'
          
          v-on:click="btnFromB"
          :class='{"btn": true, "btn-outline-primary": true, "choiceBtns": true, "active": fromBActive}'
        >
          <input type="radio" name="options">From Model B
        </label>
      </div>
    </div>

    <div class="btn-toolbar justify-content-center">
      <div class="btn-group-vertical btn-group-toggle" id="bgAll" data-toggle="buttons">
        <label v-on:click="btnAllA" class="btn btn-outline-primary choiceBtns">
          <input type="radio" name="options">Model A
        </label>
        <label v-on:click="btnAllB" class="btn btn-outline-primary choiceBtns">
          <input type="radio" name="options">Model B
        </label>
      </div>
    </div>

    <div class="progress">
      <div
        ref="progressBar"
        class="progress-bar bg-success"
        role="progressbar"
        aria-valuenow="0"
        aria-valuemin="0"
        aria-valuemax="100"
        style="width:0%"
      ></div>
    </div>
    <button
      ref="downloadBtn"
      v-on:click="downloadSBML"
      type="button"
      class="btn btn-success btn-lg btn-block"
      v-bind:class="{'disabled': disabledDownload}"
    >Download SBML</button>
  </div>
</template>

<script>
export default {
  props: ["decArr", "slideChng", "oldDoc", "newDoc"],
  data() {
    return {
      isSingleDif: true,


      keepActive: false,
      discardActive: false,

      fromAActive: false,
      fromBActive: false,

      allAActive: false,
      allBActive: false,

      changes: this.decArr,
      slide: this.slideChng,
      sliding: null,

      disabledDownload: true
    };
  },

  methods: {
    btnKeep: function() {
      this.changes[this.slide].decision = 0;
    },
    btnDiscard: function() {
      this.changes[this.slide].decision = 1;
    },
    btnFromA: function() {
      this.changes[this.slide].decision = 0;
    },
    btnFromB: function() {
      this.changes[this.slide].decision = 1;
    },
    downloadSBML: function() {
      //works always with the second document as main
      //use .ownerDocument to get doc. best from newDoc

      //do all operations use: removeChild and replace... see w3...
      //iterate through decision array
      for (var i = 0; i < this.changes.length; i++) {
        if (this.changes[i].type == "delete" && this.changes[i].decision == 0) {
          //add to the file if the user selected to keep it
          addDeleted(
            this.changes[i].xmlSnippetA,
            this.changes[i].xmlSnippetB,
            this.newDoc
          );
        } else if (
          this.changes[i].type == "insert" &&
          this.changes[i].decision == 1
        ) {
          //delete from the file if the user selected to keep it
          this.changes[i].xmlSnippetA.parentNode.removeChild(
            this.changes[i].xmlSnippetA
          );
        } else if (
          this.changes[i].type == "move" &&
          this.changes[i].decision == 0
        ) {
          // delete moved node from document and insert the old node
          //remove node from newparent
          decisiochangesnArr[i].xmlSnippetA.parentNode.removeChild(
            this.changes[i].xmlSnippetA
          );
          //add to oldParent
          addDeleted(
            this.changes[i].xmlSnippetA,
            this.changes[i].xmlSnippetB,
            this.newDoc
          );
        } else if (
          this.changes[i].type == "update" &&
          this.changes[i].decision == 0
        ) {
          // delete moved node from document and insert the old node
          this.changes[i].xmlSnippetB.parentNode.replaceChild(
            this.changes[i].xmlSnippetA,
            this.changes[i].xmlSnippetB
          ); //replaced decisionArr[i][3] with decisionArr[i][2]
        }
      }

      //check and download

      let blob = new Blob([new XMLSerializer().serializeToString(this.newDoc)], { type: "text/xml;charset=utf-8" }); //xmlDocSbmlNew
      //saveAs(blob, "SBML-Merge.xml"); //TODO: put in a useful name
      //let blob = new Blob([jsonData], { type: 'text/plain;charset=utf-8;' })
      if (navigator.msSaveBlob) { // IE 10+
        navigator.msSaveBlob(blob, "sbml-merge.xml")
      } else {
        let link = document.createElement('a')
        if (link.download !== undefined) { // feature detection
          // Browsers that support HTML5 download attribute
          let url = URL.createObjectURL(blob)
          link.setAttribute('href', url)
          link.setAttribute('download', "sbml-merge.xml")
          link.style.visibility = 'hidden'
          document.body.appendChild(link)
          link.click()
          document.body.removeChild(link)
        }
      }

    },



    saveFile: function() {
      const data = JSON.stringify(this.arr)
      const blob = new Blob([data], {type: 'text/plain'})
      const e = document.createEvent('MouseEvents'),
      a = document.createElement('a');
      a.download = "test.json";
      a.href = window.URL.createObjectURL(blob);
      a.dataset.downloadurl = ['text/json', a.download, a.href].join(':');
      e.initEvent('click', true, false, window, 0, 0, 0, 0, 0, false, false, false, false, 0, null);
      a.dispatchEvent(e);
    },



    btnAllA: function() {
      for (var i = 0; i < this.changes.length; i++) {
        if (
          this.changes[i][0] == "delete" ||
          this.changes[i][0] == "move" ||
          this.changes[i][0] == "update"
        ) {
          this.changes[i][1] = 0;
        }
      }
      //updateBtns(this.changes[this.slide].type, this.changes[this.slide].decision);
    },
    btnAllB: function() {
      for (var i = 0; i < changes.length; i++) {
        if (this.changes[i].type == "insert") {
          this.changes[i].decision = 0;
        } else if (
          this.changes[i].type == "move" ||
          this.changes[i].type == "update"
        ) {
          this.changes[i].decision = 1;
        }
      }
      //updateBtns(this.changes[this.slide].type, this.changes[this.slide].decision);
    },

    updateBtns: function(type, decision) {
      //hide and show relevant buttons

        this.keepActive = false;
        this.discardActive = false;
        this.fromAActive = false;
        this.fromBActive = false;

      if (type == "insert" || type == "delete") {
        this.isSingleDif = true;  
       
        if (decision == 0) {
          this.keepActive = true;
        } else if (decision == 1){
          this.discardActive = true;
        }
      } else {
        this.isSingleDif = false;
        if (decision == 0) {
          this.fromAActive = true;
        } else if (decision == 1){
          this.fromBActive = true;
        }
      } 
    },
  },

  watch: {
    changes: {
      handler: function() {
        var sumDecided = 0;
        var disable = false;
        for (var i = 0; i < this.changes.length; i++) {
          if (this.changes[i].decision != -1) {
            sumDecided++;
          } else disable = true;
        }
        var barPercent = (100 / this.changes.length) * sumDecided;
        //update progressbar
        this.$refs.progressBar.style.width = barPercent + "%";
        //enable button
        this.disabledDownload = disable;
      },
      deep: true
    },
    slideChng: {
      handler: function() {
        this.slide = this.slideChng;
        this.updateBtns(
          this.changes[this.slide].type,
          this.changes[this.slide].decision
        );
      }
    },
    newDoc: {
      handler: function(){
      },
      deep: true
    }
  },
  mounted: function() {
    this.$root.$on("arrChanged", data => {
      this.changes = data;
    });
    this.$root.$on("slideChng", data => {
      this.slide = data;
    });
  },

};
</script>
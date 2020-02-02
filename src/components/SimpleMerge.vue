<template>
    <div id="simpleMerge">
        <h1>Welcome to the Model Merger</h1>
        <p>Model development is often a non-linear process. </p>
        
        <p v-if="!job"> It seems like you did not upload the models you want to merge. You can upload them using cUrl -F "pathToFile1" "pathToFile2" "thisWebsite.simpleMerge.php"</p>
        
        <h2 v-if="debug">jobID: {{ $route.query.jobID }}</h2>

        <button
        v-if="!goBack"
        ref="downloadBtn"
        v-on:click="download"
        type="button"
        class="btn btn-success btn-lg btn-block"
        >
            Download SBML
        </button>
         <button
        v-else
        ref="goBackBtn"
        v-on:click="goBackToOrigin"
        type="button"
        class="btn btn-success btn-lg btn-block"
        >
            Return to Starting Page
        </button>

        
        <div id="projectInfo">
            <p>This tool was developed as part of the INCOME project (BMBF: FKZ...)</p>
            <p>Insert Logo with href</p>
        </div>
    </div>
</template>

<script>



export default {
    data() {
        return {
        job: this.$route.query.jobID,
        debug: false,
        goBack: decodeURIComponent(this.$route.query.goBack)
        };
    },
    computed: {

    },
    methods: {
        download: function (){
            this.produceSimpleMerge();

        },
        goBackToOrigin: function(){
            this.produceSimpleMerge();

            var regex = /.+?(?=merge_versions|[?#])/;
            alert(this.goBack);
            var backRoute = regex.exec(this.goBack)
            backRoute =  backRoute + "#/?mergedModel=" + "/tmp/mergestorage/" + this.job + "/mergedModel";
            alert(backRoute);
            window.open(backRoute, "_self");
        },
        produceSimpleMerge: function(){
            var file1 =  "/tmp/mergestorage/" + this.job + "/f1";
            var file2 =  "/tmp/mergestorage/" + this.job + "/f2";

            // Make a request for a user with a given ID
            var bivesJob = {
                files: [file1, file2],
                commands: ["merge"],
                jobID: [this.job]
            };
        
            const axios = require("axios");
            axios
            .post("/bives/bives.php", "postParams=" + JSON.stringify(bivesJob))       
            
        }

    }
}
</script>

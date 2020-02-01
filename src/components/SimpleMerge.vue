<template>
    <div id="simpleMerge">
        <h1>Welcome to the Model Merger</h1>
        <p>Model development is often a non-linear process. </p>
        
        <p v-if="!job"> It seems like you did not upload the models you want to merge. You can upload them using cUrl -F "pathToFile1" "pathToFile2" "thisWebsite.simpleMerge.php"</p>
        
        <h2 v-if="debug">jobID: {{ $route.query.jobID }}</h2>

        <button
        v-if="!goBack"
        ref="downloadBtn"
        v-on:click="downloadSBML"
        type="button"
        class="btn btn-success btn-lg btn-block"
        v-bind:class="{'disabled': disabledDownload}"
        >
            Download SBML
        </button>
         <button
        v-else
        ref="downloadBtn"
        v-on:click="downloadSBML"
        type="button"
        class="btn btn-success btn-lg btn-block"
        v-bind:class="{'disabled': disabledDownload}"
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
        goBack: this.$route.query.goBack
        };
    },
    computed: {

    },
    methods: {

    }
}
</script>

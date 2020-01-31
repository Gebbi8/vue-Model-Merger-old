<template>
    <div id="simpleMerge">
        <h1>simpleMerge</h1>
        <h2>jobID: {{  $route.query.jobID }}</h2>


    </div>



</template>

<script>



export default {
    data() {
        return {
        job: this.$route.query.jobID
        };
    },
    computed: {

    },
    methods: {

    }
}
</script>
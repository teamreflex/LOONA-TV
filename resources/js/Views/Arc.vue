<template>
    <div class="container">
        <div class="row justify-content-center" v-for="episode in arc.episodes" :key="episode.id">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        {{ episode.title }}
                    </div>

                    <div class="card-body p-0">
                        <div class="embed-responsive embed-responsive-16by9">
                            <iframe class="embed-responsive-item" :src="'https://www.youtube.com/embed/' + episode.videoId" allowfullscreen />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Layout from "./Layout";

    export default {
        props: ['arc'],

        layout: Layout,

        meta() {
            return {
                title: this.arc.name,
            };
        },
    }
</script>

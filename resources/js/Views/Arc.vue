<template>
    <div class="container" id="clipboard">
        <div class="row justify-content-center" v-for="episode in arc.episodes" :key="episode.id">
            <div class="col-md-8">
                <div class="card mb-4" :id="'episode-' + episode.id">
                    <div class="card-header">
                        {{ episode.title }}
                        <div class="float-right">
                            <a :href="'#' + episode.id" @click="copyAnchor(episode.id)">#</a>
                        </div>
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
    const scrollToElement = require('scroll-to-element');

    export default {
        props: ['arc'],

        layout: Layout,

        meta() {
            return {
                title: this.arc.name,
            };
        },

        created() {
            this.setColor();
        },

        methods: {
            setColor() {
                let color = this.arc.color
                if (color) {
                    // because gradients don't work with single colors
                    // and i can't be bothered switching it to anything else but a gradient
                    if (! color.includes(',')) {
                        color = `${color}, ${color}`;
                    }

                    let root = document.documentElement;
                    root.style.setProperty(
                        '--glow-color',
                        `linear-gradient(45deg, ${color})`
                    );
                }
            },

            copyAnchor(episodeId) {
                this.$clipboard(this.$route('arc', {id: this.arc.id}) + '#' + episodeId);
                scrollToElement('#episode-' + episodeId);
            },
        }
    }
</script>

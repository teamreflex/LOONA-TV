<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="list-group">
                    <inertia-link
                        :href="$route('arc', {id: arc.id})"
                        v-for="arc in arcs"
                        :key="arc.id"
                        class="list-group-item list-group-item-action"
                    >{{ arc.name }}</inertia-link>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Layout from "./Layout";

    export default {
        props: ['arcs'],

        layout: Layout,

        meta: {
            title: 'Home',
        },
    }
</script>

<template>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm nav-glow">
            <div class="container">
                <a class="navbar-brand" href="/">
                    LOONA TV
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">
                        <!-- // -->
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item">
                            <a class="nav-link" target="_blank" href="https://github.com/teamreflex/LOONA-TV">
                                <fa-icon :icon="['fab', 'github']" />
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" target="_blank" href="https://twitter.com/Reflexgg">
                                <fa-icon :icon="['fab', 'twitter']" />
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            <slot />
        </main>
    </div>
</template>

<script>
    export default {
        meta() {
            return {
                title: 'Home',
                titleTemplate: (titleChunk) => {
                    const name = this.$page.app.name;
                    return titleChunk ? `${titleChunk} - ${name}` : name;
                },
            };
        },
    }
</script>

<template>
    <div>
        <dot-loader :color="color"></grid-loader>
        <!-- <dot-loader :loading="loading" :color="color" :size="size"></dot-loader> -->
    </div>
</template>

<script>
    import DotLoader from 'vue-spinner/src/DotLoader'
    export default {
        data: function () {
            return {
                color: '#0275d8'
            }
        },
        components: {
            'dot-loader': DotLoader
        }
    }
</script>
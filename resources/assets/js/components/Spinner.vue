<template>
    <div>
        <bounce-loader :color="color" :size="size"></grid-loader>
        <!-- <dot-loader :loading="loading" :color="color" :size="size"></dot-loader> -->
    </div>
</template>

<script>
    import BounceLoader from 'vue-spinner/src/BounceLoader'
    export default {
        props: ['size'],
        data: function () {
            return {
                color: '#0275d8'
            }
        },
        components: {
            'bounce-loader': BounceLoader
        }
    }
</script>
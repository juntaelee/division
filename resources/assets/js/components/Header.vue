<template>
    <nav class="my-header navbar fixed-top">
        <div class="d-flex justify-content-end">
            <a class="mr-auto align-self-center auth-name" href="/">{{name}}</a>
            <a href="javascript:void(0)" class="btn btn-primary float-right" 
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                Logout
            </a>
        </div>
    </nav>
</template>

<script>
    export default {
        props: ['name'],

        mounted: function() {
        }
    }
</script>

<style>
    .auth-name {
        font-weight: bold;
        color: #fff;
    }
    .my-header {
        background-color: #3a3f44;
        background-image: linear-gradient(#484e55, #3a3f44 60%, #313539);
        border: 1px solid rgba(0, 0, 0, 0.6);
    }
    .my-header .auth-name:hover {
        color: #e6e0e0;
        text-decoration: none;
    }
    .my-header.navbar {
        padding: .5rem;
    }
</style>
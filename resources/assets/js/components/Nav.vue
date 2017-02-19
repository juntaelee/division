<template>
    <nav class="my-nav">
        <ul class="d-flex flex-column nav nav-pills">
            <li class="nav-item" v-for="nav in navs">
                <router-link :to="nav.link" class="nav-link" :class="{ active: $route.path == nav.link }">{{ nav.name }}</router-link>
                <!-- <a class="nav-link active" href="#">UCC</a> -->
            </li>
        </ul>
    </nav>
</template>

<script>
    const Navs = [
        { name: 'UCC', link: '/ucc' },
        { name: 'MOVIE', link: '/movie' },
        { name: 'APP', link: '/app' },
        { name: '일별 등록현황', link: '/daily' },
        { name: '유해사이트 추가/삭제', link: '/site' },
        { name: '게임사이트 추가/삭제', link: '/game' },
        { name: '유튜브 유해등록', link: '/youtube' },
        { name: '유해동영상 검색', link: '/video' }
    ];

    export default {
        data: function() {
            return {
                navs: Navs
            }
        },
        mounted: function() {
        }
    }
</script>

<style>
    .my-nav {
        background-color: #272b30;
        /*padding: .7rem 0;*/
        overflow-x: hidden;
        overflow-y: auto;
        border-right: 1px solid #eee;
    }
    .my-nav .nav-item:hover {
        /*background-color: #1997c6;*/
        font-weight: bold;
    }
    .my-nav .nav-link {
        color: #fff;
    }
    .my-nav .nav-link.active{
        border-radius: 0;
        background-color: #5d6271;
    }
</style>

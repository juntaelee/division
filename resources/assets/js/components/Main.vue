<template>
    <div class="my-main">
        <router-view></router-view>
    </div>
</template>

<script>
    import 'jquery-contextmenu'
    import 'jquery.ui.position'

    import 'bootstrap'

    import 'bootstrap-datepicker'
    import 'bootstrap-datepicker/dist/locales/bootstrap-datepicker.kr.min.js'

    // import 'datatables'
    import 'datatables.net-responsive-bs4'

    export default {
        mounted: function() {
        }
    }
</script>

<style>
    @import '../../../../node_modules/jquery-contextmenu/dist/jquery.contextMenu.css';
    @import '../../../../node_modules/bootstrap-datepicker/dist/css/bootstrap-datepicker3.standalone.css';
    /*@import 'https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap4.min.css';*/
    @import '../../../../node_modules/datatables.net-bs4/css/dataTables.bootstrap4.css';

    .my-main {
        padding: .7rem;
        width: 100%;
    }
</style>

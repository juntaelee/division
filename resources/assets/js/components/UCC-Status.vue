<template>
    <div class="modal fade" tabindex="-1" role="dialog" data-backdrop="static">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">유해UCC 등록현황</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row input-daterange">
                        <div class="col-3">
                            <div class="input-group">
                                <input type="text" class="form-control" name="start" id="start-daterange" v-model="params.sDate"/>
                                <span class="input-group-addon" aria-hidden="true" onclick="document.getElementById('start-daterange').focus()"><i class="fa fa-calendar" aria-hidden="true"></i></span>

                                
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="input-group">
                                <a href="javascript:void(0)" class="btn btn-secondary" role="button" aria-pressed="true">
                                    <i class="fa fa-arrows-h" aria-hidden="true"></i>
                                </a>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="input-group">
                                <input type="text" class="form-control" name="end" id="end-daterange" v-model="params.eDate" />
                                <span class="input-group-addon" onclick="document.getElementById('end-daterange').focus()"><i class="fa fa-calendar"></i></span>
                            </div>
                            
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-primary" @click="status()"><i class="fa fa-search" aria-hidden="true"></i>검색</button>
                        </div>
                    </div>

                    <table id="ucc-status-table" class="table table-striped table-bordered display compact" cellspacing="0" width="100%">
                        <tfoot>
                            <tr>
                                <th>합계</th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                            <tr>
                                <th>총 합계</th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">닫기</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from 'axios'

    export default {
        components: {
        },
        data: function () {
            let date = new Date();
            let interval = '14';
            let today = (date).getFullYear() + '-' + ('0' + (date.getMonth() + 1)).slice(-2) + '-' + ('0' + date.getDate()).slice(-2);

            date.setDate(date.getDate() - interval);
            let pastday = (date).getFullYear() + '-' + ('0' + (date.getMonth() + 1)).slice(-2) + '-' + ('0' + date.getDate()).slice(-2);

            return {
                columns: [ "Youtube", "TVPot", "Pandora", "Naver", "Nate", "Cyworld", "Dailymotion", "Total" ],
                dataTables: null,

                lists: [],
                total: [],

                params: {
                    sDate: pastday,
                    eDate: today
                },
                
                ajaxing: false
            }
        },
        mounted: function() {
            let vm = this;

            $('#ucc-status').on('shown.bs.modal', function () {
                $('.input-daterange').unbind().datepicker({
                    format: 'yyyy-mm-dd',
                    language: 'kr',
                    todayHighlight: true,
                    endDate: vm.params.eDate
                })
                .on('changeDate', function() {
                    vm.params.sDate = $('#start-daterange').val();
                    vm.params.eDate = $('#end-daterange').val();
                });

                vm.dataTables = $('#ucc-status-table').DataTable({
                    responsive: true,
                    paging: false,
                    destroy : true,
                    processing: true,
                    dom: 'rt',//Bfrtip
                    order: [[ 0, "desc" ]],
                    language: {
                        emptyTable: '조회된 결과가 없습니다.'
                    },
                    data: vm.lists,
                    columns: [
                        { "title": "날짜", "data": "Date", "width": "10%" },
                        { "title": "Youtube", "data": "Youtube" },
                        { "title": "TVPot", "data": "TVPot" },
                        { "title": "Pandora", "data": "Pandora" },
                        { "title": "Naver", "data": "Naver" },
                        { "title": "Nate", "data": "Nate" },
                        { "title": "Cyworld", "data": "Cyworld" },
                        { "title": "Dailymotion", "data": "Dailymotion" },
                        { "title": "Total", "data": "Total" }
                    ],
                    footerCallback: function ( row, data, start, end, display ) {
                        let api = this.api();

                        let intVal = function ( i ) {
                            return typeof i === 'string' ?
                                i.replace(/[\$,]/g, '')*1 :
                                typeof i === 'number' ?
                                    i : 0;
                        };
                        
                        [1,2,3,4,5,6,7,8].forEach(function (index) {
                            let summary = api.column(index).data().reduce(function (a, b) {
                                            return intVal(a) + intVal(b);
                                        }, 0);

                            $(api.column(index).footer()).html(summary);
                        });

                        (vm.columns).forEach(function (k, i) {
                            $($($(api.table().footer()).find('tr')[1]).find('th')[i+1]).html(vm.total[k]);
                        });
                        
                        // console.log(total);
                        
            
                        // // Total over all pages
                        // total = api
                        //     .column( 4 )
                        //     .data()
                        //     .reduce( function (a, b) {
                        //         return intVal(a) + intVal(b);
                        //     }, 0 );

                        // // Total over this page
                        // pageTotal = api
                        //     .column( 4, { page: 'current'} )
                        //     .data()
                        //     .reduce( function (a, b) {
                        //         return intVal(a) + intVal(b);
                        //     }, 0 );
            
                        // // Update footer
                        // $( api.column( 4 ).footer() ).html(
                        //     '$'+pageTotal +' ( $'+ total +' total)'
                        // );
                    }
                });
                
                vm.status();
            });

            $('#ucc-status').on('hidden.bs.modal', function () {
                vm.dataTables.clear().draw();
            });
        },
        methods: {
            /*
             * UCC 현황 가져오기
             */
            status: function () {
                this.dataTables.clear().draw();
                this.total = [];

                axios.get('/ucc/status', {
                    params: {
                        sDate: this.params.sDate,
                        eDate: this.params.eDate
                    }
                })
                .then(response => {
                    this.lists = response.data.rows;
                    this.total = response.data.total;

                    this.dataTables.rows.add(this.lists).draw();
                })
                .catch(error => {
                    
                });
            }
        }
    }
</script>

<style>
@media (min-width: 992px) {
    #ucc-status .modal-lg {
        max-width: 1200px!important;
    }
}
.input-daterange .input-group-addon {
    min-width: 45px!important;
}
#ucc-status .input-daterange .input-group-addon {
    border-width: 1px;
    margin-left: 0;
    margin-right: 0;
}
#ucc-status .close span { color: #171515; }
#ucc-status-table {
    font-size: .8rem;
}
</style>
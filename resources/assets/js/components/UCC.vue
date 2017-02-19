<template>
    <div class="ucc card">
        
        <div class="card-header p-2">
            엑스키퍼 UCC 유해 판별
        </div>

        <div class="card-header p-2">
            <div class="btn-group dropdown">
                <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    출처({{ origins[originIndex].name }})
                </button>

                <div class="dropdown-menu">
                    <a class="dropdown-item" :class="{ active: origin.value == params.where }" href="javascript:void(0)" v-for="origin, index in origins" @click="filter('where', origin.value, index)">
                        {{ origin.name }}&nbsp;<i class="fa fa-check" aria-hidden="true" v-if="origin.value == params.where"></i>
                    </a>
                </div>
            </div>
            <div class="btn-group dropdown">
                <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    유해물({{ groups[groupIndex].name }})
                </button>

                <div class="dropdown-menu">
                    <a class="dropdown-item" :class="{ active: group.value == params.adult }" href="javascript:void(0)" v-for="group, index in groups" @click="filter('adult', group.value, index)">
                        {{ group.name }}&nbsp;<i class="fa fa-check" aria-hidden="true" v-if="group.value == params.adult"></i>
                    </a>
                </div>
            </div>
            <div class="btn-group dropdown" v-if="params.adult == 'X'">
                <button type="button" class="btn btn-sm btn-info" @click="marking('normal')">일반영상 등록</button>
            </div>
            <div class="btn-group dropdown float-right">
                <my-ucc-status id="ucc-status"></my-ucc-status>
                <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#ucc-status">유해 UCC 등록현황</button>
            </div>
        </div>

        <div class="card-block p-2">
            <div class="d-flex justify-content-center align-items-center p-4" v-if="ajaxing">
                <my-spinner></my-spinner>
            </div>

            <div class="d-flex justify-content-center align-items-center p-4" v-if="lists.length == 0 && !ajaxing">
                존재하지 않습니다.
            </div>

            <div class="row">
                <div class="col-2 pb-2" v-for="list in lists">
                    <div class="card clip p-1" @click="linkTo(list.Player)" v-bind="{ 'data-log': list.Log }">
                        <img class="card-img mx-auto" :src="(list.Adult != 'X') ? '/ucc/thumbnail?adult='+list.Adult+'&pk='+list.Log : list.Thumbnail" alt="">
                        <div class="card-block p-0">
                            <p class="card-text text-center">
                                {{(list.Title).substring(0, 12)}}<br />
                                {{list.Date_Entry}}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer text-muted">
            <div class="row">
                <div class="col-8">
                    <nav aria-label="Page navigation example">
                        <ul class="pagination pagination-sm mb-0">
                            <li class="page-item" v-if="params.current != 1"><a class="page-link" href="javascript:void(0)" @click="movePage(params.current-1)">이전</a></li>

                            <li class="page-item" 
                                :class="{ active: n == params.current }" 
                                v-for="(n, index) in maxPagination" 
                                v-if="n > (maxPagination-lenPagination)">

                                <a class="page-link" href="javascript:void(0)" v-if="n != params.current" @click="movePage(n)">
                                    {{ n }}
                                </a>

                                <span class="page-link" v-if="n == params.current">
                                    {{ n }}
                                    <span class="sr-only">(current)</span>
                                </span>

                            </li>

                            <li class="page-item" v-if="params.current != maxPagination && maxPagination != 0"><a class="page-link" href="javascript:void(0)" @click="movePage(params.current+1)">다음</a></li>
                        </ul>
                    </nav>
                </div>
                <div class="col-4">
                    <p class="text-right mb-0">
                        <small>
                            <strong>{{ total }}</strong> 중 {{ ((params.current - 1) * params.rowCount) + 1 }} - {{ (params.current * params.rowCount) > total ? total : (params.current * params.rowCount) }}
                        </small>
                    </p>
                </div>
            </div>
        </div>
        
    </div>
</template>

<script>
    import spinner from './Spinner.vue'
    import uccStatus from './UCC-Status.vue'
    import axios from 'axios'

    import bootbox from 'bootbox'

    const Origins = [
        { name: '모든 사이트', value: '' },
        { name: '네이버', value: 'Naver' },
        { name: '네이트', value: 'Nate' },
        { name: '유투브', value: 'Youtube' },
        { name: '다음 TV팟', value: 'TVPot' },
        { name: '판도라', value: 'Pandora' },
        { name: '싸이월드', value: 'Cyworld' },
        { name: 'Dailymotion', value: 'Dailymotion' }
    ];

    const Groups = [
        { name: '모든영상', value: '' },
        { name: '미분류', value: 'X' },
        { name: '일반영상', value: 'N' },
        { name: '성인물', value: 'Y' }
    ];

    export default {
        components: {
            'my-spinner': spinner,
            'my-ucc-status': uccStatus
        },
        props: [],
        data: function () {
            return {
                lists: [],

                total: 0,
                maxPagination: 1,
                lenPagination: 10,

                params: {
                    current: 1,
                    rowCount: 30,
                    where: '',
                    adult: ''
                },

                origins: Origins,
                originIndex: 0,
                groups: Groups,
                groupIndex: 0,

                ajaxing: false
            }
        },
        mounted: function () {
            let vm = this;
            /*
             * 리스트 조회
             */
            this.list();
            /*
             *  jQuery Context Menu
             */
            $.contextMenu({
                selector: '.clip', 
                callback: function (key, options) {
                    if (key == 'ctxMove') {
                        this[0].click();
                    } else {
                        vm.marking(key, this[0].getAttribute('data-log'));
                    }
                    // window.console && console.log(m) || alert(m);
                },
                items: {
                    "ctxHarmful": {name: "유해영상 등록", icon: "fa-ban"},
                    "ctxNormal": {name: "일반영상 등록", icon: "fa-smile-o"},
                    "ctxMove": {name: "영상 출처로 이동", icon: "fa-youtube-play"}
                },
                events: {
                    show: function () {
                        this[0].classList.add('showContext');
                    },
                    hide: function () {
                        this[0].classList.remove('showContext');
                    }
                }
            });
        },
        methods: {
            /*
             *  Ajax 데이터 가져오기
             */
            list: function () {
                this.lists = [];

                this.ajaxing = true;

                axios.get('/ucc/ucc', {
                    params: {
                        where: this.params.where,
                        adult: this.params.adult,
                        current: this.params.current,
                        rowCount: this.params.rowCount,
                        sort: 'desc'
                    }
                })
                .then(response => {
                    this.ajaxing = false;

                    this.lists = response.data.list;
                    this.total = response.data.total;

                    this.pagination();
                })
                .catch(error => {
                    this.ajaxing = false;

                    alert(error);
                });
            },
            /*
             *  리스트 필터링
             */
            filter: function (target, v, index) {
                this.params.current = 1;

                if (target == 'where') {
                    this.params.where = v;
                    this.originIndex = index;
                } else if (target == 'adult') {
                    this.params.adult = v;
                    this.groupIndex = index;
                }

                this.list();
            },
            /*
             *  페이지 계산
             */
            pagination: function () {
                let length = this.total/this.params.rowCount;
                let remainder = Math.floor(length) != length ? 1 : 0;

                length = (Math.floor(length) + remainder);

                let min = (this.params.current - 4) < 1 ? 1 : (this.params.current - 4);
                let max = min + 9;

                this.maxPagination = (max > length) ? length : max;
            },
            /*
             *  페이지 넘김 이벤트
             */
            movePage: function (current) {
                this.params.current = current;

                this.list();
            },
            /*
             *  새창 띄우기
             */
            linkTo: function (player) {
                window.open(player, '_blank');
            },
            /*
             *  유해/일반 동영상 판별
             */
            marking: function (key, log) {
                let vm = this;
                let type = (key == 'ctxHarmful') ? 'xxx' : 'normal2';
                let maxLog = 0;

                // 일반영상 한번에 등록 할 때 물어보기.
                let _normalConfirmBox = function (param) {
                    return new Promise(function (resolve, reject) {
                        bootbox.confirm({
                            message: '현재 페이지의 영상은 모두 일반영상 입니까?', 
                            closeButton: false,
                            backdrop: true,
                            buttons: {
                                confirm: {
                                    label: '예',
                                    className: 'btn-sm btn-success'
                                },
                                cancel: {
                                    label: '아니오',
                                    className: 'btn-sm btn-danger'
                                }
                            },
                            callback: function (result) {
                                resolve(result);
                            }
                        });
                    });
                };

                // 일반영상 한번에 등록 시 등록할 영상이 없는 경우
                let _emptyAlertBox = function (param) {
                    return new Promise(function (resolve, reject) {
                        console.log('_emptyAlertBox');
                        bootbox.alert({
                            closeButton: false,
                            backdrop: true,
                            size: "small",
                            message: '<span style="color: red;">현재 페이지에 영상이 존재하지 않습니다.</span>',
                            buttons: {
                                ok: {
                                    label: '확인',
                                    className: 'btn-sm btn-info'
                                }
                            },
                            callback: function () {
                                resolve();
                            }
                        });
                    });
                };

                let _promise = function (param) {
                    return new Promise(function (resolve, reject) {
                        if (key == 'normal') {
                            type = key;

                            _normalConfirmBox()
                            .then(function (resp) {
                                if (resp) {
                                    if (vm.lists.length == 0) {
                                        _emptyAlertBox()
                                        .then(function (_resp) {
                                            // END
                                        });
                                    } else {
                                        maxLog = vm.lists[0].Log;
                                        log = vm.lists[vm.lists.length - 1].Log;

                                        resolve();
                                    }
                                }
                            });
                        } else {
                            resolve();
                        }
                    });
                };

                _promise()
                .then(function (resp) {
                    axios.patch('/ucc/marking', {
                        type: type,
                        log: log,
                        maxLog: maxLog,
                        rowCount: vm.params.rowCount
                    })
                    .then(response => {
                        let message = type == 'xxx' ? '유해 영상으로 분류 완료' : '일반 영상으로 분류 완료';

                        bootbox.alert({
                            closeButton: false,
                            backdrop: true,
                            size: "small",
                            message: message,
                            buttons: {
                                ok: {
                                    label: '확인',
                                    className: 'btn-sm btn-info'
                                }
                            },
                            callback: function () {
                                vm.list();
                            }
                        });
                    })
                    .catch(error => {
                        bootbox.alert({
                            closeButton: false,
                            backdrop: true,
                            size: "small",
                            message: '<span style="color: red;">'+error.response.data.Message+'</span>',
                            buttons: {
                                ok: {
                                    label: '확인',
                                    className: 'btn-sm btn-info'
                                }
                            }
                        });
                    });
                }, function (error) {

                });
            }
        }
    }
</script>

<style lang="scss">
    .my-main .ucc.card {
    }
    .my-main img {
        width: 120px;
        height: 90px;
    }
    .my-main .card-text {
        font-size: .7rem;
    }
    .my-main .clip.showContext {
        background-color: #efefef;
    }
    .my-main .clip:hover {
        border: 1px solid #dddddd;
        background-color: #efefef;
        cursor: pointer;
    }
    /* 
     * Context Menu
     */
    .context-menu-list .context-menu-item {
        margin-bottom: .3rem;
    }
</style>

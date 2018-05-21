var Agentsv2 = function () {

    var formatRepoOwner = function (json) {
        if (json.loading) {
            return json.text;
        }
        console.log('json' + json);
        var markup = '<div class="clearfix">' +
            '<div class="col-sm-2">' + json.description + '</div>' +
            '</div>';

        markup += '</div></div>';

        return markup;
    };

    var formatRepoSelectionOwner = function (repo) {
        return repo.description;
    };

    var formatRepoSelection = function (repo) {
        return repo.username;
    };

    // Format result
    var formatUsersResult = function (json) {
        if (json.loading) {
            return json.text;
        }

        var markup = '<div class="clearfix">' +
            '<div class="col-sm-2">' + json.username + '</div>' +
            '<div class="col-sm-10">' + json.name + ' ' + json.lastname + '</div>' +
            '</div>';

        markup += '</div></div>';
        return markup;
    };

    return {

        initUsersSearch: function () {
            var $userSearch = $('#user-search');
            $userSearch.select2({
                ajax: {
                    url: $userSearch.data('route'),
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            username: params.term
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data
                        };
                    },
                    cache: true
                },
                escapeMarkup: function (markup) {
                    return markup;
                },
                minimumInputLength: 3,
                templateResult: formatUsersResult,
                templateSelection: formatRepoSelection
            });
        },

        initOwnerSearch: function (id, username) {
            var $ownerSearch = $('#owner');
            $ownerSearch.select2({
                tags: true,
                ajax: {
                    url: $ownerSearch.data('route'),
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            agent: params.term
                        };
                    },
                    processResults: function (data, page) {
                        return {
                            results: data.agent
                        };
                    },
                    cache: true
                },
                escapeMarkup: function (markup) {
                    return markup;
                },
                minimumInputLength: 3,
                templateResult: formatRepoOwner,
                templateSelection: formatRepoSelectionOwner
            }).append("<option value='" + id + "' data-text='" + username + "' selected title>" + username + "</option>").trigger('change');

            $('#select2-owner-container').text(username);

        },

        dataAgents: function () {
            var locale = Core.getCookie('localejs');
            var lang = (locale == null || locale == '') ? 'es_ES' : locale;
            var route = $('.datatable-transactions-agent').data('route');
            var id = $("#tree ul li.jstree-node a.jstree-clicked").data('id');
            var type = $("#tree ul li.jstree-node a.jstree-clicked").data('type');
            var month_init = moment().month();
            var year_init = moment().year();
            var dateInit = moment([year_init, month_init, 1]);
            var startDate = dateInit.format('YYYY-MM-DD');
            var endDate = moment().format('YYYY-MM-DD');
            var url = route + '?startDate=' + startDate + '&endDate=' + endDate + '&id=' + id;

            $('.datatable-transactions-agent').css("width", "100%");
            var $table = $('.datatable-transactions-agent').dataTable({
                "iDisplayLength": 10,
                "processing": true,
                "serverSide": true,
                "ajax": url,
                "order": [],
                "columnDefs": [
                    {targets: 'no-sort', orderable: false},
                    {
                        "targets": [0],
                        "render": function (data, type, full) {
                            return full[0];
                        }
                    },
                    {
                        "targets": [1],
                        "render": function (data, type, full) {
                            return full[1];
                        }
                    },
                    {
                        "targets": [2],
                        "data": "balance",
                        "className": "text-right",
                        "render": function (data, type, full) {
                            var number = $.number(full[2], full[4], full[5], full[6]);
                            return number;
                        }
                    },
                    {
                        "targets": [3],
                        "render": function (data, type, full) {
                            return full[3];
                        }
                    },
                    {
                        "targets": [4],
                        "visible": false
                    },
                    {
                        "targets": [5],
                        "visible": false
                    },
                    {
                        "targets": [6],
                        "visible": false
                    }
                ],
                "language": {
                    "url": "/i18n/datatables/" + lang + ".lang"
                },
                "lengthMenu": [[10, 25, 50, 100, 200, 300, 400, 500], [10, 25, 50, 100, 200, 300, 400, 500]],
                "dom": 'B<"clear">lfrtip',
                "buttons": [
                    'print',
                    {
                        "extend": "collection",
                        "text": "Save",
                        "buttons": ['pdfHtml5', 'csvHtml5', 'excelHtml5']
                    }
                ]
            });

            $('.a-tab-agents').click(function () {
                if ($(this).hasClass('charged') == false) {
                    $(this).addClass('charged');
                    var route = $('.datatable-transactions-agent').data('route');
                    var id = $("#tree ul li.jstree-node a.jstree-clicked").data('id');
                    var type = $("#tree ul li.jstree-node a.jstree-clicked").data('type');
                    var month_init = moment().month();
                    var year_init = moment().year();
                    var dateInit = moment([year_init, month_init, 1]);
                    var startDate = dateInit.format('YYYY-MM-DD');
                    var endDate = moment().format('YYYY-MM-DD');
                    var url = route + '?startDate=' + startDate + '&endDate=' + endDate + '&id=' + id;
                    $table.fnReloadAjax(url);
                }
            })

            $("#descMto").slider({
                tooltip: 'always'
            });
            $('#percentageofprofit').click(function () {
                $("#descMto").slider({
                    tooltip: 'always'
                });
            });
            $('#customnetamount').click(function () {
                $("#descMto").slider('destroy');
            });

            $("#form-create-agent").on("submit", function (e) {
                $('.btn-create').button('loading');
                var postData = $(this).serialize();
                var formURL = $(this).attr("action");
                var route = $('.datatable-transactions-agent').data('route');
                var id = $("#tree ul li.jstree-node a.jstree-clicked").data('id');
                var type = $("#tree ul li.jstree-node a.jstree-clicked").data('type');
                var month_init = moment().month();
                var year_init = moment().year();
                var dateInit = moment([year_init, month_init, 1]);
                var startDate = dateInit.format('YYYY-MM-DD');
                var endDate = moment().format('YYYY-MM-DD');
                var url = route + '?startDate=' + startDate + '&endDate=' + endDate + '&id=' + id;
                $.ajax({
                    url: formURL,
                    type: "POST",
                    data: postData,
                }).done(function (json) {
                    showToastr(json.title, json.message, 'success');
                    $('#form-create-agent')[0].reset();
                    $("#modal-agent").modal('hide');
                    $table.fnReloadAjax(url);
                }).fail(function (response) {
                    if (response.status === 400) {
                        showToastr(response.responseJSON.title, response.responseJSON.message, 'error');
                    }
                    if (response.status === 401) {
                        $.each(response.responseJSON.motives, function (index, item) {
                            showToastr(response.responseJSON.title, item, 'failed');
                        });
                    }
                }).always(function () {
                    $('.btn-create').button('reset');
                });
                return false;
            })
        },

        convertToAgent: function () {
            $("#descMto").slider({
                tooltip: 'always'
            });
            $('#percentageofprofit').click(function () {
                $("#descMto").slider({
                    tooltip: 'always'
                });
            });
            $('#customnetamount').click(function () {
                $("#descMto").slider('destroy');
            });

            $("#form-convert-agent").on("submit", function (e) {
                $('.btn-create').button('loading');
                var postData = $(this).serialize();
                var formURL = $(this).attr("action");

                $.ajax({
                    url: formURL,
                    type: "POST",
                    data: postData,
                }).done(function (json) {
                    showToastr(json.title, json.message, 'success');
                    $("#form-convert-agent")[0].reset();
                }).fail(function (response) {
                    if (response.status === 400) {
                        showToastr(response.responseJSON.title, response.responseJSON.message, 'error');
                    }
                    if (response.status === 401) {
                        $.each(response.responseJSON.motives, function (index, item) {
                            showToastr(response.responseJSON.title, item, 'failed');
                        });
                    }
                }).always(function () {
                    $('.btn-create').button('reset');
                });
                return false;
            })
        },

        dataPlayers: function () {
            var locale = Core.getCookie('localejs');
            var lang = (locale == null || locale == '') ? 'es_ES' : locale;
            var route = $('.datatable-transactions').data('route');
            var id = $("#tree ul li.jstree-node a.jstree-clicked").data('id');
            var type = $("#tree ul li.jstree-node a.jstree-clicked").data('type');
            var month_init = moment().month();
            var year_init = moment().year();
            var dateInit = moment([year_init, month_init, 1]);
            var startDate = dateInit.format('YYYY-MM-DD');
            var endDate = moment().format('YYYY-MM-DD');
            var url = route + '?startDate=' + startDate + '&endDate=' + endDate + '&id=' + id;

            $('.datatable-transactions').css("width", "100%");
            var $table = $('.datatable-transactions').dataTable({
                "iDisplayLength": 10,
                "processing": true,
                "serverSide": true,
                "ajax": url,
                "order": [],
                "columnDefs": [
                    {targets: 'no-sort', orderable: false},
                    {
                        "targets": [0],
                        "render": function (data, type, full) {
                            return full[0];
                        }
                    },
                    {
                        "targets": [1],
                        "render": function (data, type, full) {
                            return full[1];
                        }
                    },
                    {
                        "targets": [2],
                        "render": function (data, type, full) {
                            return full[2];
                        }
                    },
                    {
                        "targets": [3],
                        "visible": false
                    },
                    {
                        "targets": [4],
                        "visible": false
                    },
                    {
                        "targets": [5],
                        "visible": false
                    }
                ],
                "language": {
                    "url": "/i18n/datatables/" + lang + ".lang"
                },
                "lengthMenu": [[10, 25, 50, 100, 200, 300, 400, 500], [10, 25, 50, 100, 200, 300, 400, 500]],
                "dom": 'B<"clear">lfrtip',
                "buttons": [
                    'print',
                    {
                        "extend": "collection",
                        "text": "Save",
                        "buttons": ['pdfHtml5', 'csvHtml5', 'excelHtml5']
                    }
                ]
            });

            $('.a-tab-players').click(function () {
                if ($(this).hasClass('charged') == false) {
                    $(this).addClass('charged');
                    var route = $('.datatable-transactions').data('route');
                    var id = $("#tree ul li.jstree-node a.jstree-clicked").data('id');
                    var type = $("#tree ul li.jstree-node a.jstree-clicked").data('type');
                    var month_init = moment().month();
                    var year_init = moment().year();
                    var dateInit = moment([year_init, month_init, 1]);
                    var startDate = dateInit.format('YYYY-MM-DD');
                    var endDate = moment().format('YYYY-MM-DD');
                    var url = route + '?startDate=' + startDate + '&endDate=' + endDate + '&id=' + id;
                    $table.fnReloadAjax(url);
                }
            })

            $("#form-create-player").on("submit", function (e) {
                $('.btn-create').button('loading');
                var postData = $(this).serialize();
                var formURL = $(this).attr("action");
                var $table = $('.datatable-transactions');
                var route = $table.data('route');
                var id = $("#tree ul li.jstree-node a.jstree-clicked").data('id');
                var type = $("#tree ul li.jstree-node a.jstree-clicked").data('type');
                var month_init = moment().month();
                var year_init = moment().year();
                var dateInit = moment([year_init, month_init, 1]);
                var startDate = dateInit.format('YYYY-MM-DD');
                var endDate = moment().format('YYYY-MM-DD');
                var url = route + '?startDate=' + startDate + '&endDate=' + endDate + '&id=' + id;
                $.ajax({
                    url: formURL,
                    type: "POST",
                    data: postData,
                }).done(function (json) {
                    showToastr(json.title, json.message, 'success');
                    $("#form-create-player")[0].reset();
                    $("#modal-user").modal('hide');
                    $table.fnReloadAjax(url);
                }).fail(function (response) {
                    if (response.status === 400) {
                        showToastr(response.responseJSON.title, response.responseJSON.message, 'error');
                    }
                    if (response.status === 401) {
                        $.each(response.responseJSON.motives, function (index, item) {
                            showToastr(response.responseJSON.title, item, 'failed');
                        });
                    }
                }).always(function () {
                    $('.btn-create').button('reset');
                });
                return false;
            })
        },

        actionsPanel: function () {
            $(".a-tab-dashboard").attr("aria-expanded", "true");
            $("#tab-dashboard").attr("aria-expanded", "true")
            $("#tab-dashboard").addClass('active');
            $(".dashboard-tab").addClass('active');
            $("#tab-players").attr("aria-expanded", "false");
            $("#tab-agents").attr("aria-expanded", "false");
            $("#tab-players").removeClass('active');
            $("#tab-agents").removeClass('active');
            $(".agents-tab").removeClass('active');
            $(".a-tab-players").removeClass('charged');
            $(".a-tab-agents").removeClass('charged');
        },


        panelAgents: function () {
            //items arbol
            $("#tree ul").on("click", "li.jstree-node a.click-tab", function () {
                var id = $(this).data('id');
                var type = $(this).data('type');
                var master = $(this).hasClass('agent-master');
                if ($(this).hasClass('master')) {
                    $('.add-agent').removeClass('hidden');
                    $('.add-player').removeClass('hidden');
                } else {
                    $('.add-agent').addClass('hidden');
                    $('.add-player').addClass('hidden');
                }
                Agentsv2.actionsPanel();
                Agentsv2.initAgentDashboard(id, type, master);
            });


            $(document).on("click", "#accredit", function (e) {
                var amount = $('#amount').val();
                var userr = $('#userr').val();
                var type = $('#type').val();
                var dep = $('#dep').val();
                var formURL = $(this).data('route');

                $.ajax({
                    url: formURL,
                    type: "POST",
                    data: {
                        amount: amount,
                        user: userr,
                        type: type,
                        dep: dep
                    },
                }).done(function (json) {
                    showToastr(json.title, json.message, 'success');
                    Agentsv2.initAgentDashboard(userr, type);
                }).fail(function (response) {
                    if (response.status === 400) {
                        showToastr(response.responseJSON.title, response.responseJSON.message, 'error');
                    }
                    if (response.status === 401) {
                        $.each(response.responseJSON.motives, function (index, item) {
                            showToastr(response.responseJSON.title, item, 'failed');
                        });
                    }
                })
            })

            $(document).on("click", "#debit", function (e) {
                var amount = $('#amount').val();
                var userr = $('#userr').val();
                var type = $('#type').val();
                var dep = $('#dep').val();
                var formURL = $(this).data('route');

                $.ajax({
                    url: formURL,
                    type: "POST",
                    data: {
                        amount: amount,
                        user: userr,
                        type: type,
                        dep: dep
                    },
                }).done(function (json) {
                    showToastr(json.title, json.message, 'success');
                    Agentsv2.initAgentDashboard(userr, type);
                }).fail(function (response) {
                    if (response.status === 400) {
                        showToastr(response.responseJSON.title, response.responseJSON.message, 'error');
                    }
                    if (response.status === 401) {
                        $.each(response.responseJSON.motives, function (index, item) {
                            showToastr(response.responseJSON.title, item, 'failed');
                        });
                    }
                })

            });

        },

        initAgentDashboard: function (id, type, master) {
            var $agentstab = $(".agents-tab");
            var $playerstab = $(".players-tab");
            var route = $('#tab-dashboard').data('route');
            var month_init = moment().month();
            var year_init = moment().year();
            var dateInit = moment([year_init, month_init, 1]);
            var startDate = dateInit.format('YYYY-MM-DD');
            var endDate = moment().format('YYYY-MM-DD');
            var select = $("#tree ul li.jstree-node a.jstree-clicked").hasClass('master');

            $('#tab-dashboard').html("<div class='loader loader-tab'> </div>");
            $.ajax({
                url: route,
                type: "POST",
                data: {
                    id: id,
                    type: type,
                    startDate: startDate,
                    endDate: endDate
                },
            }).done(function (json) {
                    $('#tab-dashboard').html(json);
                    if (type == 'agent' && master == true) {
                        $agentstab.removeClass('hidden');
                        $playerstab.removeClass('hidden');
                    }
                    if (type != 'agent') {
                        $agentstab.addClass('hidden');
                        $playerstab.addClass('hidden');
                    }
                    if (type == 'agent' && master == false) {
                        $playerstab.removeClass('hidden');
                        $agentstab.removeClass('hidden');
                        $agentstab.addClass('hidden');
                    }
                }
            ).fail(function (response) {
                if (response.status === 400) {
                    showToastr(response.responseJSON.title, response.responseJSON.message, 'error');
                }
                if (response.status === 401) {
                    $.each(response.responseJSON.motives, function (index, item) {
                        showToastr(response.responseJSON.title, item, 'failed');
                    });
                }
            });
        },

        treeDependency: function () {
            $('#tree').jstree({
                "core": {
                    "themes": {
                        "responsive": true
                    }
                },
                "types": {
                    "default": {
                        "icon": "fa fa-folder icon-state-warning icon-lg"
                    },
                    "file": {
                        "icon": "fa fa-file icon-state-warning icon-lg"
                    }
                },
                "plugins": ["types"]
            }).bind("ready.jstree", function (event, data) {
                $(this).jstree("open_all");
            });


            $('#tree').on('select_node.jstree', function (e, data) {
                var link = $('#' + data.selected).find('a');
                if (link.attr("href") != "#" && link.attr("href") != "javascript:;" && link.attr("href") != "") {
                    if (link.attr("target") == "_blank") {
                        link.attr("href").target = "_blank";
                    }
                    document.location.href = link.attr("href");
                    return false;
                }
            });
        },

        // Init financial State by day, week and total
        initFinancialState: function (isAdmin) {
            $('#bt_search_financial_state').on('click', function () {
                var url = $('.list-todo').data('route');
                $.ajax({
                    url: url,
                    type: 'post',
                    dataType: 'json',
                    data: {
                        agent: $('#agent_username').val()
                    }
                }).done(function (response) {
                    if (response.status === 200) {
                        showToastr(response.title, response.message, 'success');

                        $('#agents_financial_state_day').html(response.data.financialStateDay);
                        $('#agents_financial_state_week').html(response.data.financialStateWeek);
                        $('#agents_financial_state_total').html(response.data.financialState);
                    }
                }).fail(function (response, data) {

                    if (response.status === 400) {
                        showToastr(response.responseJSON.title, response.responseJSON.message, 'error');
                    }

                    if (response.status === 401) {
                        $.each(response.responseJSON.motives, function (index, item) {
                            showToastr(response.responseJSON.title, item, 'failed');
                        });

                    }
                });
            });


        },

    }
}();
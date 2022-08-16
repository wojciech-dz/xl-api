var gens = {
    var: currentPage = $('#page_number').val(),
    const: currentLimit = $('#documents_limit').val(),

    showFirstPage: function () {
        $('#page_number').val(1);
        gens.fillData(event, $('.invoice-lines'), function () {});
    },
    showLastPage: function () {
        var pagesCount = parseInt($('#page_number').attr('max'));

        $('#page_number').val(pagesCount);
        gens.fillData(event, $('#by_not_in_offer'), function () {});
    },
    showPrevPage: function () {
        var pageNumber = parseInt($('#page_number').val());

        if (pageNumber > 1) {
            $('#page_number').val(pageNumber - 1);
            gens.fillData(event, $('#by_not_in_offer'), function () {
            });
        }
    },
    showNextPage: function () {
        var pageNumber = parseInt($('#page_number').val());

        if (pageNumber < parseInt($('#page_number').attr('max'))) {
            $('#page_number').val(pageNumber + 1);
            gens.fillData(event, $('#by_not_in_offer'), function () {
            });
        }
    },
    showPage: function (obj) {
        var pageNumber = parseInt($(obj).val());
        var pagesCount = parseInt($(obj).attr('max'))

        if (pageNumber < 1) {
            gens.showFirstPage();
        } else if (pageNumber > pagesCount) {
            gens.showLastPage(obj);
            $('#page_number').val(pagesCount);
            gens.fillData(event, $('#by_not_in_offer'), function () {});
        } else {
            $('#page_number').val(pageNumber);
            gens.fillData(event, $('#by_not_in_offer'), function () {});
        }
    },
    fillData: function (event) {
        event.preventDefault();
        const xhr = new XMLHttpRequest();
        let params = "page=" + $('#page_number').val() + "&limit=" + $('#documents_limit').val();
        let url = $('#PATH_invoices_dose').val() + '?' + params;
        xhr.open("GET", url, true);
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhr.addEventListener("load", e => {
            if (xhr.status === 200) {
                document.getElementById('invoice-lines').innerHTML = xhr.response; // this one works
                gens.initiateAccordion();
            }
        });
        xhr.send();
    },
    getPdf: function (button) {
        $.ajax({
            url: button.data('url'),
            success: function () {
                button.prop( "disabled", true );
            }
        });
    },
    showAjax: function (button) {
        const xhr = new XMLHttpRequest();
        let id = button.data('id');
        xhr.addEventListener("load", e => {
            if (xhr.status === 200) {
                document.getElementById('just-items-' + id).innerHTML = xhr.response;
                $('#just-items' + id).load(window.location.href + " #just-items-" + id);
            }
        });
        xhr.open("GET", button.data('url'), true);
        xhr.send();
    },
    initiateAccordion: function () {
        $('.accordion-ajax').accordion({
            collapsible: true,
            active: false,
            clearStyle: true,
            autoHeight: false,
            header: '.accordion-head',
            heightStyle: 'content',
            icons: false,
            beforeActivate: function (event, ui) {
                if (ui.newPanel.html() === '') {
                    ui.newPanel.load($(ui.newHeader[0]).data('url'));
                }
            }
        });
    },

    init: function () {
        $(document).ready(function(){
            gens.initiateAccordion();
        });
        $(document).on('click', '.page_first', function () {
            gens.showFirstPage();
        });
        $(document).on('click', '.page_last', function () {
            gens.showLastPage();
        });
        $(document).on('click', '.page_prev', function () {
            gens.showPrevPage();
        });
        $(document).on('click', '.page_next', function () {
            gens.showNextPage();
        });
        $(document).on('change', '#page_number', function () {
            gens.showPage(this);
        });
        $(document).on('click', '#get_pdf', function () {
            gens.getPdf($(this));
        });
        $(document).on('click', '#show_items', function () {
            gens.showAjax($(this));
        });
        $(document).on('click', '#show_details', function () {
            gens.showAjax($(this));
        });
    }
};

gens.init();

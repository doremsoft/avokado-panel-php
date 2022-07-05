$.ajaxSetup({
    headers: {
        'csrftoken': $('meta[name="csrf-token"]').attr('content')
    }
});
function notify(message, type) {
    $.growl({
        message: message
    }, {
        type: type,
        allow_dismiss: false,
        label: 'Cancel',
        className: 'btn-xs btn-inverse',
        placement: {
            from: 'top',
            align: 'right'
        },
        delay: 2500,
        animate: {
            enter: 'animated fadeInRight',
            exit: 'animated fadeOutRight'
        },
        offset: {
            x: 30,
            y: 30
        }
    });
}
;

function ajax_veriKaydet(value, id, url) {

    $.ajax({
        url: url,
        type: "POST",
        data: 'value=' + value + '&id=' + id,
        success: function (data) {

            if (data == "ok") {


                swalert("success", "Kayıt Güncellendi", "İşlem başarı ile tamamlandı");
            } else {
                swalert("error", "İşlem Başarısız!", "Kayıt Güncellenemedi!");

            }
        }
    });
}

function ajax_duzenle(deger, id, url) {

    var $lbl = $(deger);
    var o = $lbl.text();
    var $txt = $('<input type="text" class="form-control" value="' + o + '" />');
    $lbl.replaceWith($txt);
    $txt.focus();

    $txt.blur(function () {

        swal({
            title: "Are you sure?",
            text: "Once deleted, you will not be able to recover this imaginary file!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
                .then((willDelete) => {
                    if (willDelete) {

                        var no = $(this).val();
                        $lbl.text(no);
                        $txt.replaceWith($lbl);
                        ajax_veriKaydet(no, id, url);



                    } else {
                        $txt.replaceWith($lbl);
                    }
                });




    }).keydown(function (evt) {
        if (evt.keyCode == 13) {
            var no = $(this).val();
            $lbl.text(no);
            $txt.replaceWith($lbl);
            ajax_veriKaydet(no, id, url);
        }
    });

}


function ajax_label_duzenle(label_id) {

    var label = $("#" + label_id);
    var label_text = label.text();
    var label_input = $('<input type="text" class="form-control" value="' + label_text + '" />');
    label.replaceWith(label_input);
    label_input.focus();
    label_input.blur(function () {
        swal({
            title: "Eminmisiniz?",
            text: "Kaydı Güncellemek Üzeresiniz!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
            buttons: ["İptal Et", "Tamam Anladım !"],
        })
                .then((willDelete) => {
                    if (willDelete) {

                        var no = $(this).val();
                        label.text(no);
                        label_input.replaceWith(label);
                        ajax_veriKaydet(no, label.attr("data-id"), label.attr("data-url"));


                    } else {
                        label_input.replaceWith(label);
                    }
                });
    }).keydown(function (evt) {
        if (evt.keyCode == 13) {
            var no = $(this).val();
            label.text(no);
            label_input.replaceWith(label);
            ajax_veriKaydet(no, label.attr("data-id"), label.attr("data-url"));
        }
    });

}



function swalert(type, header, message) {

    swal(header, message, type);

}

function fullcontent() {


    $(".mobile-menu").toggleClass('on');
    $(".pcoded-navbar").toggleClass("navbar-collapsed");
}


function setDataTable(selector, buttons, file_name, title) {

    if (buttons == 1) {


        $(selector).DataTable({
            paging: false,
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    title: file_name
                },
                {
                    extend: 'pdfHtml5',
                    title: file_name
                }
                ,
                {
                    extend: 'copyHtml5',
                    title: file_name
                }, {
                    extend: 'csvHtml5',
                    title: file_name
                },
                {
                    extend: 'print',

                    messageBottom: null
                }
            ],
            "language": {
                "search": "Arama",
                "emptyTable": "Hiç Veri Bulunamadı"
            }


        });

    } else {


        $(selector).DataTable({paging: false});


    }

}
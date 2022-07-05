$.ajaxSetup({headers: {'csrftoken': $('meta[name="csrf-token"]').attr('content')}});

const socketUrl = $('meta[name="socket-url"]').attr('content');

var url = $("#systemurl").val();
var public_url = $("#system_public_url").val();
var account_id = $("#system_account_id").val();
var systemmobile = $("#systemmobile").val();
systemmobile = parseInt(systemmobile);

function urlify(text) {



    var urlRegex = /(https?:\/\/[^\s]+)/g;

    var hashtagRegex = /(^|\s)(#[a-z\d-]+)/ig;



    var stringdata =  text.replace(urlRegex, function(durl) {

        return '<a href="' + durl + '" target="_blank">' + durl + '</a>';

        });

  return  stringdata.replace(hashtagRegex, "$1<a href='"+url+"/post/hashtag/$2'><span class='hash_tag'>$2</a></span>");


}


function postTextarea(textdata){

    /*
        var tagsRegex = /(^|\s)(@[a-z\d-]+)/ig;

    return stringdata.replace(tagsRegex, "$1<span style='font-weight: bold;' class='usertag' data-user-tag='$2'>$2</span>");
     */



}



function loading(status){
    if (status == true) {

        $("#dipapreloader").show();
    } else {
        $("#dipapreloader").delay(1000).hide();
    }

}



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

function ajax_veriKaydet(value, id, url) {

    loading(true);
    $.ajax({
        url: url,
        type: "POST",
        data: 'value=' + value + '&id=' + id,
        success: function (data) {

            loading(false);

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


    $('body').addClass('fuse-aside-folded');
}

function ttip(position , msg){

    return " data-toggle=\"tooltip\" data-placement=\""+position+"\" title=\""+msg+"\" ";

}

function setDataTable(selector, buttons, file_name,search ) {

    if (buttons == 1) {


        $(selector).DataTable({
            searching: search,
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


        $(selector).DataTable({
            paging: false,
            searching: search,
            "language": {
            "search": "Arama",
                "emptyTable": "Hiç Veri Bulunamadı"
        }}
    );


    }

}




function sendPost(textareaid , type , id , reloadFunction){



    var url = $("#systemurl").val();

    var posttext = $("#"+textareaid).val();

    posttext = posttext.trim();

    if(posttext != ""){

        loading(true);

        var fd = new FormData();
        var files = $('#post-image-file-upload')[0].files[0];
        fd.append('file',files);
        fd.append('postdata',posttext);
        fd.append('type',type);
        fd.append('id',id );
        $.ajax({
            url: url+"/post/send",
            type: 'post',
            data: fd,
            contentType: false,
            processData: false,
            success: function(data){
                data = data.trim();



                loading(false);


                if (data == "ok") {
                    $("#"+textareaid).val("");
                    $('#post-image-file-upload').val("");



                    eval(reloadFunction);

                } else if (data == "ok-noimage") {

                    swalert("error","Resim Yükleme Başarısız Oldu","Depolama Limiti Yetersiz! \nYazı İçerik Paylaşıldı....");


                    $("#"+textareaid).val("");
                    $('#post-image-file-upload').val("");



                    eval(reloadFunction);


                }else {




                    swalert("error","Kayıt Başarısız Oldu","Post Kayıt Edilemedi!");
                }



            },
        });


    }else{

        swalert("error","Gönderim Başarısız Oldu","Boş Paylaşım Yapılamaz!");
    }



}
function sleep(milliseconds) {
    var start = new Date().getTime();
    for (var i = 0; i < 1e7; i++) {
        if ((new Date().getTime() - start) > milliseconds){
            break;
        }
    }
}


function hideSending(){

    sleep(1000);
    $("#footer-helper").removeClass("postsending");
}


function getLoadMorePosts(type , id , wallid , firstid) {

    var url = $("#systemurl").val();


    $.post(url+"/post/get-posts-with-id", {
        islem : "more",
        type : type,
        id : id,
        firstid : firstid
    })
        .done(function (data) {
            $(".load-more").attr("data-load",0);
            data = data.trim();
            var json_data = JSON.parse(data)
            if (json_data.status == 1) {
                setWallMoreData(json_data.posts , wallid,0);
            }


        })
        .fail(function () {
            $(".load-more").attr("data-load",0);
            loading(false);
            swalert("error","Veri Gönderimi Başarısız Oldu!","");
        });


    
}


function getIdPosts(type , id ,  last_id , wallid , firstid,first){

    var url = $("#systemurl").val();

    $.post(url+"/post/get-posts-with-id", {

        islem : "standart",
        type : type,
        id : id,
        lastid : last_id
    })
        .done(function (data) {




            data = data.trim();

            var json_data = JSON.parse(data);


            if (json_data.status == 1) {
                setWallData(json_data.posts , wallid,first);
            }
        })
        .fail(function () {
            swalert("error","Veri Gönderimi Başarısız Oldu!","");
        });

}


function setWallMoreData(data , wallid){



    var url = $("#systemurl").val();
    var div = $("#"+wallid);

    $.each(data,function(key , val){


            div.attr("data-first-id",val.id);

            div.append(writeWallPost(val,url));




    });


    $(".usertag").each(function() {

        if( $(this).attr("data-rep") != 1){

            $(this).attr("href",url+"/user/profile/"+$(this).attr("data-user-tag-id"));

            $(this).attr("data-rep",1);
        }

    });


    myLazyLoad.update();

    $(".load-more").hide();


}


function setWallData(data , wallid , first){

    var url = $("#systemurl").val();
    var div = $("#"+wallid);
    var first_id = 0;
    var i = 0;

    $.each(data,function(key , val){

        if(i == 0){
            first_id = val.id;
            div.attr("data-last-id",val.id);
        }

        i = i + 1;

        if(first == 1){

            div.append(writeWallPost(val,url));
            div.attr("data-first-id",val.id);

        }else{

          div.prepend(writeWallPost(val,url));
        }





        setUsertagUrl();

        myLazyLoad.update();

    });
    
}



function setUsertagUrl() {


    $(".usertag").each(function() {

        if( $(this).attr("data-rep") != 1){

            $(this).attr("href",url+"/user/profile/"+$(this).attr("data-user-tag-id"));

            $(this).attr("data-rep",1);
        }

    });

}

function writeWallPost(data,url){


    var tip = "";

   if(data.tip == "hesap") {

       tip = "- <a href='" + url + "/cari/show/" + data.cari_id + "'>" + data.cari_adi + "</a>";

   }else if(data.tip == "stok") {

       tip = "- <a href='" + url + "/stok/show/" + data.stok_id + "'>" + data.stok_adi +" "+data.stok_varyant_adi +" "+data.stok_varyant_deger +"</a>";

   }

    var html = "";
    html += " <div class=\"timeline-item card  mb-6\" data-comment-news-id =\"0\"  data-post-id-area =\""+data.id+"\"   data-post-id=\""+data.id+"\" data-post-last-comment-id='0' >";

    html += " <header class=\"row no-gutters align-items-center justify-content-between p-4\">";

    html += " <div class=\"user col\">";

    html += " <div class=\"row no-gutters align-items-center justify-content-start\">";


    html +='<img class="avatar col-auto mr-2 lazy"  alt="A lazy image"  data-src="'+url+'/storage-noi/images/'+data.pfimage+'">';


    html += " <div class=\"col\">";

    html += " <div class=\"title font-weight-bold\">";

    html += " <span class=\"username\"><a href='"+url+"/user/profile/"+data.userid+"' style='text-decoration: none;'>"+data.name+" "+data.surname+"</a></span>";

    html += " </div>";

    html += " <div class=\"time text-muted\">"+data.created_date+"  "+tip+"</div>";

    html += " </div>";
    html += " </div>";
    html += " </div>";

    html += " <button type=\"button\" class=\"col-auto btn btn-icon\" onclick=\"window.location.href='"+url+"/post/show/"+data.id+"'\" aria-label=\"more\">";
    html += " <i class=\"icon icon-dots-vertical\"></i>";
    html += " </button>";

    html += " </header>";

    html += " <div class=\"content\" >";

    html += " <div class=\"message py-2 px-4\" >";
    html += urlify(data.mesaj);
    html += " </div>";


if(data.image != null && data.image != "noimage.jpg"){
    html += "<div class=\"post-media\"> <a href=\""+url+"/post/show/"+data.id+"\"> ";
    html += "<img class=\"media-img lazy\" style='width:100%;max-width: 100%;'  data-src=\""+url+"/storage-noi/images/"+data.image+"\"></a>";
    html += "</div>";

}


    html += " </div>";

    var comment =  data.commentcount;

    if(comment == null){
        comment = 0;
    }


    if(data.yorum_durum == 1){

        html += " <div class=\"comment-count px-2 py-2\">";
        html += "<button type='button' style='background: none;cursor:pointer;' onclick='yorumGetir("+data.id+",\"first\")' class=''><span id='post-comment-count-"+data.id+"'>"+comment+"</span>  Yorum</button>";
        html += "</div>";


        html += " <footer class=\"bg-light p-4\">";
        html += " <div class=\"reply row no-gutters\">";
        html += " <form class=\"col\">";
        html += " <textarea class=\"p-2 mb-2 w-100\" id='yorumtextarea-"+data.id+"' placeholder=\"Yorum yazın...\" ></textarea>";
        html += " <button type=\"button\" class=\"post-comment-button btn btn-sm btn-secondary\" onclick=\"yorumEkle("+data.id+")\" aria-label=\"Post Comment\">Yorum Kaydet";
        html += " </button>";
        html += " </form>";
        html += " </div>";
        html += " <div id='post-comments-"+data.id+"' class='m-t-15'>";
        html += " </div>";

        html += "<div class=\"comments-loadmore px-2 py-2 more-unvisible\" data-comment-more="+data.id+">";
        html += "<button type='button' style='background: none;cursor:pointer;' onclick='yorumGetir("+data.id+",\"more\")' class=''><span id='post-comment-count-"+data.id+"'>Daha Fazla Yükle...</button>";
        html += "</div>";


        html += " </footer>";



    }



    html += " </div>";


return html;


}



function yorumGetir(post_id , type){

    var lastid =  $("[data-post-id-area = "+post_id+" ]").attr("data-post-last-comment-id");

    var bigid = $("[data-post-id-area = "+post_id+" ]").attr("data-comment-news-id");

    var bigno = bigid;

   var last_comment_id =  $("[data-post-id-area = "+post_id+" ]").attr("data-post-last-comment-id");

    var post_data ={
        postid : post_id,
        lastcomment : lastid,
        type : type,
        bigid : bigid

    };


    $.post(url+"/post/comments/"+post_id, post_data).done(function (data) {

        var result = JSON.parse(data);

        if(result.status == 1){

            var comments = result.comments;



            var ii = 0;

            if(lastid == 0){

                comments = comments.reverse();

            }else if(type == "news"){

                comments = comments.reverse();
            }

            $.each(comments,function(key , val){

                if(lastid == 0){

                    $("#post-comments-"+post_id).prepend(yorumHtml(val,url));

                }else if(type == "news"){

                    $("#post-comments-"+post_id).prepend(yorumHtml(val,url));

                }else{

                    $("#post-comments-"+post_id).append(yorumHtml(val,url));
                }



                if(lastid == 0 && type != "news"){

                    if(ii == 0){
                        $("[data-post-id-area = "+post_id+" ]").attr("data-post-last-comment-id",val.id);
                    }

                }else if(type == "news"){

                    if(ii == 0 && last_comment_id > val.id){

                        $("[data-post-id-area = "+post_id+" ]").attr("data-post-last-comment-id",val.id);
                    }

                }else{

                        $("[data-post-id-area = "+post_id+" ]").attr("data-post-last-comment-id",val.id);


                }



                if(val.id > bigid ){

                    bigno = val.id;

                }

                ii = ii +1;

            });


            $("[data-post-id-area = "+post_id+" ]").attr("data-comment-news-id",bigno);


         var lcmkontrol =    $("[data-post-id-area = "+post_id+" ]").attr("data-post-last-comment-id");

            if(bigno > 0 && lcmkontrol == 0 ){

                $("[data-post-id-area = "+post_id+" ]").attr("data-post-last-comment-id",bigno);
            }




            $("#post-comment-count-"+post_id).html(result.commentcount);



           if(result.more == 0){

               $("[data-comment-more = "+post_id+" ]").addClass("more-unvisible");

           }else if(result.more == 1){

               $("[data-comment-more = "+post_id+" ]").removeClass("more-unvisible");

           }

            setUsertagUrl();

        }else{
            $("[data-comment-more = "+post_id+" ]").addClass("more-unvisible");

           }


    });




}

function yorumEkle(post_id){

    var commenttext = $("#yorumtextarea-"+post_id).val();
    commenttext = commenttext.trim();

    if(commenttext != ""){


        $.post(url+"/post/comment/add", {
            postid : post_id,
            comment : commenttext,
            commentid : 0
        }).done(function (data) {


            if(data.trim() == "ok"){

                $("#yorumtextarea-"+post_id).val("");

                yorumGetir(post_id,"news");

            }else{

                swalert("error","Yorum Başarısız Oldu","Yorum Eklenemedi! Bu Paylaşım Silmiş Olabilir!");
            }

        });

    }




}

function formatDate(date) {
    var monthNames = [
        "Ocak", "Şubat", "Mart",
        "Nisan", "Mayıs", "Haziran", "Temmuz",
        "Ağustos", "Eylül", "Ekim",
        "Kasım", "Aralık"
    ];

    var day = date.getDate();
    var monthIndex = date.getMonth();
    var year = date.getFullYear();
var hours =date.getHours();
var min = date.getMinutes();
    return day + ' ' + monthNames[monthIndex] + ' ' + year+' '+hours+':'+min;
}


function yorumHtml(data,url){


    var datetime = new Date(data.created_date);

     var html = "";
    html += " <div class=\"comment row no-gutters mb-6\" data-post-id='"+data.post_id+"' data-comment-id='"+data.id+"''>";
    html += '<img src="'+url+'/storage-noi/images/'+data.pfimage+'" class="col-auto avatar mr-2" />';
    html += " <div class=\"col\">";
    html += " <div class=\"row no-gutters align-items-center\">";
    html += " <span class=\"username font-weight-bold mr-1\">"+data.name+" "+data.surname+"</span>";
    html += " <span class=\"time text-muted\">"+data.id+")"+formatDate(datetime)+"</span>";
    html += " </div>";

    html += " <div class=\"message comment-msg\">";


    html += urlify(data.comment);
    html += " </div>";
/*
    html += " <div class=\"actions row no-gutters align-items-center justify-content-start\">";
    html += " <a href=\"#\" class=\"reply-button\">Yanıtla</a>";
    html += " </div>";

 */
    html += " </div>";
    html += " </div>";
    return html;

}


/*
BİLDİRİMLER
 */





function yeniBildirimKontrol() {

    if (systemmobile != 1) {

    var ignore = "";

    var i = 0;

    $(".bildirimlistesi").each(function () {

        if ($(this).attr("data-okunmayan-bildirim") == 1) {

            ignore += $(this).attr("data-bildirim-id") + ",";

            i = i + 1;
        }


    });


    if (i > 0) {

        ignore = ignore.replace(/,+$/, '');

    }


    $.post(url + "/bildirim/kontrol", {
        tip: 1,
        ignorelist: ignore

    }).done(function (data) {

        data = data.trim();

        if (data == 1) {

            $("#data-alert").attr("data-alert", 1);

            $("#bildirimleri-icon").addClass("bildirim-aktif-icon");


            $.post(url + "/bildirim/yenibildirimler", {
                adet: 15,
                ignorelist: ignore
            }).done(function (data) {
                data = data.trim();
                var result = JSON.parse(data);


                if (result.status == 1) {

                    $.each(result.bildirimler, function (key, val) {

                        //alertify.success(val.bildirim_mesaj);

                        $("#anasayfa-bildirimleri").prepend(anasayfaBildirimYaz(val,0));

                        $("#butun-bildirimler-bolumu").prepend(anasayfaBildirimYaz(val,1));



                    });

                }

                $("#data-alert").attr("data-alert", 0);


            }).fail(function () {

                $("#data-alert").attr("data-alert", 0);

            });


        }
    }).fail(function () {
    });
}
}

/*

setInterval(function(){

    if($("#bildirimleri-icon").attr("data-alert") == "0"){

        yeniBildirimKontrol();

    }


}, 10000);

 */



$("#bildirimleri-icon").on("click",function () {


    var  okunanlar = "";

    var i = 0;

    $(".bildirimlistesi").each(function() {


        if($(this).attr("data-okunmayan-bildirim") == 1){

            okunanlar+=$(this).attr("data-bildirim-id")+",";

            i = i+1;

        }


    });


    if(i > 0){




        okunanlar = okunanlar.replace(/,+$/, '');

        $.post(url+"/bildirim/okunduyap", {
            liste : okunanlar
        }).done(function (data) {

            data = data.trim();

            var result = JSON.parse(data);

            if (result.status == 1) {

                $(".bildirimlistesi").each(function() {

                    $(this).attr("data-okunmayan-bildirim",0);

                });
                $("#bildirimleri-icon").removeClass("bildirim-aktif-icon");
            }

        }).fail(function () {



        });


    }





});
function timeConverter(UNIX_timestamp){
    var a = new Date(UNIX_timestamp * 1000);
    var months = ['1','2','3','4','5','6','7','8','9','10','11','12'];
    var year = a.getFullYear();
    var month = months[a.getMonth()];
    var date = a.getDate();
    var hour = a.getHours();
    var min = a.getMinutes();
    //var sec = a.getSeconds();
    var time = date + '-' + month + '-' + year + '-' + hour + ':' + min  ;
    return time;
}

function anasayfaBildirimYaz(data , type){



    var zaman = timeConverter(data.epoch_time );


    var url_ek = "";

    if(data.tip ==0){

    }else if(data.tip == 1){

        url_ek = "post/show/"+data.islem_id;


    }else if(data.tip == 2){

        url_ek = "post/show/"+data.islem_id;


    }else if(data.tip == 3){

        url_ek = "post/show/"+data.islem_id;


    }else if(data.tip == 5){


        url_ek = "post/show/"+data.islem_id;

    }else if(data.tip == 6){

        url_ek = "post/show/"+data.islem_id;

    }else if(data.tip == 7){

        url_ek = "post/show/"+data.islem_id;


    }



    var html = "";


    if(type == 0){

        html+="<div class=\"activity row no-gutters bildirimlistesi bildirim-kalem p-4 text-white\" style='padding:5px;background-color: #2196F3;' data-bildirim-id=\""+data.id+"\"  data-okunmayan-bildirim=\"1\" >";
        html+="<div style=\"width: 100%;\">";

        html+="<div style=\"width: 30px;float: left;\">";
        if(data.bildirim_icon == ""){
            html+="<i class=\"secondary-text s-48 mat-icon notranslate material-icons mat-icon-no-color\" role=\"img\" aria-hidden=\"true\">keyboard_arrow_right</i>";
        }else{
            html+="<i class=\"secondary-text s-48 mat-icon notranslate material-icons mat-icon-no-color\" role=\"img\" aria-hidden=\"true\">"+data.bildirim_icon+"</i>";
        }
        html+="</div>";
        html+=" <div  style=\" width: 100%; padding-right: 150px; margin-right: -150px;float: left;\">";
        html+=" <span class=\"bildirim-baslik  text-white\">"+data.bildirim_baslik+"</span> <br>";
        html+="<span class=\"message\">";
        html+="<a class=\"bildirim bildirim-mesaj text-white\" href='"+url+"/"+url_ek+"'>"+data.bildirim_mesaj+"</a>";
        html+="</span></div>";
        html+="<div style=\"width: 120px;float: left;\"><span class=\"message\"><span class=\"time text-muted bildirim-mesaj text-white\">";
        html+=zaman;
        html+="</span></span></div>";
        html+="</div>";
        html+="</div>";


    }else if(type == 1){


        html+="<div class=\"activity row no-gutters bildirimlistesi bildirim-kalem text-white\" style='width:100%;padding:5px;padding-bottom:10px;padding-top: 10px;background-color: #2196F3;' data-bildirim-id=\""+data.id+"\"  data-okunmayan-bildirim=\"1\" >";


        html+="<div style=\"width: 100%;float: left;text-align: center\"\">";
        if(data.bildirim_icon == ""){
            html+="<i class=\"secondary-text s-48 mat-icon notranslate material-icons mat-icon-no-color\" role=\"img\" aria-hidden=\"true\">keyboard_arrow_right</i>";
        }else{
            html+="<i class=\"secondary-text s-48 mat-icon notranslate material-icons mat-icon-no-color\" role=\"img\" aria-hidden=\"true\">"+data.bildirim_icon+"</i>";
        }
        html+="</div>";


        html+=" <div  style=\" width: 100%;float: left;text-align: center\"\">";
        html+=" <span class=\"bildirim-baslik  text-white\">"+data.bildirim_baslik+"</span> <br>";
        html+="<span class=\"message\"><span class=\"time text-muted bildirim-mesaj text-white\">";
        html+=zaman;
        html+="</span></span><br>";
        html+="<span class=\"message\">";
        html+="<a class=\"bildirim bildirim-mesaj text-white\" href='"+url+"/"+url_ek+"'>"+data.bildirim_mesaj+"</a>";
        html+="</span></div>";





        html+="</div>";


    }



    return html;
}




function open_popup(url)
{
    var w = 880;
    var h = 570;
    var l = Math.floor((screen.width-w)/2);
    var t = Math.floor((screen.height-h)/2);
    var win = window.open(url, 'Avokado Yazılım', "scrollbars=1,width=" + w + ",height=" + h + ",top=" + t + ",left=" + l);
    win.document.title = "Avokado Yazılım";
}



function publi_image_callback(field_id){


    var url_string=jQuery('#'+field_id).val();


    if (url_string.indexOf('http://') === -1 && url_string.indexOf('https://') === -1) {

        url_string = public_url+"/"+account_id+"/s/"+ url_string;

    }

    $("#img-"+field_id).attr("src",url_string);

}


function image_callback(field_id){

    var url_string=jQuery('#'+field_id).val();


    if (url_string.indexOf('http://') === -1 && url_string.indexOf('https://') === -1) {

        url_string = url+"/storage-noi/"+ url_string;

    }

    $("#img-"+field_id).attr("src",url_string);

}

function imageKontrol(field_id) {

    var url_string=jQuery('#'+field_id).val();


    if (url_string.indexOf('http://') === -1 && url_string.indexOf('https://') === -1) {

        url_string = url+"/storage-noi/"+ url_string;

    }

    $("#img-"+field_id).attr("src",url_string);

}
/*

var socket = io(socketUrl);

socket.on('bildirim', function (data) {

    if(data.no == account_id){

        yeniBildirimKontrol();
    }

});

 */




function closeFrameModel() {

    $("#frameLayoutModelIframe").css("display", "none");
    $("#frameLayoutModelLoading").css("display", "block");
    $("#frameLayoutModelIframe").attr("src", "");
}

function openFrameModel(src) {

    $("#frameLayoutModelIframe").attr("src", src);
    $("#frameLayoutModel").modal("show");
    $("#frameLayoutModelIframe").css("display", "block");
    $("#frameLayoutModelLoading").css("display", "none");
}

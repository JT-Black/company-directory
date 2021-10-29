Handlebars.registerHelper('json', function(obj) {
    return JSON.stringify(obj);
  });

Handlebars.registerPartial('employeesList', $("#employees-list-template").html());

var router = new Router({
    mode: 'hash',
});

router.add('', function () {
    $("#navigation li.active").removeClass("active");
    $(".nav-empl").addClass("active");

    $.when($.ajax("/employees"),$.ajax("/departments"))
    .done(function( employees, departments ) {
        let data = {
            employees:employees[0],
            departments:departments[0],
        }
        let template = Handlebars.compile($("#employees-template").html());
        $("#content").html(template(data));
        jdenticon();

    });
});

router.add('/departments', function () {
    $("#navigation li.active").removeClass("active");
    $(".nav-depa").addClass("active");
    $.when($.ajax("/departments"),$.ajax("/locations"))
    .done(function( departments, locations ) {
        let data = {
            departments:departments[0],
            locations:locations[0],
        }
        let template = Handlebars.compile($("#departments-template").html());
        $("#content").html(template(data));

    });
});

router.add('/locations', function () {
    $("#navigation li.active").removeClass("active");
    $(".nav-loca").addClass("active");
    $.ajax({
        method: "GET",
        url: "/locations",

    })
    .always(function( response ) {
        let template = Handlebars.compile($("#locations-template").html());
        $("#content").html(template(response));

    });
});

router.addUriListener();
router.check('')

// common event listeners
$( "#navigation" ).on( "click", "a", function( event ) {
    event.preventDefault();
    router.navigateTo($( this ).attr("href"));
    $("#search [name='search']").val("");

});

$("body").on("click",".delete", function( event ) {
    event.preventDefault();

    $.ajax({
        type:"post",
        url:this.href,
        data:{
            _method: "delete",
        },
        cache:false,
        statusCode: {
            200: function(){
                router.check();
            },
            500: function(response) {
                $("#message-modal").modal();
                $("#message-modal").find("#error-messages").html(response.responseJSON.message);
            },
        },
    })
});

$("body").on("click", ".edit", function( event ){
    event.preventDefault();
    let link = $(this);
    let modal = $(link.data("modal"));
    let form = modal.find("form");
    form.attr('action', link.data("url"));
    populate(form.get(0), link.data("item"));

    modal.modal();
});

$("#content").on("submit","form", function( event ) {

    event.preventDefault();
    let form = $(this);
    $.ajax({
        type: "post",
        url: this.action,
        data: $(this).serialize(),
        statusCode: {
            200: function(){
                form.closest(".modal").modal("hide");
                router.check();
            },
            422: function(response) {

                let template = Handlebars.compile($("#error-template").html());
                form.find(".form-errors").html(template(response.responseJSON));
            },
        },
    })
});

$('.modal').on('hidden.bs.modal', function (e) {
    $(this).find(".form-errors").html("");
  });

$("#search [name='search']").on("keyup", function( event ) {
//   event.preventDefault();
    let form = $(this).closest("form");
    $.ajax({
        type: "get",
        url: form.attr("action"),
        data: form.serialize(),
        statusCode: {
            200: function(response){
            let template = Handlebars.compile($("#search-template").html());
            $("#content").html(template({employees:response}));

            jdenticon();
            },
        },
    })
});

$("#content").on("click","#clear-search", function(event){
    event.preventDefault();
    $("#search [name='search']").val("");
    router.check();
});


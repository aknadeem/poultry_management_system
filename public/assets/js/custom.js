// delete Form
$(document).on("click", ".delete-confirm", function (event) {
    /* Act on the event */
    event.preventDefault();
    var action = $(this).attr("href");
    var del_title = $(this).attr("del_title");

    $.confirm({
        columnClass: "col-md-5",
        autoClose: false,
        theme: "modern",
        title: "Confirm Please?",
        content:
            " Are You Sure You Want to Delete <br> <b>" + del_title + " </b>?",
        type: "dark",
        typeAnimated: true,
        draggable: false,
        buttons: {
            ok: {
                useBootstrap: false,
                text: "Delete",
                btnClass: "btn-danger",
                keys: ["enter"],
                action: function () {
                    // var action = event.$target.attr('href');
                    $("form#delete-form").attr("action", action);
                    $("form#delete-form").submit();
                    // alert('heelo');
                    console.log("the user clicked confirm");
                },
            },
            cancel: {
                text: "Cancel",
                keys: ["esc"],
                cancel: function () {
                    console.log("the user clicked cancel");
                },
            },
        },
    });
});

//open add form
$("#OpenAddForm").click(function (event) {
    $("#addForm").show();
});

$("#CloseAddForm").click(function (abc) {
    $("#addForm").hide();
});

$("a.confirm-delete").confirm({
    theme: "modern",
    title: "Confirm Please?",
    content: $(this).attr("msg"),
});

// Confirmation msg

$("a.btn-confirm").confirm({
    theme: "modern",

    title: "Confirm Please?",
    content: $(this).attr("msg"),
    type: "dark",
    typeAnimated: true,
    draggable: false,
    buttons: {
        ok: {
            useBootstrap: false,
            text: "Yes!",
            btnClass: "btn-primary",
            keys: ["enter"],
            action: function () {
                location.href = this.$target.attr("href");
                console.log("the user clicked confirm");
            },
        },
        cancel: {
            text: "No",
            keys: ["esc"],
            cancel: function () {
                console.log("the user clicked cancel");
            },
        },
    },
});

$(".confirm-form").submit(function () {
    currentForm = this;
    $.confirm({
        theme: "modern",
        title: "Confirm Please?",
        content: $(this).attr("msg"),
        type: "dark",
        typeAnimated: true,
        draggable: false,
        buttons: {
            Confirm: function () {
                currentForm.submit();
            },
            Cancel: function () {
                // $(this).dialog('close');
            },
        },
    });
    return false;
});

$(".confirm-select").change(function () {
    currentForm = this;
    $.confirm({
        theme: "modern",
        title: "Confirm Please?",
        content: $(this).attr("msg"),
        type: "dark",
        typeAnimated: true,
        draggable: false,
        buttons: {
            Confirm: function () {
                currentForm.form.submit();
            },
            Cancel: function () {
                // $(this).dialog('close');
            },
        },
    });
    return false;
});

// User Active InActive Status Confirmation msg

$(document).on("click", ".confirm-logout", function (event) {
    /* Act on the event */
    event.preventDefault();
    var action = $(this).attr("href");
    var msg = $(this).attr("msg_content");
    $.confirm({
        theme: "modern",
        title: "Confirm Please?",
        content: "<b>" + msg + "</b>",
        type: "red",
        typeAnimated: true,
        draggable: false,
        buttons: {
            ok: {
                useBootstrap: false,
                text: "Yes!",
                btnClass: "btn-red btn-xs",
                keys: ["enter"],
                action: function () {
                    // var action = this.$target.attr('href')
                    $("form#logout-form").attr("action", action);
                    $("form#logout-form").submit();
                    // console.log("the user clicked confirm");
                },
            },
            cancel: {
                text: "No",
                keys: ["esc"],
                cancel: function () {
                    // console.log("the user clicked cancel");
                },
            },
        },
    });
});

$(document).on("click", ".confirm-status", function (event) {
    /* Act on the event */
    event.preventDefault();
    var action = $(this).attr("href");
    var msg = $(this).attr("msg") || "Are you sure to continue?";
    $.confirm({
        columnClass: "col-3",
        theme: "modern",
        title: "Confirm Please?",
        content: msg,
        type: "dark",
        typeAnimated: true,
        draggable: false,
        buttons: {
            ok: {
                useBootstrap: false,
                text: "Yes!",
                btnClass: "btn-info btn-xs",
                keys: ["enter"],
                action: function () {
                    // var action = this.$target.attr('href')
                    $("form#status-form").attr("action", action);
                    $("form#status-form").submit();
                    console.log("the user clicked confirm");
                },
            },
            cancel: {
                text: "No",
                keys: ["esc"],
                cancel: function () {
                    console.log("the user clicked cancel");
                },
            },
        },
    });
});

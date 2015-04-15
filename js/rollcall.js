/*
 * elbmun-desktop, Web Based MUN Software
 * (c) by Jannes Riffert, Elbe Model United Nations e.V. Dresden
 * https://github.com/schnaejae/elbmun-desktop/, http://elbmun.org
 * MIT License
 */
$('.rc-reset').click(function () {
    $('.rc-present').removeClass('highlight');
    $('.rc-voting').removeClass('highlight');
    $('.rc-absent').addClass('highlight');
    jQuery.ajax({
        type: "POST",
        url: "script.php",
        data: {
            "a": "rollcall",
            "action": "resetRollCall"
        },
        dataType: "text"
    });
});
$('.rc-voting').click(function () {
    var tr = $(this).parents('tr:first');
    tr.find('.rc-present').removeClass('highlight');
    tr.find('.rc-absent').removeClass('highlight');
    $(this).addClass('highlight');
    jQuery.ajax({
        type: "POST",
        url: "script.php",
        data: {
            "a": "rollcall",
            "action": "setRollCall",
            "call": "voting",
            "iso": tr.data("iso")
        },
        dataType: "text"
    });
});

$('.rc-present').click(function () {
    var tr = $(this).parents('tr:first');
    tr.find('.rc-voting').removeClass('highlight');
    tr.find('.rc-absent').removeClass('highlight');
    $(this).addClass('highlight');
    jQuery.ajax({
        type: "POST",
        url: "script.php",
        data: {
            "a": "rollcall",
            "action": "setRollCall",
            "call": "present",
            "iso": tr.data("iso")
        },
        dataType: "text"
    });
});

$('.rc-absent').click(function () {
    var tr = $(this).parents('tr:first');
    tr.find('.rc-present').removeClass('highlight');
    tr.find('.rc-voting').removeClass('highlight');
    $(this).addClass('highlight');
    jQuery.ajax({
        type: "POST",
        url: "script.php",
        data: {
            "a": "rollcall",
            "action": "setRollCall",
            "call": "absent",
            "iso": tr.data("iso")
        },
        dataType: "text"
    });
});

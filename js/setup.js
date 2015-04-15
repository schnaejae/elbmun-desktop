/*
 * elbmun-desktop, Web Based MUN Software
 * (c) by Jannes Riffert, Elbe Model United Nations e.V. Dresden
 * https://github.com/schnaejae/elbmun-desktop/, http://elbmun.org
 * MIT License
 */
var topicTable = $('#setup-general-topic');
var generalChangeTopicName = function (e) {
    if (!$(e.target).val()) {
        return;
    }
    var tr = $(e.target).parents('tr:first');
    var index = topicTable.children().index(tr);
    jQuery.ajax({
        type: "POST",
        url: "script.php",
        data: {
            "a": "setup",
            "action": "setGeneralTopicName",
            "index": index,
            "value": $(e.currentTarget).val()
        },
        dataType: "text"
    });
};
var generalChangeCurrentTopic = function (e) {
    var tr = $(e.currentTarget).parents('tr:first');
    var index = topicTable.children().index(tr);
    jQuery.ajax({
        type: "POST",
        url: "script.php",
        data: {
            "a": "setup",
            "action": "setGeneralTopicCurrent",
            "index": index
        },
        dataType: "text"
    });
};
var generalRemoveTopic = function (e) {
    var tr = $(e.currentTarget).parents('tr:first');
    var index = topicTable.children().index(tr);
    tr.remove();
    jQuery.ajax({
        type: "POST",
        url: "script.php",
        data: {
            "a": "setup",
            "action": "setGeneralTopicRemove",
            "index": index
        },
        dataType: "text"
    });
};
$('#setup-general-name').bind("change", function () {
    if (!$(this).val()) {
        return;
    }
    jQuery.ajax({
        type: "POST",
        url: "script.php",
        data: {
            "a": "setup",
            "action": "setGeneralName",
            "value": $(this).val()
        },
        dataType: "text"
    });
});

$('#setup-general-organization').bind("change", function () {
    if (!$(this).val()) {
        return;
    }
    jQuery.ajax({
        type: "POST",
        url: "script.php",
        data: {
            "a": "setup",
            "action": "setGeneralOrganization",
            "value": $(this).val()
        },
        dataType: "text"
    });
});
$('.setup-general-topic-name').bind("change", function (e) {
    generalChangeTopicName(e);
});
$('.setup-general-topic-current').bind("change", function (e) {
    generalChangeCurrentTopic(e);
});
$('.setup-general-topic-remove').bind("click", function (e) {
    generalRemoveTopic(e);
    return false;
});
$('#setup-general-topic-add').bind("click", function () {
    topicTable.append("<tr><td><input type='radio' name='setup-general-topic-current' class='setup-general-topic-current'></td><td><input type='text' class='setup-general-topic-name input-xxlarge' placeholder='Topic' value=''/></td><td style='font-size:1.3em'><a href='' style='text-decoration:none;' class='setup-general-topic-remove'><i class='icon-remove'></i></a></th></tr>");
    jQuery.ajax({
        type: "POST",
        url: "script.php",
        data: {
            "a": "setup",
            "action": "setGeneralTopicAdd"
        },
        dataType: "text",
        success: function (data) {
            console.log(data);
        }
    });
    $('.setup-general-topic-name:last').bind("change", function (e) {
        generalChangeTopicName(e);
    });
    $('.setup-general-topic-current:last').bind("change", function (e) {
        generalChangeCurrentTopic(e);
    });
    $('.setup-general-topic-remove:last').bind("click", function (e) {
        generalRemoveTopic(e);
        return false;
    });
    return false;
});

$('.setup-countries-member').click(function () {
    var tr = $(this).parents('tr:first');
    tr.find('button').removeClass('highlight');
    $(this).addClass('highlight');
    jQuery.ajax({
        type: "POST",
        url: "script.php",
        data: {
            "a": "setup",
            "action": "countryMember",
            "iso": tr.data('iso')
        },
        dataType: "text"
    });
});

$('.setup-countries-observer').click(function () {
    var tr = $(this).parents('tr:first');
    tr.find('button').removeClass('highlight');
    $(this).addClass('highlight');
    jQuery.ajax({
        type: "POST",
        url: "script.php",
        data: {
            "a": "setup",
            "action": "countryObserver",
            "iso": tr.data('iso')
        },
        dataType: "text"
    });
});

$('.setup-countries-none').click(function () {
    var tr = $(this).parents('tr:first');
    tr.find('button').removeClass('highlight');
    $(this).addClass('highlight');
    jQuery.ajax({
        type: "POST",
        url: "script.php",
        data: {
            "a": "setup",
            "action": "countryRemove",
            "iso": tr.data('iso')
        },
        dataType: "text"
    });
});

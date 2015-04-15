/*
 * elbmun-desktop, Web Based MUN Software
 * (c) by Jannes Riffert, Elbe Model United Nations e.V. Dresden
 * https://github.com/schnaejae/elbmun-desktop/, http://elbmun.org
 * MIT License
 */
var dodrTime = $('#dodr-speaking-time');
var dodrWarning = $('#dodr-warning-time');
var dodrCurrentSpeaker = $('#dodr-current-speaker');
var dodrSpeakerList = $('#dodr-list');
var dodrPool = $('#dodr-pool');
var dodrSpeakerName, dodrSpeakerIso;
var dodrCircle = new ProgressBar.Circle('#dodr-progress-bar', {
    color: '#4AC8E9',
    trailColor: '#eee',
    trailWidth: 1,
    strokeWidth: 5,
    text: {
        autoStyle: true
    },

    // Set default step function for all animate calls
    step: function (state, circle) {
        if (circle.value() == 1 || circle.value() == 0) {
            circle.setText(circle.value() * dodrTime.val());
        } else {
            circle.setText(parseInt(circle.value() * dodrTime.val(), 0) + 1);
        }
        if (circle.value() < (dodrWarning.val() / dodrTime.val())) {
            circle.path.setAttribute('stroke', '#ff0000');
            circle.text.setAttribute('style', "color:#ff0000");
        } else {
            circle.path.setAttribute('stroke', '#4AC8E9');
            circle.text.setAttribute('style', "color:#4AC8E9");
        }

    }
});
$('#dodr-start-button').click(function () {
    if (dodrSpeakerName != null && dodrSpeakerIso != null) {
        dodrTime.prop("disabled", true);
        dodrCircle.animate(0, {duration: dodrTime.val() * 1000 * dodrCircle.value()}, function () {
            dodrTime.prop("disabled", false);
            if ($('#dodr-auto-next').prop('checked')) {
                dodrNextSpeaker();
            }
        });
    }
    return false;
});
$('#dodr-reset-button').click(function () {
    if (dodrSpeakerName != null && dodrSpeakerIso != null) {
        dodrCircle.set(1);
        dodrTime.prop("disabled", false);
    }
    return false;
});
$('#dodr-pause-button').click(function () {
    dodrCircle.stop();
    dodrTime.prop("disabled", false);
    return false;
});
$('#dodr-next-button').click(function () {
    dodrTime.prop("disabled", false);
    dodrNextSpeaker();
    return false;
});
$('#dodr-resize-button').click(function () {
    var tab = $('#tabs-1');
    if ($(this).hasClass('highlight')) {
        $(this).removeClass('highlight');
        tab.find('.debate-container:first').css('width', '40%');
        setTimeout(function () {
            tab.find('.debate-container:last').fadeIn("fast")
        }, 1000);
    } else {
        $(this).addClass('highlight');
        tab.find('.debate-container:last').fadeOut("fast", "linear", function () {
            tab.find('.debate-container:first').css('width', '65%');
        });
    }
    return false;
});
dodrTime.bind("change", function () {
    jQuery.ajax({
        type: "POST",
        url: "script.php",
        data: {
            "a": "debate",
            "action": "setResolutionSpeakingTime",
            "value": $(this).val()
        },
        dataType: "text"
    });
    dodrCircle.set(dodrCircle.value());
});
dodrWarning.bind("change", function () {
    jQuery.ajax({
        type: "POST",
        url: "script.php",
        data: {
            "a": "debate",
            "action": "setResolutionWarningTime",
            "value": $(this).val()
        },
        dataType: "text"
    });
});
$('#dodr-auto-next').bind("change", function () {
    jQuery.ajax({
        type: "POST",
        url: "script.php",
        data: {
            "a": "debate",
            "action": "setResolutionAutoNext",
            "value": $(this).prop("checked") ? "true" : "false"
        },
        dataType: "text"
    });
});
$(document).ready(function () {
    if (dodrCurrentSpeaker.data('name') != null && dodrCurrentSpeaker.data('iso') != null) {
        dodrSpeakerName = dodrCurrentSpeaker.data('name');
        dodrSpeakerIso = dodrCurrentSpeaker.data('iso');
        dodrCircle.set(1);
    }
    dodrPool.children().each(function () {
        $(this).tipsy();
        $(this).click(function () {
            if (dodrSpeakerName == null && dodrSpeakerIso == null) {
                dodrSpeakerIso = $(this).data('iso');
                dodrSpeakerName = $(this).data('name');
                dodrCurrentSpeaker.html('<img src="img/flags_big/' + dodrSpeakerIso.toUpperCase() + '.png" alt="' + dodrSpeakerIso.toUpperCase() + '" width="70%"><h3>' + dodrSpeakerName + '</h3>');
                $(this).hide();
                dodrCircle.set(1);
            } else {
                dodrSpeakerList.append("<tr data-iso='" + $(this).data('iso') + "' data-name='" + $(this).data('name') + "'><td class='label-cell'><img src='img/flags_small/" + $(this).data('iso') + ".png' alt='" + $(this).data('iso') + "' /></td><td style='font-size: 1.5em'>" + $(this).data('name') + "</td><td class='label-cell'><a href='' style='text-decoration: none;' class='dodr-list-up'><i class='icon-arrow-up'></i></a><a href='' style='text-decoration: none;' class='dodr-list-down'><i class='icon-arrow-down'></i></a></td><td class='label-cell'  style='padding-right:14px'><a href='' style='text-decoration: none;' class='dodr-list-remove'><i class='icon-remove'></i></a></td></tr>");
                $('.dodr-list-remove').last().click(function (e) {
                    dodrRemoveSpeaker(e);
                    return false;
                });
                $('.dodr-list-up').last().click(function (e) {
                    dodrMoveSpeakerUp(e);
                    return false;
                });
                $('.dodr-list-down').last().click(function (e) {
                    dodrMoveSpeakerDown(e);
                    return false;
                });
                $(this).hide();
            }
            jQuery.ajax({
                type: "POST",
                url: "script.php",
                data: {
                    "a": "debate",
                    "action": "addResolutionSpeaker",
                    "iso": $(this).data('iso')
                },
                dataType: "text"
            });
        });
    });
    $('.dodr-list-remove').click(function (e) {
        dodrRemoveSpeaker(e);
        return false;
    });
    $('.dodr-list-up').click(function (e) {
        dodrMoveSpeakerUp(e);
        return false;
    });
    $('.dodr-list-down').click(function (e) {
        dodrMoveSpeakerDown(e);
        return false;
    });
});

dodrNextSpeaker = function () {
    dodrPool.children('*[data-iso="' + dodrSpeakerIso + '"]').show();
    jQuery.ajax({
        type: "POST",
        url: "script.php",
        data: {
            "a": "debate",
            "action": "removeResolutionSpeaker",
            "iso": dodrSpeakerIso
        },
        dataType: "text"
    });
    var tr = dodrSpeakerList.contents('tr:first');
    if (tr.length > 0) {
        dodrSpeakerIso = tr.data('iso');
        dodrSpeakerName = tr.data('name');
        dodrCurrentSpeaker.html('<img src="img/flags_big/' + dodrSpeakerIso.toUpperCase() + '.png" alt="' + dodrSpeakerIso.toUpperCase() + '" width="70%"><h3>' + dodrSpeakerName + '</h3>');
        tr.remove();
        dodrCircle.set(1);
    } else {
        dodrSpeakerIso = null;
        dodrSpeakerName = null;
        dodrCurrentSpeaker.html("");
        dodrCircle.set(0);
    }
};

dodrRemoveSpeaker = function (e) {
    var tr = $(e.currentTarget).parents('tr:first');
    dodrPool.children('*[data-iso="' + tr.data('iso') + '"]').show();
    tr.remove();
    jQuery.ajax({
        type: "POST",
        url: "script.php",
        data: {
            "a": "debate",
            "action": "removeResolutionSpeaker",
            "iso": tr.data('iso')
        },
        dataType: "text"
    });
};

dodrMoveSpeakerUp = function (e) {
    var tr = $(e.currentTarget).parents('tr:first');
    jQuery.ajax({
        type: "POST",
        url: "script.php",
        data: {
            "a": "debate",
            "action": "moveResolutionSpeaker",
            "iso": tr.data('iso'),
            "direction": "up"
        },
        dataType: "text"
    });
    tr.insertBefore(tr.prev());
};

dodrMoveSpeakerDown = function (e) {
    var tr = $(e.currentTarget).parents('tr:first');
    jQuery.ajax({
        type: "POST",
        url: "script.php",
        data: {
            "a": "debate",
            "action": "moveResolutionSpeaker",
            "iso": tr.data('iso'),
            "direction": "down"
        },
        dataType: "text",
        success: function (data) {
            console.log(data);
        }
    });
    tr.insertAfter(tr.next());
};


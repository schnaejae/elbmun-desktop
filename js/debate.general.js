/*
 * elbmun-desktop, Web Based MUN Software
 * (c) by Jannes Riffert, Elbe Model United Nations e.V. Dresden
 * https://github.com/schnaejae/elbmun-desktop/, http://elbmun.org
 * MIT License
 */
var gslTime = $('#gsl-speaking-time');
var gslWarning = $('#gsl-warning-time');
var gslCurrentSpeaker = $('#gsl-current-speaker');
var gslSpeakerList = $('#gsl-list');
var gslPool = $('#gsl-pool');
var gslSpeakerName, gslSpeakerIso;
var gslCircle = new ProgressBar.Circle('#gsl-progress-bar', {
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
            circle.setText(circle.value() * gslTime.val());
        } else {
            circle.setText(parseInt(circle.value() * gslTime.val(), 0) + 1);
        }
        if (circle.value() < (gslWarning.val() / gslTime.val())) {
            circle.path.setAttribute('stroke', '#ff0000');
            circle.text.setAttribute('style', "color:#ff0000");
        } else {
            circle.path.setAttribute('stroke', '#4AC8E9');
            circle.text.setAttribute('style', "color:#4AC8E9");
        }

    }
});
$('#gsl-start-button').click(function () {
    if (gslSpeakerName != null && gslSpeakerIso != null) {
        gslTime.prop("disabled", true);
        gslCircle.animate(0, {duration: gslTime.val() * 1000 * gslCircle.value()}, function () {
            gslTime.prop("disabled", false);
            if ($('#gsl-auto-next').prop('checked')) {
                gslNextSpeaker();
            }
        });
    }
    return false;
});
$('#gsl-reset-button').click(function () {
    if (gslSpeakerName != null && gslSpeakerIso != null) {
        gslCircle.set(1);
        gslTime.prop("disabled", false);
    }
    return false;
});
$('#gsl-pause-button').click(function () {
    gslCircle.stop();
    gslTime.prop("disabled", false);
    return false;
});
$('#gsl-next-button').click(function () {
    gslTime.prop("disabled", false);
    gslNextSpeaker();
    return false;
});
$('#gsl-resize-button').click(function () {
    var tab = $('#tabs-0');
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
gslTime.bind("change", function () {
    jQuery.ajax({
        type: "POST",
        url: "script.php",
        data: {
            "a": "debate",
            "action": "setGeneralSpeakingTime",
            "value": $(this).val()
        },
        dataType: "text"
    });
    gslCircle.set(gslCircle.value());
});
gslWarning.bind("change", function () {
    jQuery.ajax({
        type: "POST",
        url: "script.php",
        data: {
            "a": "debate",
            "action": "setGeneralWarningTime",
            "value": $(this).val()
        },
        dataType: "text"
    });
});
$('#gsl-auto-next').bind("change", function () {
    jQuery.ajax({
        type: "POST",
        url: "script.php",
        data: {
            "a": "debate",
            "action": "setGeneralAutoNext",
            "value": $(this).prop("checked") ? "true" : "false"
        },
        dataType: "text"
    });
});
$(document).ready(function () {
    if (gslCurrentSpeaker.data('name') != null && gslCurrentSpeaker.data('iso') != null) {
        gslSpeakerName = gslCurrentSpeaker.data('name');
        gslSpeakerIso = gslCurrentSpeaker.data('iso');
        gslCircle.set(1);
    }
    gslPool.children().each(function () {
        $(this).tipsy();
        $(this).click(function () {
            if (gslSpeakerName == null && gslSpeakerIso == null) {
                gslSpeakerIso = $(this).data('iso');
                gslSpeakerName = $(this).data('name');
                gslCurrentSpeaker.html('<img src="img/flags_big/' + gslSpeakerIso.toUpperCase() + '.png" alt="' + gslSpeakerIso.toUpperCase() + '" width="70%"><h3>' + gslSpeakerName + '</h3>');
                $(this).hide();
                gslCircle.set(1);
            } else {
                gslSpeakerList.append("<tr data-iso='" + $(this).data('iso') + "' data-name='" + $(this).data('name') + "'><td class='label-cell'><img src='img/flags_small/" + $(this).data('iso') + ".png' alt='" + $(this).data('iso') + "' /></td><td style='font-size: 1.5em'>" + $(this).data('name') + "</td><td class='label-cell'><a href='' style='text-decoration: none;' class='gsl-list-up'><i class='icon-arrow-up'></i></a><a href='' style='text-decoration: none;' class='gsl-list-down'><i class='icon-arrow-down'></i></a></td><td class='label-cell'  style='padding-right:14px'><a href='' style='text-decoration: none;' class='gsl-list-remove'><i class='icon-remove'></i></a></td></tr>");
                $('.gsl-list-remove').last().click(function (e) {
                    gslRemoveSpeaker(e);
                    return false;
                });
                $('.gsl-list-up').last().click(function (e) {
                    gslMoveSpeakerUp(e);
                    return false;
                });
                $('.gsl-list-down').last().click(function (e) {
                    gslMoveSpeakerDown(e);
                    return false;
                });
                $(this).hide();
            }
            jQuery.ajax({
                type: "POST",
                url: "script.php",
                data: {
                    "a": "debate",
                    "action": "addGeneralSpeaker",
                    "iso": $(this).data('iso')
                },
                dataType: "text"
            });
        });
    });
    $('.gsl-list-remove').click(function (e) {
        gslRemoveSpeaker(e);
        return false;
    });
    $('.gsl-list-up').click(function (e) {
        gslMoveSpeakerUp(e);
        return false;
    });
    $('.gsl-list-down').click(function (e) {
        gslMoveSpeakerDown(e);
        return false;
    });
});
gslNextSpeaker = function () {
    gslPool.children('*[data-iso="' + gslSpeakerIso + '"]').show();
    jQuery.ajax({
        type: "POST",
        url: "script.php",
        data: {
            "a": "debate",
            "action": "removeGeneralSpeaker",
            "iso": gslSpeakerIso
        },
        dataType: "text"
    });
    var tr = gslSpeakerList.contents('tr:first');
    if (tr.length > 0) {
        gslSpeakerIso = tr.data('iso');
        gslSpeakerName = tr.data('name');
        gslCurrentSpeaker.html('<img src="img/flags_big/' + gslSpeakerIso.toUpperCase() + '.png" alt="' + gslSpeakerIso.toUpperCase() + '" width="70%"><h3>' + gslSpeakerName + '</h3>');
        tr.remove();
        gslCircle.set(1);
    } else {
        gslSpeakerIso = null;
        gslSpeakerName = null;
        gslCurrentSpeaker.html("");
        gslCircle.set(0);
    }
};

gslRemoveSpeaker = function (e) {
    var tr = $(e.currentTarget).parents('tr:first');
    gslPool.children('*[data-iso="' + tr.data('iso') + '"]').show();
    tr.remove();
    jQuery.ajax({
        type: "POST",
        url: "script.php",
        data: {
            "a": "debate",
            "action": "removeGeneralSpeaker",
            "iso": tr.data('iso')
        },
        dataType: "text"
    });
};

gslMoveSpeakerUp = function (e) {
    var tr = $(e.currentTarget).parents('tr:first');
    jQuery.ajax({
        type: "POST",
        url: "script.php",
        data: {
            "a": "debate",
            "action": "moveGeneralSpeaker",
            "iso": tr.data('iso'),
            "direction": "up"
        },
        dataType: "text"
    });
    tr.insertBefore(tr.prev());
};

gslMoveSpeakerDown = function (e) {
    var tr = $(e.currentTarget).parents('tr:first');
    jQuery.ajax({
        type: "POST",
        url: "script.php",
        data: {
            "a": "debate",
            "action": "moveGeneralSpeaker",
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
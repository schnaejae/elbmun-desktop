/*
 * elbmun-desktop, Web Based MUN Software
 * (c) by Jannes Riffert, Elbe Model United Nations e.V. Dresden
 * https://github.com/schnaejae/elbmun-desktop/, http://elbmun.org
 * MIT License
 */
var modSecToMinSec = function (seconds) {
    var sec = (parseInt(seconds) % 60);
    return parseInt(seconds / 60, 10) + ":" + (sec > 9 ? sec : "0" + sec);
};
var modStartTimer = function () {
    modTimeSpeaker.prop("disabled", true);
    modTimeDuration.prop("disabled", true);
    var d = new Date();
    document.cookie = "moderated-state=started";
    document.cookie = "moderated-value=" + modDurationCircle.value();
    document.cookie = "moderated-time=" + d.getTime();
    modDurationCircle.animate(0, {duration: (modTimeDuration.val() * 60000) * modDurationCircle.value()}, function () {
        modTimeDuration.prop("disabled", false);
        document.cookie = "moderated-state=";
        document.cookie = "moderated-value=";
        document.cookie = "moderated-time=";
    });
    modSpeakerCircle.animate(0, {duration: (modTimeSpeaker.val() * 1000) * modSpeakerCircle.value()}, function () {
        modTimeSpeaker.prop("disabled", false);
    });
};
var modTimeSpeaker = $('#mod-speaking-time');
var modTimeDuration = $('#mod-duration-time');
var modSpeakerWarning = $('#mod-warning-time');
var modDurationWarning = $('#mod-duration-warning');
var modCurrentSpeaker = $('#mod-current-speaker');
var modPool = $('#mod-pool');
var modSpeakerName, modSpeakerIso;
var modSpeakerCircle = new ProgressBar.Circle('#mod-progress-bar', {
    color: '#4AC8E9',
    trailColor: '#eee',
    trailWidth: 1,
    strokeWidth: 5,
    text: {
        autoStyle: true
    },
    step: function (state, circle) {
        if (circle.value() == 1 || circle.value() == 0) {
            circle.setText(circle.value() * modTimeSpeaker.val());
        } else {
            circle.setText(parseInt(circle.value() * modTimeSpeaker.val()) + 1);
        }
        if (circle.value() < (modSpeakerWarning.val() / modTimeSpeaker.val())) {
            circle.path.setAttribute('stroke', '#ff0000');
            circle.text.setAttribute('style', "color:#ff0000");
        } else {
            circle.path.setAttribute('stroke', '#4AC8E9');
            circle.text.setAttribute('style', "color:#4AC8E9");
        }
    }
});
var modDurationCircle = new ProgressBar.Circle('#mod-duration-progress-bar', {
    color: '#435468',
    trailColor: '#eee',
    trailWidth: 1,
    strokeWidth: 5,
    text: {
        autoStyle: true
    },
    step: function (state, circle) {
        var time = modTimeDuration.val() * 60;
        if (circle.value() == 1 || circle.value() == 0) {
            circle.setText(modSecToMinSec(circle.value() * time));
        } else {
            circle.setText(modSecToMinSec(circle.value() * time + 1));
        }
        if (circle.value() < ((modDurationWarning.val() * 60) / time)) {
            circle.path.setAttribute('stroke', '#ff0000');
            circle.text.setAttribute('style', "color:#ff0000");
        } else {
            circle.path.setAttribute('stroke', '#435468');
            circle.text.setAttribute('style', "color:#435468");
        }
    }
});
$('#mod-start-button').click(function () {
    if (modSpeakerName != null && modSpeakerIso != null) {
        modStartTimer();
    }
    return false;
});
$('#mod-reset-button').click(function () {
    if (modSpeakerName != null && modSpeakerIso != null) {
        modSpeakerCircle.set(1);
        modTimeSpeaker.prop("disabled", false);
    }
    return false;
});
$('#mod-pause-button').click(function () {
    document.cookie = "moderated-state=stopped";
    document.cookie = "moderated-value=" + modDurationCircle.value();
    document.cookie = "moderated-time=";
    modSpeakerCircle.stop();
    modDurationCircle.stop();
    modTimeDuration.prop("disabled", false);
    modTimeSpeaker.prop("disabled", false);
    return false;
});
$('#mod-remove-button').click(function () {
    modPool.children('*[data-iso="' + modSpeakerIso + '"]').show();
    modSpeakerName = null;
    modSpeakerIso = null;
    modCurrentSpeaker.html("");
    document.cookie = "moderated-state=stopped";
    document.cookie = "moderated-value=" + modDurationCircle.value();
    document.cookie = "moderated-time=";
    modSpeakerCircle.set(0);
    modDurationCircle.stop();
    modTimeDuration.prop("disabled", false);
    modTimeSpeaker.prop("disabled", false);
    jQuery.ajax({
        type: "POST",
        url: "script.php",
        data: {
            "a": "debate",
            "action": "addModeratedSpeaker",
            "iso": ""
        },
        dataType: "text"
    });
    return false;
});
$('#mod-duration-reset-button').click(function () {
    modDurationCircle.set(1);
    modTimeDuration.prop("disabled", false);
    if (modSpeakerName != null && modSpeakerIso != null) {
        modSpeakerCircle.set(1);
        modTimeSpeaker.prop("disabled", false);
    }
    document.cookie = "moderated-state=";
    document.cookie = "moderated-value=";
    document.cookie = "moderated-time=";
    return false;
});
modTimeSpeaker.bind("change", function () {
    jQuery.ajax({
        type: "POST",
        url: "script.php",
        data: {
            "a": "debate",
            "action": "setModeratedSpeakingTime",
            "value": $(this).val()
        },
        dataType: "text"
    });
    modSpeakerCircle.set(modSpeakerCircle.value());
});
modTimeDuration.bind("change", function () {
    jQuery.ajax({
        type: "POST",
        url: "script.php",
        data: {
            "a": "debate",
            "action": "setModeratedDurationTime",
            "value": $(this).val()
        },
        dataType: "text"
    });
    modDurationCircle.set(modDurationCircle.value());
});
modSpeakerWarning.bind("change", function () {
    jQuery.ajax({
        type: "POST",
        url: "script.php",
        data: {
            "a": "debate",
            "action": "setModeratedWarningTime",
            "value": $(this).val()
        },
        dataType: "text"
    });
});
modDurationWarning.bind("change", function () {
    jQuery.ajax({
        type: "POST",
        url: "script.php",
        data: {
            "a": "debate",
            "action": "setModeratedWarningDuration",
            "value": $(this).val()
        },
        dataType: "text"
    });
});
$('#mod-auto-play').bind("change", function () {
    jQuery.ajax({
        type: "POST",
        url: "script.php",
        data: {
            "a": "debate",
            "action": "setModeratedAutoPlay",
            "value": $(this).prop("checked") ? "true" : "false"
        },
        dataType: "text"
    });
});
$('#mod-topic').bind("change", function () {
    $('#mod-heading').text("Moderated Caucus - " + $(this).val());
    jQuery.ajax({
        type: "POST",
        url: "script.php",
        data: {
            "a": "debate",
            "action": "setModeratedTopic",
            "value": $(this).val()
        },
        dataType: "text"
    });
});
$(document).ready(function () {
    var cookies = document.cookie.split(";");
    var state, value, time;
    for (var i = 0; i < cookies.length; i++) {
        var c = cookies[i];
        while (c.charAt(0) == ' ') c = c.substring(1);
        if (c.indexOf("moderated-state=") == 0) state = c.substring("moderated-state=".length, c.length);
        if (c.indexOf("moderated-value=") == 0) value = c.substring("moderated-value=".length, c.length);
        if (c.indexOf("moderated-time=") == 0) time = c.substring("moderated-time=".length, c.length);
    }
    modDurationCircle.set(1);
    if (state != null) {
        if (state == "started") {
            var d = new Date();
            value = value - (d.getTime() - time) / (modTimeDuration.val() * 60000);
            if (value > 0) {
                modDurationCircle.set(value);
                modStartTimer();
            } else {
                modDurationCircle.set(0);
            }
        } else if (state == "stopped") {
            modDurationCircle.set(value);
        }
    }
    if (modCurrentSpeaker.data('name') != null && modCurrentSpeaker.data('iso') != null) {
        modSpeakerName = modCurrentSpeaker.data('name');
        modSpeakerIso = modCurrentSpeaker.data('iso');
        modSpeakerCircle.set(1);
    }
    $('#mod-heading').text("Moderated Caucus - " + $('#mod-topic').val());
    modPool.children().each(function () {
        $(this).tipsy();
        $(this).click(function () {
            modPool.children('*[data-iso="' + modSpeakerIso + '"]').show();
            modTimeSpeaker.prop("disabled", false);
            modSpeakerIso = $(this).data('iso');
            modSpeakerName = $(this).data('name');
            modCurrentSpeaker.html('<img src="img/flags_big/' + modSpeakerIso.toUpperCase() + '.png" alt="' + modSpeakerIso.toUpperCase() + '" width="70%"><h3>' + modSpeakerName + '</h3>');
            $(this).hide();
            modSpeakerCircle.set(1);
            if ($('#mod-auto-play').prop("checked")) {
                modStartTimer();
            }
            jQuery.ajax({
                type: "POST",
                url: "script.php",
                data: {
                    "a": "debate",
                    "action": "addModeratedSpeaker",
                    "iso": $(this).data('iso')
                },
                dataType: "text"
            });
        });
    });
});
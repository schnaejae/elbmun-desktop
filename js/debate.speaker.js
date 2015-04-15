/*
 * elbmun-desktop, Web Based MUN Software
 * (c) by Jannes Riffert, Elbe Model United Nations e.V. Dresden
 * https://github.com/schnaejae/elbmun-desktop/, http://elbmun.org
 * MIT License
 */
var sspSecToMinSec = function (seconds) {
    var sec = (parseInt(seconds) % 60);
    return parseInt(seconds / 60, 10) + ":" + (sec > 9 ? sec : "0" + sec);
};
var sspStartTimer = function () {
    sspTimeMin.prop("disabled", true);
    sspTimeSec.prop("disabled", true);
    var d = new Date();
    document.cookie = "speaker-state=started";
    document.cookie = "speaker-value=" + sspCircle.value();
    document.cookie = "speaker-time=" + d.getTime();
    sspCircle.animate(0, {duration: (sspTimeMin.val() * 60000 + sspTimeSec.val() * 1000) * sspCircle.value()}, function () {
        sspTimeMin.prop("disabled", false);
        sspTimeSec.prop("disabled", false);
        document.cookie = "speaker-state=";
        document.cookie = "speaker-value=";
        document.cookie = "speaker-time=";
    });
};
var sspTimeMin = $('#ssp-speaking-time-min');
var sspTimeSec = $('#ssp-speaking-time-sec');
var sspWarning = $('#ssp-warning-time');
var sspCurrentSpeaker = $('#ssp-current-speaker');
var sspPool = $('#ssp-pool');
var sspSpeakerName, sspSpeakerIso;
var sspCircle = new ProgressBar.Circle('#ssp-progress-bar', {
    color: '#4AC8E9',
    trailColor: '#eee',
    trailWidth: 1,
    strokeWidth: 5,
    text: {
        autoStyle: true
    },
    step: function (state, circle) {
        var time = sspTimeMin.val() * 60 + sspTimeSec.val() * 1;
        if (circle.value() == 1 || circle.value() == 0) {
            circle.setText(sspSecToMinSec(circle.value() * time));
        } else {
            circle.setText(sspSecToMinSec(circle.value() * time + 1));
        }
        if (circle.value() < (sspWarning.val() / time)) {
            circle.path.setAttribute('stroke', '#ff0000');
            circle.text.setAttribute('style', "color:#ff0000");
        } else {
            circle.path.setAttribute('stroke', '#4AC8E9');
            circle.text.setAttribute('style', "color:#4AC8E9");
        }
    }
});
$('#ssp-start-button').click(function () {
    if (sspSpeakerName != null && sspSpeakerIso != null) {
        sspStartTimer();
    }
    return false;
});
$('#ssp-reset-button').click(function () {
    if (sspSpeakerName != null && sspSpeakerIso != null) {
        document.cookie = "speaker-state=";
        document.cookie = "speaker-value=";
        document.cookie = "speaker-time=";
        sspCircle.set(1);
        sspTimeMin.prop("disabled", false);
        sspTimeSec.prop("disabled", false);
    }
    return false;
});
$('#ssp-pause-button').click(function () {
    document.cookie = "speaker-state=stopped";
    document.cookie = "speaker-value=" + sspCircle.value();
    document.cookie = "speaker-time=";
    sspCircle.stop();
    sspTimeMin.prop("disabled", false);
    sspTimeSec.prop("disabled", false);
    return false;
});
$('#ssp-remove-button').click(function () {
    sspPool.children('*[data-iso="' + sspSpeakerIso + '"]').show();
    sspSpeakerName = null;
    sspSpeakerIso = null;
    sspCurrentSpeaker.html("");
    document.cookie = "speaker-state=";
    document.cookie = "speaker-value=";
    document.cookie = "speaker-time=";
    sspCircle.set(0);
    sspTimeMin.prop("disabled", false);
    sspTimeSec.prop("disabled", false);
    jQuery.ajax({
        type: "POST",
        url: "script.php",
        data: {
            "a": "debate",
            "action": "addSpeakerSpeaker",
            "iso": ""
        },
        dataType: "text"
    });
    return false;
});
sspTimeMin.bind("change", function () {
    jQuery.ajax({
        type: "POST",
        url: "script.php",
        data: {
            "a": "debate",
            "action": "setSpeakerSpeakingTime",
            "value": sspTimeMin.val() * 60 + sspTimeSec.val() * 1
        },
        dataType: "text"
    });
    sspCircle.set(sspCircle.value());
});
sspTimeSec.bind("change", function () {
    jQuery.ajax({
        type: "POST",
        url: "script.php",
        data: {
            "a": "debate",
            "action": "setSpeakerSpeakingTime",
            "value": sspTimeMin.val() * 60 + sspTimeSec.val() * 1
        },
        dataType: "text"
    });
    sspCircle.set(sspCircle.value());
});
sspWarning.bind("change", function () {
    jQuery.ajax({
        type: "POST",
        url: "script.php",
        data: {
            "a": "debate",
            "action": "setSpeakerWarningTime",
            "value": $(this).val()
        },
        dataType: "text"
    });
});
$('#ssp-auto-play').bind("change", function () {
    jQuery.ajax({
        type: "POST",
        url: "script.php",
        data: {
            "a": "debate",
            "action": "setSpeakerAutoPlay",
            "value": $(this).prop("checked") ? "true" : "false"
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
        if (c.indexOf("speaker-state=") == 0) state = c.substring("speaker-state=".length, c.length);
        if (c.indexOf("speaker-value=") == 0) value = c.substring("speaker-value=".length, c.length);
        if (c.indexOf("speaker-time=") == 0) time = c.substring("speaker-time=".length, c.length);
    }
    if (sspCurrentSpeaker.data('name') != null && sspCurrentSpeaker.data('iso') != null) {
        sspSpeakerName = sspCurrentSpeaker.data('name');
        sspSpeakerIso = sspCurrentSpeaker.data('iso');
        sspCircle.set(1);
        if (state != null) {
            if (state == "started") {
                var d = new Date();
                value = value - (d.getTime() - time) / (sspTimeMin.val() * 60000 + sspTimeSec.val() * 1000);
                if (value > 0) {
                    sspCircle.set(value);
                    sspStartTimer();
                } else {
                    sspCircle.set(0);
                }
            } else if (state == "stopped") {
                sspCircle.set(value);
            }
        }
    }
    sspPool.children().each(function () {
        $(this).tipsy();
        $(this).click(function () {
            sspPool.children('*[data-iso="' + sspSpeakerIso + '"]').show();
            sspTimeMin.prop("disabled", false);
            sspTimeSec.prop("disabled", false);
            sspSpeakerIso = $(this).data('iso');
            sspSpeakerName = $(this).data('name');
            sspCurrentSpeaker.html('<img src="img/flags_big/' + sspSpeakerIso.toUpperCase() + '.png" alt="' + sspSpeakerIso.toUpperCase() + '" width="70%"><h3>' + sspSpeakerName + '</h3>');
            $(this).hide();
            sspCircle.set(1);
            if ($('#ssp-auto-play').prop("checked")) {
                sspStartTimer();
            }
            jQuery.ajax({
                type: "POST",
                url: "script.php",
                data: {
                    "a": "debate",
                    "action": "addSpeakerSpeaker",
                    "iso": $(this).data('iso')
                },
                dataType: "text"
            });
        });
    });
});
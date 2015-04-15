/*
 * elbmun-desktop, Web Based MUN Software
 * (c) by Jannes Riffert, Elbe Model United Nations e.V. Dresden
 * https://github.com/schnaejae/elbmun-desktop/, http://elbmun.org
 * MIT License
 */
var unmSecToMinSec = function (seconds) {
    var sec = (parseInt(seconds) % 60);
    return parseInt(seconds / 60, 10) + ":" + (sec > 9 ? sec : "0" + sec);
};
var unmStartTimer = function () {
    unmTimeDuration.prop("disabled", true);
    var d = new Date();
    document.cookie = "unmoderated-state=started";
    document.cookie = "unmoderated-value=" + unmDurationCircle.value();
    document.cookie = "unmoderated-time=" + d.getTime();
    unmDurationCircle.animate(0, {duration: (unmTimeDuration.val() * 60000) * unmDurationCircle.value()}, function () {
        unmTimeDuration.prop("disabled", false);
        document.cookie = "unmoderated-state=";
        document.cookie = "unmoderated-value=";
        document.cookie = "unmoderated-time=";
    });
};
var unmTimeDuration = $('#unm-duration-time');
var unmDurationWarning = $('#unm-duration-warning');
var unmDurationCircle = new ProgressBar.Circle('#unm-progress-bar', {
    color: '#4AC8E9',
    trailColor: '#eee',
    trailWidth: 1,
    strokeWidth: 5,
    text: {
        autoStyle: true
    },
    step: function (state, circle) {
        var time = unmTimeDuration.val() * 60;
        if (circle.value() == 1 || circle.value() == 0) {
            circle.setText(unmSecToMinSec(circle.value() * time));
        } else {
            circle.setText(unmSecToMinSec(circle.value() * time + 1));
        }
        if (circle.value() < ((unmDurationWarning.val() * 60) / time)) {
            circle.path.setAttribute('stroke', '#ff0000');
            circle.text.setAttribute('style', "color:#ff0000");
        } else {
            circle.path.setAttribute('stroke', '#4AC8E9');
            circle.text.setAttribute('style', "color:#4AC8E9");
        }
    }
});
$('#unm-start-button').click(function () {
    unmStartTimer();
    return false;
});
$('#unm-reset-button').click(function () {
    document.cookie = "unmoderated-state=";
    document.cookie = "unmoderated-value=";
    document.cookie = "unmoderated-time=";
    unmDurationCircle.set(1);
    unmTimeDuration.prop("disabled", false);
    return false;
});
$('#unm-pause-button').click(function () {
    document.cookie = "unmoderated-state=stopped";
    document.cookie = "unmoderated-value=" + unmDurationCircle.value();
    document.cookie = "unmoderated-time=";
    unmDurationCircle.stop();
    unmTimeDuration.prop("disabled", false);
    return false;
});
unmTimeDuration.bind("change", function () {
    jQuery.ajax({
        type: "POST",
        url: "script.php",
        data: {
            "a": "debate",
            "action": "setUnmoderatedDurationTime",
            "value": $(this).val()
        },
        dataType: "text"
    });
    unmDurationCircle.set(unmDurationCircle.value());
});
unmDurationWarning.bind("change", function () {
    jQuery.ajax({
        type: "POST",
        url: "script.php",
        data: {
            "a": "debate",
            "action": "setUnmoderatedWarningDuration",
            "value": $(this).val()
        },
        dataType: "text"
    });
});
$('#unm-topic').bind("change", function () {
    $('#unm-heading').text("Unmoderated Caucus - " + $(this).val());
    jQuery.ajax({
        type: "POST",
        url: "script.php",
        data: {
            "a": "debate",
            "action": "setUnmoderatedTopic",
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
        if (c.indexOf("unmoderated-state=") == 0) state = c.substring("unmoderated-state=".length, c.length);
        if (c.indexOf("unmoderated-value=") == 0) value = c.substring("unmoderated-value=".length, c.length);
        if (c.indexOf("unmoderated-time=") == 0) time = c.substring("unmoderated-time=".length, c.length);
    }
    unmDurationCircle.set(1);
    if (state != null) {
        if (state == "started") {
            var d = new Date();
            value = value - (d.getTime() - time) / (unmTimeDuration.val() * 60000);
            if (value > 0) {
                unmDurationCircle.set(value);
                unmStartTimer();
            } else {
                unmDurationCircle.set(0);
            }
        } else if (state == "stopped") {
            unmDurationCircle.set(value);
        }
    }
    $('#unm-heading').text("Unmoderated Caucus - " + $('#unm-topic').val());
});
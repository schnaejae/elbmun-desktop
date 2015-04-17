/*
 * elbmun-desktop, Web Based MUN Software
 * (c) by Jannes Riffert, Elbe Model United Nations e.V. Dresden
 * https://github.com/schnaejae/elbmun-desktop/, http://elbmun.org
 * MIT License
 */
var vote = vote;
var prepareNumber = $('#vote-prepare-number');
var firstVoteList = $('#vote-first-table');
var secondVoteList = $('#vote-second-table');
var outcomeMessage = $('#vote-outcome-message');
var outcomeResult = $('#vote-outcome-result');
var outcomeRights = $('#vote-outcome-rights');
var calculateVotesNeeded = function () {
    var present = vote.type == "substantial" ? vote.members : vote.present;
    if (!isNaN(parseInt(vote.majority))) {
        return vote.majority;
    } else if (vote.majority == "simple") {
        return parseInt((present * 0.5) + 1);
    } else if (vote.majority == "twothird") {
        return present % 3 == 0 ? (present * 2 / 3) : parseInt((present * 2 / 3) + 1);
    }
};
var prepareFirstVotingList = function () {
    if (vote.type == "substantial") {
        firstVoteList.children('*[data-status="observer"]').hide()
            .find('button').removeClass('highlight');
        $('.vote-first-substantial-column').show();
        if (vote.divide) {
            $('.vote-first-divide-column').hide()
                .find('button').removeClass('highlight');
        } else {
            $('.vote-first-divide-column').show();
        }
        firstVoteList.children('*[data-status="state"]').each(function () {
            if ($(this).data('voting')) {
                $(this).find('.vote-first-abstain').hide()
                    .removeClass('highlight');
                $(this).find('.vote-first-pass').hide()
                    .removeClass('highlight');
            }
        });
    } else {
        firstVoteList.children('*[data-status="observer"]').show();
        $('.vote-first-divide-column').hide()
            .find('button').removeClass('highlight');
        $('.vote-first-substantial-column').hide()
            .find('button').removeClass('highlight');
    }
};
var prepareSecondVotingList = function () {
    secondVoteList.children().each(function () {
        $(this).find('button').removeClass('highlight');
        $(this).hide();
    });

};
var calculateSecondOutcome = function () {
    var result = {
        yes: 0,
        no: 0,
        uncast: 0,
        veto: false
    };
    secondVoteList.children().each(function () {
        if ($(this).data('passing')) {
            var call = $(this).find('.highlight');
            switch (call.text()) {
                case "Yes":
                    result.yes = result.yes + 1;
                    break;
                case "No":
                    result.no = result.no + 1;
                    if (vote.veto && $(this).data('veto')) {
                        result.veto = true;
                    }
                    break;
                case "Pass":
                    result.no = result.no + 1;
                    if (vote.veto && $(this).data('veto')) {
                        result.veto = true;
                    }
                    break;
                case "Yes (Rights)":
                    outcomeRights.append("<tr data-iso='" + $(this).data('iso') + "'><td class='vote-list'><img src='img/flags_small/" + $(this).data('iso') + ".png' alt='" + $(this).data('iso') + "' /></td><td class='vote-list'>" + $(this).data('name') + "</td><td class='vote-list' style='width:auto;text-align: center;'>In Favour</td>");
                    result.yes = result.yes + 1;
                    break;
                case "No (Rights)":
                    outcomeRights.append("<tr data-iso='" + $(this).data('iso') + "'><td class='vote-list'><img src='img/flags_small/" + $(this).data('iso') + ".png' alt='" + $(this).data('iso') + "' /></td><td class='vote-list'>" + $(this).data('name') + "</td><td class='vote-list' style='width:auto;text-align: center;'>Against</td>");
                    result.no = result.no + 1;
                    break;
                default:
                    result.uncast = result.uncast + 1;
                    break;
            }
        }
    });
    return result;
};
var calculateOutcome = function () {
    var yes = 0;
    var no = 0;
    var abstain = 0;
    var uncast = 0;
    var veto = false;
    var voted;
    var needed;
    var absText = "", yesText, noText;
    if (vote.type == "substantial") {
        firstVoteList.children('*[data-status="state"]').each(function () {
            outcomeRights.children('*[data-iso="' + $(this).data('iso') + '"]').remove();
            secondVoteList.children('*[data-iso="' + $(this).data('iso') + '"]').hide()
                .data('passing', false);
            var call = $(this).find('.highlight');
            switch (call.text()) {
                case "Yes":
                    yes++;
                    break;
                case "No":
                    no++;
                    if (vote.veto && $(this).data('veto')) {
                        veto = true;
                    }
                    break;
                case "Abstain":
                    abstain++;
                    break;
                case "Pass":
                    secondVoteList.children('*[data-iso="' + $(this).data('iso') + '"]').show()
                        .data('passing', true);
                    break;
                case "Yes (Rights)":
                    outcomeRights.append("<tr data-iso='" + $(this).data('iso') + "'><td class='vote-list'><img src='img/flags_small/" + $(this).data('iso') + ".png' alt='" + $(this).data('iso') + "' /></td><td class='vote-list'>" + $(this).data('name') + "</td><td class='vote-list' style='width:auto;text-align: center;'>In Favour</td>");
                    yes++;
                    break;
                case "No (Rights)":
                    outcomeRights.append("<tr data-iso='" + $(this).data('iso') + "'><td class='vote-list'><img src='img/flags_small/" + $(this).data('iso') + ".png' alt='" + $(this).data('iso') + "' /></td><td class='vote-list'>" + $(this).data('name') + "</td><td class='vote-list' style='width:auto;text-align: center;'>Against</td>");
                    no++;
                    break;
                default:
                    uncast++;
                    break;
            }
        });
        var secondOutcome = calculateSecondOutcome();
        yes += secondOutcome.yes;
        no += secondOutcome.no;
        uncast += secondOutcome.uncast;
        veto = veto || secondOutcome.veto;
        //console.log("Yes: " + yes + ",No: " + no + ",Abstain: " + abstain + ",Uncast: " + uncast);
        if (uncast > 0) {
            outcomeMessage.html("Vote still in progress!");
            outcomeResult.text("No Results yet");
            return "inconclusive";
        }
        if (abstain > 0) {
            absText = abstain > 1 ? " and " + abstain + " Abstentions" : " and " + abstain + " Abstention";
        }
        if (yes > 0) {
            yesText = yes > 1 ? yes + " Votes in Favour" : yes + " Vote in Favour";
        } else {
            yesText = "No Vote in Favour";
        }
        if (no > 0) {
            noText = no > 1 ? ", " + no + " Votes Against" : ", " + no + " Vote Against";
        } else {
            noText = ", No Vote Against";
        }
        voted = yes + no;
        if (!isNaN(parseInt(vote.majority))) {
            if (yes >= parseInt(vote.majority)) {
                if (vote.veto && veto) {
                    outcomeMessage.html("Vote <b>failed</b> due to a Veto!");
                    outcomeResult.html(yesText + noText + absText);
                    return "failed"
                }
                outcomeMessage.html("Vote <b>succeeded</b>!");
                outcomeResult.html(yesText + noText + absText);
                return "succeeded"
            } else {
                outcomeMessage.html("Vote <b>failed</b>!");
                outcomeResult.html(yesText + noText + absText);
                return "failed";
            }
        } else if (vote.majority == "simple") {
            if (yes >= parseInt((voted * 0.5) + 1)) {
                if (vote.veto && veto) {
                    outcomeMessage.html("Vote <b>failed</b> due to a Veto!");
                    outcomeResult.html(yesText + noText + absText);
                    return "failed"
                }
                outcomeMessage.html("Vote <b>succeeded</b>!");
                outcomeResult.html(yesText + noText + absText);
                return "succeeded"
            } else {
                outcomeMessage.html("Vote <b>failed</b>!");
                outcomeResult.html(yesText + noText + absText);
                return "failed";
            }
        } else if (vote.majority == "twothird") {
            needed = voted % 3 == 0 ? (voted * 2 / 3) : parseInt((voted * 2 / 3) + 1);
            if (yes >= needed) {
                if (vote.veto && veto) {
                    outcomeMessage.html("Vote <b>failed</b> due to a Veto!");
                    outcomeResult.html(yesText + noText + absText);
                    return "failed"
                }
                outcomeMessage.html("Vote <b>succeeded</b>!");
                outcomeResult.html(yesText + noText + absText);
                return "succeeded"
            } else {
                outcomeMessage.html("Vote <b>failed</b>!");
                outcomeResult.html(yesText + noText + absText);
                return "failed";
            }
        }
    } else {
        firstVoteList.children().each(function () {
            var call = $(this).find('.highlight');
            switch (call.text()) {
                case "Yes":
                    yes++;
                    break;
                case "No":
                    no++;
                    break;
                default:
                    uncast++;
                    break;
            }
        });
        if (uncast > 0) {
            outcomeMessage.html("First Vote still in progress!");
            outcomeResult.text("No Results yet");
            return "inconclusive";
        }
        if (yes > 0) {
            yesText = yes > 1 ? yes + " Votes in Favour" : yes + " Vote in Favour";
        } else {
            yesText = "No Vote in Favour";
        }
        if (no > 0) {
            noText = no > 1 ? ", " + no + " Votes Against" : ", " + no + " Vote Against";
        } else {
            noText = ", No Vote Against";
        }
        voted = yes + no;
        if (!isNaN(parseInt(vote.majority))) {
            if (yes >= parseInt(vote.majority)) {
                outcomeMessage.html("Vote <b>succeeded</b>!");
                outcomeResult.html(yesText + noText);
                return "succeeded"
            } else {
                outcomeMessage.html("Vote <b>failed</b>!");
                outcomeResult.html(yesText + noText);
                return "failed";
            }
        } else if (vote.majority == "simple") {
            if (yes >= parseInt((voted * 0.5) + 1)) {
                outcomeMessage.html("Vote <b>succeeded</b>!");
                outcomeResult.html(yesText + noText);
                return "succeeded"
            } else {
                outcomeMessage.html("Vote <b>failed</b>!");
                outcomeResult.html(yesText + noText);
                return "failed";
            }
        } else if (vote.majority == "twothird") {
            needed = voted % 3 == 0 ? (voted * 2 / 3) : parseInt((voted * 2 / 3) + 1);
            if (yes >= needed) {
                outcomeMessage.html("Vote <b>succeeded</b>!");
                outcomeResult.html(yesText + noText);
                return "succeeded"
            } else {
                outcomeMessage.html("Vote <b>failed</b>!");
                outcomeResult.html(yesText + noText);
                return "failed";
            }
        }
    }
};
$('#vote-prepare-type-substantial').bind('change', function () {
    vote.type = "substantial";
    prepareNumber.text(calculateVotesNeeded());
    prepareFirstVotingList();
    calculateOutcome();
    jQuery.ajax({
        type: "POST",
        url: "script.php",
        data: {
            "a": "vote",
            "action": "setType",
            "value": "substantial"
        },
        dataType: "text"
    });
});
$('#vote-prepare-type-procedural').bind('change', function () {
    vote.type = "procedural";
    prepareNumber.text(calculateVotesNeeded());
    prepareFirstVotingList();
    calculateOutcome();
    jQuery.ajax({
        type: "POST",
        url: "script.php",
        data: {
            "a": "vote",
            "action": "setType",
            "value": "procedural"
        },
        dataType: "text"
    });
});
$('#vote-prepare-majority-simple').bind('change', function () {
    vote.majority = "simple";
    prepareNumber.text(calculateVotesNeeded());
    calculateOutcome();
    jQuery.ajax({
        type: "POST",
        url: "script.php",
        data: {
            "a": "vote",
            "action": "setMajority",
            "value": "simple"
        },
        dataType: "text"
    });
});
$('#vote-prepare-majority-twothird').bind('change', function () {
    vote.majority = "twothird";
    prepareNumber.text(calculateVotesNeeded());
    calculateOutcome();
    jQuery.ajax({
        type: "POST",
        url: "script.php",
        data: {
            "a": "vote",
            "action": "setMajority",
            "value": "twothird"
        },
        dataType: "text"
    });
});
$('#vote-prepare-majority-custom').bind('change', function () {
    vote.majority = $('#vote-prepare-majority-custom-number').val();
    prepareNumber.text(calculateVotesNeeded());
    calculateOutcome();
    jQuery.ajax({
        type: "POST",
        url: "script.php",
        data: {
            "a": "vote",
            "action": "setMajority",
            "value": vote.majority
        },
        dataType: "text"
    });
});
$('#vote-prepare-majority-custom-number').bind('change', function () {
    if ($('#vote-prepare-majority-custom').prop('checked')) {
        vote.majority = $('#vote-prepare-majority-custom-number').val();
        prepareNumber.text(calculateVotesNeeded());
        calculateOutcome();
        jQuery.ajax({
            type: "POST",
            url: "script.php",
            data: {
                "a": "vote",
                "action": "setMajority",
                "value": vote.majority
            },
            dataType: "text"
        });
    }
});
$('#vote-prepare-veto').bind('change', function () {
    vote.veto = $(this).prop('checked');
    calculateOutcome();
    jQuery.ajax({
        type: "POST",
        url: "script.php",
        data: {
            "a": "vote",
            "action": "setVeto",
            "value": vote.veto ? "true" : "false"
        },
        dataType: "text"
    });
});
$('#vote-prepare-divide').bind('change', function () {
    vote.divide = $(this).prop('checked');
    prepareFirstVotingList();
    calculateOutcome();
    jQuery.ajax({
        type: "POST",
        url: "script.php",
        data: {
            "a": "vote",
            "action": "setDivide",
            "value": vote.divide ? "true" : "false"
        },
        dataType: "text"
    });
});
$('#vote-prepare-rollcall').click(function () {
    firstVoteList.find('button').removeClass('highlight');
    prepareSecondVotingList();
    calculateOutcome();
    window.location.hash = "1";
    var tabs = $("#tabs");
    tabs.find("a[href='#tabs-0']").removeClass("tabulous_active");
    tabs.find("a[href='#tabs-1']").addClass("tabulous_active");
    $("#tabs-0").hide();
    $('#tabs-1').fadeIn(500);
    window.scrollTo(0, 0);
    return false;
});
$('#vote-prepare-first-next').click(function () {
    var revote = secondVoteList.children().filter(function () {
        return $(this).data('passing');
    }).length > 0 ? 2 : 3;
    window.location.hash = revote;
    var tabs = $("#tabs");
    tabs.find("a[href='#tabs-1']").removeClass("tabulous_active");
    tabs.find("a[href='#tabs-" + revote + "']").addClass("tabulous_active");
    $("#tabs-1").hide();
    $('#tabs-' + revote).fadeIn(500);
    window.scrollTo(0, 0);
    return false;
});
$('#vote-prepare-second-next').click(function () {
    window.location.hash = '3';
    var tabs = $("#tabs");
    tabs.find("a[href='#tabs-2']").removeClass("tabulous_active");
    tabs.find("a[href='#tabs-3']").addClass("tabulous_active");
    $("#tabs-2").hide();
    $('#tabs-3').fadeIn(500);
    window.scrollTo(0, 0);
    return false;
});
$('.vote-first-yes').click(function () {
    var tr = $(this).parents('tr:first');
    tr.find('button').removeClass('highlight');
    $(this).addClass('highlight');
    calculateOutcome();
});
$('.vote-first-no').click(function () {
    var tr = $(this).parents('tr:first');
    tr.find('button').removeClass('highlight');
    $(this).addClass('highlight');
    calculateOutcome();
});
$('.vote-first-abstain').click(function () {
    var tr = $(this).parents('tr:first');
    tr.find('button').removeClass('highlight');
    $(this).addClass('highlight');
    calculateOutcome();
});
$('.vote-first-pass').click(function () {
    var tr = $(this).parents('tr:first');
    tr.find('button').removeClass('highlight');
    $(this).addClass('highlight');
    calculateOutcome();
});
$('.vote-first-yes-rights').click(function () {
    var tr = $(this).parents('tr:first');
    tr.find('button').removeClass('highlight');
    $(this).addClass('highlight');
    calculateOutcome();
});
$('.vote-first-no-rights').click(function () {
    var tr = $(this).parents('tr:first');
    tr.find('button').removeClass('highlight');
    $(this).addClass('highlight');
    calculateOutcome();
});
$('.vote-second-yes').click(function () {
    var tr = $(this).parents('tr:first');
    tr.find('button').removeClass('highlight');
    $(this).addClass('highlight');
    calculateOutcome();
});
$('.vote-second-no').click(function () {
    var tr = $(this).parents('tr:first');
    tr.find('button').removeClass('highlight');
    $(this).addClass('highlight');
    calculateOutcome();
});
$('.vote-second-pass').click(function () {
    var tr = $(this).parents('tr:first');
    tr.find('button').removeClass('highlight');
    $(this).addClass('highlight');
    calculateOutcome();
});
$('.vote-second-yes-rights').click(function () {
    var tr = $(this).parents('tr:first');
    tr.find('button').removeClass('highlight');
    $(this).addClass('highlight');
    calculateOutcome();
});
$('.vote-second-no-rights').click(function () {
    var tr = $(this).parents('tr:first');
    tr.find('button').removeClass('highlight');
    $(this).addClass('highlight');
    calculateOutcome();
});
$(document).ready(function () {
    prepareNumber.text(calculateVotesNeeded());
    prepareFirstVotingList();
    prepareSecondVotingList();
    calculateOutcome();
});

// JS for the hexagons fading in

function runWelcome(i) {
    $('#hex' + i).animate({opacity: 0.8});
    $('#welcomeText' + i).fadeIn();
    //  $('#hex' + (i - 1)).animate({opacity: 0.1});
    //  $('#welcomeText' + (i - 1)).fadeOut();
}


function setupWelcome() {
    // find out how wide the screen is   
    var hexHeight = 199;
    var bottomArray = new Array();
    var leftArray = new Array();
    var vFit = Math.ceil($(window).height() / 200);
    var welcomeMessage = [" ", "", "", "", ""];
    var k = 0;
    var bottom = 0;
    var left = -55;
    var i = 0;
    var col = 0;
    while (left < $(window).width()) {
        col++;
        while (bottom < $(window).height() + 100) {
            bottomArray[i] = bottom;
            leftArray[i] = left;
            if (col % 2) {
                bottomArray[i] -= 100;
            }
            i++;
            bottom += hexHeight;
        }

        left += 175;
        bottom = 0;
    }
    for (i = 0; i < bottomArray.length; i++) {
        var html = '<div id="hex' + i + '"  class = "hexagon" style="bottom:' + bottomArray[i] + 'px;left:' + leftArray[i] + 'px;">\n\
<div class = "hexLeft"></div><div class = "hexMid"></div><div class = "hexRight"></div></div>';
        $('body').append(html);
        if (welcomeMessage[k] && i % vFit == (2 || 3)) {
            var message = '<span id="welcomeText' + i + '" class="welcomeText" style="bottom:' + bottomArray[i] + 'px;left:' + leftArray[i] + 'px;"> ' + welcomeMessage[k] + '</span>';
            $('body').append(message);
            k++;
        }
    }
    return i;
}
var utsFrozen = false;
var uswFrozen = false;
var ucrFrozen = false;

function uwwEnableAjax(){

    if (utsFrozen) {
        return;
    }

    utsFrozen = true;

    wp.ajax.post('uut_callback', {'is_green': document.getElementById('utb_icon').classList.contains('uut-green')})
        .done(function(uutracker_active) {

            if ( uutracker_active ) {

                document.getElementById('utb_icon').classList.add('uut-green');
                document.getElementById('wp-admin-bar-uut-enable').firstChild.innerText = "Disable";

            }else{

                document.getElementById('utb_icon').classList.remove('uut-green');
                document.getElementById('wp-admin-bar-uut-enable').firstChild.innerText = "Enable";
            }

            utsFrozen = false;
        })
        .fail(function(){
            utsFrozen = false;
        });
}

function utwCloseList(){
    document.getElementById("utw_overlayid").style.width = '0px';
    document.getElementById("wp-admin-bar-uut-show").firstChild.innerText = "Show Links";
}

function uwwShowAjax(){

    if (document.getElementById("utw_overlayid").style.width === '0px') {

        if (uswFrozen) {
            return;
        }

        uswFrozen = true;

        wp.ajax.post('usww_callback', {'usww_trigger': true})
            .done(function(dataArray) {

                document.getElementById("utw_contentid").innerText = "";
                document.getElementById("utw_overlayid").style.width = '50%';

                const swDataTable = document.createElement("ul");

                for (let p = 0; p < dataArray.length; p++) {

                    let swTemp = document.createElement("li");
                    swTemp.innerText = dataArray[p]["link"];
                    swDataTable.appendChild(swTemp);
                }

                if ( dataArray.length < 1 ) {

                    let swTemp = document.createElement("li");
                    swTemp.innerText = "No web links yet :/ Make sure you enable tracking and try uploading some files.";
                    swDataTable.appendChild(swTemp);
                }

                document.getElementById("utw_contentid").appendChild(swDataTable);
                
                document.getElementById("wp-admin-bar-uut-show").firstChild.innerText = "Close List";
                uswFrozen = false;
            })
            .fail(function(){
                document.getElementById("utw_contentid").innerText = "Ajax error";
                uswFrozen = false;
            });

    }else{
        utwCloseList();
    }  
}

function uwwClearAjax(){

    if (ucrFrozen) {
        return;
    }

    ucrFrozen = true;

    wp.ajax.post('usrr_callback', {'usrr_clr': true})
        .done(function(result) {

            if (result == true) {
                document.getElementById("utw_contentid").innerText = "Success!";
                document.getElementById("wp-admin-bar-uut-clear").firstChild.innerText = "ok!";
                setTimeout(() => {document.getElementById("wp-admin-bar-uut-clear").firstChild.innerText = "Clear";}, 3000);
            }else{
                document.getElementById("utw_contentid").innerText = "Database error";
            }

            ucrFrozen = false;
        })
        .fail(function(){
            document.getElementById("utw_contentid").innerText = "Ajax error";
            ucrFrozen = false;
        });
}

//construct overlay

window.addEventListener('DOMContentLoaded', () => {
    
    const utOverlay = document.createElement("div");
    utOverlay.setAttribute("id", "utw_overlayid");
    utOverlay.classList.add('utw_overlay');

    const utOverlayContent = document.createElement("div");
    utOverlayContent.classList.add('utw_overlay_content');
    utOverlayContent.setAttribute("id", "utw_contentid");

    const utCloseBtn = document.createElement("a");
    utCloseBtn.classList.add('utw_closebtn');
    utCloseBtn.setAttribute("href", "javascript:void(0)");
    utCloseBtn.onclick = function(){utwCloseList()};
    utCloseBtn.innerHTML = "&times;";

    utOverlay.appendChild(utCloseBtn);
    utOverlay.appendChild(utOverlayContent);

    document.body.appendChild(utOverlay);

    document.getElementById("utw_overlayid").style.width = '0px';
});
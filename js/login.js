function TogglePassword(icon) {
    var x = document.getElementById("password");
    if (x.type == "password") {
        x.type = "text";
        icon.src = "img/icon/password_hide.png";
    } else {
        x.type = "password";
        icon.src = "img/icon/password_show.png";
    }
}

function ShowPass_MouseOn(icon){
    var x = document.getElementById("password");
    if (x.type == "password") {
        icon.src = "img/icon/password_show.png";
    } else {
        icon.src = "img/icon/password_hide.png";
    }
}

function ShowPass_MouseOff(icon){
    var x = document.getElementById("password");
    if (x.type == "password") {
        icon.src = "img/icon/password_hide.png";
    } else {
        icon.src = "img/icon/password_show.png";
    }
}
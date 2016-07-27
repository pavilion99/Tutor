var root = "/";

function logout() {
    var x = new XMLHttpRequest();
    x.open("GET", root + "logout/index.php", true);
    x.onreadystatechange = function () {
        if (x.readyState == 4 && x.status == 200) {
            location.reload();
        }
    };
    x.send();
}

function login() {
    var email = document.getElementById("email").value;
    var password = md5(document.getElementById("password").value);

    document.getElementById("error").classList.remove("visible");

    var data = new FormData();
    data.append("email", email);
    data.append("password", password);

    document.getElementById("error").classList.remove("visible");

    var x = new XMLHttpRequest();
    x.open("POST", root + "login/index.php", true);
    x.onreadystatechange = function () {
        if (x.readyState == 4 && x.status == 200) {
            var res = JSON.parse(x.responseText);

            if (!res.success) {
                var error = document.getElementById("error");

                var children = error.childNodes;
                for (var i = 0; i < children.length; i++) {
                    error.removeChild(children[i]);
                }

                var text = document.createTextNode(res.error);

                error.appendChild(text);
                error.classList.add("visible");
            } else {
                window.location.assign(root);
            }
        }
    };
    x.send(data);
}

function next() {
    var newPage = window.currentPage + 1;
    go(newPage);
}

function prev() {
    var newPage = window.currentPage - 1;
    go(newPage);
}

function go(page) {
    if (!window.formTemp)
        window.formTemp = {};

    if (window.currentPage == 1) {
        window.formTemp.phone = {
            "value": parseInt(document.getElementById("phone").value)
        };
    } else if (window.currentPage == 8) {
        var boxes = document.querySelectorAll("input[type='checkbox']");

        window.formTemp.schedule = [];
        window.formTemp.schedule = [
            null,
            [],
            [],
            [],
            [],
            [],
            [],
            []
        ];
        for (var j = 0; j < boxes.length; j++) {
            var day = parseInt(boxes[j].dataset["day"]);
            var slot = parseInt(boxes[j].dataset["slot"]);

            window.formTemp.schedule[day][slot] = boxes[j].checked;
        }
    } else {
        var options = document.getElementsByTagName("OPTION");
        for (var x = 0; x < options.length; x++) {
            var id = options[x].id;
            var value = options[x].value;
            var selected = options[x].selected;
            window.formTemp[id] = {};
            window.formTemp[id].value = value;
            window.formTemp[id].selected = selected;
        }
    }

    if (page == 9) {
        var schedule = JSON.stringify(window.formTemp.schedule);

        var list = [];

        for (var prop in window.formTemp) {
            if (!window.formTemp.hasOwnProperty(prop))
                continue;
            if (prop == 'phone' || prop == 'schedule')
                continue;
            if (window.formTemp[prop].selected)
                list.push(parseInt(window.formTemp[prop].value));
        }

        var listSubmit = JSON.stringify(list);

        var phone = window.formTemp.phone.value + "";

        var datum = new FormData();
        datum.append("phone", phone);
        datum.append("subjects", listSubmit);
        datum.append("schedule", schedule);
        datum.append("submit", true);

        var ajax = new XMLHttpRequest();
        ajax.open("POST", root + "account/upgrade/index.php", true);
        ajax.onreadystatechange = function () {
            if (ajax.readyState == 4 && ajax.status == 200) {
                var res = JSON.parse(ajax.responseText);

                if (res.success) {
                    location.assign(root + "index.php?tutor");
                } else {
                    document.getElementById("error").innerHTML = res.error;
                    document.getElementById("error").classList.add("visible");
                }
            }
        };
        ajax.send(datum);
    } else {
        var y = new XMLHttpRequest();
        var data = new FormData();
        data.append("page", page);

        y.open("POST", root + "account/upgrade/index.php", true);
        y.onreadystatechange = function () {
            if (y.readyState == 4 && y.status == 200) {
                document.getElementById("data").innerHTML = y.responseText;

                if (page == 8 && window.formTemp.schedule) {
                    for (var i = 1; i < window.formTemp.schedule.length; i++) {
                        for (var j = 1; j < window.formTemp.schedule[1].length; j++) {
                            document.getElementById(i + "-" + j).checked = window.formTemp.schedule[i][j];
                        }
                    }
                } else {
                    for (var el in window.formTemp) {
                        if (!window.formTemp.hasOwnProperty(el))
                            continue;

                        var pel = document.getElementById(el);

                        if (pel == null)
                            continue;

                        for (var attr in window.formTemp[el]) {
                            if (!window.formTemp[el].hasOwnProperty(attr))
                                continue;
                            pel[attr] = window.formTemp[el][attr];
                        }
                    }
                }

                window.currentPage = page;
            }
        };
        y.send(data);
    }
}

function signup() {
    var error = document.getElementById("error");

    error.classList.remove("visible");

    var nameBox, emailBox, gradeSelect, passwordBox, passwordConfirmationBox;
    var name, email, grade, password, confirmation;

    nameBox = document.getElementById("name");
    emailBox = document.getElementById("email");
    gradeSelect = document.getElementById("grade");
    passwordBox = document.getElementById("password");
    passwordConfirmationBox = document.getElementById("password-confirmation");

    name = nameBox.value;
    email = emailBox.value;
    grade = parseInt(gradeSelect.value);
    password = md5(passwordBox.value);
    confirmation = passwordConfirmationBox.value;

    if (password != confirmation) {
        error.innerHTML = "Password and confirmation do not match.";
        error.classList.add("visible");
        return;
    }

    var data = new FormData();

    data.append("email", email);
    data.append("password", password);
    data.append("name", name);
    data.append("grade", grade);

    var x = new XMLHttpRequest();
    x.open("GET", root + "signup/index.php", true);
    x.onreadystatechange = function() {
        if (x.readyState == 4 && x.status == 200) {
            var res = JSON.parse(x.responseText);
            if (res.success) {
                location.assign(root);
            } else {
                error.innerHTML = res.error;
                error.classList.add("visible");
            }
        }
    };
}

//noinspection JSUnusedGlobalSymbols
function update() {
    var errors = document.getElementsByClassName("error");

    for (var err in errors) {
        if (!errors.hasOwnProperty(err))
            continue;

        err.classList.remove("visible");
        //noinspection JSPrimitiveTypeWrapperUsage

        err.innerHTML = "";
    }

    var name, email, grade, phone, password, passwordConfirmation;

    var nameBox, emailBox, gradeSelect, phoneBox, passwordBox, passwordConfirmationBox;

    nameBox = document.getElementById("name");
    emailBox = document.getElementById("email");
    gradeSelect = document.getElementById("grade");

    name = nameBox.value;
    email = emailBox.value + "@ohschools.org";
    grade = parseInt(gradeSelect.value);

    if (document.getElementById("password").value != "") {
        passwordBox = document.getElementById("password");
        passwordConfirmationBox = document.getElementById("password-confirmation")
    }

    if (window.tutor) {
        phoneBox = document.getElementById("phone");
        phone = phoneBox.value;
    }

    password = passwordBox.value;
    passwordConfirmation = passwordConfirmationBox.value;

    if (password !== "") {
        if (passwordConfirmation !== password) {
            var securityError = document.getElementById("security-error");
            securityError.innerHTML = "Password and password confirmation do not match.";
            securityError.classList.add("visible");
            return;
        }

        password = md5(password);
    }

    var data = new FormData();

    data.append("name", name);
    data.append("email", email);
    data.append("grade", grade);


    if (password !== "") {
        data.append("password", password);
    }

    var x = new XMLHttpRequest();
    x.open("POST", root + "account/edit/index.php");
    x.onreadystatechange = function () {
        if (x.readyState == 4 && x.status == 200) {

        }
    };
    x.send(data);
}
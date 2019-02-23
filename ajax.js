/*


https://scotch.io/tutorials/how-to-use-the-javascript-fetch-api-to-get-data


*/


//const baseurl = 'http://expense.atw.hu/api.php';
const baseurl = 'api.php';
const version = '0.4';
const NULLUSER = 'nulluser';
var User = { name: 'nobody', token: NULLUSER }
var categories = [];

function sendExpense() {
    let amount = f('amount').value;
    let category = f('category').value;
    let description = f('description').value;
    //let param = '?amount=' + amount + '&category=' + category + '&description=' + description;
    let data = {
        amount: amount,
        category: category,
        description: description,
        token: User.token
    }
    let fd = new FormData();
    for (var i in data) {
        fd.append(i, data[i]);
    }
    let fetchData = {
        method: 'POST',
        mode: 'cors',
        body: fd,
        headers: new Headers()
    }
    fetch(baseurl, fetchData)
        .then(function (data) {
            f('amount').value = '';
            f('category').value = '';
            f('description').value = '';
            fetchTransactions();
            fetchCategories();
            console.log(data);
        })

        .catch(function (error) {
            console.log('error');
            // This is where you run code if the server returns any errors
        });
}

function fetchTransactions() {
    let url = 'api.php?token=' + User.token + '&top=5';

    fetch(url)
        .then(response => response.json())
        .then(body => {
            let t = '<table class="w3-table-all w3-xxlarge">';
            t += '<tr><th>Amount</th><th>Category</th><th>Description</th><th>Time</th><th>Balance</th></tr>';

            for (var i in body) {
                t += '<tr>';
                t += '<td>' + body[i][0] + '</td>';
                t += '<td>' + body[i][1] + '</td>';
                t += '<td>' + body[i][2] + '</td>';
                t += '<td>' + body[i][3] + '</td>';
                t += '<td>' + body[i][4] + '</td>';
                t += '</tr>';
            }
            t += '</table>';
            f('transactions').innerHTML = t;
        })

        .catch(function (error) {
            console.log('error');
            // This is where you run code if the server returns any errors
        });
}

function insertCategory(n){
    f('category').value = n;
}

function fetchCategories() {
    let url = 'api.php?categories=' + User.token;
    fetch(url)
        .then(response => response.json())
        .then(body => {
            let t = '';
            categories = body;
            for (var i in body) {
                t += '<button class="w3-button w3-blue w3-round-xxlarge w3-xlarge" onclick="insertCategory(\''+body[i]+'\')">';
                t += body[i];
                t += '</button>&nbsp;';
            }
            f('category_container').innerHTML = t;
        })

        .catch(function (error) {
            console.log('error');
            // This is where you run code if the server returns any errors
        });
}

function fetchNumberOfUsers() {
    let url = 'api.php?count=users';

    fetch(url)
        .then(response => response.json())
        .then(body => {
            f('users').innerHTML = 'We have ' + body.count + ' users.';
        })

        .catch(function (error) {
            console.log('error');
            // This is where you run code if the server returns any errors
        });
}

function createUser() {
    let name = f('name').value;
    let password = f('password').value;
    let email = f('email').value;

    let data = {
        name: name,
        password: password,
        email: email
    }
    let fd = new FormData();
    for (var i in data) {
        fd.append(i, data[i]);
    }
    let fetchData = {
        method: 'POST',
        mode: 'cors',
        body: fd,
        headers: new Headers()
    }

    fetch(baseurl, fetchData)
        .then(response => response.json())
        .then(body => {
            f('name').value = '';
            f('password').value = '';
            f('email').value = '';
            console.log(body.result);
            if (body.result == 'exists') {
                show('register_wrong');
            } else if (body.result == 'success') {
                hide('register_wrong');
                User.name = body.name;
                User.token = body.token;
                processLogin();
            }
        })

        .catch(function (error) {
            console.log('error');
            // This is where you run code if the server returns any errors
        });
}

function backToLogin() {
    show('login');
    hide('create_user');
}

function init() {
    writeVersion();
    fetchNumberOfUsers();
}

function login() {
    //fetch user
    let name = f('login_name').value;
    let password = f('login_password').value;
    let url = 'api.php?name=' + name + '&password=' + password;

    fetch(url)
        .then(response => response.json())
        .then(body => {
            if (body.result == 'success') {
                User.name = body.name;
                User.token = body.token;
                processLogin();
            } else {
                //wrong password
                f('login_wrong').style.display = 'block';
            }
            f('login_name').value = '';
            f('login_password').value = '';
        })

        .catch(function (error) {
            console.log('error');
            // This is where you run code if the server returns any errors
        });
    if (User.code == NULLUSER) {
        f('user').innerHTML = 'You are not logged in';
    } else {
        f('user').innerHTML = 'Hi ' + User.name;
    }
}

function processLogin() {
    show('main');
    hide('login');
    hide('create_user');
    hide('login_wrong');
    f('user').innerHTML = 'Hi ' + User.name + '!';
    fetchTransactions();
    fetchCategories();
}

function register() {
    hide('login');
    show('create_user');
}

function writeVersion() {
    text('version', 'version: ' + version);
}

function f(i) {
    return document.getElementById(i);
}

function show(i) {
    f(i).style.display = 'block';
}

function hide(i) {
    f(i).style.display = 'none';
}

function text(i, txt) {
    f(i).innerHTML = txt;
}
/*


https://scotch.io/tutorials/how-to-use-the-javascript-fetch-api-to-get-data


*/


//const baseurl = 'http://expense.atw.hu/api.php';
const baseurl = 'api.php';
const version = '0.3';

function sendExpense() {
    let amount = f('amount').value;
    let category = f('category').value;
    let description = f('description').value;
    //let param = '?amount=' + amount + '&category=' + category + '&description=' + description;
    let data = {
        amount: amount,
        category: category,
        description: description
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
            //empty field
            f('amount').value = '';
            f('category').value = '';
            f('description').value = '';
            //refresh transactions
            fetchTransactions();
            console.log(data);
        })

        .catch(function (error) {
            console.log('error');
            // This is where you run code if the server returns any errors
        });
}

function fetchTransactions() {
    let url = 'api.php?id=1&top=5';

    fetch(url)
        .then(response => response.json())
        .then(body => {
            let t = '<table class="w3-table-all w3-xxxlarge">';
            t += '<tr><th>Amount</th><th>Category</th><th>Description</th></tr>';

            for (var i in body) {
                t += '<tr>';
                t += '<td>' + body[i][0] + '</td>';
                t += '<td>' + body[i][1] + '</td>';
                t += '<td>' + body[i][2] + '</td>';
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

function createUser() {
    let name = f('name').value;
    let password = f('password').value;
    let email = f('email').value;
    //let param = '?amount=' + amount + '&category=' + category + '&description=' + description;
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
        .then(function (data) {
            f('name').value = '';
            f('password').value = '';
            f('email').value = '';
            console.log(data);
        })

        .catch(function (error) {
            console.log('error');
            // This is where you run code if the server returns any errors
        });
}

function init() {
    writeVersion();
    fetchTransactions();
}

function writeVersion() {
    f('version').innerHTML = 'version: ' + version;
}

function f(i) {
    return document.getElementById(i);
}
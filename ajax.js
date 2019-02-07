/*


https://scotch.io/tutorials/how-to-use-the-javascript-fetch-api-to-get-data


*/


const baseurl = 'http://expense.atw.hu/api.php';

function send() {
    let amount = f('amount').value;
    let category = f('category').value;
    let description = f('description').value;
    //let param = '?amount=' + amount + '&category=' + category + '&description=' + description;
    let data = {
        amount: amount,
        category: category,
        description: description
    }
    let fetchData = {
        method: 'POST',
        body: data,
        headers: new Headers()
    }

    fetch(baseurl, fetchData)
        .then(function (data) {
            console.log('success');
        })
       
        .catch(function(error) {
            console.log('error');
            // This is where you run code if the server returns any errors
        });
}

function f(i) {
    return document.getElementById(i);
}
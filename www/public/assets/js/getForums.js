$(document).ready(function() {
    $('#forum-form').submit(function(event) {
        let payload = {
            title: $('input[name=title]').val(),
            description: $('input[name=description]').val()
        };

        $.ajax({
            type: 'GET',
            url: '/api/forums',
            contentType: 'application/json;charset=utf-8',
            data: JSON.stringify(payload), // our data object
            dataType: 'json' // what type of data do we expect back from the server
        })
            .done(function(data) {
                console.log(data);

            })
            .fail(function(error) {
                console.log(error);
            });

        // stop the form from submitting the normal way and refreshing the page
        event.preventDefault();
    });
});
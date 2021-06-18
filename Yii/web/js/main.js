'use strict';
document.getElementById('project-selector').addEventListener('change', function (e) {
    console.log(e.target.value);
    $.ajax({
        url: '/project/select',
        type: 'POST', dataType: 'json',
        data: {id: e.target.value},
        cache: false,
        success: function (data) {
            console.log(data);
        },
        error: function (err) {
            console.error(err);
        },
    });
})
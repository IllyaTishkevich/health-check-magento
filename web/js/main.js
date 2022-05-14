'use strict';

document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('project-selector').addEventListener('change', function (e) {
        console.log(e.target.value);
        $.ajax({
            url: '/project/select',
            type: 'POST', dataType: 'json',
            data: {id: e.target.value},
            cache: false,
            success: function () {
               location.reload();
            },
            error: function (err) {
                console.error(err);
            },
        });
    });
});

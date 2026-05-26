$(document).ready(function() {
    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': csrfToken
        }
    });

    $('#btn-logout').on('click', function(e) {
        e.preventDefault();
        window.location.href = '?url=login/logout';
    });
});

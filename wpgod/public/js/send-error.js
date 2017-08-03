jQuery(document).ready(function($){
    $.post(
        configGodError.url, 
        {
            'action': configGodError.action
        }
    );
});
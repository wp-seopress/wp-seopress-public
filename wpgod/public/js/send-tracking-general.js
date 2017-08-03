jQuery(document).ready(function($){
    $.post(
        configGodTrackingGeneral.url, 
        {
            'action': configGodTrackingGeneral.action
        }
    );
});
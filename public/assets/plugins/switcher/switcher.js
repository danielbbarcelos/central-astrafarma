/* Abre o menu lateral */

$(function() {
    var $switcher            = $('#style_switcher'),
        $switcher_toggle     = $('#style_switcher_toggle');

    $switcher_toggle.click(function(e) {
        e.preventDefault();
        $switcher.toggleClass('switcher_active');
    });

});


function selecionaFilial(id) {

    $('#style_switcher_toggle').click();

    window.location = '/filial/'+id;
}


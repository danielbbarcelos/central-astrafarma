function selecionaTemplate(color)
{
    $(".template-selected").attr("hidden",true);
    $(".template-ribbon-"+color).attr("hidden",false);
    $("#pdf_template").val("/assets/img/pdf/border-"+color+".png");
}

function exibeTemplate()
{
    window.open($("#pdf_template").val());
}


$('#logo-empresa-img').on('click', function(){
    $("#logo-empresa-input").click();
});

$("#logo-empresa-input").change(function (){
    var fileTypes = ['jpg', 'jpeg', 'png'];
    if (this.files && this.files[0]) {
        var reader = new FileReader();
        var extension = this.files[0].name.split('.').pop().toLowerCase(),  //file extension from input file
            isSuccess = fileTypes.indexOf(extension) > -1;  //is extension in acceptable types
        if (isSuccess) {
            reader.onload = function(e) {
                $('#logo-empresa-img').attr('src', e.target.result);
            };
            reader.readAsDataURL(this.files[0]);
        }
        else
        {
            $("#logo-empresa-input").val("");

            Materialize.toast('Arquivo inválido. Extensões permitidas: jpg, jpeg e png', 5000, 'red');
        }
    }
});

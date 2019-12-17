function filter(){
    if($("#filterForm").length){    //Le form existe
        if($("#filterForm").attr('style')=="display: none;"){
            $("#filterForm").show();
        }
        else{
            $("#filterForm").hide();
        }
    }
    else{           //Génération du form
        form="<form id=filterForm class='form-inline' method=get>"
        form+="<div class='form-group'>";
        form+="<select class='form-control' name='order'>";
        form+="<option value='Titre'>Titre</option>";
        form+="<option value='Adresse'>Adresse</option>";
        form+="<option value='Date'>Date</option>";
        form+="<option value='Nom'>Thème</option>";
        form+="<option value='EffectifActuel'>Effectif</option>";
        form+="</select>";
        form+="<select class='form-control' name='crois'>";
        form+="<option value='DESC'>Décroissant</option>";
        form+="<option value='ASC'>Croissant</option>";
        form+="</select>";
        form+="<input class='btn btn-primary' type=submit value='Filtrer' name='filt'>";
        form+="</div>";
        form+="</form>"
        $(form).insertAfter("#filterButton");
    }
}

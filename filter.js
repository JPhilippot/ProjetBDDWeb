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
        form="<form id='filterForm' method=get>";
        form+="<select name='order'>";
        form+="<option value='Titre'>Titre</option>";
        form+="<option value='Adresse'>Adresse</option>";
        form+="<option value='Date'>Date</option>";
        form+="<option value='Nom'>Thème</option>";
        form+="<option value='EffectifActuel'>Effectif</option>";
        form+="</select>";
        form+="<select name='crois'>";
        form+="<option value='DESC'>Décroissant</option>";
        form+="<option value='ASC'>Croissant</option>";
        form+="</select>";
        form+="<input type=submit value='Filtrer' name='filt'>";
        $(form).insertAfter("#filterButton");
    }
}

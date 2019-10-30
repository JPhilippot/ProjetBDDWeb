function generationForm(val){
    if(val=="log"){
        if($("#connexion").length){             //Le form existe
            if($("#connexion").attr('style')=='display: none;'){
                $("#connexion").show();
            }
            else{
                $("#connexion").hide();
            }
        }else{          //Generation du form
            var form="<form id='connex' method='post'>";
            form+="Login:<br>";
            form+='<input name="login" type="text" placeholder="'+"Adresse mail ou nom d'utilisateur"+'" required><br>';
            form+="Mot de passe:<br>";
            form+="<input name='pass' type='text' placeholder='Mot de passe' required><br>";
            form+="Se souvenir de moi ";
            form+="<input name='remember' type='checkbox'><br>";
            form+="<input name='"+val+"' type='submit' value='Se connecter'>"
            form+="</form>";

            $("body").append("<div id='connexion'>"+form+"</div>");
        }
    }

    else if(val=="reg"){
        if($("#registration").length){              //Le form existe
            if($("#registration").attr('style')=='display: none;'){
                $("#registration").show();
            }
            else{
                $("#registration").hide();
            }
        }
        else{           //Generation du form
            var form="<form id='connex' method='post'>";
            form+="Login:<br>";
            form+='<input name="login" type="text" placeholder="'+"Nom d'utilisateur"+'" required><br>';
            form+="email:<br>";
            form+="<input name='email' type='text' placeholder='Adresse mail' required><br>";
            form+="Mot de passe:<br>";
            form+="<input name='pass' type='text' placeholder='Mot de passe' required><br>";
            form+="Se souvenir de moi ";
            form+="<input name='remember' type='checkbox'><br>";
            form+="<input name='"+val+"' type='submit' value='S'enregistrer'>"
            form+="</form>";

            $("#registration").empty();
            $("body").append("<div id='registration'>"+form+"</div>");
        }
    }
}

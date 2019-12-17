
function addMarker(id,title, login, lat, long, address) {
    let image = $("#markerProto").clone();
    image.attr("id", "marker" + id).attr('style','display:block').attr('height','50px').attr('width','40px');
    $("body").append(image);

    let popupC = $("#popupProto").clone();
    popupC.attr("id", "popup" + id);
    popupC.append("<a href='./contenu.php?lastevent="+id+"'><p style='text-align:center;'>"+ title 
    + "</p><p style='padding-top:5px; font-size:90%;'> Par "+login+" à "+address+".</p></a>");;
    $("body").append(popupC);

    var marker = document.getElementById('marker' + id);
    map.addOverlay(new ol.Overlay({
        position: ol.proj.fromLonLat([long, lat]),
        positioning:'bottom-right',
        element: marker
    })); console.log('marked')
    
    var popup = document.getElementById('popup' + id);
    map.addOverlay(new ol.Overlay({
        offset: [10, -50],
        position: ol.proj.fromLonLat([long, lat]),
        element: popup
    })); console.log('popped')
    console.log("long"+long+" lat"+lat);


}

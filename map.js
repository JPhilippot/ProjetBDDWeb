
function addMarker(id,title, login, lat, long, address) {
    let image = $("#markerProto").clone();
    image.attr("id", "marker" + id).attr('style','display:block').attr('height','50px').attr('width','50px');
    $("body").append(image);

    let popupC = $("#popupProto").clone();
    popupC.attr("id", "popup" + id);
    popupC.append("<a href='./contenu.php?lastevent="+id+"'>"+ title + " , par "+login+" à "+address+".</a>");;
    $("body").append(popupC);

    var marker = document.getElementById('marker' + id);
    map.addOverlay(new ol.Overlay({
        position: ol.proj.fromLonLat([long, lat]),
        element: marker
    })); console.log('marked')
    var popup = document.getElementById('popup' + id);
    map.addOverlay(new ol.Overlay({
        offset: [0, -50],
        position: ol.proj.fromLonLat([long, lat]),
        element: popup
    })); console.log('popped')


}




jQuery(document).ready(function($){
var facID = $("div.publications_container").attr("id");
    $.ajax({
		url:"/sharedassets/curvita/pubs_load.php?facultyid="+facID,
		dataType: 'html',
		success:function(data){
     $("div#publications-"+facID).html(data);
    	
		}});
});

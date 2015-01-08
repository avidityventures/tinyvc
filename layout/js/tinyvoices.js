$(function () {
    $('a[href="#search"]').on('click', function(event) {
        event.preventDefault();
        $('#search').addClass('open');
        $('#search > form > input[type="search"]').focus();
    });
    
    $('#search, #search button.close').on('click keyup', function(event) {
        if (event.target == this || event.target.className == 'close' || event.keyCode == 27) {
            $(this).removeClass('open');
        }
    });
    
    
    
    $('a[href="#login"]').on('click', function(event) {
        event.preventDefault();
        $('#login').addClass('open');
        $('#login > form > input[type="search"]').focus();
    });
    
    $('#login, #login button.close').on('click keyup', function(event) {
        if (event.target == this || event.target.className == 'close' || event.keyCode == 27) {
            $(this).removeClass('open');
        }
    });

  
    $('form').submit(function(event) {
        event.preventDefault();
        return false;
    })

});

$("#searchformsubmit").click(function() {
    $('#searchform').submit();
});



$('#saveForm').click(function() {
      $.ajax({
            type: "post",
            url: ""+SERVER_URL+"system_files/process/register-save",
            data: $('#registrationForm').serialize(),
            contentType: "application/x-www-form-urlencoded",
            success: function(responseData, textStatus, jqXHR) {
                $("#results").html(responseData);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        })
  });


$("form[name='form-profile']").on('submit', function(e){
        var formData = new FormData($(this)[0]);
        
        $.ajax({
            url: ""+SERVER_URL+"system_files/process/profile-save",
            type: "POST",
            data: formData,
            async: false,
            success: function(responseData, textStatus, jqXHR) {
                $("#results").html(responseData);
            },
            cache: false,
            contentType: false,
            processData: false
        });

        e.preventDefault();
    });







$("form[name='form-share-social']").on('submit', function(e){
        var formData = new FormData($(this)[0]);
        
        $.ajax({
            url: ""+SERVER_URL+"system_files/process/voice-share",
            type: "POST",
            data: formData,
            async: false,
            success: function(responseData, textStatus, jqXHR) {
                $("#results").html(responseData);
            },
            cache: false,
            contentType: false,
            processData: false
        });

        e.preventDefault();
    });

$("form[name='form-response-voice']").on('submit', function(e){
        var formData = new FormData($(this)[0]);
        
        $.ajax({
            url: ""+SERVER_URL+"system_files/process/voice-response",
            type: "POST",
            data: formData,
            async: false,
            dataType:"json", //to parse string into JSON object,
            success: function(data){ 
              
              if (data.error) {
               $("#resultsThrown").html("<div class='error-message text-left'>"+data.error+"</div>");
               $(".error-message").fadeOut(5000);
              }else{
               var uid = data.id;
               var fullname = data.fullname;
               var avatar = data.fd;
               var response = data.response;
               var vid = data.vid;
               var country = data.country;
               
               if (data.record==0) {
                $(".voiceComments-header-title").show();
               }
               
               var colors = ['#95A5A6','#4ECDC4','#52B3D9','#C0392B','#336E7B','#913D88','#F5D76E','#D2527F','#26C281'];
               var random_color = colors[Math.floor(Math.random() * colors.length)];
              
               var string1='<div class="voicesComments-response" style="border-bottom:1px solid '+random_color+'">';
               var string2='<img src="'+SERVER_URL+'system_files/modules/resizer/imgresize?src='+SERVER_URL+'system_files/users_data/'+avatar+'/media/'+avatar+'.jpg&w=200&h=200&zc=1&a=t" class="img-circle avatar-voices">';
               var string3='<h4 style="display: inline;">'+fullname+' . <small>'+country+'</small><div></div></h4>';
               var string4='<div class="voicesComments-time">just now</div>';
               var string5='<div class="voiceComments-paragraph">'+response+'</div>';
               
               var compileStr=string1+string2+string3+string4+string5;
               $("#commentsBox").prepend(compileStr);
               
               
               
               var aTag = $("#topResponse");
               $('html,body').animate({scrollTop: aTag.offset().top},'slow'); 
              }
             
           
            },
            cache: false,
            contentType: false,
            processData: false
        });

        e.preventDefault();
    });


$('#confirmDelete').click(function() {
      $.ajax({
            type: "post",
            url: ""+SERVER_URL+"system_files/process/profile-delete",
            data: $('#confirmDeleteForm').serialize(),
            contentType: "application/x-www-form-urlencoded",
            success: function(responseData, textStatus, jqXHR) {
                $("#resultsDelete").html(responseData);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        })
  });


$(document).ready(function(){
     $("#voice_response").autosize();

    });
    
    
$('.delete-comment').click(function() {
 var id=$(this).attr('id');
 $("#delete_confirm_comment_"+id).show();
 
 
 var string1='<div class="delete-comments-box">';
 var string2='Confirm delete this comment ?';
 var string3='<div style="margin-top:5px;"><button type="button" class="btn btn-warning  cancel-delete-comments" id="'+id+'">Cancel</button> <a href="#" id="'+id+'" type="button" class="btn btn-danger  confirm-delete-remove-comments"> Confirm Delete </a></div>';
 var string4='</div>';
 
 var fullstring=string1+string2+string3;
 
 $("#delete_confirm_comment_"+id).html(fullstring);
 
 $('.cancel-delete-comments').click(function() {
 var id=$(this).attr('id');
 $("#delete_confirm_comment_"+id).hide();
 });
 
 $('.confirm-delete-remove-comments').click(function() {
 var id=$(this).attr('id');
 $.ajax({
        type: "post",
        url: ""+SERVER_URL+"system_files/process/voice-response-delete",
        data: {id:id},
        contentType: "application/x-www-form-urlencoded",
         success: function(data){ 
            $(".voice-comments-box_"+id).fadeOut(1000);
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log(errorThrown);
        }
    })
 });
});
  

$('.delete-post').click(function() {
 var id=$(this).attr('id');
 $("#delete_confirm_post_"+id).show();
 $(".hide_div_"+id).hide();
 
 
 var string1='<div class="delete-comments-box delete-postings ">';
 var string2='<div class="delete-comments-box-title">Confirm delete this voice ?</div>';
 var string3='<div class="delete-postings-actions"><button type="button" class="btn btn-warning  cancel-delete-comments" id="'+id+'">Cancel</button> <a href="#" id="'+id+'" type="button" class="btn btn-danger  confirm-delete-remove-post"> Confirm Delete </a></div>';
 var string4='</div>';
 
 var fullstring=string1+string2+string3;
 
 $("#delete_confirm_post_"+id).html(fullstring);
 
 $('.cancel-delete-comments').click(function() {
 var id=$(this).attr('id');
 $("#delete_confirm_post_"+id).hide();
 $(".hide_div_"+id).show();
 
 });
 
 $('.confirm-delete-remove-post').click(function() {
 var id=$(this).attr('id');

 $.ajax({
        type: "post",
        url: ""+SERVER_URL+"system_files/process/voice-post-delete",
        data: {id:id},
        contentType: "application/x-www-form-urlencoded",
         success: function(data){ 
            $(".voice-post-box-"+id).fadeOut(1000);
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log(errorThrown);
        }
    })

 });
});    
    


$('#confirmReport').click(function() {
      $.ajax({
            type: "post",
            url: ""+SERVER_URL+"system_files/process/voice-report",
            data: $('#confirmReportForm').serialize(),
            contentType: "application/x-www-form-urlencoded",
            success: function(responseData, textStatus, jqXHR) {
                $("#resultsDelete").html(responseData);
                
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        })
  });



$("form[name='submitVoice']").on('submit', function(e){
        $(".submitButton").hide();  
        $(".submitMessage").html("<h3><i class='fa fa-cog fa-spin'></i> Previewing...</h3>");
        
        tinyMCE.triggerSave();
        var formData = new FormData($(this)[0]);
        $.ajax({
            url: ""+SERVER_URL+"system_files/process/voice-save",
            type: "POST",
            data: formData,
            async: true,
            dataType:"json",
            success: function(data, textStatus, jqXHR) {
                
                if (data.status=="error") {
                   
                 $("#resultSave").html('<div class="error-message">'+data.message+'</div>');
                 $(".submitMessage").hide();
                 $(".submitButton").show();
                 
                }
                
                if (data.status=="success") {
                  location.href =""+SERVER_URL+"voice-preview?v="+data.action+"";
                }
                
            },
            cache: false,
            contentType: false,
            processData: false
        });
        
        e.preventDefault();
    });


$(".postAction").on('click', function(e){
  var currValue=$(this).attr('id');
  var typeValue=$(this).attr('rel');
   
        $.ajax({
            url: ""+SERVER_URL+"system_files/process/voice-backers?v="+currValue+"&tp="+typeValue,
            type: "POST",
            async: false,
            dataType:"json", //to parse string into JSON object,
            success: function(data){ 
           
            if (typeValue=="backers") {
              
              var valueSet1 = data.value_backers;
              var valueSet2 = data.value_deniers;
              
              
              
              if (valueSet1 < 2) {
                var stringSet1="<i class='fa fa-plus'></i>";
              }else{
                var stringSet1="<i class='fa fa-plus'></i>";
              }
              
              if (valueSet2 < 2) {
                var stringSet2="<i class='fa fa-minus'></i>";
              }else{
                var stringSet2="<i class='fa fa-minus'></i>";
              }
              
              
              $("#resultsBackers_Plus_"+currValue).html(stringSet1+' '+valueSet1);
              $("#resultsDeniers_Plus_"+currValue).html(stringSet2+' '+valueSet2);
              
              
              $(".replace_minus_"+currValue).removeClass("timeline-minus-selected").addClass("timeline-minus");
              $(".replace_plus_"+currValue).removeClass("timeline-plus").addClass("timeline-plus-selected");
            
            }
            
            
            if (typeValue=="deniers") {
              
              var valueSet1 = data.value_backers;
              var valueSet2 = data.value_deniers;
              
              
              
              if (valueSet1 < 2) {
                var stringSet1="<i class='fa fa-plus'></i>";
              }else{
                var stringSet1="<i class='fa fa-plus'></i>";
              }
              
              if (valueSet2 < 2) {
                var stringSet2="<i class='fa fa-minus'></i>";
              }else{
                var stringSet2="<i class='fa fa-minus'></i>";
              }
              
              
              $("#resultsBackers_Plus_"+currValue).html(stringSet1+' '+valueSet1);
              $("#resultsDeniers_Plus_"+currValue).html(stringSet2+' '+valueSet2);
              
              
              $(".replace_minus_"+currValue).removeClass("timeline-minus").addClass("timeline-minus-selected");
              $(".replace_plus_"+currValue).removeClass("timeline-plus-selected").addClass("timeline-plus");
            
            }
              
            
            
        },
            cache: false,
            contentType: false,
            processData: false
        });

        e.preventDefault();
    });


$(".switch-state").bootstrapSwitch();
      
    jQuery(function($) { 
    function fixDiv() {
      if ($(window).scrollTop() > 290) {
        $('.profile-left-menu').css({'top':'80px'});
      } else{
        $('.profile-left-menu').css({'top':'366px'});
      }
    }
      $(window).scroll(fixDiv);
      fixDiv();
    });
    
    
   
    
    

tinymce.init({
      selector: "textarea",
      skin: 'light',
      theme: "modern",
      height: 200,
      menubar : false,
      plugins: ["link","visualblocks visualchars  fullscreen insertdatetime media nonbreaking","save table  directionality emoticons   textcolor"],
      toolbar: "bold italic | bullist numlist | link",
      visual: false,
      statusbar : false,
      link_title: false,
      target_list: false,
      valid_elements: "p[style],br,b,i,strong,em,a,ul,ol,li,p,"
      });
     
$("#mediaUpload").fileinput({showUpload: false, fileTypeSettings: "image", maxFileCount: 6, showCaption: false, mainClass: "input-group-lg"});
$("#profilePhoto").fileinput({showUpload: false, fileTypeSettings: "image", maxFileCount: 1, showCaption: false, mainClass: "input-group-lg"});    
//---------------------------------------------
// Typeahead with FS
//---------------------------------------------
$('#brands_services').typeahead({
    remote: {
        url: "https://api.foursquare.com/v2/venues/search?v=20140812&client_id=Q4EYGAQKVBB3PE55L2KPDBOZVVCIYRPJ4BZXELADAQZW45LG&client_secret=X5JF2RIKSVGYAFPI4IWWPDZ4YNKH540TFEOBK35HYAV2U3NA",
        replace: function(url, uriEncodedQuery) {
        var QUERY = $('#brands_services').val();
        
        var val = $('#resultUpdateSetLat').val()+','+$('#resultUpdateSetLong').val();
        $("#resultsLoading").show();
        if (!val) return url;
        return url + '&near='+val+'&query='+QUERY
        },
        
        filter: function(data) {
            var dataset = [];
            
             for(i = 0; i < data.response.venues.length; i++) {
                dataset.push({
                    id:data.response.venues[i].id,
                    name:data.response.venues[i].name.toUpperCase(),
                    location:data.response.venues[i].location.address,
                    city:data.response.venues[i].location.city,
                    country:data.response.venues[i].location.country,
                    lat:data.response.venues[i].location.lat,
                    lng:data.response.venues[i].location.lng                   
                });
             }
             
             $("#resultsLoading").hide();
             return dataset;
        },
    },
    template: ['<div class="type-header">{{name}}</div><div>{{location}} {{city}} {{country}}</div>'].join(),
    valueKey: 'name',
    limit:100,
    engine: Hogan,
    }).bind('typeahead:selected', function(obj, datum) {
       changeTypeahead(obj, datum);
    }).bind('typeahead:autocompleted', function(obj, datum) {
       changeTypeahead(obj, datum);
});


function errorSET(msg) {
  var s = document.querySelector('#google_canvas');
  s.innerHTML = typeof msg == 'string' ?
  msg : "<div style='background-color:#e9e9e9; height:350px;'><span style='display:inline-block;  color:#000; padding:15px; padding-top:50px; font-weight:bold;'><center><h3>Turn on your Geo-location service to find brands and services near you. It helps you to spot and identify efficiently. <br> <i class='fa fa-map-marker' style='margin-top:20px; color:#f19e9e; font-size:85px;'></i></h3></center></span></div>";
  s.className = 'fail';
  $('#resultUpdateSetLat').val(50.85);
  $('#resultUpdateSetLong').val(4.35);
}

//---------------------------------------------
// Google Map
//---------------------------------------------
(function() {
                        var map;
                        var pinkParksStyles = [
			{
				featureType: 'road.highway',
				elementType: 'all',
				stylers: [
					{ hue: '#e5e5e5' },
					{ saturation: -100 },
					{ lightness: 72 },
					{ visibility: 'simplified' }
				]
			},{
				featureType: 'water',
				elementType: 'all',
				stylers: [
					{ hue: '#30a5dc' },
					{ saturation: 47 },
					{ lightness: -31 },
					{ visibility: 'simplified' }
				]
			},{
				featureType: 'road',
				elementType: 'all',
				stylers: [
					{ hue: '#cccccc' },
					{ saturation: -100 },
					{ lightness: 44 },
					{ visibility: 'on' }
				]
			},{
				featureType: 'landscape',
				elementType: 'all',
				stylers: [
					{ hue: '#ffffff' },
					{ saturation: -100 },
					{ lightness: 100 },
					{ visibility: 'on' }
				]
			},{
				featureType: 'poi.park',
				elementType: 'all',
				stylers: [
					{ hue: '#d2df9f' },
					{ saturation: 12 },
					{ lightness: -4 },
					{ visibility: 'on' }
				]
			},{
				featureType: 'road.arterial',
				elementType: 'all',
				stylers: [
					{ hue: '#e5e5e5' },
					{ saturation: -100 },
					{ lightness: 56 },
					{ visibility: 'on' }
				]
			},{
				featureType: 'administrative.locality',
				elementType: 'all',
				stylers: [
					{ hue: '#000000' },
					{ saturation: 0 },
					{ lightness: 0 },
					{ visibility: 'on' }
				]
			}
		];

                        var pinkMapType = new google.maps.StyledMapType(pinkParksStyles,{name: "Pink Parks"});
	    	
		    	var mapOptions = {
		    		zoom: 15,
		    		mapTypeId: google.maps.MapTypeId.ROADMAP,
                                panControl: false,
                                zoomControl: true,
                                scaleControl: true,
                                mapTypeControl: false,
                                
                                mapTypeControlOptions: {
                                mapTypeIds: [google.maps.MapTypeId.ROADMAP, 'map_layout_styles']
                                }   
		    	};
            
	    	if(!!navigator.geolocation) {
	    	
	    		map = new google.maps.Map(document.getElementById('google_canvas'), mapOptions);
                        map.mapTypes.set('map_layout_styles',pinkMapType);
                        map.setMapTypeId('map_layout_styles'); 
                
	    		navigator.geolocation.getCurrentPosition(function(position) {
	    		
		    		var geolocate = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
		    		
		    		var infowindow = new google.maps.InfoWindow({
		    			map: map,
		    			position: geolocate
		    		});
		    		
                                
                                $('#resultUpdateSetLat').val(position.coords.latitude);
                                $('#resultUpdateSetLong').val(position.coords.longitude);
                                
                               
                                
                                
                                var myMarker = new google.maps.Marker({
                                position: new google.maps.LatLng(position.coords.latitude, position.coords.longitude),
                                draggable: true,
                                //icon: iconNew,
                                });
                                
                                
                               
                                
                                google.maps.event.addListener(myMarker, 'dragend', function(evt){
                                 //document.getElementById('current').innerHTML = '<p>Marker dropped:  ' + evt.latLng.lat().toFixed(3) + ',' + evt.latLng.lng().toFixed(3) + '</p>';
                                  $('#resultUpdateSetLat').val(evt.latLng.lat());
                                  $('#resultUpdateSetLong').val(evt.latLng.lng());
                                });
                               
                                
                               
              
                                google.maps.event.addListener(myMarker, 'dragstart', function(evt){
                                 //document.getElementById('current').innerHTML = '<p>Currently dragging marker...</p>';
                                });
                                

                                
                                
                                myMarker.setMap(map);
                                map.panTo(geolocate);
                                
                                
                             
	    		},errorSET);
                        
	    	} else {
	    		errorSET;
                        
                        
	    	}
                })();
//---------------------------------------------
// Google Map trigger with typeahead
//---------------------------------------------
            function changeTypeahead(obj, datum) {
             $('#resultUpdateSet').val(datum.id);
             
             var langLong=datum.lat+','+datum.lng;
             $('#resultUpdateSetLat').val(datum.lat);
             $('#resultUpdateSetLong').val(datum.lng);
             
             var pinkParksStyles = [
			{
				featureType: 'road.highway',
				elementType: 'all',
				stylers: [
					{ hue: '#e5e5e5' },
					{ saturation: -100 },
					{ lightness: 72 },
					{ visibility: 'simplified' }
				]
			},{
				featureType: 'water',
				elementType: 'all',
				stylers: [
					{ hue: '#30a5dc' },
					{ saturation: 47 },
					{ lightness: -31 },
					{ visibility: 'simplified' }
				]
			},{
				featureType: 'road',
				elementType: 'all',
				stylers: [
					{ hue: '#cccccc' },
					{ saturation: -100 },
					{ lightness: 44 },
					{ visibility: 'on' }
				]
			},{
				featureType: 'landscape',
				elementType: 'all',
				stylers: [
					{ hue: '#ffffff' },
					{ saturation: -100 },
					{ lightness: 100 },
					{ visibility: 'on' }
				]
			},{
				featureType: 'poi.park',
				elementType: 'all',
				stylers: [
					{ hue: '#d2df9f' },
					{ saturation: 12 },
					{ lightness: -4 },
					{ visibility: 'on' }
				]
			},{
				featureType: 'road.arterial',
				elementType: 'all',
				stylers: [
					{ hue: '#e5e5e5' },
					{ saturation: -100 },
					{ lightness: 56 },
					{ visibility: 'on' }
				]
			},{
				featureType: 'administrative.locality',
				elementType: 'all',
				stylers: [
					{ hue: '#000000' },
					{ saturation: 0 },
					{ lightness: 0 },
					{ visibility: 'on' }
				]
			}
		];
             
             
               var pinkMapType = new google.maps.StyledMapType(pinkParksStyles,{name: "Pink Parks"});
               var mapOptions = {
                   center: new google.maps.LatLng(datum.lat, datum.lng),
                   zoom: 15,
                   mapTypeId: google.maps.MapTypeId.ROADMAP,
                   panControl: false,
                   zoomControl: true,
                   scaleControl: true,
                   mapTypeControl: false,
                   mapTypeControlOptions: {
                   mapTypeIds: [google.maps.MapTypeId.ROADMAP, 'map_layout_styles']
                   }
               };
               
              var map = new google.maps.Map(document.getElementById("google_canvas"),mapOptions);
              map.mapTypes.set('map_layout_styles',pinkMapType);
              map.setMapTypeId('map_layout_styles'); 
               
              var center = new google.maps.LatLng(datum.lat, datum.lng);
              map.setZoom(15);
              map.panTo(center);
              map.setZoom(15);
              
             var myMarker = new google.maps.Marker({
             position: new google.maps.LatLng(datum.lat, datum.lng),
             draggable: true
             });
             
             google.maps.event.addListener(myMarker, 'dragend', function(evt){
                //document.getElementById('current').innerHTML = '<p>Marker dropped:  ' + evt.latLng.lat().toFixed(3) + ',' + evt.latLng.lng().toFixed(3) + '</p>';
             $('#resultUpdateSetLat').val(evt.latLng.lat());
             $('#resultUpdateSetLong').val(evt.latLng.lng());
             });
             
             google.maps.event.addListener(myMarker, 'dragstart', function(evt){
             $('#resultUpdateSetLat').val(evt.latLng.lat());
             $('#resultUpdateSetLong').val(evt.latLng.lng());
             });
             
             myMarker.setMap(map);
             map.panTo(datum.lat,datum.lng);
             //map.setCenter(datum.lat,datum.lng);
       };
       
       
$('#radioBtn a').on('click', function(){
    var sel = $(this).data('title');
    var tog = $(this).data('toggle');
    $('#'+tog).prop('value', sel);
    
    $('a[data-toggle="'+tog+'"]').not('[data-title="'+sel+'"]').removeClass('active').addClass('notActive');
    $('a[data-toggle="'+tog+'"][data-title="'+sel+'"]').removeClass('notActive').addClass('active');
})



$('#url_expander').keyup(function() {
$("#mediaurl").val(null);
$("#mediatype").val(null);
$("#resultsLoadingEmbedError").html("");
$("#embedMediaHolder").html("");

var url_expand=$("#url_expander").val();  
delay(function(){	
  
  $("#resultsLoadingEmbed").show();
  $("button[name='save']").hide();
  
    $.ajax({
        type: 'GET',
        url: 'https://api.embed.ly/1/extract?key=2c419230c289488fa9bf8c23f4a5faa0&url='+url_expand+'&maxwidth=500&format=json',
        dataType:"json", //to parse string into JSON object,
        success: function(data){ 
           
            
            var image = data.images[0]['url'].length;
            var mediaType = data.media.type;
           
            if (mediaType=="video") {
              var widthReplace = data.media.html;
              mediavalue = widthReplace.replace('width="500"','width="100%"');
              
              var heightReplace = mediavalue;
              NewMediavalue = heightReplace.replace('height="281"','height="400"');
              
              var mediaurl = data.url;
              $("#embedMediaHolder").html(""+NewMediavalue+"<br><br>");
              $("#resultsLoadingEmbed").hide();
              $("#mediaurl").val(mediaurl);
              $("#mediatype").val("video");
              
              var brands_services=$("#brands_services").val().length;

               if(brands_services < 1) {
               $("button[name='save']").hide();
               }else{
               $("button[name='save']").show();
               }
              
              
             }else{
             
            if(image > 0){
              $("#embedMediaHolder").html("<img src='"+SERVER_URL+"system_files/modules/resizer/imgresize?src="+data.images[0]['url']+"&w=800&h=400&zc=1&a=t' class='img-responsive embedImg'>");
              $("#mediaurl").val(data.images[0]['url']);
              $("#mediatype").val("image");
              $("#resultsLoadingEmbed").hide();
              var brands_services=$("#brands_services").val().length;

               if(brands_services < 1) {
               $("button[name='save']").hide();
               }else{
               $("button[name='save']").show();
               }
              
             }
            }
             
        },
        
        error: function(jqXHR, textStatus, errorThrown){
          $("#resultsLoadingEmbed").hide();
	  $("#resultsLoadingEmbedError").html("No image or video found");
          $("#resultsLoadingEmbedError").fadeOut(5000);
          $("#mediaurl").val(null);
          $("#mediatype").val(null);
          
           var brands_services=$("#brands_services").val().length;

            if(brands_services < 1) {
            $("button[name='save']").hide();
            }else{
            $("button[name='save']").show();
            }
         
          
        }
    });
   
    return false;//suppress natural form submission
     }, 1500 );
});



var delay = (function(){
  var timer = 0;
  return function(callback, ms){
    clearTimeout (timer);
    timer = setTimeout(callback, ms);
  };
})();

$(function () {
  $('[data-toggle="tooltip"]').tooltip()
});
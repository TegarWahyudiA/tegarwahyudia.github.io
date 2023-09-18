//$(window).resize(function(){
  if($(window).width()<720){
	  $("a.brand").append("<i class=\'g g-list\'></i>");
	  $(".navbar .g-list")
	    .attr("style","color:#aaa; position:absolute; top:10px; right:10px;")
	    .click(function(){
	      if($(".navbar").css("overflow")=="hidden") $(".navbar").css("overflow", "visible");
	      else $(".navbar").css("overflow","hidden");
	      return false;
	    });
  } else {
	$(".navbar .g-list").attr("style","color:#000;")
  	$(".navbar").css("overflow","visible");
  }
//});

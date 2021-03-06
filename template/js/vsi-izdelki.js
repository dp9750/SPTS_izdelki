document.title = 'SPTŠ | Vsi izdelki';

var coll = document.getElementsByClassName("moznosti-buttom");
var i;

for (i = 0; i < coll.length; i++) {
  coll[i].addEventListener("click", function() {
    this.classList.toggle("moznosti-active");
    var content = this.nextElementSibling;
    if (content.style.display === "block") {
      content.style.display = "none";
    } else {
      content.style.display = "block";
    }
  });
}

$("button.switcher").bind("click", function(e){
		e.preventDefault();

		var theid = $(this).attr("id");
		var theproducts = $("ul#products");
		var classNames = $(this).attr('class').split(' ');

		if($(this).hasClass("active")) {
			return false;
		} else {
      if(theid == "gridview") {
          $(this).addClass("btn-active");
          $("#listview").removeClass("btn-active");
          theproducts.removeClass("list");
          theproducts.addClass("grid");
  		} else if(theid == "listview") {
    			$(this).addClass("btn-active");
    			$("#gridview").removeClass("btn-active");
          theproducts.removeClass("grid")
    			theproducts.addClass("list");
  		}
		}
});

function izberiVse(){
  if(document.getElementsByName('oddelek')[0].checked) {
    for(var i = 0; i < document.getElementsByName('oddelki[]').length; i++)
      document.getElementsByName("oddelki[]")[i].checked = true;
  } else {
    for(var i = 0; i < document.getElementsByName('oddelki[]').length; i++)
      document.getElementsByName("oddelki[]")[i].checked = false;
  }
}

function showHint(str) {
  var xhttp;
  if (str.length == 0) {
    document.getElementById("txtHint").innerHTML = "";
    return;
  }
  xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      document.getElementById("txtHint").innerHTML = this.responseText;
    }
  };
  xhttp.open("GET", "vrniAvtorja.php?q="+str, true);
  xhttp.send();
}

function showHintNaslov(str) {
  var xhttp;
  if (str.length == 0) {
    document.getElementById("txtHintNaslov").innerHTML = "";
    return;
  }
  xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      document.getElementById("txtHintNaslov").innerHTML = this.responseText;
    }
  };
  xhttp.open("GET", "vrniNaslov.php?q="+str, true);
  xhttp.send();
}

function showPredlogHTML(idInput, idPanel, idSpan) {
  if($("#" + idInput).val() != "") {
    document.getElementById(idPanel).innerHTML = "<p>Predlog: </p><span id='" + idSpan + "'></span>";
  } else {
    $("#" + idPanel).empty();
  }
}

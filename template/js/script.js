function navbarSwitch() {
  var x = document.getElementById("navbar");
  if (x.className === "") {
    x.className += " responsive";
  } else {
    x.className = "";
  }
}

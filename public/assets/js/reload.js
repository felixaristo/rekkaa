// handling if duplicate page
if(!document.getElementById('layout-menu')) {
  let bd = document.getElementsByTagName('body');
  bd[0].innerHTML = '';
  window.location.reload();
}
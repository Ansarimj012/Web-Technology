document.addEventListener('DOMContentLoaded', function () {

  var fileTag = document.getElementById("pfp");
  var preview = document.getElementById("preview");

  if (!fileTag || !preview) return;

  fileTag.addEventListener("change", function () {
    changeImage(this);
  });

  function changeImage(input) {
    if (input.files && input.files[0]) {
      var reader = new FileReader();
      reader.onload = function (e) {
        preview.setAttribute('src', e.target.result);
      };
      reader.readAsDataURL(input.files[0]);
    }
  }

});
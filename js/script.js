function checkFirstFormValid() {
  if (
    $("#nb_variable").val().length >= 1 &&
    $("#nb_contrainte").val().length >= 1 &&
    $("#method").val().length >= 1
  ) {
    $("#continue").removeClass("disable_btn").attr("disabled", false);
  } else {
    $("#continue").addClass("disable_btn").attr("disabled", true);
  }
}


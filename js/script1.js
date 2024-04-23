document.getElementById('formFillContraintes').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent default form submission
    var c_v_values = document.getElementById('c_v_values').value;
    var contrainte_values = document.getElementById('contrainte_values').value;
    if (!c_v_values || !contrainte_values) {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Fill the fields!',
        });
    } else {
      
            this.submit();
            } 

});

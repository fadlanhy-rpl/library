function confirmDelete(event, formId) {
  event.preventDefault(); // Mencegah form submit secara langsung
  Swal.fire({
    title: "Are you sure?",
    text: "You won't be able to revert this!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Yes, delete it!",
  }).then((result) => {
    if (result.isConfirmed) {
      // Jika dikonfirmasi, submit form yang sesuai
      document.getElementById(formId).submit();
    }
  });
}

function confirmApprove(event, formId) { // Diadaptasi untuk submit form
  event.preventDefault(); // Mencegah form submit secara langsung
  Swal.fire({
    title: "Are you sure?",
    text: "Do you want to approve this report?", // Sesuaikan teks
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Yes, approve it!",
  }).then((result) => {
    if (result.isConfirmed) {
      // Jika dikonfirmasi, submit form yang sesuai
      document.getElementById(formId).submit();
    }
  });
}

// Anda bisa menambahkan fungsi konfirmasi lain di sini
// Misalnya:
function confirmAction(event, formId, title = "Are you sure?", text = "Do you want to proceed?", confirmButtonText = "Yes, proceed!") {
  event.preventDefault();
  Swal.fire({
    title: title,
    text: text,
    icon: "question", // Atau 'info', 'warning'
    showCancelButton: true,
    confirmButtonColor: "#3085d6", // Biru
    cancelButtonColor: "#d33",   // Merah
    confirmButtonText: confirmButtonText,
  }).then((result) => {
    if (result.isConfirmed) {
      document.getElementById(formId).submit();
    }
  });
}
<script>
    // Currency CRUD AJAX
    $(document).ready(function() {
        // Delete Currency
        $(document).on('click', '.delete-currency', function(e) {
            e.preventDefault();
            var id = $(this).data('id');

            Swal.fire({
                title: 'Are you sure?',
                text: "You want to delete this currency?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: base_url + '/admin/currency/delete/' + id,
                        data: {
                            id: id,
                            [csrf_token]: csrf_hash
                        },
                        dataType: 'json',
                        success: function(result) {
                            console.log('Delete response:', result);

                            if (result.csrf_token) {
                                csrf_token = result.csrf_token;
                                csrf_hash = result.csrf_hash;
                            }

                            if (result.error == true) {
                                iziToast.error({
                                    title: 'Error!',
                                    message: result.message,
                                    position: 'topRight'
                                });
                            } else {
                                $('#currency_table').bootstrapTable('refresh');
                                showToastMessage(result.message, 'success');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Delete Error Details:');
                            console.error('Status:', status);
                            console.error('Error:', error);
                            console.error('Response Text:', xhr.responseText);
                            console.error('Status Code:', xhr.status);
                            console.error('Status Text:', xhr.statusText);

                            try {
                                var response = JSON.parse(xhr.responseText);
                                if (response.message) {
                                    showToastMessage(response.message, 'error');
                                    // reload the table to reflect changes
                                    $('#currency_table').bootstrapTable('refresh');
                                } else {
                                    showToastMessage('An error occurred while deleting the currency. Please try again.', 'error');
                                }
                            } catch (e) {
                                showToastMessage('An error occurred while deleting the currency. Please try again.', 'error');
                            }
                        }
                    });
                }
            });
        });

        // Edit Currency (redirect to edit page)
        $(document).on('click', '.edit-currency', function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            window.location = base_url + '/admin/currency/edit/' + id;
        });
    });
</script>
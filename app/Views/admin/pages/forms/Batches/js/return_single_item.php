<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('confirmComponent', () => ({
            confirmReturn(item) {
                let validator = null; // Shared validator instance

                Swal.fire({
                    title: 'Confirm Return',
                    draggable: true,
                    html: `
                    <form id="returnForm">
                        <div class="form-group row">
                            <label for="swal-input-quantity" class="col-sm-2 col-form-label">Return Quantity:</label>
                            <div class="col-sm-10">
                                <input id="swal-input-quantity" name="quantity" type="number" class="form-control" min="1" step="1" value="${item.quantity}">
                                <small class="form-text text-muted">quantity: ${item.quantity}</small>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="swal-input-price" class="col-sm-2 col-form-label">Return Price:</label>
                            <div class="col-sm-10">
                                <input id="swal-input-price" name="price" type="number" class="form-control" step="0.1" value="${item.cost_price}">
                                <small class="form-text text-muted">cost price: ${item.cost_price}</small>
                            </div>
                        </div>
                        <div class="form-group">
                            <textarea id="swal-input-reason" name="reason" class="swal2-textarea" placeholder="Reason for return..."></textarea>
                        </div>
                    </form>
                `,
                    focusConfirm: false,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, return it!',
                    cancelButtonText: 'No, cancel!',
                    willOpen: () => {
                        validator = new JustValidate('#returnForm', {
                            validateBeforeSubmitting: true,
                            lockForm: true,
                            validateOnInput: true,
                            focusInvalidField: true,
                            errorFieldCssClass: 'is-invalid',
                            successFieldCssClass: 'is-valid',
                            errorLabelCssClass: 'invalid-feedback',
                            errorLabelStyle: {
                                color: '#dc3545',
                                fontSize: '0.875rem',
                            },
                        });

                        validator
                            .addField('#swal-input-quantity', [{
                                    rule: 'required',
                                    errorMessage: 'Quantity is required'
                                },
                                {
                                    rule: 'number',
                                    errorMessage: 'Quantity must be a number'
                                },
                                {
                                    rule: 'minNumber',
                                    value: 1,
                                    errorMessage: 'Minimum is 1'
                                },
                                {
                                    rule: 'maxNumber',
                                    value: Number(item.quantity) || 1,
                                    errorMessage: `Maximum is ${item.quantity}`
                                }
                            ])
                            .addField('#swal-input-price', [{
                                    rule: 'required',
                                    errorMessage: 'Price is required'
                                },
                                {
                                    rule: 'number',
                                    errorMessage: 'Cost Price must be a number'
                                },
                                {
                                    rule: 'minNumber',
                                    value: 0,
                                    errorMessage: 'Minimum is 0'
                                },
                                {
                                    rule: 'maxNumber',
                                    value: Number(item.cost_price) || 0,
                                    errorMessage: `Maximum is ${item.cost_price}`
                                }
                            ]);
                    },
                    preConfirm: () => {

                        if (!validator.isValid) {
                            Swal.showValidationMessage('Please fix form errors.');
                            return; // or reject('Validation failed');
                        }

                        Swal.showLoading(); // ✅ Show loading only if valid


                        const quantity = document.getElementById('swal-input-quantity').value;
                        const price = document.getElementById('swal-input-price').value;
                        const reason = document.getElementById('swal-input-reason').value;

                        return ({
                            quantity: parseFloat(quantity),
                            return_price: parseFloat(price),
                            return_reason: reason
                        });

                    }

                }).then((result) => {
                    if (result.isConfirmed) {
                        const result_data = result.value;

                        const formData = new FormData();
                        formData.append('return_data', JSON.stringify(item)); // must be a string
                        formData.append('return_quantity', result_data.quantity);
                        formData.append('return_price', result_data.return_price);
                        formData.append('return_reason', result_data.return_reason || 'No reason provided');
                        formData.append('return_date', new Date().toISOString().slice(0, 10)); // YYYY-MM-DD
                        formData.append('action', 'return');

                        const response = axios.post('<?= base_url('admin/batches/return_batch') ?>', formData)
                            .then(res => {
                                Swal.fire('Success!', 'Item returned successfully.', 'success').then(() => {
                                    // Optionally, you can reload the page or update the UI
                                    location.reload();
                                });
                            }).catch(err => {
                                Swal.fire('Error!', 'Failed to return item.', 'error');
                            });
                    }
                });
            }
        }));
    });
</script>
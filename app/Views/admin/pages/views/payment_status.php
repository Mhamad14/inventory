    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Payment Status</h1>
            </div>
            <div class="row justify-content-center">
                <div class="col-md-4">
                    <?php
                    if ($status) {
                    ?>
                        <div class="card-payment">
                            <div class="h1-payment  p-payment">
                                <i class="bi bi-check-circle-fill text-success checkmark i-payment"></i>
                                <h1><?= $status ? "success" : " Failed" ?> </h1>
                            </div>
                            <p>Your subscription has been created Successfully! </p>
                        </div>
                    <?php
                    } else {
                    ?>
                        <div class="card-payment">
                            <div class="h1-payment  p-payment">
                                <i class="bi bi-x-circle-fill text-danger checkmark i-payment-failed"></i>
                                <h1><?= $status ? "success" : " Failed" ?> </h1>
                            </div>
                            <p>Oops, Your transaction has been failed! </p>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </section>
    </div>
             <div class="row mt-4">
                    <div class="col">
                        <div class="card">
                            <div class="card-body">

                                <h5 class="card-title mb-1 text-small text-secondery" style="cursor: pointer;" data-toggle="collapse" data-target="#orderMessageBody" aria-expanded="false" aria-controls="orderMessageBody">
                                    <?= labels('order_message', 'Order Message') ?>
                                </h5>
                                <blockquote class="blockquote collapse show text-small mb-0" id="orderMessageBody">
                                    <p><?= $purchase['message'] ?></p>
                                </blockquote>
                            </div>
                        </div>
                    </div>
                </div>
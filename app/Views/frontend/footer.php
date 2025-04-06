<!-- footer -->
<?php $data = get_settings('general', true) ?>
<footer id="footer" class="fixed-bottom">
    <div class="container">
        <div class="row">
            <!-- <div class="col-lg-3 col-md-6 footer-contact mt-5">
                <h3><?= !empty($data['title']) ? $data['title'] : "UpBiz" ?></h3>
                <p class="text-white my-1">
                    <?= !empty($data['address']) ? strip_tags($data['address']) : "" ?>
                </p>
                <strong><i class="bi bi-phone"></i></strong> <?= !empty($data['phone']) ? $data['phone'] : "+1 5589 55488 55" ?><br>
                <strong><i class="bi bi-envelope"></i></strong> <?= !empty($data['support_email']) ? $data['support_email'] : "support@example.com" ?><br>
            </div> -->

            <!-- <div class="col-lg-3 col-md-6 footer-links mt-5">
                <h4>Useful Links</h4>
                <ul>
                    <li>
                        <i class="bi bi-arrow-right-short"></i> <a href="<?= base_url('home') ?>">Home</a>
                    </li>
                    <li><i class="bi bi-arrow-right-short"></i> <a href="<?= base_url('about') ?>">About us</a></li>
                    <li><i class="bi bi-arrow-right-short"></i> <a href="<?= base_url('features') ?>">Features</a></li>
                    <li><i class="bi bi-arrow-right-short"></i> <a href="<?= base_url('pricing') ?>">Pricing</a></li>
                </ul>
            </div> -->
            <div class="col-lg-3 col-md-6 footer-links mt-5">
                <h4></h4>

                <ul>
                    <li><i class="bi bi-arrow-right-short"></i> <a href="<?= base_url('privacy_policy') ?>">Privacy Policy</a></li>
                    <li><i class="bi bi-arrow-right-short"></i> <a href="<?= base_url('terms_and_conditions') ?>">Terms & Condition</a></li>
                    <li><i class="bi bi-arrow-right-short"></i> <a href="<?= base_url('refundpolicy') ?>">Refund Policy</a></li>
                    <li><i class="bi bi-arrow-right-short"></i> <a href="<?= base_url('contact') ?>">Contact</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-6 footer-links mt-5">
                <h4>Our Social Links</h4>
                <div class="mt-3">
                    <a href="" class="social-links"><i class="bi bi-twitter"></i></a>
                    <a href="" class="social-links"><i class="bi bi-facebook"></i></a>
                    <a href="" class="social-links"><i class="bi bi-instagram bx bxl-instagram"></i></a>
                    <a href="" class="social-links"><i class="bi bi-linkedin bx bxl-linkedin"></i></a>
                </div>
            </div>

            <hr>
            <div class="d-flex justify-content-center copyright">
                <p> Copyright &copy; <?= date("Y") ?>
                    Designed By <a href="https://infinitietech.com" target="_blank">Infinitie Technologies</a>
                </p>
            </div>
        </div>
    </div>
</footer>

<a href="#" class="back-to-top d-flex align-items-center justify-content-center active"><i class="bi bi-arrow-up-short"></i></a>
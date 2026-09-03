<footer class="footer">
    <div class="footer-container">

        <!-- Brand -->
        <div class="footer-brand">
            <a href="index.php?lang=<?= $lang ?>" class="footer-logo">
                <img src="assets/images/logo.png" alt="FRISUCODE Logo" class="footer-logo-img" style="height: 60px; width: auto; align-content: center; object-fit: contain;" onerror="this.style.display='none'">
                <span class="logo-text">FRISU<span style="color:var(--primary);">CODE</span></span>
            </a>
            <p><?= $L['footer_about'] ?? 'Empowering communities through education across East Africa.' ?></p>
            <div class="social-links">
                <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook"></i></a>
                <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                <a href="#" aria-label="Twitter"><i class="fa-brands fa-twitter"></i></a>
                <a href="#" aria-label="LinkedIn"><i class="fa-brands fa-linkedin"></i></a>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="footer-links">
            <h4><?= $L['quick_links'] ?? 'Quick Links' ?></h4>
            <a href="about.php?lang=<?= $lang ?>"><?= $L['about'] ?? 'About Us' ?></a>
            <a href="programs.php?lang=<?= $lang ?>"><?= $L['programs'] ?? 'Our Programs' ?></a>
            <a href="impact.php?lang=<?= $lang ?>"><?= $L['impact'] ?? 'Impact Stories' ?></a>
            <a href="partners.php?lang=<?= $lang ?>"><?= $L['nav_partners'] ?? 'Partners' ?></a>
            <a href="reports.php?lang=<?= $lang ?>"><?= $L['nav_reports'] ?? 'Reports' ?></a>
            <a href="contact.php?lang=<?= $lang ?>"><?= $L['contact'] ?? 'Contact Us' ?></a>
        </div>

        <!-- Contact Info -->
        <div class="footer-links">
            <h4><?= $L['contact'] ?? 'Contact' ?></h4>
            <a href="#"><i class="fa-solid fa-location-dot"></i> Nambala, Kikwe, Arusha - Tanzania</a>
            <a href="mailto:frisucode641@gmail.com"><i class="fa-solid fa-envelope"></i> frisucode641@gmail.com</a>
            <a href="tel:+255754917546"><i class="fa-solid fa-phone"></i> +255 754 917 546</a>
            <a href="donate.php?lang=<?= $lang ?>" style="margin-top:8px;background:rgba(37,99,235,0.1);border-radius:8px;padding:8px 12px;color:var(--primary);font-weight:700;">
                <i class="fa-solid fa-heart"></i> <?= $L['donate'] ?? 'Donate Now' ?>
            </a>
        </div>

        <!-- Newsletter -->
        <div class="footer-newsletter">
            <h4><?= $L['newsletter'] ?? 'Stay Updated' ?></h4>
            <p><?= $L['newsletter_text'] ?? 'Subscribe to our newsletter for impact stories and updates.' ?></p>
            <form action="#" class="newsletter-form" onsubmit="handleNewsletter(event)">
                <input type="email" placeholder="<?= $L['donor_email'] ?? 'Your email address' ?>" required aria-label="Email">
                <button type="submit" class="btn-primary" style="width:100%;">
                    <i class="fa-solid fa-paper-plane"></i> <?= $L['subscribe'] ?? 'Subscribe' ?>
                </button>
            </form>
            <div id="nl-msg" style="display:none;margin-top:10px;background:rgba(255,255,255,0.07);padding:10px;border-radius:8px;color:#4ade80;font-size:0.9rem;text-align:center;">
                <i class="fa-solid fa-circle-check"></i> <?= $L['subscribed_msg'] ?? 'Thank you for subscribing!' ?>
            </div>
        </div>

    </div>

    <div class="footer-bottom">
        <div class="container d-flex" style="justify-content: space-between; border-top: none; padding-top: 0; align-items: center; flex-wrap: wrap; gap: 12px;">
            <p style="margin-bottom: 0;"><?= $L['copyright'] ?? ('© ' . date('Y') . ' FRISUCODE. All rights reserved.') ?></p>
            <div class="footer-legal">
                <a href="privacy.php?lang=<?= $lang ?>" style="display:inline; margin-right: 15px;"><?= $L['privacy'] ?? 'Privacy Policy' ?></a>
                <a href="terms.php?lang=<?= $lang ?>" style="display:inline;"><?= $L['terms'] ?? 'Terms of Service' ?></a>
            </div>
        </div>
    </div>
</footer>

<script>
function handleNewsletter(e) {
    e.preventDefault();
    document.getElementById('nl-msg').style.display = 'block';
    e.target.reset();
}
</script>
</body>
</html>

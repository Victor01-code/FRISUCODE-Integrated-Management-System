<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* =========================
   LANGUAGE HANDLING
========================= */
$lang = $_GET['lang'] ?? $_SESSION['lang'] ?? 'en';
$_SESSION['lang'] = $lang;

$langFile = __DIR__ . "/../lang/{$lang}.php";
if (!file_exists($langFile)) {
    $langFile = __DIR__ . "/../lang/en.php";
}

require $langFile;

/* Safety fallback */
if (!isset($L) || !is_array($L)) {
    $L = [];
}

/* Determine active page for nav highlighting */
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>">
<head>
    <meta charset="UTF-8">
    <title>
        FRISUCODE – <?= $L['hero_title'] ?? 'Empowering Communities Through Education' ?>
    </title>
    <meta name="description" content="<?= $L['about_subtitle'] ?? 'Friends Support for Community Development' ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- SEO & Social Meta Tags -->
    <meta property="og:title" content="FRISUCODE – Empowering Communities Through Education">
    <meta property="og:description" content="<?= $L['about_subtitle'] ?? 'Friends Support for Community Development' ?>">
    <meta property="og:image" content="/frisucode_ms/assets/images/logo.png">
    <meta property="og:url" content="https://www.frisucode.org/">
    <meta property="og:type" content="website">
    <meta name="twitter:card" content="summary_large_image">
    <link rel="canonical" href="https://www.frisucode.org/">
    <!-- JSON-LD Structured Data -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "NGO",
      "name": "FRISUCODE",
      "url": "https://www.frisucode.org/",
      "logo": "https://www.frisucode.org/assets/images/logo.png",
      "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "+255-754-917-546",
        "contactType": "customer service"
      }
    }
    </script>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="icon" type="image/png" href="/frisucode_ms/assets/images/logo.png">
</head>

<body>

<header class="navbar" id="mainNavbar">
    <!-- Logo -->
    <div class="logo">
        <a href="index.php?lang=<?= $lang ?>">
            <img src="assets/images/logo.png" alt="FRISUCODE Logo" style="height:40px;" onerror="this.style.display='none'">
            <strong>FRISUCODE</strong>
        </a>
    </div>

    <!-- Navigation -->
    <nav class="nav-links" id="navLinks">
        <a href="index.php?lang=<?= $lang ?>" class="<?= $currentPage === 'index.php' ? 'nav-active' : '' ?>"><?= $L['home'] ?? 'Home' ?></a>
        <a href="about.php?lang=<?= $lang ?>" class="<?= $currentPage === 'about.php' ? 'nav-active' : '' ?>"><?= $L['about'] ?? 'About' ?></a>
        <a href="programs.php?lang=<?= $lang ?>" class="<?= $currentPage === 'programs.php' ? 'nav-active' : '' ?>"><?= $L['programs'] ?? 'Programs' ?></a>
        <a href="news.php?lang=<?= $lang ?>" class="<?= $currentPage === 'news.php' ? 'nav-active' : '' ?>"><?= $L['news'] ?? 'News' ?></a>
        <a href="impact.php?lang=<?= $lang ?>" class="<?= $currentPage === 'impact.php' ? 'nav-active' : '' ?>"><?= $L['impact'] ?? 'Impact' ?></a>
        <a href="partners.php?lang=<?= $lang ?>" class="<?= $currentPage === 'partners.php' ? 'nav-active' : '' ?>"><?= $L['nav_partners'] ?? 'Partners' ?></a>
        <a href="contact.php?lang=<?= $lang ?>" class="<?= $currentPage === 'contact.php' ? 'nav-active' : '' ?>"><?= $L['contact'] ?? 'Contact' ?></a>
        
        <!-- Mobile Actions -->
        <div class="mobile-menu-actions">
            <a href="system/auth/login.php" class="btn-outline" style="text-align: center; margin-top: 15px; display: block; border-bottom: none;">
                <?= $L['staff_login'] ?? 'Staff Login' ?>
            </a>
            <a href="donate.php?lang=<?= $lang ?>" class="btn-primary" style="text-align: center; margin-top: 10px; display: flex; justify-content: center; width: 100%;">
                <?= $L['donate'] ?? 'Donate Now' ?>
            </a>
        </div>
    </nav>

    <!-- Actions -->
    <div class="nav-actions">
        <!-- Language Switcher -->
        <form method="get" class="lang-form">
            <?php
            // Pass all current GET params except lang
            foreach ($_GET as $key => $val) {
                if ($key !== 'lang') {
                    echo '<input type="hidden" name="' . htmlspecialchars($key) . '" value="' . htmlspecialchars($val) . '">';
                }
            }
            ?>
            <select name="lang" onchange="this.form.submit()" aria-label="Language">
                <option value="en" <?= $lang=='en'?'selected':'' ?>>🇬🇧 EN</option>
                <option value="sw" <?= $lang=='sw'?'selected':'' ?>>🇹🇿 SW</option>
                <option value="de" <?= $lang=='de'?'selected':'' ?>>🇩🇪 DE</option>
                <option value="fr" <?= $lang=='fr'?'selected':'' ?>>🇫🇷 FR</option>
                <option value="es" <?= $lang=='es'?'selected':'' ?>>🇪🇸 ES</option>
            </select>
        </form>

        <a href="system/auth/login.php" class="btn-outline desktop-only-btn">
            <?= $L['staff_login'] ?? 'Staff Login' ?>
        </a>

        <a href="donate.php?lang=<?= $lang ?>" class="btn-primary desktop-only-btn">
            <?= $L['donate'] ?? 'Donate Now' ?>
        </a>

        <!-- Mobile hamburger -->
        <button class="hamburger-btn" id="hamburgerBtn" onclick="toggleMobileNav()" aria-label="Toggle menu" aria-expanded="false">
            <span></span><span></span><span></span>
        </button>
    </div>
</header>

<!-- Mobile nav overlay -->
<div class="mobile-nav-overlay" id="mobileOverlay" onclick="toggleMobileNav()"></div>

<script>
function toggleMobileNav() {
    const nav = document.getElementById('navLinks');
    const btn = document.getElementById('hamburgerBtn');
    const overlay = document.getElementById('mobileOverlay');
    const isOpen = nav.classList.toggle('mobile-open');
    btn.classList.toggle('open', isOpen);
    overlay.classList.toggle('visible', isOpen);
    btn.setAttribute('aria-expanded', isOpen);
}

// Sticky navbar scroll effect
window.addEventListener('scroll', function() {
    const navbar = document.getElementById('mainNavbar');
    if (window.scrollY > 50) {
        navbar.classList.add('scrolled');
    } else {
        navbar.classList.remove('scrolled');
    }
});
</script>
